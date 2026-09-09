<?php

if (!defined('IS_ADMIN_FLAG') || IS_ADMIN_FLAG !== true) {
    die('Illegal Access');
}

use Zencart\PluginSupport\ScriptedInstaller as ScriptedInstallBase;

class ScriptedInstaller extends ScriptedInstallBase
{
    protected function executeInstall()
    {
        global $db;

        $column = $db->Execute("SHOW COLUMNS FROM " . TABLE_ORDERS . " LIKE 'gift_message'");
        if ($column->EOF) {
            $db->Execute("ALTER TABLE " . TABLE_ORDERS . " ADD gift_message TEXT NULL DEFAULT NULL");
        }

        $settings = [
            'CHECKOUT_GIFT_MESSAGE_STATUS' => [
                'configuration_title' => 'Checkout gift message: Enable field',
                'configuration_value' => 'true',
                'configuration_description' => 'Display the gift message field during checkout?',
                'configuration_group_id' => 5,
                'sort_order' => 900,
                'set_function' => "zen_cfg_select_option(array('true', 'false'), ",
            ],
            'CHECKOUT_GIFT_MESSAGE_NAME' => [
                'configuration_title' => 'Checkout gift message: Field name',
                'configuration_value' => 'Gift message',
                'configuration_description' => 'Enter the name shown beside the field, in order details, and in email.',
                'configuration_group_id' => 5,
                'sort_order' => 901,
            ],
            'CHECKOUT_GIFT_MESSAGE_DIRECTIONS' => [
                'configuration_title' => 'Checkout gift message: Directions',
                'configuration_value' => 'Enter the message you would like included with this order.',
                'configuration_description' => 'Enter the directions shown above the field. Leave blank for no directions.',
                'configuration_group_id' => 5,
                'sort_order' => 902,
            ],
            'CHECKOUT_GIFT_MESSAGE_PLACEHOLDER' => [
                'configuration_title' => 'Checkout gift message: Placeholder',
                'configuration_value' => 'Your gift message',
                'configuration_description' => 'Enter the placeholder shown inside the field. Leave blank for no placeholder.',
                'configuration_group_id' => 5,
                'sort_order' => 903,
            ],
            'CHECKOUT_GIFT_MESSAGE_MAX_LENGTH' => [
                'configuration_title' => 'Checkout gift message: Maximum length',
                'configuration_value' => '500',
                'configuration_description' => 'Choose the maximum checkout entry length, from 1 through 5000 characters.',
                'configuration_group_id' => 5,
                'sort_order' => 904,
            ],
        ];

        foreach ($settings as $key => $setting) {
            $existing = $db->Execute("SELECT configuration_id FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = '" . zen_db_input($key) . "' LIMIT 1");
            if ($existing->EOF) {
                $this->addConfigurationKey($key, $setting);
            }
        }

        parent::executeInstall();
    }

    protected function executeUninstall()
    {
        $this->deleteConfigurationKeys([
            'CHECKOUT_GIFT_MESSAGE_STATUS',
            'CHECKOUT_GIFT_MESSAGE_NAME',
            'CHECKOUT_GIFT_MESSAGE_DIRECTIONS',
            'CHECKOUT_GIFT_MESSAGE_PLACEHOLDER',
            'CHECKOUT_GIFT_MESSAGE_MAX_LENGTH',
        ]);

        // The orders.gift_message column contains store order records and is intentionally preserved.
        parent::executeUninstall();
    }
}
