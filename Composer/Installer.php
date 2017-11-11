<?php

namespace wiejakp\SymfonyPress\Composer;

// composer classes
use Composer\IO\IOInterface;
use Composer\Script\Event;

// symfony classes
use Symfony\Component\Yaml\Yaml;

/**
 * Class Installer
 *
 * @package wiejakp\SymfonyPress\Composer
 */
class Installer
{
    /**
     * @var string
     */
    private static $DIR;

    /**
     * @var array
     */
    private static $CONFIG;

    /**
     * @param $method
     * @param $args
     */
    public static function __callStatic($method, $args)
    {
        self::$DIR = dirname(dirname(__FILE__)) . '/';

        if (!empty($args) && $args[0] instanceof Event) {
            self::$CONFIG = $args[0]->getComposer()->getPackage()->getExtra();
        }

        forward_static_call_array([self::class, $method], $args);
    }

    protected static function input(Event $event): void
    {
        // store all information in following file
        $settings_path = self::$CONFIG['dir'] . self::$CONFIG['filename'];

        // input/output handler
        $io = $event->getIO();

        // if settings already exist, skip input prompts
        if (file_exists($settings_path)) {
            $io->write(": \n\r: \n\r: Existing Config: $settings_path\n\r: \n\r: ");

            return;
        }

        // store all user inputs
        $data = [];
        $params = &$data['parameters'];
        $person = &$data['admin'];
        $project = &$data['project'];

        // generate secret token
        $secret = uniqid('SymfonyPress_', true);

        $io->write(": \n\r: \n\r: Server Information\n\r: ");

        $params['database_host'] = $io->ask(": Database Host [localhost]: ", 'localhost');
        $params['database_port'] = $io->ask(": Database Port [3306]: ", '3306');
        $params['database_name'] = $io->ask(": Database Name [symfonypress]: ", 'symfonypress');
        $params['database_user'] = $io->ask(": Database User [symfonypress]: ", 'symfonypress');
        $params['database_password'] = $io->ask(": Database Password [symfonypress]: ", 'symfonypress');
        $params['database_prefix'] = $io->ask(": Database Prefix [wp_]: ", 'wp_');
        $params['database_charset'] = $io->ask(": Database Char Set [utf8]: ", 'utf8');
        $params['mailer_transport'] = $io->ask(": E-Mail Protocol [smtp]: ", 'smtp');
        $params['mailer_host'] = $io->ask(": E-Mail Host [127.0.0.1]: ", '127.0.0.1');
        $params['mailer_user'] = $io->ask(": E-Mail User [null]: ", 'null');
        $params['mailer_password'] = $io->ask(": E-Mail Password [null]: ", 'null');
        $params['token'] = $io->ask(": Secret Token [$secret]: ", $secret);

        $io->write(": \n\r: \n\r: Web Site Information\n\r: ");

        $project['url'] = $io->ask(": Web Site URL [symfonypress.dev]: ", 'symfonypress.dev');
        $project['title'] = $io->ask(": Web Site Title [SymfonyPress]: ", 'SymfonyPress');

        $io->write(": \n\r: \n\r: Administrator User Information\n\r: ");

        $person['user'] = $io->ask(": User Name [symfonypress]: ", 'symfonypress');
        $person['mail'] = $io->ask(": E-Mail Address [symfonypress@symfonypress.dev]: ", 'symfonypress@symfonypress.dev');
        $person['pass'] = $io->ask(": Password [symfonypress]: ", 'symfonypress');

        $io->write(": \n\r: \n\r: No more information needed!\n\r: \n\r: ");

        // save settings into a file
        file_put_contents($settings_path, Yaml::dump($data));
    }

    protected static function install_symfony(Event $event): void
    {
        // extracted settings
        $settings_dir = self::$CONFIG['symfony']['dir'];
        $settings_version = self::$CONFIG['symfony']['version'];

        // fetch info
        $directory = self::$DIR . $settings_dir;
        $command = "composer create-project $settings_version $settings_dir -q;";

        if (!file_exists($directory)) {
            exec($command);
        }
    }

    protected static function update_symfony(Event $event): void
    {
        // extracted settings
        $settings_dir = self::$CONFIG['symfony']['dir'];

        // fetch info
        $directory = self::$DIR . $settings_dir;
        $command = "composer update -d $directory";

        if (file_exists($directory)) {
            exec($command);
        }
    }

    protected static function install_wordpress(Event $event): void
    {
        // extracted settings
        $settings_dir = self::$CONFIG['wordpress']['dir'];

        // fetch info
        $directory = self::$DIR . $settings_dir;
        $command_download = "wp core download --path='$directory';";
        $command_install = "wp core install --path='$directory';";

        if (!file_exists($directory)) {
            exec("$command_download . $command_install");
        }
    }

    protected static function update_wordpress(Event $event): void
    {
        // extracted settings
        $settings_dir = self::$CONFIG['wordpress']['dir'];

        // fetch info
        $directory = self::$DIR . $settings_dir;
        $command = "wp core update --path='$directory';";

        if (!file_exists($directory)) {
            exec($command);
        }
    }

    protected static function save_input(Event $event, array $data): void
    {

    }
}
