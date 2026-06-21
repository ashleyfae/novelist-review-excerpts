<?php

use AshleyFae\SoftwareUpdater\DataTransferObjects\PluginLicenseConfig;
use AshleyFae\SoftwareUpdater\Exceptions\ApiRequestFailedException;
use AshleyFae\SoftwareUpdater\SDK;

/**
 * NovelistLicense.php
 *
 * @package   novelist
 * @copyright Copyright (c) 2026, Ashley Gibson
 * @license   MIT
 */
class NovelistLicense
{
    public function __construct(
        protected string $pluginName,
        protected string $pluginFile,
        protected string $productUuid,
        protected string $currentPluginVersion,
        protected string $optionName
    ) {
        $this->addHooks();
    }

    protected function addHooks() : void
    {
        // initializes with the licensing SDK
        add_action('software_updater_sdk_loaded', [$this, 'registerLicense']);

        // register in settings UI
        add_filter('novelist/settings/licenses', [$this, 'addSettingsField']);

        // intercept get_option() for the compat status option and morph SDK data into EDD format
        // this is necessary because Novelist core still expects EDD format
        add_filter('pre_option_' . $this->optionName . '_compat_status', [$this, 'compatStatusOption']);

        // license activation and deactivation
        add_action('admin_init', [$this, 'handleLicenseAction']);

        // display any stored activation/deactivation error notices
        add_action('admin_notices', [$this, 'displayAdminNotice']);
    }

    public function registerLicense(SDK $sdk) : void
    {
        try {
            $sdk->register(
                new PluginLicenseConfig(
                    optionName: $this->optionName,
                    productId: $this->productUuid,
                    pluginFile: $this->pluginFile,
                    version: $this->currentPluginVersion
                )
            );
        } catch(Exception $e) {
            error_log($e->getMessage());
        }
    }

    /**
     * Reads the real status provided by the SDK and transforms it into the old-style EDD format expected by
     * {@see novelist_license_key_callback()}
     */
    public function compatStatusOption(mixed $value) : ?object
    {
        try {
            $status = SDK::instance()->license($this->optionName)->getStatus();
        } catch (Exception $e) {
            return null;
        }

        if ($status === null) {
            return null;
        }

        if ($status->status->isActive()) {
            if (! $status->isActivatedFor(home_url())) {
                return (object) [
                    'success' => false,
                    'error'   => 'site_inactive',
                ];
            }

            return (object) [
                'success' => true,
                'license' => 'valid',
                'expires' => $status->expiresAt?->format('Y-m-d H:i:s') ?? 'lifetime',
            ];
        }

        if ($status->status->isExpired()) {
            return (object) [
                'success' => false,
                'error'   => 'expired',
                'expires' => $status->expiresAt?->format('Y-m-d H:i:s') ?? '',
            ];
        }

        // disabled or any unrecognised status
        return (object) [
            'success' => false,
            'error'   => 'missing',
        ];
    }

    /**
     * Adds the settings field to the UI.
     */
    public function addSettingsField($settings) : array
    {
        $newSettings = [
            'main' => [
                $this->optionName => [
                    'id' => $this->optionName,
                    'name' => sprintf(__('%s License Key', 'novelist'), $this->pluginName),
                    'desc' => '',
                    'type' => 'license_key',
                    'options' => [
                        'version' => 2.0,

                        // legacy EDD licensing
                        'is_valid_license_option' => $this->optionName.'_compat_status',
                    ],
                    'size' => 'regular',
                ],
            ],
        ];

        if (is_array($settings) && array_key_exists('main', $settings)) {
            $newSettings = array_merge($settings['main'], $newSettings['main']);
        } else {
            $newSettings = $newSettings['main'];
        }

        return ['main' => $newSettings];
    }

    public function handleLicenseAction() : void
    {
        if (
            ! isset($_POST['novelist_settings']) ||
            ! current_user_can('manage_novelist_settings')
        ) {
            return;
        }

        if (
            ! isset($_POST[$this->optionName.'-nonce']) ||
            ! wp_verify_nonce($_POST[$this->optionName.'-nonce'], $this->optionName.'-nonce')
        ) {
            return;
        }

        if (isset($_POST[$this->optionName.'_deactivate'])) {
            $this->handleDeactivation();
        } else {
            $submittedKey = sanitize_text_field($_POST['novelist_settings'][$this->optionName] ?? '');

            if (empty($submittedKey)) {
                delete_option($this->optionName);
                delete_option($this->optionName.'_status');
                return;
            }

            // Don't activate while another license on the same page is being deactivated
            foreach (array_keys($_POST) as $postKey) {
                if (str_ends_with($postKey, '_deactivate')) {
                    return;
                }
            }

            $currentKey = (string) get_option($this->optionName, '');
            $status     = SDK::instance()->license($this->optionName)->getStatus();
            if ($submittedKey === $currentKey && $status?->isActivatedFor(home_url())) {
                return;
            }

            $this->handleActivation($submittedKey);
        }
    }

    protected function handleActivation(string $submittedKey) : void
    {
        update_option($this->optionName, $submittedKey, false);

        try {
            SDK::instance()->license($this->optionName)->activate();
        } catch (ApiRequestFailedException $e) {
            $this->storeAdminNotice(
                sprintf(
                    /* translators: 1: plugin name */
                    __('Could not activate the %1$s license key. Please double check your key is correct and contact support if the issue persists.', 'novelist'),
                    $this->pluginName
                )
            );
        } catch (Exception $e) {
            $this->storeAdminNotice(
                sprintf(
                    /* translators: 1: plugin name */
                    __('Could not activate the %1$s license key. Please try again.', 'novelist'),
                    $this->pluginName
                )
            );
            error_log($e->getMessage());
        }
    }

    protected function handleDeactivation() : void
    {
        if (! get_option($this->optionName)) {
            $key = novelist_get_option($this->optionName, '');
            if (! empty($key)) {
                update_option($this->optionName, sanitize_text_field($key), false);
            }
        }

        if (! get_option($this->optionName)) {
            return;
        }

        try {
            SDK::instance()->license($this->optionName)->deactivate();
        } catch (ApiRequestFailedException $e) {
            $this->storeAdminNotice(
                sprintf(
                    /* translators: 1: plugin name, 2: error message from API */
                    __('Could not deactivate the %1$s license key: %2$s', 'novelist'),
                    $this->pluginName,
                    $e->getMessage()
                )
            );
        } catch (Exception $e) {
            $this->storeAdminNotice(
                sprintf(
                    /* translators: 1: plugin name */
                    __('Could not deactivate the %1$s license key. Please try again.', 'novelist'),
                    $this->pluginName
                )
            );
            error_log($e->getMessage());
        }
    }

    public function displayAdminNotice() : void
    {
        $transientKey = $this->noticeTransientKey();
        $message      = get_transient($transientKey);

        if (! is_string($message) || empty($message)) {
            return;
        }

        delete_transient($transientKey);

        echo '<div class="notice notice-error is-dismissible"><p>' . esc_html($message) . '</p></div>';
    }

    private function storeAdminNotice(string $message) : void
    {
        set_transient($this->noticeTransientKey(), $message, 60);
    }

    private function noticeTransientKey() : string
    {
        return $this->optionName . '_notice_' . get_current_user_id();
    }
}
