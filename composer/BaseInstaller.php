<?php

namespace wiejakp\SymfonyPress\Composer;

// php classes
use Exception;

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

        // composer cli
        self::$COMPOSER = self::$BASE_ROOT . 'composer.pharK';

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
        });
    }

    private static function base_init_file($class): void
    {
        self::$BASE_FILE = self::$BASE_ROOT . self::$BASE_FILE;
    }

    protected static function base_init_conf($class): ?iterable
    {
        $conf_array = self::$BASE_CONF;
        $conf_file = self::$BASE_FILE;
        $inout = self::$INOUT;

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
            $params = &$conf['parameters'];
            $project = &$conf['project'];

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
}
