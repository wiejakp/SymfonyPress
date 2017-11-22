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
define( 'AUTH_KEY',         'Q_U`)Epr;V4f=u(<<W%bU T_le1L[8Tju%Bh&fc%ZzVN>[r}%Nj}M2K2-;CGt0ZI' );
define( 'SECURE_AUTH_KEY',  '}I_STc> q[DtN4T]E}lJW(~,UaOL$~.2L-4`{j7Y6[_dC.b^kVTF]tR MeXo/`~e' );
define( 'LOGGED_IN_KEY',    '<tj@fx?F.sG*!d}k1)p}R|3;7>_?2HUc!C SXULt/4/?g!T4cTA| xwkQ;zW0H&Z' );
define( 'NONCE_KEY',        'bD<Z^XFL/p=,@X`Mmna:>EGz/TRz.H9/tHRliQhV+3!T.!T4^Ae<Zeviaiem8H55' );
define( 'AUTH_SALT',        '{WW9xEcBrd3/N9B$V6HL$X!kH1Y_ufpLD8PNjsTntLR}~`m`bcA JI^b:beR-(^t' );
define( 'SECURE_AUTH_SALT', '0X7Kt[,D(H ?D9`$+uj%nG?tD[4KZ;# jiKd2l|tk+OBHW(a(IX.LdRM]*@i]#9O' );
define( 'LOGGED_IN_SALT',   'KC-40~J+D+7%g8i_(v.bnH,Jqlk<Rw4BLV+%2XyFFcl+IdZ+5pe@1AC*E2`Pfa;V' );
define( 'NONCE_SALT',       '=Ez>|i3|2<w*~Y>=<#P](ur3TQ6a!mJ3Nd&y@jKaJE%VDjspp-%%`7!dvUq7%G*c' );

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
