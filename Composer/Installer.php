<?php

namespace wiejakp\SymfonyPress\Composer;

// php classes
use \Exception;

// composer classes
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
    private static $ROOT;

    /**
     * @var array
     */
    private static $CONFIG;

    /**
     * @var array
     */
    private static $SETTINGS;

    /**
     * @param $method
     * @param $args
     */
    public static function __callStatic($method, $args)
    {
        // root directory
        self::$ROOT = dirname(dirname(__FILE__)) . '/';

        // parse projects configs
        if (!empty($args) && $args[0] instanceof Event) {
            self::$CONFIG = $args[0]->getComposer()->getPackage()->getExtra();
        }

        // parse projects settings
        if (!empty(self::$CONFIG)) {
            $settings_dir = self::$CONFIG['dir'];
            $settings_path = $settings_dir . self::$CONFIG['filename'];

            // create config directory when it doesn't exist
            if (!is_dir($settings_dir)) {
                mkdir($settings_dir);
            }

            // parse settings if they are already set
            if (file_exists($settings_path)) {
                self::$SETTINGS = Yaml::parse(file_get_contents($settings_path));
            }
        }

        forward_static_call_array([self::class, $method], $args);
    }

    /**
     * @param Event $event
     */
    protected static function input(Event $event): void
    {
        // local vars
        $function = __FUNCTION__;

        // store all information in following file
        $settings_dir = self::$CONFIG['dir'];
        $settings_path = $settings_dir . self::$CONFIG['filename'];
        $settings_full_path = self::$ROOT . $settings_path;

        // input/output handler
        $io = $event->getIO();

        $io->write("\n\r\n\r: METHOD STARTED: $function() \n\r:");

        // if settings already exist, skip input prompts
        if (file_exists($settings_path)) {
            $io->write(":\n\r: Existing Config: $settings_path\n\r:\n\r:");
        } else {
            // store all user inputs
            $data = [];
            $params = &$data['parameters'];
            $project = &$data['project'];

            // generate secret token
            $secret = uniqid('SymfonyPress_', true);

            $io->write(": Server Information\n\r:");

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
            $params['secret'] = $io->ask(": Secret Token [$secret]: ", $secret);

            $io->write(":\n\r:\n\r: Web Site Information\n\r:");

            $project['url'] = $io->ask(": Web Site URL [symfonypress.dev]: ", 'symfonypress.dev');
            $project['title'] = $io->ask(": Web Site Title [SymfonyPress]: ", 'SymfonyPress');

            $io->write(":\n\r:\n\r: Administrator User Information\n\r:");

            $project['mail'] = $io->ask(": Admin E-Mail Address [symfonypress@symfonypress.dev]: ", 'symfonypress@symfonypress.dev');
            $project['user'] = $io->ask(": Admin User Name [symfonypress]: ", 'symfonypress');
            $project['pass'] = $io->ask(": Admin Password [symfonypress]: ", 'symfonypress');

            // save settings into a file
            try {
                $io->write(":\n\r:\n\r: Settings Saved At: $settings_full_path\n\r:");

                file_put_contents($settings_path, Yaml::dump($data));
            } catch (Exception $exception) {
                $error = $exception->getMessage();

                $io->write("\n\r: ERROR: $error\n\r");
            }
        }

        $io->write(":\n\r: METHOD FINISHED: $function() \n\r\n\r");
    }

    /**
     * @param Event $event
     */
    protected static function install_symfony(Event $event): void
    {
        // local vars
        $function = __FUNCTION__;

        // input/output handler
        $io = $event->getIO();

        // extracted config
        $config_system = self::$CONFIG['symfony'];
        $config_location = $config_system['dir'];
        $config_absolute = self::$ROOT . $config_location;
        $config_filename = $config_system['filename'];
        $config_version = $config_system['version'];
        $config_physical = self::$ROOT . self::$CONFIG['dir'] . $config_filename;
        $config_virtual = self::$ROOT . $config_location . $config_system['config'] . $config_filename;

        $io->write("\n\r\n\r: METHOD STARTED: $function() \n\r:");

        if (!is_dir($config_system['dir'])) {
            $io->write(": Installing Symfony: " . self::$ROOT . $config_location);

            // create new project
            exec("composer create-project '$config_version' '$config_location' -q;");

            if (array_key_exists('require', $config_system)) {
                foreach ($config_system['require'] as $require) {
                    $repo_user = $require['user'];
                    $repo_title = $require['title'];
                    $repo_name = $repo_user . '/' . $repo_title;
                    $repo_type = $require['type'];
                    $repo_version = $require['version'];
                    $repo_url = $require['url'];

                    $cmd_repository = "composer config repositories.$repo_title '$repo_type' '$repo_url' -q -d '$config_absolute'";
                    $cmd_require = "composer require $repo_name '$repo_version' -q -d '$config_absolute'";

                    $io->write(": + Repository: $cmd_repository");
                    exec($cmd_repository);

                    $io->write(": + Require: $cmd_require");
                    exec($cmd_require);
                }
            }
        } else {
            $io->write(": Symfony Already Installed: " . self::$ROOT . $config_location);
        }

        if (!file_exists($config_physical)) {
            $io->write(": Generating Symfony Parameters: $config_physical");

            file_put_contents($config_physical, Yaml::dump([
                'parameters' => self::$SETTINGS['parameters']
            ]));
        } else {
            $io->write(": Symfony Parameters Found: $config_physical");
        }

        if (file_exists($config_virtual)) {
            if (is_link($config_virtual)) {
                $io->write(": UnLinking Old Settings: $config_virtual");
            } else {
                $io->write(": Removing Old Settings: $config_virtual");
            }

            unlink($config_virtual);
        }

        $io->write(":\n\r: SymLink Source: $config_physical");
        $io->write(": SymLink Destination: $config_virtual");

        symlink($config_physical, $config_virtual);


        $io->write(":\n\r: METHOD FINISHED: $function() \n\r\n\r");
    }

    /**
     * @param Event $event
     */
    protected static function update_symfony(Event $event): void
    {
        // local vars
        $function = __FUNCTION__;

        // input/output handler
        $io = $event->getIO();

        // extracted config
        $config_system = self::$CONFIG['symfony'];
        $config_location = $config_system['dir'];

        $io->write("\n\r\n\r: METHOD STARTED: $function() \n\r:");

        if (is_dir($config_location)) {
            $io->write(": Updating Symfony: " . self::$ROOT . $config_location);

            exec("composer update -d '$config_location' -q");
        }

        $io->write(":\n\r: METHOD FINISHED: $function() \n\r\n\r");
    }

    /**
     * @param Event $event
     */
    protected static function install_symfony_symlinks(Event $event): void
    {
        // local vars
        $function = __FUNCTION__;

        // input/output handler
        $io = $event->getIO();

        $config = self::$CONFIG['symfony'];
        $console = self::$ROOT . $config['dir'] . 'bin/console';
        $symlinks = $config['symlink'];

        $io->write("\n\r\n\r: METHOD STARTED: $function() \n\r:");

        // install symlinks
        foreach ($symlinks as $symlink) {
            $root = $config['dir'];
            $input_file = $symlink['input_filename'];
            $input_dir = $symlink['input_dir'];
            $output_dir = $symlink['output_dir'];
            $command = $symlink['command'];

            $abs_root = self::$ROOT . $root;
            $abs_input = self::$ROOT . $input_dir . $input_file;
            $abs_output = $abs_root . $output_dir;

            if (!file_exists($abs_output)) {
                mkdir($abs_output);
            }

            foreach (glob($abs_input) as $file) {
                $base = basename($file);
                $link = $abs_root . $output_dir . $base;

                if(file_exists($link)) {
                    unlink($link);
                }

                var_dump($file);
                var_dump($link);

                symlink($file, $link);
            }

            exec("$console $command");
        }

        $io->write(":\n\r: METHOD FINISHED: $function() \n\r\n\r");
    }

    /**
     * @param Event $event
     */
    protected static function install_wordpress(Event $event): void
    {
        // local vars
        $function = __FUNCTION__;

        // input/output handler
        $io = $event->getIO();

        // extracted config
        $config_system = self::$CONFIG['wordpress'];
        $config_location = $config_system['dir'];
        $config_filename = $config_system['filename'];
        $config_physical = self::$ROOT . self::$CONFIG['dir'] . $config_filename;
        $config_virtual = self::$ROOT . $config_system['dir'] . $config_system['config'] . $config_filename;

        $io->write("\n\r\n\r: METHOD STARTED: $function() \n\r:");

        if (!is_dir($config_location)) {
            $io->write(": Downloading WordPress: " . self::$ROOT . $config_location);

            // download wp files
            exec("wp core download --path='$config_location';");
        } else {
            $io->write(": WordPress Already Downloaded: " . self::$ROOT . $config_location);
        }

        if (file_exists($config_virtual)) {
            if (is_link($config_virtual)) {
                $io->write(": UnLinking Old Settings: $config_virtual");
            } else {
                $io->write(": Removing Old Settings: $config_virtual");
            }

            unlink($config_virtual);
        }

        if (file_exists($config_physical)) {
            $io->write(": WordPress Config File Found: " . $config_physical);
        } else {
            $io->write(": Generating WordPress Config File: " . $config_virtual);

            exec(implode(' ', [
                "wp config create",
                "--path='" . $config_location . "'",
                "--dbname='" . self::$SETTINGS['parameters']['database_name'] . "'",
                "--dbuser='" . self::$SETTINGS['parameters']['database_user'] . "'",
                "--dbpass='" . self::$SETTINGS['parameters']['database_password'] . "'",
                "--dbhost='" . self::$SETTINGS['parameters']['database_host'] . "'",
                "--dbprefix='" . self::$SETTINGS['parameters']['database_prefix'] . "'",
                "--dbcharset='" . self::$SETTINGS['parameters']['database_charset'] . "'"
            ]));

            $io->write(": Moving New Config To: " . $config_physical);

            rename($config_virtual, $config_physical);
        }

        $io->write(":\n\r: SymLink Source: $config_physical");
        $io->write(": SymLink Destination: $config_virtual");

        symlink($config_physical, $config_virtual);

        $io->write(":\n\r: Setting Up WordPress Site: $config_virtual");

        exec(implode(' ', [
            "wp core install",
            "--path='" . $config_location . "'",
            "--url='" . self::$SETTINGS['project']['url'] . "'",
            "--title='" . self::$SETTINGS['project']['title'] . "'",
            "--admin_user='" . self::$SETTINGS['project']['user'] . "'",
            "--admin_password='" . self::$SETTINGS['project']['pass'] . "'",
            "--admin_email='" . self::$SETTINGS['project']['mail'] . "'",
            "--skip-email"
        ]));

        $io->write(":\n\r: METHOD FINISHED: $function() \n\r\n\r");
    }

    /**
     * @param Event $event
     */
    protected static function update_wordpress(Event $event): void
    {
        // local vars
        $function = __FUNCTION__;

        // input/output handler
        $io = $event->getIO();

        // extracted config
        $config_system = self::$CONFIG['wordpress'];
        $config_location = $config_system['dir'];

        $io->write("\n\r\n\r: METHOD STARTED: $function() \n\r:");

        if (is_dir($config_location)) {
            $io->write(": Updating WordPress Core: " . self::$ROOT . $config_location);

            exec("wp core update --path='$config_location'");
        }

        $io->write(":\n\r: METHOD FINISHED: $function() \n\r\n\r");
    }

    /**
     * @param Event $event
     */
    protected static function install_wordpress_symlinks(Event $event): void
    {
        // local vars
        $function = __FUNCTION__;

        // input/output handler
        $io = $event->getIO();

        $config = self::$CONFIG['wordpress'];
        $symlinks = $config['symlink'];

        $io->write("\n\r\n\r: METHOD STARTED: $function() \n\r:");

        // install symlinks
        foreach ($symlinks as $symlink) {
            $root = $config['dir'];
            $input_file = $symlink['input_filename'];
            $input_dir = $symlink['input_dir'];
            $output_dir = $symlink['output_dir'];

            $abs_root = self::$ROOT . $root;
            $abs_input = self::$ROOT . $input_dir . $input_file;
            $abs_output = $abs_root . $output_dir;

            if (!file_exists($abs_output)) {
                mkdir($abs_output);
            }

            foreach (glob($abs_input) as $file) {
                $base = basename($file);
                $link = $abs_root . $output_dir . $base;

                var_dump($link);

                if(file_exists($link)) {
                    //unlink($link);
                }

                var_dump($file);
                var_dump($link);

                //symlink($file, $link);
            }
        }

        $io->write(":\n\r: METHOD FINISHED: $function() \n\r\n\r");
    }
}
