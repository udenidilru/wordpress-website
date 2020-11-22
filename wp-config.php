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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wpnew' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'Rootroot1!' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '8Q)Cv898=!]9/o /G+}a}LR)>u v[TY5GdAPB1sZ#ym,XOH|o<~k]X]3c]p!vWq+' );
define( 'SECURE_AUTH_KEY',  ']7;m5k#3^9XP,8{ihBpvCNP;f/zlKQW~yap~ew/iAB#ituL,Z.!mzNUh2^}+}:0Q' );
define( 'LOGGED_IN_KEY',    '}p>B|UAXEAW&p we/,Q[~mA#D:i8.DLO!K&;yH$M8uU#Mlj+_DTv$]>oOrOZi~V$' );
define( 'NONCE_KEY',        'd*7q(%2w5IwM]So083KCk0U*P f_U6V~BmFbhu]Ie0M*QhTi^I3m@HR:pALrrX=[' );
define( 'AUTH_SALT',        'm-_V[SC:I ACWw:gSW->6;SGC#m7kU1g&u`$d1lv%9b]FY9X >>pB(L:U`K*R-B5' );
define( 'SECURE_AUTH_SALT', 'u+$bA4W1T~o#1a6IdI$:Kh00QE@lT,k(fLJ>@?RCI7OUsw}s<A,6l+qdl<XhIEoI' );
define( 'LOGGED_IN_SALT',   'giHu<j3Kh#rC[1LXnP[!Tq$~}.;p.Z~fh uP.V9[e~X+K#Daaeb(S:s-.W1,K_w#' );
define( 'NONCE_SALT',       'H7<0(c?p55/mEKZ)dur?i+[Vhk|gR*`On|66{gry0+]2[qsW/2tNC(n&%bd%_-u4' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
