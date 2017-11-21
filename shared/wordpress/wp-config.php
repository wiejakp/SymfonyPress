<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'symfonypress' );

/** MySQL database username */
define( 'DB_USER', 'symfonypress' );

/** MySQL database password */
define( 'DB_PASSWORD', 'symfonypress' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'RMwP|ew8m$F%f@>v) b 8OY|R?GVD>EcG)ocU9/{WMRYKutX>2}Xs=]$,kmycwtU' );
define( 'SECURE_AUTH_KEY',  'a4Q#UhhI6i,aI?Ps8ez&sqJz5h4y8RyKy]*.!) ZVp@T3I4jhPb8oIF J;OJ,S&7' );
define( 'LOGGED_IN_KEY',    'G@)Gm/b82sl0[X=Y)2zw-oBSj,6{B}]67,RfTUO3lDQ*r2fN3(YOak+d#Tgi2KUl' );
define( 'NONCE_KEY',        'hBxk4;e3?4Ngf`;!P@{D5$D#h^Pg`U=AnqHh@-~%zfW4#+V`rB|{V}u[|fNAnmvI' );
define( 'AUTH_SALT',        'cAGRk9=v`0a<d$0+AG[H?s~Yl}<lO.~$6%W*Garz3NwPplx<%M+_!;Y}goEV^8}O' );
define( 'SECURE_AUTH_SALT', 'S-zGHZWRy5HStXViF1Tc_Ffu6zrW!;Zsm!$J1tfQd9d@(~EYefiIC {yO[f]B}x|' );
define( 'LOGGED_IN_SALT',   'qr}wS(+F@f}w[hMspkHgvR[?/PgRw$y[l;XvXkz>u$eoc^}~}#hIDGj-( w4(_]>' );
define( 'NONCE_SALT',       '*pN)*mLY!meCBW>.60hLy?VwQ8sh}eneQB!onjy ]$%g!Kb`Q8G7Tm!,c?H3&zRx' );

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';




/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) )
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
