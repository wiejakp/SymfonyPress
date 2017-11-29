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
        self::$DIR_APPEND = self::$BASE_DIRS['append'] . self::$PROJECT . '/';
        self::$DIR_PRIVATE = self::$BASE_DIRS['private'] . self::$SCRIPT['dir'];
        self::$DIR_SHARED = self::$BASE_DIRS['shared'] . self::$SCRIPT['dir'];
        self::$DIR_PUBLIC = self::$BASE_DIRS['public'] . self::$SCRIPT['dir'];

        self::$CONSOLE = self::$DIR_PUBLIC . self::$SCRIPT['console'];

        // init dirs
        self::dir_create(self::$DIR_APPEND);
        self::dir_create(self::$DIR_PRIVATE);
        self::dir_create(self::$DIR_SHARED);
        self::dir_create(self::$DIR_PUBLIC);
    }

    protected static function check_install()
    {
        $config = self::$DIR_PUBLIC . self::$SCRIPT['config'];

        if (file_exists($config) && is_link($config)) {
            return true;
        }

        return false;
    }

    protected static function update()
    {
        // init script content
        self::init();

        if (!self::check_install()) {
            self::install();
        }

        // global vars
        $composer = self::$COMPOSER;
        $directory = self::$DIR_PUBLIC;

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
        self::install_permissions();
    }

    private static function install_project()
    {
        // global vars
        $composer = self::$COMPOSER;

        // project vars
        $project_edition = self::$SCRIPT['edition'];
        $project_version = self::$SCRIPT['version'];
        $project_path = rtrim(self::$DIR_PUBLIC, '/');
        $project_installed = !empty(self::file_list($project_path));

        $config_private = self::$DIR_PRIVATE . self::$SCRIPT['config'];

        // if project isn't installed, create it
        if (!$project_installed) {
            $cmd_project = "$composer create-project $project_edition $project_path '$project_version' --no-interaction";

            self::command($cmd_project);
        }

        // if config doesn't exist, create it
        if (!file_exists($config_private)) {
            $config_array['parameters'] = self::$BASE_CONF['parameters'];
            $config_string = self::yaml_dump($config_array);

            self::file_write($config_private, $config_string);
        }
    }

    private static function install_config()
    {
        $files = self::file_list(self::$DIR_PRIVATE);

        foreach ($files as $target) {
            $symlink = str_replace(self::$DIR_PRIVATE, self::$DIR_PUBLIC, $target);

            if (!file_exists($symlink) || !is_link($symlink) || realpath($symlink) != $target) {
                self::symlink_create($target, $symlink);
            }
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

            if(!file_exists($symlink) || !is_link($symlink) || realpath($symlink) != $target) {
                self::symlink_create($target, $symlink);
            }
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

        foreach ($files as $file_source) {
            $file_destination = str_replace(self::$DIR_APPEND, self::$DIR_PUBLIC, $file_source);

            $array_src = self::yaml_read($file_source);
            $array_dst = self::yaml_read($file_destination);

            $yaml_array = array_merge($array_dst, $array_src);

            if($yaml_array != $array_src) {
                $yaml_string = self::yaml_dump($yaml_array);

                self::file_write($file_destination, $yaml_string);
            }
        }
    }

    private static function install_permissions()
    {
        $dir_var = self::$DIR_PUBLIC . 'var/';

        $cmd_array = [
            "HTTPDUSER=$(ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1)",
            "sudo setfacl -dR -m u:\"" . '$HTTPDUSER' . "\":rwX -m u:$(whoami):rwX $dir_var",
            "sudo setfacl -R -m u:\"" . '$HTTPDUSER' . "\":rwX -m u:$(whoami):rwX $dir_var"
        ];

        self::command(implode(';', $cmd_array));
    }
}
