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
define( 'AUTH_KEY',         '(+du14Gg(MW&bi[Q3%cx:|Ht.ug077@yt.Gu~{YJUVr`1.^8iK!{F2GW4_CilzN(' );
define( 'SECURE_AUTH_KEY',  'O|{$~0+~@+1tI>U:d@@n,/4~I,w*&w6=;u/8Yoo&H1yg^B,XGX{W!YGV<XBIKIO7' );
define( 'LOGGED_IN_KEY',    'Fd$4J$<P6.eg(M2Px)B8r+RJND[Ht/ahSB%uyG`W}:X]}k~q^&lTk wHh|!UD8[.' );
define( 'NONCE_KEY',        'U%rrKH4eu:~WGd8/j$m?8d4gOWZ$R=KQTo8nN;^?AX*9W~PKzsiE}a#v-M#&ENud' );
define( 'AUTH_SALT',        'eb9eaSK1C84iAyzywWV$Mh#mh@cFIt>|>~7x;l~/rn+wgq:#E]uMPzDuT6>rSP)Y' );
define( 'SECURE_AUTH_SALT', '^S3~jOSbd;2<xgGR60^WUG^vzy+_XQ#lI[@]DA!kQcNH3l8UWx8t3|j*.F;UC.v=' );
define( 'LOGGED_IN_SALT',   'l>qvK[KO/y}r|v/;JrAuZ!s19wx/yz]zjYAob(KH~Z0rI|=E.?_-w5u2:KYAyr<P' );
define( 'NONCE_SALT',       ']*puyv,DG4gulN($M4r1aU[Jk0$YbUiFGdyrFOlK/60+ =>:2g5> Fp6(r]qnJ+p' );

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
