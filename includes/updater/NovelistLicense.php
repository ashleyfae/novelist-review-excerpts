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
        public string $pluginFile,
        public string $productUuid,
        public string $currentPluginVersion,
        public string $optionName
    ) {
        $this->addHooks();
    }

    protected function addHooks() : void
    {
        add_action('software_updater_sdk_loaded', function(SDK $sdk) {
            try {
                $sdk->register(
                    new PluginLicenseConfig(
                        optionName: $this->optionName,
                        productId: $this->productUuid,
                        pluginFile: $this->pluginFile,
                        version: $this->currentPluginVersion
                    )
                );
            } catch(\Exception $e) {
                error_log($e->getMessage());
            }
        });
    }
}
