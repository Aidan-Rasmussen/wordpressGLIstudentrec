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
define( 'DB_NAME', 'wordpressglitest' );

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
define( 'AUTH_KEY',         'VWqiR*J)R=@8)]0A)%kgZzsnU1V10~XB7L}lVbHi~7qp)DF-92PlOL>z#*tP35&5' );
define( 'SECURE_AUTH_KEY',  'tlxi|a=)<jr=Cv)ExiC$#Qo97}U~2reW2[^qJ(<G=S3S%@S+P-602Xsb(dk-I^mj' );
define( 'LOGGED_IN_KEY',    '_d*cc;Mtt{FgX=2Os0[t5v&M^,x-$EkW[YuBI @xBE@QseXl}e&dSq>)xzprX#hY' );
define( 'NONCE_KEY',        ',E<&,!P+U1pwqnKJ0Wo1z#dz!Jqk)fX!>sZaPO]K-Uh!gbe7gK{HE%b6%_N;D,-V' );
define( 'AUTH_SALT',        'NO5XZrhY4#^#1bcg T_,D8M>I-7BCBuZ0n?#BJqxSo[>~7t:&Km^`?mjJgPAS<80' );
define( 'SECURE_AUTH_SALT', 'm%#<5rs^u4!hOD ^`IE~4QQ2>eGMlT!&1YdQB_50/2?1g$l#`+fJYkSw&%y[`D;@' );
define( 'LOGGED_IN_SALT',   'k,#w=-i/VM+;11IKvjwY@sR,ExMKtrl*fY3$3s<oSra19B[?n2G_NYe75/GV+Y2]' );
define( 'NONCE_SALT',       '2br3hI^}YeDB`:;yP>{!,Vz KbsYGxjV,8:0jQ#Jb:etd`/&*]UD3+.F4>{fj>=3' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
