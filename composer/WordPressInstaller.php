<?php

namespace wiejakp\SymfonyPress\Composer;

use wiejakp\SymfonyPress\Composer\BaseInstaller;

// composer classes
use Composer\Script\Event;

class WordPressInstaller extends BaseInstaller
{
    // installer settings
    private static $SCRIPT;

    // installer directories
    private static $DIR_PRIVATE;
    private static $DIR_SHARED;
    private static $DIR_PUBLIC;

    // installer files
    private static $FILE_CONFIG;

    protected static function run(Event $event)
    {
        self::$SCRIPT = self::$INSTALLERS['wordpress'];
    }

    private static function require()
    {
        $required = array_key_exists('require', self::$SCRIPT) ? self::$SCRIPT['require'] null;
        $composer = self::$COMPOSER;

        if($required) {
            foreach($required as $repository => $version) {
                $cmd_require = "php $composer require $repository:$version";

                evec($cmd_require);
            }
        }
    }
}
