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
define( 'AUTH_KEY',         '>sffyO+{+%B!Litmk3epId~>DmKVn,%B(a4AGHmX|HuDwi[zVLmZrTN3hX)J)KBU' );
define( 'SECURE_AUTH_KEY',  '7eOMc@q@$v,^BpF3L}M[pKBPT{TP<p7x<^y6`c[CRNyGx3YIpkPqVE!<2BC<%N1o' );
define( 'LOGGED_IN_KEY',    ';g9Y<&VRiMW=p6!~vrk.DXrdh7B Fg6i47op+x@n=Uu`$o2T<T&rG>;Z$:c6m!<b' );
define( 'NONCE_KEY',        '*jrr1z2~[^xA]x<2}l=EgssjDx^~S U|L+=yciIidz?NP,z}L,xe+:aMC ))#R~d' );
define( 'AUTH_SALT',        'ssKiHDeOo-P]QVwz5/uTB9r*CC!N:d`=6@vcNSn#p=NVgc)NX.QO}IMEP4]cb]K:' );
define( 'SECURE_AUTH_SALT', ')7|$CVV0#AAole6sOBkaDbKkn9VbEUS+?})*t3xeZg]<7@3it/k^C^~{W(qKuex<' );
define( 'LOGGED_IN_SALT',   'go&KOy@mC%Wl<0?ae2@;MUe33Ia^Jz*y(3SNq;E$J2swo)u/)Nm&2=3N@kxO(ooP' );
define( 'NONCE_SALT',       '(GJsbgyje$CN5MXQ3G0BhwHX8iWt82O&~kJV[7D![UHv!0h~mQx|@w3N]#%G56s)' );

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
