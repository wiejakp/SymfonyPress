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
define( 'AUTH_KEY',         'H1`y4q.S3cNr2H4DjWlr1xWTGILu-zgT84MJK6!5?Cseowe.*:>kl^4h=LrS2wr}' );
define( 'SECURE_AUTH_KEY',  'EQXIhAbWH{zG$;_)YKyDveG}xJMQLf6gJ:aR.Q8mQaMV4}pBer^E$UhMjZ2?[+;r' );
define( 'LOGGED_IN_KEY',    '/uz5S7K$<!Cx7T9FxBq}#?:JYGV(pF|=GhIPGRL@Zocv}jIq`d0G-#u!X[o5T~W0' );
define( 'NONCE_KEY',        '#qlf</Bq@)uVS1+EgMY?=DH6.~+v=cckqg-FZ(>/,(I7QQnjdgG8IYgV3y+(<8Su' );
define( 'AUTH_SALT',        'A7eb=-2M5h x>.zk8~U}EfaK=3-EAM }iTq{tpu=l:~:[r|67V8mXRWnPTmp)hq:' );
define( 'SECURE_AUTH_SALT', 'xCH_%=(.DP>ST;Ktb4ZwbwR}<S:~1j<P@Uep>|-D7v}Mj|Dt2x,[_Kgt{0xm#+%>' );
define( 'LOGGED_IN_SALT',   'T>FG-aek_nkqnU#vgc68/MDJYp8oh32Abyj%%Llc+M4K_V)YMS0%qwrDb*h3tP!f' );
define( 'NONCE_SALT',       'qlovwZv[#uGx:TNS[7tu<M(}Q/YSP8-B5&wuJK[W>1I(v-lTNAVq|=!XTLV)+_~{' );

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
