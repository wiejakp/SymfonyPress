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
    private static $METHOD;

    /**
     * @var string
     */
    private static $IO;

    /**
     * @var string
     */
    private static $WP_CLI;

    /**
     * @var string
     */
    private static $WP_DIR;

    /**
     * @var array
     */
    private static $EXTRA;

    /**
     * @var string
     */
    private static $ROOT;

    /**
     * @var string
     */
    private static $CONFIG_DIR;

    /**
     * @var string
     */
    private static $CONFIG_FILE;

    /**
     * @var string
     */
    private static $SHARED_DIR;

    /**
     * @var string
     */
    private static $PRIVATE_DIR;

    /**
     * @var array
     */
    private static $PARAMETERS;

    /**
     * @param $method
     * @param $args
     */
    public static function __callStatic($method, $args)
    {
        if (empty($args) || false === $args[0] instanceof Event) {
            throw new InvalidArgumentException('Every method call must contain Event parameter.');
        }

        self::$METHOD = $method;
        self::$IO = $args[0]->getIO();
        self::$EXTRA = $args[0]->getComposer()->getPackage()->getExtra();
        self::$ROOT = dirname(dirname(__FILE__)) . '/';
        self::$CONFIG_DIR = self::$ROOT . stripslashes(self::$EXTRA['dir']['config']);
        self::$PRIVATE_DIR = self::$ROOT . stripslashes(self::$EXTRA['dir']['private']);
        self::$SHARED_DIR = self::$ROOT . stripslashes(self::$EXTRA['dir']['shared']);
        self::$CONFIG_FILE = self::$CONFIG_DIR . self::$EXTRA['config'];
        self::$WP_CLI = $args[0]->getComposer()->getConfig()->get('vendor-dir') . '/wp-cli/wp-cli/bin/wp';
        self::$WP_DIR = $args[0]->getComposer()->getConfig()->get('vendor-dir') . '/wordpress/wordpress/';

        if (!is_dir(self::$SHARED_DIR)) {
            mkdir(self::$SHARED_DIR, 0777, true);
        }

        if (!is_dir(self::$CONFIG_DIR)) {
            mkdir(self::$CONFIG_DIR, 0777, true);
        }

        if (!file_exists(self::$CONFIG_FILE)) {
            touch(self::$CONFIG_FILE);
        }

        self::$PARAMETERS = json_decode(stripslashes(file_get_contents(self::$CONFIG_FILE)), true);

        // parse projects settings
        /*
        if (!empty(self::$EXTRA)) {
            $settings_dir = self::$EXTRA['dir'];
            $settings_path = $settings_dir . self::$EXTRA['filename'];

            // create config directory when it doesn't exist
            if (!is_dir($settings_dir)) {
                mkdir($settings_dir, 0777, true);
            }

            // parse settings if they are already set
            if (file_exists($settings_path)) {
                self::$SETTINGS = Yaml::parse(file_get_contents($settings_path));
            }
        }
        */

        self::$IO->write("\n\r\n\r: METHOD STARTED: $method() \n\r:");

        forward_static_call_array([self::class, $method], $args);

        self::$IO->write(": METHOD FINISHED: $method() \n\r\n\r");
    }

    /**
     * @param Event $event
     */
    protected static function input(Event $event): void
    {
        // if settings already exist, skip input prompts
        if (!self::$PARAMETERS) {
            // store all user inputs
            $data = [];
            $params = &$data['parameters'];
            $project = &$data['project'];

            // generate secret token
            $secret = uniqid('SymfonyPress_', true);

            self::$IO->write(": [ ! ] Server Information");
            self::$IO->write(":");

            $params['database_host'] = self::$IO->ask(": [ ? ] Database Host [localhost]: ", 'localhost');
            $params['database_port'] = self::$IO->ask(": [ ? ] Database Port [3306]: ", '3306');
            $params['database_name'] = self::$IO->ask(": [ ? ] Database Name [symfonypress]: ", 'symfonypress');
            $params['database_user'] = self::$IO->ask(": [ ? ] Database User [symfonypress]: ", 'symfonypress');
            $params['database_password'] = self::$IO->ask(": [ ? ] Database Password [symfonypress]: ", 'symfonypress');
            $params['database_prefix'] = self::$IO->ask(": [ ? ] Database Prefix [wp_]: ", 'wp_');
            $params['database_charset'] = self::$IO->ask(": [ ? ] Database Char Set [utf8]: ", 'utf8');
            $params['mailer_transport'] = self::$IO->ask(": [ ? ] Mail Server Protocol [smtp]: ", 'smtp');
            $params['mailer_host'] = self::$IO->ask(": [ ? ] Mail Server Host [127.0.0.1]: ", '127.0.0.1');
            $params['mailer_user'] = self::$IO->ask(": [ ? ] Mail Server User [null]: ", 'null');
            $params['mailer_password'] = self::$IO->ask(": [ ? ] Mail Server Password [null]: ", 'null');
            $params['secret'] = self::$IO->ask(": [ ? ] Secret Token [$secret]: ", $secret);

            self::$IO->write(":");
            self::$IO->write(": [ ! ] Web Site Information");
            self::$IO->write(":");

            $project['url'] = self::$IO->ask(": [ ? ] Web Site URL [symfonypress.dev]: ", 'symfonypress.dev');
            $project['title'] = self::$IO->ask(": [ ? ] Web Site Title [SymfonyPress]: ", 'SymfonyPress');

            self::$IO->write(":");
            self::$IO->write(": [ ! ] Administrator User Information");
            self::$IO->write(":");

            $project['mail'] = self::$IO->ask(": [ ? ] Admin E-Mail Address [symfonypress@symfonypress.dev]: ", 'symfonypress@symfonypress.dev');
            $project['user'] = self::$IO->ask(": [ ? ] Admin User Name [symfonypress]: ", 'symfonypress');
            $project['pass'] = self::$IO->ask(": [ ? ] Admin Password [symfonypress]: ", 'symfonypress');

            self::$IO->write(":");
            self::$IO->write(":");

            file_put_contents(self::$CONFIG_FILE, json_encode($data, JSON_PRETTY_PRINT));
        }
    }

    /**
     * @param Event $event
     */
    protected static function install_symfony(Event $event): void
    {
        $dir_private = self::$ROOT . self::$EXTRA['dir']['private'] . self::$EXTRA['symfony']['dir'];
        $dir_shared = self::$ROOT . self::$EXTRA['dir']['shared'] . self::$EXTRA['symfony']['dir'];
        $dir_system = self::$ROOT . self::$EXTRA['symfony']['dir'];

        // extracted config
        $config_system = self::$EXTRA['symfony'];
        $config_location = $config_system['config'];
        $config_filename = $config_system['filename'];
        $config_version = $config_system['version'];
        $config_physical = $dir_private . $config_location . $config_filename;
        $config_virtual = $dir_system . $config_location . $config_filename;

        if (!is_dir($config_system['dir'])) {
            $cmd_create = "composer create-project '$config_version' '$dir_system' --no-interaction";

            self::$IO->write(": [ + ] $cmd_create");

            exec($cmd_create);

            if (array_key_exists('require', $config_system)) {
                foreach ($config_system['require'] as $require) {
                    $repo_user = $require['user'];
                    $repo_title = $require['title'];
                    $repo_name = $repo_user . '/' . $repo_title;
                    $repo_type = $require['type'];
                    $repo_version = $require['version'];
                    $repo_url = $require['url'];

                    $cmd_repository = "composer config repositories.$repo_title '$repo_type' '$repo_url' --working-dir '$dir_system'";
                    $cmd_require = "composer require $repo_name '$repo_version' --working-dir '$dir_system'";

                    self::$IO->write(": [ + ] $cmd_repository");
                    exec($cmd_repository);

                    self::$IO->write(": [ + ] $cmd_require");
                    exec($cmd_require);
                }
            }
        }

        if (!file_exists($config_physical)) {
            $parameters['parameters'] = self::$PARAMETERS['parameters'];
            $string_yaml = Yaml::dump($parameters);
            $string_encoded = filter_var($string_yaml, FILTER_SANITIZE_ENCODED);

            self::$IO->write(": [ + ] echo '$string_encoded' | tee '$config_physical'");

            file_put_contents($config_physical, $string_yaml);
        }

        if (file_exists($config_virtual)) {
            if (!is_link($config_virtual)) {
                self::$IO->write(": [ - ] unlink '$config_virtual'");

                unlink($config_virtual);
            }
        }

        if (!file_exists($config_virtual)) {
            self::$IO->write(": [ + ] ln -s '$config_physical' '$config_virtual'");

            symlink($config_physical, $config_virtual);
        }

        $cmd_chmod = "chmod -R 777 " . $dir_system . "var/";
        self::$IO->write(": [ + ] $cmd_chmod");

        exec($cmd_chmod);
    }

    /**
     * @param Event $event
     */
    protected static function update_symfony(Event $event): void
    {
        $dir_shared = self::$ROOT . self::$EXTRA['dir']['shared'] . self::$EXTRA['symfony']['dir'];
        $dir_system = self::$ROOT . self::$EXTRA['symfony']['dir'];

        // extracted config
        $config_system = self::$EXTRA['symfony'];
        $config_location = $config_system['dir'];

        if (is_dir($config_location)) {
            $cmd_update = "composer update --working-dir '$config_location' ";

            self::$IO->write(": [ + ] $cmd_update");

            exec($cmd_update);
        }

        $cmd_chmod = "chmod -R 777 " . $dir_system . "var/";
        self::$IO->write(": [ + ] $cmd_chmod");

        exec($cmd_chmod);
    }

    /**
     * @param Event $event
     */
    protected static function install_symfony_symlinks(Event $event): void
    {
        $config = self::$EXTRA['symfony'];
        $console = self::$ROOT . $config['dir'] . 'bin/console';
        $symlinks = $config['symlink'];

        // install symlinks
        foreach ($symlinks as $symlink) {
            $root = $config['dir'];
            $input_file = $symlink['input_filename'];
            $input_dir = $symlink['input_dir'];
            $output_dir = $symlink['output_dir'];
            $cmd_command = array_key_exists('command', $symlink) ? "$console " . $symlink['command'] : null;

            $abs_root = self::$ROOT . $root;
            $abs_input = self::$ROOT . $input_dir . $input_file;
            $abs_output = $abs_root . $output_dir;

            if (!file_exists($abs_output)) {
                self::$IO->write(": [ + ] mkdir '$abs_output' 0777, true");

                mkdir($abs_output, 0777, true);
            }

            foreach (glob($abs_input) as $file) {
                $base = basename($file);
                $link = $abs_root . $output_dir . $base;

                if (file_exists($link) && !is_dir($link)) {
                    if (!is_link($link)) {
                        self::$IO->write(": [ - ] unlink '$link'");

                        unlink($link);
                    }
                }

                if (!file_exists($link) && !is_dir($link)) {
                    self::$IO->write(": [ + ] ln -s '$file' '$link'");

                    symlink($file, $link);
                }
            }

            if ($cmd_command) {
                self::$IO->write(": [ + ] $cmd_command");

                exec($cmd_command);
            }
        }
    }

    /**
     * @param Event $event
     */
    protected static function install_symfony_config(Event $event): void
    {
        $config = self::$EXTRA['symfony'];
        $appends = $config['append'];
        $replace = $config['replace'];

        foreach ($appends as $file) {
            $dir_source = self::$ROOT . $file['source'];
            $dir_destination = self::$ROOT . $config['dir'] . $file['destination'];

            $array_source = Yaml::parse(file_get_contents($dir_source));
            $array_destination = Yaml::parse(file_get_contents($dir_destination));

            if (!array_key_exists(key($array_source), $array_destination)) {
                $array_merged = array_merge($array_destination, $array_source);
                $string_yaml = Yaml::dump($array_merged, 10);
                $string_encoded = filter_var($string_yaml, FILTER_SANITIZE_ENCODED);

                self::$IO->write(": [ + ] echo '$string_encoded' | tee '$dir_destination'");

                file_put_contents($dir_destination, $string_yaml);
            }
        }

        foreach ($replace as $file) {
            $dir_source = self::$ROOT . $file['source'];
            $dir_destination = self::$ROOT . $config['dir'] . $file['destination'];

            $array_source = Yaml::parse(file_get_contents($dir_source));

            if (!array_key_exists(key($array_source), $array_destination)) {
                $yamp_string = Yaml::dump($array_source, 10);
                $yamp_encoded = filter_var($yamp_string, FILTER_SANITIZE_ENCODED);

                self::$IO->write(": [ + ] echo '$yamp_encoded' | tee '$dir_destination'");

                file_put_contents($dir_destination, $yamp_string);
            }
        }
    }

    /**
     * @param Event $event
     */
    protected static function install_wordpress(Event $event): void
    {
        $wp_cli = self::$WP_CLI;

        $dir_private = self::$ROOT . self::$EXTRA['dir']['private'] . self::$EXTRA['wordpress']['dir'];
        $dir_shared = self::$ROOT . self::$EXTRA['dir']['shared'] . self::$EXTRA['wordpress']['dir'];
        $dir_system = self::$ROOT . self::$EXTRA['wordpress']['dir'];

        // extracted config
        $config_system = self::$EXTRA['wordpress'];
        $config_location = $config_system['config'];
        $config_filename = $config_system['filename'];
        $config_version = $config_system['version'];
        $config_physical = $dir_private . $config_location . $config_filename;
        $config_virtual = $dir_system . $config_location . $config_filename;

        // config data
        $parameters = self::$PARAMETERS['parameters'];
        $project = self::$PARAMETERS['project'];

        if (!is_dir($dir_system)) {
            $cmd_download = "$wp_cli core download --version='$config_version' --path='$dir_system'";

            self::$IO->write(": [ + ] $cmd_download");

            exec($cmd_download);
        }

        if (file_exists($config_virtual)) {
            if (!is_link($config_virtual)) {
                self::$IO->write(": [ - ] unlink '$config_virtual'");

                unlink($config_virtual);
            }
        }

        if (!file_exists($config_virtual)) {
            $cmd_config = implode(' ', [
                "$wp_cli config create",
                "--path='" . $config_location . "'",
                "--dbname='" . $parameters['database_name'] . "'",
                "--dbuser='" . $parameters['database_user'] . "'",
                "--dbpass='" . $parameters['database_password'] . "'",
                "--dbhost='" . $parameters['database_host'] . "'",
                "--dbprefix='" . $parameters['database_prefix'] . "'",
                "--dbcharset='" . $parameters['database_charset'] . "'",
                "--path='" . $dir_system . "'",
            ]);

            self::$IO->write(": [ + ] $cmd_config");

            exec($cmd_config);

            self::$IO->write(": [ + ] mv '$config_virtual' '$config_physical'");

            rename($config_virtual, $config_physical);
        }

        if (!file_exists($config_virtual) && file_exists($config_physical)) {
            self::$IO->write(": [ + ] ln -s '$config_physical' '$config_virtual'");

            symlink($config_physical, $config_virtual);
        }

        $cmd_core = implode(' ', [
            "$wp_cli core install",
            "--url='" . $project['url'] . "'",
            "--title='" . $project['title'] . "'",
            "--admin_user='" . $project['user'] . "'",
            "--admin_password='" . $project['pass'] . "'",
            "--admin_email='" . $project['mail'] . "'",
            "--skip-email",
            "--path='" . $dir_system . "'",
        ]);

        self::$IO->write(": [ + ] $cmd_core");

        exec($cmd_core);
    }

    /**
     * @param Event $event
     */
    protected static function update_wordpress(Event $event): void
    {
        $wp_cli = self::$WP_CLI;

        // extracted config
        $config_system = self::$EXTRA['wordpress'];
        $config_location = $config_system['dir'];

        if (is_dir($config_location)) {
            // unlink files to prevent overriding originals
            self::install_wordpress_unlink($event);

            $cmd_update = "$wp_cli core update --path='$config_location'";

            self::$IO->write(": [ + ] $cmd_update");

            exec($cmd_update);

            // re-link files
            self::install_wordpress_symlinks($event);
        }
    }

    /**
     * @param Event $event
     */
    protected static function install_wordpress_symlinks(Event $event): void
    {
        $wp_cli = self::$WP_CLI;

        $config = self::$EXTRA['wordpress'];
        $symlinks = $config['symlink'];

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
                self::$IO->write(": [ + ] mkdir('$abs_output', 0777, true)");

                mkdir($abs_output, 0777, true);
            }

            foreach (glob($abs_input) as $file) {
                $base = basename($file);
                $link = $abs_root . $output_dir . $base;

                if (file_exists($link) && !is_dir($link)) {
                    if (!is_link($link)) {
                        self::$IO->write(": [ - ] unlink '$link'");

                        unlink($link);
                    }
                }

                if (!file_exists($link)) {
                    self::$IO->write(": [ + ] ln -s '$file' '$link'");

                    symlink($file, $link);
                }
            }
        }
    }

    /**
     * @param Event $event
     */
    protected static function install_wordpress_unlink(Event $event): void
    {
        $wp_cli = self::$WP_CLI;

        $config = self::$EXTRA['wordpress'];
        $symlinks = $config['symlink'];

        // install symlinks
        foreach ($symlinks as $symlink) {
            $root = $config['dir'];
            $input_file = $symlink['input_filename'];
            $input_dir = $symlink['input_dir'];
            $output_dir = $symlink['output_dir'];

            $abs_root = self::$ROOT . $root;
            $abs_input = self::$ROOT . $input_dir . $input_file;

            foreach (glob($abs_input) as $file) {
                $base = basename($file);
                $link = $abs_root . $output_dir . $base;

                if (file_exists($link) && !is_dir($link)) {
                    if (is_link($link)) {
                        self::$IO->write(": [ - ] unlink '$link'");

                        unlink($link);
                    }
                }
            }
        }
    }
}
