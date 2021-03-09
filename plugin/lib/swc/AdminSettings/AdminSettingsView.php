<?php

namespace Swc\AdminSettings;

class AdminSettingsView {
    public static function render($settingName) {
        // this field is added by Wordpress on success
        if(isset($_GET['settings-updated'])) {
            add_settings_error($settingName, $settingName . '-success', 'Settings saved.', 'updated');
        }

        ob_start();

        // messages
        settings_errors($settingName);
        ?>

        <div class="wrap swc-settings">
            <h1><?= esc_html(get_admin_page_title()); ?></h1>

            <form action="options.php" method="post">
                <?php
                settings_fields($settingName);
                do_settings_sections($settingName);
                submit_button('Save Settings');
                ?>
            </form>
        </div>

        <?php
        return ob_get_clean();
    }

    public static function renderTextField($settingName, $optionName, $optionVal, $description, $width='regular') {
        $descriptionHtml = empty($description)
            ? ''
            : "<p class='description'>{$description}</p>";

        switch($width) {
            case 'tiny': $widthClass = 'tiny-text'; break;
            case 'small': $widthClass = 'small-text'; break;
            case 'large': $widthClass = 'large-text'; break;
            case 'regular':
            default: $widthClass = 'regular-text'; break;
        }

        ob_start();
        ?>
        <input type='text' class='<?= $widthClass; ?>' name='<?= esc_attr($settingName); ?>[<?= esc_attr($optionName); ?>]' value='<?= esc_attr($optionVal); ?>' />
        <?= $descriptionHtml; ?>
        <?php
        return ob_get_clean();
    }
}
