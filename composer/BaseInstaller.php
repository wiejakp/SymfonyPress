<?php

namespace wiejakp\SymfonyPress\Composer;

// php classes
use Exception;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

// composer classes
use Composer\Script\Event;

// symfony classes
use Symfony\Component\Yaml\Yaml;

/**
 * Class Installer
 *
 * @package wiejakp\SymfonyPress\Composer
 */
class BaseInstaller
{
    public static $COMPOSER;
    public static $EVENT;
    public static $INOUT;
    public static $EXTRAS;
    public static $BASE;
    public static $INSTALLERS;

    public static $BASE_ROOT;
    public static $BASE_DIRS;
    public static $BASE_FILE;
    public static $BASE_CONF;
    public static $BASE_VENDOR;

    /**
     * @param $method
     * @param $args
     */
    public static function __callStatic($method, $args)
    {
        if (empty($args) || false === $args[0] instanceof Event) {
            throw new InvalidArgumentException('Every method call must contain Event parameter.');
        }

        $class = self::getClass();

        // create privates
        self::$EVENT = $args[0];
        self::$INOUT = self::$EVENT->getIO();
        self::$EXTRAS = self::$EVENT->getComposer()->getPackage()->getExtra();
        self::$BASE = self::$EXTRAS['base'];
        self::$INSTALLERS = self::$EXTRAS['installers'];

        // create public vars
        self::$BASE_ROOT = dirname(dirname(__FILE__)) . '/';
        self::$BASE_DIRS = self::$BASE['dirs'];
        self::$BASE_FILE = self::$BASE['file'];
        self::$BASE_VENDOR = self::$EVENT->getComposer()->getConfig()->get('vendor-dir') . '/';

        // composer cli
        self::$COMPOSER = self::$BASE_ROOT . 'composer.phar';

        self::base_init($class);

        self::$INOUT->write("\n\r\n\r: METHOD STARTED: $method() \n\r:");

        forward_static_call_array([$class, $method], $args);

        self::$INOUT->write(": METHOD FINISHED: $method() \n\r\n\r");
    }

    private static function base_init($class)
    {
        self::base_init_dirs($class);
        self::base_init_file($class);
        self::base_init_conf($class);
    }

    private static function base_init_dirs($class): void
    {
        array_walk(self::$BASE_DIRS, function (&$dir_path, $dir_name) {
            $dir_path = self::$BASE_ROOT . $dir_path;

            if (!file_exists($dir_path)) {
                self::dir_create($dir_path);
            }
        });
    }

    private static function base_init_file($class): void
    {
        self::$BASE_FILE = self::$BASE_ROOT . self::$BASE_FILE;
    }

    protected static function base_init_conf($class): ?iterable
    {
        $conf_array = &$class::$BASE_CONF;
        $conf_file = $class::$BASE_FILE;
        $inout = $class::$INOUT;

        // if settings already exist, skip input prompts
        if (is_file($conf_file)) {
            try {
                $conf_array = json_decode(file_get_contents($conf_file), true);
            } catch (Exception $exception) {
                // quiet
            }
        }

        if (!$conf_array) {
            // store all user inputs
            $params = &$conf_array['parameters'];
            $project = &$conf_array['project'];

            // generate secret token
            $secret = uniqid('SymfonyPress_', true);

            $inout->write(": [ ! ] Server Information");
            $inout->write(":");

            $params['database_host'] = $inout->ask(": [ ? ] Database Host [localhost]: ", 'localhost');
            $params['database_port'] = $inout->ask(": [ ? ] Database Port [3306]: ", '3306');
            $params['database_name'] = $inout->ask(": [ ? ] Database Name [symfonypress]: ", 'symfonypress');
            $params['database_user'] = $inout->ask(": [ ? ] Database User [symfonypress]: ", 'symfonypress');
            $params['database_password'] = $inout->ask(": [ ? ] Database Password [symfonypress]: ", 'symfonypress');
            $params['database_prefix'] = $inout->ask(": [ ? ] Database Prefix [wp_]: ", 'wp_');
            $params['database_charset'] = $inout->ask(": [ ? ] Database Char Set [utf8]: ", 'utf8');
            $params['mailer_transport'] = $inout->ask(": [ ? ] Mail Server Protocol [smtp]: ", 'smtp');
            $params['mailer_host'] = $inout->ask(": [ ? ] Mail Server Host [127.0.0.1]: ", '127.0.0.1');
            $params['mailer_user'] = $inout->ask(": [ ? ] Mail Server User [null]: ", 'null');
            $params['mailer_password'] = $inout->ask(": [ ? ] Mail Server Password [null]: ", 'null');
            $params['secret'] = $inout->ask(": [ ? ] Secret Token [$secret]: ", $secret);

            $inout->write(":");
            $inout->write(": [ ! ] Web Site Information");
            $inout->write(":");

            $project['url'] = $inout->ask(": [ ? ] Web Site URL [symfonypress.dev]: ", 'symfonypress.dev');
            $project['title'] = $inout->ask(": [ ? ] Web Site Title [SymfonyPress]: ", 'SymfonyPress');

            $inout->write(":");
            $inout->write(": [ ! ] Administrator User Information");
            $inout->write(":");

            $project['mail'] = $inout->ask(": [ ? ] Admin E-Mail Address [symfonypress@symfonypress.dev]: ", 'symfonypress@symfonypress.dev');
            $project['user'] = $inout->ask(": [ ? ] Admin User Name [symfonypress]: ", 'symfonypress');
            $project['pass'] = $inout->ask(": [ ? ] Admin Password [symfonypress]: ", 'symfonypress');

            $inout->write(":");
            $inout->write(":");

            file_put_contents($conf_file, json_encode($conf_array, JSON_PRETTY_PRINT));
        }

        return $conf_array;
    }

