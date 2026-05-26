<?php

use AshleyFae\SoftwareUpdater\DataTransferObjects\PluginLicenseConfig;
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
}
