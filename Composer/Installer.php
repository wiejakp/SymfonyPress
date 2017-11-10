<?php

namespace wiejakp\SymfonyPress\Composer;

use Composer\Script\Event;

/**
 * Class Installer
 *
 * @package wiejakp\SymfonyPress\Composer
 */
class Installer
{
    public static function install_symfony(Event $event): void
    {
        // composer settings
        $extras = $event->getComposer()->getPackage()->getExtra();

        // extracted settings
        $settings_dir = $extras['symfonypress-symfony-dir'];
        $settings_version = $extras['symfonypress-symfony-version'];

        // fetch info
        $directory = dirname(dirname(__FILE__)) . '/' . $settings_dir;
        $command = "composer create-project $settings_version $settings_dir -q;";

        if (!file_exists($directory)) {
            exec($command);
        }
    }

    public static function update_symfony(Event $event): void
    {
        // composer settings
        $extras = $event->getComposer()->getPackage()->getExtra();

        // extracted settings
        $settings_dir = $extras['symfonypress-symfony-dir'];

        // fetch info
        $directory = dirname(dirname(__FILE__)) . '/' . $settings_dir;
        $command = "composer update -d $directory";

        if (file_exists($directory)) {
            exec($command);
        }
    }

    public static function install_wordpress(Event $event): void
    {
        // composer settings
        $extras = $event->getComposer()->getPackage()->getExtra();

        // extracted settings
        $settings_dir = $extras['symfonypress-wordpress-dir'];

        // fetch info
        $directory = dirname(dirname(__FILE__)) . '/' . $settings_dir;
        $command = "wp core download --path='$directory';";

        if (!file_exists($directory)) {
            exec($command);
        }
    }

    public static function update_wordpress(Event $event): void
    {
        // composer settings
        $extras = $event->getComposer()->getPackage()->getExtra();

        // extracted settings
        $settings_dir = $extras['symfonypress-wordpress-dir'];

        // fetch info
        $directory = dirname(dirname(__FILE__)) . '/' . $settings_dir;
        $command = "wp core update --path='$directory';";

        if (!file_exists($directory)) {
            exec($command);
        }
    }
}
