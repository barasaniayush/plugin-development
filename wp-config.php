<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'plugin_development' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'ur1)]5>tR+t/cx(n/7YPzP:TnNG=|2M?Jn;sJ]}gc4l6{/`1 z?QQ/R*HEs8!~XZ' );
define( 'SECURE_AUTH_KEY',  'R+Z*{O%T2:Z`$gmd=})bXOIkomRGoCLl<>g*A}O@K?G-!~Ttae$[K{k_G[u)2=xJ' );
define( 'LOGGED_IN_KEY',    'E>IJk>P7j7 _!/)&:nI6S7]5*:2q.-dix .Gch4Q2mD<mQu:%lttta |p]*li[iX' );
define( 'NONCE_KEY',        'Rk`xCYPab1Y8Jv%kM{#}zCaq3=x#f:DXiDG%m9+cf>KWsT3yDtO=pf}XCgrdBO+K' );
define( 'AUTH_SALT',        'S~S.u!PrFSvltyFhMp][H!x[PSJ_,dLM?z1.5DgyjUJ^PRi_55L HE`-vCxEA`ut' );
define( 'SECURE_AUTH_SALT', '}C:)-Pp*Ub$++VD@uSj3mEoD7V3!8J|(If?%y7ln$n1;9`eab^LM6vev?Ie;2*2A' );
define( 'LOGGED_IN_SALT',   'i[SeE00e)70GKTJ)::!6P-yZIayt$6.[.]2Wn%V9^s`s>l0LPC j-)k>Dqj(*.(6' );
define( 'NONCE_SALT',       '4GRogF?Im~`=eFR8upmst{8>X.uxAKQx)j`$vtB7j`s0%_2NQ%kah~JpQCp[w?Pk' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'pd_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );
define( 'WP_DEBUG_DISPLAY', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
