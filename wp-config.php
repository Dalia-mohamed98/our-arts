<?php
/**define( 'WPCACHEHOME', '/home3/ourarts1/public_html/wp-content/plugins/wp-super-cache/' );*/
define('SAVEQUERIES', false);
define('SCRIPT_DEBUG', false);
define('WP_DEBUG_DISPLAY', false);
define('WP_DEBUG_LOG', false);
define( 'WP_CACHE', true);
 // WP-Optimize Cache
define('WP_AUTO_UPDATE_CORE', 'minor');// This setting is required to make sure that WordPress updates can be properly managed in WordPress Toolkit. Remove this line if this WordPress website is not managed by WordPress Toolkit anymore.
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
define( 'DB_NAME', 'ourartsc_3bb' );
/** MySQL database username */
define( 'DB_USER', 'root' );
/** MySQL database password */
define( 'DB_PASSWORD', '' );
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
define('AUTH_KEY', '2gkFVlVJnL6hrY(20MNK@);(M0)-(5Kxs+8sU-5]#O71)H]1_0Q%_tH4IH1625QD');
define('SECURE_AUTH_KEY', '6!4FQc1o_e2vvmP2v*Xn[77J[gk:7299YD)+M)Z:X#hz00r0(#5&v_;+EonqTh+6');
define('LOGGED_IN_KEY', 'FF*y-QY|n3632SRd-B4kH3hMHe29[wH2[!h1%+0Jz85mNq106;&%0+z_l(n(lF+p');
define('NONCE_KEY', '8KoJjC*O04JU_pzj(9a506F&1wkQi-3~r3zB2m:;g!Lp8fI7l5yNW:p28&[F6bRr');
define('AUTH_SALT', 'yK)!~7e2f6A5%Xx)J*0H40I29_d~9791MsJc%2Xr|RR|X5m1[@|WJ%T6nc8#07Z8');
define('SECURE_AUTH_SALT', '&MPJvb~q9E;;3z0u0l88ej:B2uQ;/It:7L&]+*4+]4K:w174i]FY+E6Y)VhO6!8M');
define('LOGGED_IN_SALT', 'K@964%@*4;(6067c6pBe!X;Vl;X[O_!T]K-:*;Z4d/[;Ib|f25M+MuQ2r309C+_1');
define('NONCE_SALT', ';+iA*-GmX&Kbl43(E5FgVT~9KQ(3nw38hG9*fase3vg%h3lG8Og-_aV682z9]p8)');
/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = '3bb_';
define('WP_ALLOW_MULTISITE', true);
define('WP_DEBUG', false);
define( 'AUTOSAVE_INTERVAL', 300 );
define( 'WP_POST_REVISIONS', 5 );
define( 'EMPTY_TRASH_DAYS', 7 );
define( 'WP_CRON_LOCK_TIMEOUT', 120 );
define( 'WP_HOME', 'http://localhost/ourarts' );
define( 'WP_SITEURL', 'http://localhost/ourarts' );
/* That's all, stop editing! Happy blogging. */
/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) )
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';