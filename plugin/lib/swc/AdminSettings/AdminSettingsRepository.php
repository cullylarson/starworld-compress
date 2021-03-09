<?php

namespace Swc\AdminSettings;

use Swc\Tools\Arr;

class AdminSettingsRepository {
    const WP_ADMIN_SETTINGS_NAME = "swc_settings";
    const WP_META_NODE_PATH = 'swc_node_path';

    /**
     * @return Entity\AdminSettings
     */
    public static function getAll() {
        $settingsArr = self::fetchSettings();

        $settings = new AdminSettingsEntity();

        $settings->NodePath = Arr::get($settingsArr, self::WP_META_NODE_PATH);

        /*
         * Done!
         */

        return $settings;
    }

    private static function fetchSettings() {
        $options = get_option(self::WP_ADMIN_SETTINGS_NAME);

        return is_array($options)
            ? $options
            : [];
    }
}
