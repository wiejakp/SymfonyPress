<?php

namespace wiejakp\SymfonyPress\Composer;

use wiejakp\SymfonyPress\Composer\BaseInstaller;

// composer classes
use Composer\Script\Event;

class WordPressInstaller extends BaseInstaller
{
    // installer settings
    private static $SCRIPT;
    private static $CONSOLE;
    private static $PACKAGES;

    // installer directories
    private static $DIR_PRIVATE;
    private static $DIR_SHARED;
    private static $DIR_PUBLIC;

    private static function init()
    {
        // installer config
        self::$SCRIPT = self::$INSTALLERS['wordpress'];
        self::$CONSOLE = self::$BASE_VENDOR . self::$SCRIPT['console'];

        // config dirs
        self::$DIR_PRIVATE = self::$BASE_DIRS['private'] . self::$SCRIPT['dir'];
        self::$DIR_SHARED = self::$BASE_DIRS['shared'] . self::$SCRIPT['dir'];
        self::$DIR_PUBLIC = self::$BASE_DIRS['public'] . self::$SCRIPT['dir'];

        // init dirs
        self::dir_create(self::$DIR_PRIVATE);
        self::dir_create(self::$DIR_SHARED);
        self::dir_create(self::$DIR_PUBLIC);
    }

    protected static function update()
    {
        // init script content
        self::init();

        $wp_console = self::$CONSOLE;
        $wp_path = self::$DIR_PUBLIC;

        $cmd_update = "$wp_console core update --path='$wp_path'";

        self::command($cmd_update);
    }

    protected static function install()
    {
        // init script content
        self::init();

        // init install script
        self::install_required();
        self::install_core();
        self::install_shared();
    }

    private static function install_required()
    {
        $required = array_key_exists('require', self::$SCRIPT) ? self::$SCRIPT['require'] : null;

        if ($required) {
            foreach ($required as $repository => $version) {
                self::$PACKAGES[$repository] = self::base_vendor_path($repository);
            }
        }
    }

    private static function install_core()
    {
        $wp_console = self::$CONSOLE;
        $wp_version = self::$SCRIPT['version'];
        $wp_config = self::$SCRIPT['config'];
        $wp_path = self::$DIR_PUBLIC;
        $wp_params = self::$BASE_CONF['parameters'];
        $wp_project = self::$BASE_CONF['project'];

        $config_private = self::$DIR_PRIVATE . $wp_config;
        $config_public = self::$DIR_PUBLIC . $wp_config;

        $cmd_core = "$wp_console core download --version='$wp_version' --path='$wp_path'";;
        $cmd_config = implode(' ', [
            "$wp_console config create",
            "--dbname='" . $wp_params['database_name'] . "'",
            "--dbuser='" . $wp_params['database_user'] . "'",
            "--dbpass='" . $wp_params['database_password'] . "'",
            "--dbhost='" . $wp_params['database_host'] . "'",
            "--dbprefix='" . $wp_params['database_prefix'] . "'",
            "--dbcharset='" . $wp_params['database_charset'] . "'",
            "--path='" . $wp_path . "'",
        ]);
        $cmd_install = implode(' ', [
            "$wp_console core install",
            "--url='" . $wp_project['url'] . "'",
            "--title='" . $wp_project['title'] . "'",
            "--admin_user='" . $wp_project['user'] . "'",
            "--admin_password='" . $wp_project['pass'] . "'",
            "--admin_email='" . $wp_project['mail'] . "'",
            "--skip-email",
            "--path='" . $wp_path . "'",
        ]);

        self::command($cmd_core);

        // set up wp-config.php
        if (file_exists($config_private)) {
            if (file_exists($config_public)) {
                self::file_remove($config_public);
            }
        } else {
            self::command($cmd_config);

            self::file_move($config_public, $config_private);
        }

        // symlink config
        self::symlink_create($config_private, $config_public);

        // set up project settings and user
        self::command($cmd_install);
    }

    private static function install_shared()
    {
        $files = self::file_list(self::$DIR_SHARED);

        foreach ($files as $target) {
            $symlink = str_replace(self::$DIR_SHARED, self::$DIR_PUBLIC, $target);

            self::symlink_create($target, $symlink);
        }
    }
}
