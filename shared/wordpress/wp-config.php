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
define( 'AUTH_KEY',         '&9A|ssp1qC4l}Fmyw*Bi24)0YyDt/ku4=z]qW~W>(t=bdH;gLh&V<Kko{e7[Us~^' );
define( 'SECURE_AUTH_KEY',  'jU%9w9*c[YNJxln=I<z[?:h_Iy<F@y6-`N2jq,0xJa,^ABjwLVC%u]DkOj!5olg_' );
define( 'LOGGED_IN_KEY',    '`A!Wr&<8=1hnJxsKU4F6%e(:yN>nqaIuoOIL3q}G@Z{|&z1;ZS*JbvTESp6EF}+w' );
define( 'NONCE_KEY',        'LA6hMMd[l97{kV+V,nUj;,SSY2>_@&yXkZ}n%y[5y7O>`4*5>Jpzd,YD/k}P%79}' );
define( 'AUTH_SALT',        '@$1iJZ[6t2A7 TY}RoD}|D;?#JOns!S5dTX5;<XVlhfV/3$Z/D~1!&KQa`jXM)7m' );
define( 'SECURE_AUTH_SALT', '<x;pg86p]wn8m8zeU#3&<I&31XE&&!yPY-(/-8ZnWAr_(|VXzh0CGmI{|dj`(-a1' );
define( 'LOGGED_IN_SALT',   'uFgUGj|/|ARwN|aQ6@%l9dmI~+_@+HH*wtB;2AJB6IbT=7gIY9c-z,czq2 3Qeet' );
define( 'NONCE_SALT',       '^c#h+z}M<#5ML2rOj91d;ko:kU/-XN~<X4FLCB{[SYUW>>bU^7}[B9u-a8> W)(R' );

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
