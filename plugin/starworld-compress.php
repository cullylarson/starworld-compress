<?php
   /**
   * Plugin Name: Starworld Compress
   * Description: Lossfully (but not noticeably) compresses images uploaded to Wordpress.
   * Version: 1.0.1
   * Author: Cully Larson (MAC)
   */

use Swc\Tools\Command;
use Swc\Tools\Wordpress;
use Swc\Tools\Rando;
use Swc\AdminSettings\AdminSettingsRepository;

use Phugly as F;
use function Phugly\call;
use function Phugly\compose;
use function Phugly\map;
use function Phugly\filter;
use function Phugly\getAt;

call_user_func( function() {
    $setupScripts = [
        __DIR__ . '/lib/setup/Autoload.php',
        __DIR__ . '/lib/setup/Config.php',
        __DIR__ . '/lib/setup/AdminMenu.php',
    ];

    foreach($setupScripts as $setupScript) {
        require_once($setupScript);
    }
});

// This is triggered after an upload and all image sizes are generated. Lets use compress
// all image sizes.
add_filter('wp_generate_attachment_metadata', function($metadata, $attachmentId, $context) {
    $getExtension = function($filePath) {
        $fileName = basename($filePath);
        $bits = explode('.', $fileName);

        return empty($bits)
            ? ''
            : $bits[count($bits) - 1];
    };

    $runCommand = function($commandStr) {
        $command = new Command();
        $command->exec($commandStr, Wordpress::getPluginPath());

        return $command->success();
    };

    $resizeOne = function($nodePath, $tmpFolder, $filePath) use ($runCommand, $settings) {
        $tmpPath = path_join($tmpFolder, basename($filePath));

        $success = $runCommand(implode(' ', [
            $nodePath,
            Wordpress::getPluginPath('image-minify.js'),
            '--in=' . escapeshellcmd($filePath),
            '--out=' . escapeshellcmd($tmpFolder),
        ]));

        if(!$success) {
            return false;
        }

        // replace the original file with our resized version
        @rename($tmpPath, $filePath);

        return true;
    };

    // only run when we originally upload the file
    if($context !== 'create') return $metadata;
    if(empty($metadata['file'])) return $metadata;

    $extension = strtolower($getExtension($metadata['file']));

    // only accept these file types
    if(!in_array($extension, ['jpg', 'jpeg', 'gif', 'png'])) {
        return $metadata;
    }

    $settings = AdminSettingsRepository::getAll();

    if(!$settings->NodePath || !@is_executable($settings->NodePath)) {
        return $upload;
    }

    // resize to a new file
    $originalPath = path_join(Wordpress::getUploadFolder(), $metadata['file']);
    $originalFolder = dirname($originalPath);
    $tmpFolder = path_join($originalFolder, 'tmp--starworld--' . Rando::str(12));

    if(!@mkdir($tmpFolder)) {
        return $metadata;
    }

    $sizes = empty($metadata['sizes']) ? [] : $metadata['sizes'];

    $resizedSourceFiles = call(compose(
        'array_values',
        map(function($name) use ($originalFolder) {
            return path_join($originalFolder, $name);
        }),
        filter(F\notEmpty),
        map(getAt('file', null))
    ), $sizes);

    $sourceFiles = array_merge($resizedSourceFiles, [$originalPath]);

    foreach($sourceFiles as $filePath) {
        $resizeOne($settings->NodePath, $tmpFolder, $filePath);
    }

    // clean up
    @rmdir($tmpFolder);

    return $metadata;
}, 10, 3);
