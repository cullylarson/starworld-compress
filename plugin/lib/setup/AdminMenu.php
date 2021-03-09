<?php

use Swc\AdminSettings\AdminSettingsRepository;
use Swc\AdminSettings\AdminSettingsView;
use Swc\Tools\Wordpress;

add_action('admin_menu', function() {
    $outputSettingsPage = function() {
        if(!current_user_can('manage_options')) return;

        echo AdminSettingsView::render(AdminSettingsRepository::WP_ADMIN_SETTINGS_NAME);
    };

    add_menu_page(
        'Starworld Compress',
        'Starworld Compress',
        'manage_options',
        'starworld-compress',
        $outputSettingsPage,
        '' // URL to icon
    );
});

add_action('admin_init', function() {
    register_setting(AdminSettingsRepository::WP_ADMIN_SETTINGS_NAME, AdminSettingsRepository::WP_ADMIN_SETTINGS_NAME);

    $doNothing = function($args) { return; };

    $outputTextField = function($args) {
        $description = empty($args['description']) ? null : $args['description'];
        $width = empty($args['width']) ? null : $args['width'];

        $labelFor = $args['label_for'];
        $optionVal = Wordpress::getSettingOption(AdminSettingsRepository::WP_ADMIN_SETTINGS_NAME, $labelFor);

        echo AdminSettingsView::renderTextField(AdminSettingsRepository::WP_ADMIN_SETTINGS_NAME, $labelFor, $optionVal, $description, $width);
    };

    $getOutputFieldFromType = function($type) use ($outputTextField, $doNothing) {
        switch($type) {
            case 'text': return $outputTextField;
            default: return $doNothing;
        }
    };

    $addSettingsField = function($type, $name, $title, $sectionName, $description=null, $width='regular') use ($getOutputFieldFromType) {
        add_settings_field(
            $name,
            $title,
            $getOutputFieldFromType($type),
            AdminSettingsRepository::WP_ADMIN_SETTINGS_NAME,
            $sectionName,
            [
                'label_for' => $name,
                'description' => $description,
                'width' => $width,
            ]
        );
    };

    add_settings_section(
        'swc_settings_general',
        'General',
        $doNothing,
        AdminSettingsRepository::WP_ADMIN_SETTINGS_NAME
    );

    // GENERAL

    $addSettingsField(
        'text',
        AdminSettingsRepository::WP_META_NODE_PATH,
        'Node Path',
        'swc_settings_general',
        "The filesystem path tot he <code>node</code> command. If left blank, Starworld Compress will be disabled."
    );
});
