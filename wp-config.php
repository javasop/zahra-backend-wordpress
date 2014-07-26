<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'javasop_zahrahc');

/** MySQL database username */
define('DB_USER', 'javasop_zahrah');

/** MySQL database password */
define('DB_PASSWORD', 'zahrahcp1989');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'hL`NR`<p4qt(`TCZ6O}? 6?OD*NjK]N$+etqH |ez}j_b5RJc l}Q{h5S|zFpJy0');
define('SECURE_AUTH_KEY',  '~l7Bq:/q!k:N=VclX^0D-!cN}lT)qSRASioQB(2yeu~#k@+@_qq$}-e){nz^?L#l');
define('LOGGED_IN_KEY',    'pjgKk)5~X):/[YCUeI%/kplAu,(St?zMz[(<3INe#5D|dl|./ssE|[-X1m{SWD8|');
define('NONCE_KEY',        'uC;u$s nM![o>d?Sf2n&x&d3=~IG}xxWeGlo?i[bhc|^{`|6vI}$v|qi<~bB7@^v');
define('AUTH_SALT',        'v<F.nc!=UCtTa&/%G|psL7x&xX{9ok.(7pMr+FzDQ6@I`@@s>rA+,)7Jpe*TAO0+');
define('SECURE_AUTH_SALT', '(9{@^!uTJ/Rw|N_p-thH;%?m_c;+fa+E;=fCNB|PYsjZE1@b1J|{|Q_u*(Q,,@$#');
define('LOGGED_IN_SALT',   '0|If,aF_G`8|.@9S+)s?1m9)Z K.}B{xnBf+9j~:s0V&|B[d9sA&-^$&6]0d+HQc');
define('NONCE_SALT',       'VG&dY.hU^cju[jIAi+Rq.O5tUg4jL.m`=t4}n/CEba3MsLE^$S$2_|zQhu;oid^_');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
