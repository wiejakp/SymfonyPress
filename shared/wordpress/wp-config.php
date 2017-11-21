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
define( 'AUTH_KEY',         'WSYQ960IZQl<<ZXGw~b5Gz4%C*>unM0GF&EP_14|e=0c@,@-EA.x:%Fci -GVNS7' );
define( 'SECURE_AUTH_KEY',  'IqSdgpa{,;n^c9SAKScq=F=#I4=|/>D2/z@B(f_}o]otKLfj0rpkGLmo7vVTT4/+' );
define( 'LOGGED_IN_KEY',    '.9DH(y#p!#11bz6tkajIIXdk/F/CZ=g;|01@HAk-a=jk#j!J02Nyk=q!9;6%dVFG' );
define( 'NONCE_KEY',        '{Pnjj40#J4Q_P3@V6EX*x&6{z:y22:w%p_ZN|@P+}=7G-niKzAM!sIE?Xolo*`G!' );
define( 'AUTH_SALT',        'HO&qjyHByYV)%uKyZwyb9*O&M1_G@O+0x2LcJquj70dDI0&=?TvK#c]ukftUyP~@' );
define( 'SECURE_AUTH_SALT', 'eMCZu|33`4z`Sd02B>:C!Jg=w>O;/f&owDN`M>.{n#J.2!N~ro60fe>5i0]KRR$p' );
define( 'LOGGED_IN_SALT',   'hv}?D}d-ojyrZi[qxxr8[t)/Go$LzWvsKjlFQiS3*gL?zw-oQWd>4,}j(aF~sqd!' );
define( 'NONCE_SALT',       'h5qs5+.]]|*>ADu_QW);L#qf*~cQ~ tyEpC8xLp&Hk{h`wcD{%@P(G:Lg3AJXNRc' );

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
