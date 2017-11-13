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
            $settings_dir  = self::$CONFIG['dir'];
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
        $settings_dir  = self::$CONFIG['dir'];
        $settings_path = $settings_dir . self::$CONFIG['filename'];

        // input/output handler
        $io = $event->getIO();

        $io->write("\n\r\n\r: METHOD STARTED: $function() \n\r:");

        // if settings already exist, skip input prompts
        if (!file_exists($settings_path)) {
            // store all user inputs
            $data    = [];
            $params  = &$data['parameters'];
            $project = &$data['project'];

            // generate secret token
            $secret = uniqid('SymfonyPress_', true);

            $io->write(": [ ! ] Server Information\n\r:");

            $params['database_host']     = $io->ask(": [ ? ] Database Host [localhost]: ", 'localhost');
            $params['database_port']     = $io->ask(": [ ? ] Database Port [3306]: ", '3306');
            $params['database_name']     = $io->ask(": [ ? ] Database Name [symfonypress]: ", 'symfonypress');
            $params['database_user']     = $io->ask(": [ ? ] Database User [symfonypress]: ", 'symfonypress');
            $params['database_password'] = $io->ask(": [ ? ] Database Password [symfonypress]: ", 'symfonypress');
            $params['database_prefix']   = $io->ask(": [ ? ] Database Prefix [wp_]: ", 'wp_');
            $params['database_charset']  = $io->ask(": [ ? ] Database Char Set [utf8]: ", 'utf8');
            $params['mailer_transport']  = $io->ask(": [ ? ] E-Mail Protocol [smtp]: ", 'smtp');
            $params['mailer_host']       = $io->ask(": [ ? ] E-Mail Host [127.0.0.1]: ", '127.0.0.1');
            $params['mailer_user']       = $io->ask(": [ ? ] E-Mail User [null]: ", 'null');
            $params['mailer_password']   = $io->ask(": [ ? ] E-Mail Password [null]: ", 'null');
            $params['secret']            = $io->ask(": [ ? ] Secret Token [$secret]: ", $secret);

            $io->write(":\n\r:\n\r: [ ! ] Web Site Information\n\r:");

            $project['url']   = $io->ask(": [ ? ] Web Site URL [symfonypress.dev]: ", 'symfonypress.dev');
            $project['title'] = $io->ask(": [ ? ] Web Site Title [SymfonyPress]: ", 'SymfonyPress');

            $io->write(":\n\r:\n\r: [ ! ] Administrator User Information\n\r:");

            $project['mail'] = $io->ask(": [ ? ] Admin E-Mail Address [symfonypress@symfonypress.dev]: ", 'symfonypress@symfonypress.dev');
            $project['user'] = $io->ask(": [ ? ] Admin User Name [symfonypress]: ", 'symfonypress');
            $project['pass'] = $io->ask(": [ ? ] Admin Password [symfonypress]: ", 'symfonypress');

            $io->write(":");

            try {
                $string_yaml    = Yaml::dump($data);
                $string_encoded = filter_var($string_yaml, FILTER_SANITIZE_ENCODED);

                $io->write(": [ + ] echo '$string_encoded' | tee '$settings_path'");

                file_put_contents($settings_path, $string_yaml);
            } catch (Exception $exception) {
                $error = $exception->getMessage();

                $io->write(": [ - ] ERROR: $error");
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
        $config_system   = self::$CONFIG['symfony'];
        $config_location = $config_system['dir'];
        $config_absolute = self::$ROOT . $config_location;
        $config_filename = $config_system['filename'];
        $config_version  = $config_system['version'];
        $config_physical = self::$ROOT . self::$CONFIG['dir'] . $config_filename;
        $config_virtual  = self::$ROOT . $config_location . $config_system['config'] . $config_filename;

        $io->write("\n\r\n\r: METHOD STARTED: $function() \n\r:");

        if (!is_dir($config_system['dir'])) {
            $cmd_create = "composer create-project '$config_version' '$config_location' -q";

            $io->write(": [ + ] $cmd_create");

            exec($cmd_create);

            if (array_key_exists('require', $config_system)) {
                foreach ($config_system['require'] as $require) {
                    $repo_user    = $require['user'];
                    $repo_title   = $require['title'];
                    $repo_name    = $repo_user . '/' . $repo_title;
                    $repo_type    = $require['type'];
                    $repo_version = $require['version'];
                    $repo_url     = $require['url'];

                    $cmd_repository = "composer config repositories.$repo_title '$repo_type' '$repo_url' -q -d '$config_absolute'";
                    $cmd_require    = "composer require $repo_name '$repo_version' -q -d '$config_absolute'";

                    $io->write(": [ + ] $cmd_repository");
                    exec($cmd_repository);

                    $io->write(": [ + ] $cmd_require");
                    exec($cmd_require);
                }
            }
        }

        if (!file_exists($config_physical)) {
            $string_yaml    = Yaml::dump([
                'parameters' => self::$SETTINGS['parameters'],
            ]);
            $string_encoded = filter_var($string_yaml, FILTER_SANITIZE_ENCODED);

            $io->write(": [ + ] echo '$string_encoded' | tee '$config_physical'");

            file_put_contents($config_physical, $string_yaml);
        }

        if (file_exists($config_virtual)) {
            $io->write(": [ - ] unlink '$config_virtual'");

            //unlink($config_virtual);
            unlink(realpath($config_virtual));
        }

        $io->write(": [ + ] ln -s '$config_physical' '$config_virtual'");

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
        $config_system   = self::$CONFIG['symfony'];
        $config_location = $config_system['dir'];

        $io->write("\n\r\n\r: METHOD STARTED: $function() \n\r:");

        if (is_dir($config_location)) {
            $cmd_update = "composer update -d '$config_location' -q";

            $io->write(": [ + ] $cmd_update");

            exec($cmd_update);
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

        $config   = self::$CONFIG['symfony'];
        $console  = self::$ROOT . $config['dir'] . 'bin/console';
        $symlinks = $config['symlink'];

        $io->write("\n\r\n\r: METHOD STARTED: $function() \n\r:");

        // install symlinks
        foreach ($symlinks as $symlink) {
            $root       = $config['dir'];
            $input_file = $symlink['input_filename'];
            $input_dir  = $symlink['input_dir'];
            $output_dir = $symlink['output_dir'];
            $command    = "$console " . $symlink['command'];

            $abs_root   = self::$ROOT . $root;
            $abs_input  = self::$ROOT . $input_dir . $input_file;
            $abs_output = $abs_root . $output_dir;

            if (!file_exists($abs_output)) {
                $io->write(": [ + ] mkdir '$abs_output'");

                mkdir($abs_output);
            }

            foreach (glob($abs_input) as $file) {
                $base = basename($file);
                $link = $abs_root . $output_dir . $base;

                if (file_exists($link)) {
                    $io->write(": [ - ] unlink '$link'");

                    //unlink($link);
                    unlink(realpath($link));
                }

                $io->write(": [ + ] ln -s '$file' '$link'");

                symlink($file, $link);
            }

            $io->write(": [ + ] $command");

            exec($command);
        }

        $io->write(":\n\r: METHOD FINISHED: $function() \n\r\n\r");
    }

    /**
     * @param Event $event
     */
    protected static function install_symfony_config(Event $event): void
    {
        // local vars
        $function = __FUNCTION__;

        // input/output handler
        $io = $event->getIO();

        $config  = self::$CONFIG['symfony'];
        $appends = $config['append'];
        $replace = $config['replace'];

        $io->write("\n\r\n\r: METHOD STARTED: $function() \n\r:");

        foreach ($appends as $file) {
            $dir_source      = self::$ROOT . $file['source'];
            $dir_destination = self::$ROOT . $config['dir'] . $file['destination'];

            $array_source      = Yaml::parse(file_get_contents($dir_source));
            $array_destination = Yaml::parse(file_get_contents($dir_destination));

            if (!array_key_exists(key($array_source), $array_destination)) {
                $array_merged   = array_merge($array_destination, $array_source);
                $string_yaml    = Yaml::dump($array_merged, 10);
                $string_encoded = filter_var($string_yaml, FILTER_SANITIZE_ENCODED);

                $io->write(": [ + ] echo '$string_encoded' | tee '$dir_destination'");

                file_put_contents($dir_destination, $string_yaml);
            }
        }

        foreach ($replace as $file) {
            $dir_source      = self::$ROOT . $file['source'];
            $dir_destination = self::$ROOT . $config['dir'] . $file['destination'];

            $array_source = Yaml::parse(file_get_contents($dir_source));

            if (!array_key_exists(key($array_source), $array_destination)) {
                $yamp_string  = Yaml::dump($array_source, 10);
                $yamp_encoded = filter_var($yamp_string, FILTER_SANITIZE_ENCODED);

                $io->write(": [ + ] echo '$yamp_encoded' | tee '$dir_destination'");

                file_put_contents($dir_destination, $yamp_string);
            }
        }

        $io->write(":\n\r: METHOD FINISHED: $function() \n\r\n\r");
    }

    /**
     * @param Event $event
     */
    protected static function install_symfony_permissions(Event $event): void
    {
        // local vars
        $function = __FUNCTION__;

        // input/output handler
        $io = $event->getIO();

        $config = self::$CONFIG['symfony'];

        $io->write("\n\r\n\r: METHOD STARTED: $function() \n\r:");

        $cmd_chown = ": [ + ] chown ";

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
        $config_system   = self::$CONFIG['wordpress'];
        $config_location = $config_system['dir'];
        $config_filename = $config_system['filename'];
        $config_physical = self::$ROOT . self::$CONFIG['dir'] . $config_filename;
        $config_virtual  = self::$ROOT . $config_system['dir'] . $config_system['config'] . $config_filename;

        $io->write("\n\r\n\r: METHOD STARTED: $function() \n\r:");

        if (!is_dir($config_location)) {
            $cmd_download = "wp core download --path='$config_location'";

            $io->write(": [ + ] $cmd_download");

            exec($cmd_download);
        }

        if (file_exists($config_virtual)) {
            $io->write(": [ - ] unlink '$config_virtual'");

            unlink(realpath($config_virtual));
        }

        if (!file_exists($config_physical)) {
            $cmd_config = implode(' ', [
                "wp config create",
                "--path='" . $config_location . "'",
                "--dbname='" . self::$SETTINGS['parameters']['database_name'] . "'",
                "--dbuser='" . self::$SETTINGS['parameters']['database_user'] . "'",
                "--dbpass='" . self::$SETTINGS['parameters']['database_password'] . "'",
                "--dbhost='" . self::$SETTINGS['parameters']['database_host'] . "'",
                "--dbprefix='" . self::$SETTINGS['parameters']['database_prefix'] . "'",
                "--dbcharset='" . self::$SETTINGS['parameters']['database_charset'] . "'",
            ]);

            $io->write(": [ + ] $cmd_config");

            exec($cmd_config);

            rename($config_virtual, $config_physical);
        }

        $io->write(": [ + ] ln -s '$config_physical' '$config_virtual'");

        symlink($config_physical, $config_virtual);

        $cmd_core = implode(' ', [
            "wp core install",
            "--path='" . $config_location . "'",
            "--url='" . self::$SETTINGS['project']['url'] . "'",
            "--title='" . self::$SETTINGS['project']['title'] . "'",
            "--admin_user='" . self::$SETTINGS['project']['user'] . "'",
            "--admin_password='" . self::$SETTINGS['project']['pass'] . "'",
            "--admin_email='" . self::$SETTINGS['project']['mail'] . "'",
            "--skip-email",
        ]);

        $io->write(": [ + ] $cmd_core");

        exec($cmd_core);

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
        $config_system   = self::$CONFIG['wordpress'];
        $config_location = $config_system['dir'];

        $io->write("\n\r\n\r: METHOD STARTED: $function() \n\r:");

        if (is_dir($config_location)) {
            $cmd_update = "wp core update --path='$config_location'";

            $io->write(": [ + ] $cmd_update");

            exec($cmd_update);
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

        $config   = self::$CONFIG['wordpress'];
        $symlinks = $config['symlink'];

        $io->write("\n\r\n\r: METHOD STARTED: $function() \n\r:");

        // install symlinks
        foreach ($symlinks as $symlink) {
            $root       = $config['dir'];
            $input_file = $symlink['input_filename'];
            $input_dir  = $symlink['input_dir'];
            $output_dir = $symlink['output_dir'];

            $abs_root   = self::$ROOT . $root;
            $abs_input  = self::$ROOT . $input_dir . $input_file;
            $abs_output = $abs_root . $output_dir;

            if (!file_exists($abs_output)) {
                $io->write(": [ + ] mkdir $abs_output");

                mkdir($abs_output);
            }

            foreach (glob($abs_input) as $file) {
                $base = basename($file);
                $link = $abs_root . $output_dir . $base;

                if (file_exists($link)) {
                    $io->write(": [ - ] unlink $link");

                    //unlink($link);
                    unlink(realpath($link));
                }

                $io->write(": [ + ] ln -s '$file' '$link'");

                symlink($file, $link);
            }
        }

        $io->write(":\n\r: METHOD FINISHED: $function() \n\r\n\r");
    }
}
