<?php

namespace wiejakp\SymfonyPress\Composer;

use wiejakp\SymfonyPress\Composer\BaseInstaller;

// composer classes
use Composer\Script\Event;

class SymfonyInstaller extends BaseInstaller
{
    // installer settings
    private static $PROJECT;
    private static $SCRIPT;
    private static $CONSOLE;

    // installer directories
    private static $DIR_APPEND;
    private static $DIR_PRIVATE;
    private static $DIR_SHARED;
    private static $DIR_PUBLIC;

    private static function init()
    {
        // installer config
        self::$PROJECT = 'symfony';
        self::$SCRIPT = self::$INSTALLERS[self::$PROJECT];

        // config dirs
        self::$DIR_APPEND = self::$BASE_DIRS['private'] . 'composer/append/symfony/';
        self::$DIR_PRIVATE = self::$BASE_DIRS['private'] . self::$SCRIPT['dir'];
        self::$DIR_SHARED = self::$BASE_DIRS['shared'] . self::$SCRIPT['dir'];
        self::$DIR_PUBLIC = self::$BASE_DIRS['public'] . self::$SCRIPT['dir'];

        self::$CONSOLE = self::$DIR_PUBLIC . self::$SCRIPT['console'];

        // init dirs
        self::dir_create(self::$DIR_PRIVATE);
        self::dir_create(self::$DIR_SHARED);
        self::dir_create(self::$DIR_PUBLIC);
    }

    protected static function update()
    {
        // init script content
        self::init();

        // global vars
        $composer = self::$COMPOSER;
        $directory = self::$DIR_PUBLIC;

        // init install script
        $cmd_update = "$composer update --working-dir='$directory'";

        self::command($cmd_update);
    }

    protected static function install()
    {
        // init script content
        self::init();

        // init install script
        self::install_project();
        self::install_config();
        self::install_repository();
        self::install_required();
        self::install_shared();
        self::install_command();
        self::install_append();
    }

    private static function install_project()
    {
        // global vars
        $composer = self::$COMPOSER;

        // project vars
        $project_edition = self::$SCRIPT['edition'];
        $project_version = self::$SCRIPT['version'];
        $project_path = rtrim(self::$DIR_PUBLIC, '/');;

        $cmd_core = "$composer create-project $project_edition $project_path '$project_version' --no-interaction";

        self::command($cmd_core);
    }

    private static function install_config()
    {
        $files = self::file_list(self::$DIR_PRIVATE);

        foreach ($files as $target) {
            $symlink = str_replace(self::$DIR_PRIVATE, self::$DIR_PUBLIC, $target);

            self::symlink_create($target, $symlink);
        }
    }

    private static function install_repository()
    {
        $repositories = array_key_exists('repository', self::$SCRIPT) ? self::$SCRIPT['repository'] : null;

        if ($repositories) {
            foreach ($repositories as $repository) {
                self::base_vendor_repository($repository, self::$DIR_PUBLIC);
            }
        }
    }

    private static function install_required()
    {
        $required = array_key_exists('require', self::$SCRIPT) ? self::$SCRIPT['require'] : null;

        if ($required) {
            foreach ($required as $repository => $version) {
                self::base_vendor_require($repository, $version, self::$DIR_PUBLIC);
            }
        }
    }

    private static function install_shared()
    {
        $files = self::file_list(self::$DIR_SHARED);

        foreach ($files as $target) {
            $symlink = str_replace(self::$DIR_SHARED, self::$DIR_PUBLIC, $target);

            self::symlink_create($target, $symlink);
        }
    }

    private static function install_command()
    {
        $console = self::$CONSOLE;
        $commands = array_key_exists('command', self::$SCRIPT) ? self::$SCRIPT['command'] : null;

        foreach ($commands as $command) {
            $cmd = "$console $command";

            self::command($cmd);
        }
    }

    private static function install_append()
    {
        $files = self::file_list(self::$DIR_APPEND);

        var_dump($files);
    }
}