    public static function base_vendor_require(string $repository, string $version, ?string $directory = null)
    {
        $isInstalled = self::base_vendor_check($repository);

        $composer = self::$COMPOSER;
        $cmd_return = null;

        if (!$isInstalled) {
            $cmd_require = "php $composer require $repository:$version";

            if ($directory) {
                $cmd_require .= " --working-dir='$directory'";
            }

            $cmd_return = self::command($cmd_require);
        }

        return $cmd_return ? $cmd_return['code'] == 0 ? true : false : false;
    }

    public static function base_vendor_repository(array $repository, ?string $directory = null)
    {
        $composer = self::$COMPOSER;

        $repo_title = $repository['title'];
        $repo_json = json_encode([
            'type' => $repository['type'],
            'url' => $repository['url'],
            'version' => $repository['version'],
        ]);

        $cmd_repository = "php $composer config repositories.$repo_title '$repo_json'";

        if ($directory) {
            $cmd_repository .= " --file='" . $directory . "composer.json'";
        }

        self::command($cmd_repository);
    }

    public static function base_vendor_check(string $repository = null, string $version = '*'): bool
    {
        $composer = self::$EVENT->getComposer();

        $composerManager = $composer->getRepositoryManager();
        $composerRepository = $composerManager->getLocalRepository();
        $composerPackage = $composerRepository->findPackage($repository, $version);

        if ($composerPackage) {
            return true;
        }

        return false;
    }

    public static function base_vendor_path(string $repository = null, string $version = '*'): ?string
    {
        $composer = self::$EVENT->getComposer();

        $composerRepositoryManager = $composer->getRepositoryManager();
        $composerInstallationManager = $composer->getInstallationManager();
        $composerPackage = $composerRepositoryManager->findPackage($repository, $version);

        if ($composerPackage) {
            return $composerInstallationManager->getInstallPath($composerPackage);
        }

        return null;
    }

    /**
     * @return string
     */
    public static function getClass(): string
    {
        return get_called_class();
    }

    public function check_dir(string $path, bool $create = true)
    {
        if (is_dir($path)) {
            return true;
        }

        if ($create) {
            mkdir('/test1/test2', 0775, true);

            self::check_dir($path);
        }

        return false;
    }

    public static function command(string $command, bool $returnValue = true, bool $returnCode = true)
    {
        $command_return = [];
        $command_value = null;
        $command_code = null;

        self::$INOUT->write($command);

        exec($command, $command_value, $command_code);

        if ($returnValue) {
            $command_return['value'] = $command_value;
        }

        if ($returnCode) {
            $command_return['code'] = $command_code;
        }

        return $command_return;
    }

    public static function dir_create(string $path, string $permission = '0775')
    {
        $command_mkdir = "mkdir --parents $path";
        $command_chmod = "chmod $permission $path";

        if (!is_dir($path) && !file_exists($path)) {
            self::command($command_mkdir);
            self::command($command_chmod);
        }
    }

    public static function file_list(string $path): iterable
    {
        $rdi = new RecursiveDirectoryIterator($path);
        $rii = new RecursiveIteratorIterator($rdi);

        $files = [];

        foreach ($rii as $file) {
            if ($file->isDir()) {
                continue;
            }
            $files[] = $file->getPathname();
        }


        return $files;
    }

    public static function file_move(string $source, string $destination)
    {
        $command_copy = "cp -r '$source' '$destination'";
        $command_remove = "rm -rf '$source'";

        self::command($command_copy);
        self::command($command_remove);
    }

    public static function file_remove(string $path)
    {
        $command = "rm -rf '$path'";

        self::command($command);
    }

    public static function symlink_create(string $target, string $path)
    {
        $path = rtrim($path, '/');
        $root = dirname($path);

        if (!file_exists($root)) {
            self::dir_create($root);
        }

        $command = "ln --symbolic --force '$target' '$path'";

        return self::command($command);
    }

    public static function symlink_remove(string $path)
    {
        $path = rtrim($path, '/');
        $command = "rm '$path'";

        return self::command($command);
    }
}
