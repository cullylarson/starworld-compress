<?php

namespace Swc\Tools;

class Wordpress {
    // if pathTo provided will returnt the path to that asset
    public static function getPluginPath($pathTo = null) {
        $pluginPath = dirname(SWC_PLUGIN_FILE);

        if($pathTo) {
            return path_join($pluginPath, $pathTo);
        }
        else {
            return $pluginPath;
        }
    }

    public static function getUploadFolder() {
        $info = wp_upload_dir();

        return $info['basedir'];
    }

    public static function getSettingOption($settingName, $optionName, $default=null) {
        if(empty($optionName)) return $default;

        $settings = get_option($settingName);
        if(empty($settings)) return $default;

        return isset($settings[$optionName]) ? $settings[$optionName] : $default;
    }
}
