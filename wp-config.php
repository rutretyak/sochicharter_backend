<?php
/**
 * Основные параметры WordPress.
 *
 * Скрипт для создания wp-config.php использует этот файл в процессе
 * установки. Необязательно использовать веб-интерфейс, можно
 * скопировать файл в "wp-config.php" и заполнить значения вручную.
 *
 * Этот файл содержит следующие параметры:
 *
 * * Настройки MySQL
 * * Секретные ключи
 * * Префикс таблиц базы данных
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** Параметры MySQL: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define( 'DB_NAME', 'sochicharter' );

/** Имя пользователя MySQL */
define( 'DB_USER', 'root' );

/** Пароль к базе данных MySQL */
define( 'DB_PASSWORD', '' );

/** Имя сервера MySQL */
define( 'DB_HOST', 'localhost' );

/** Кодировка базы данных для создания таблиц. */
define( 'DB_CHARSET', 'utf8mb4' );

/** Схема сопоставления. Не меняйте, если не уверены. */
define( 'DB_COLLATE', '' );

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу.
 * Можно сгенерировать их с помощью {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными. Пользователям потребуется авторизоваться снова.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'SFj{VT1{Kd]u9*9&P6t>=!tM; NUV|J|VF*1@>)b@Ej<moJFk%RHqPa?~zm#C*S!' );
define( 'SECURE_AUTH_KEY',  'Y<>PV<phuOkj8hl=0ENr+O1It>hNxf1x~>CE[vkANh`^2D0W]Zv={T[4[l5;i<eM' );
define( 'LOGGED_IN_KEY',    'tywx>FhQ8fg<+Z~cNZPS4wmaOPopH.7}dzdU@V6<Pnd^cV_CgR*b%>XV{&5V;dlD' );
define( 'NONCE_KEY',        '}ON$me34dfN)uvPM1h6&l]eD0YQyj]&6(>,bhh7]MybKaBt6:#eGIy*^t)*e?lqb' );
define( 'AUTH_SALT',        'DlvyMMs sE-k~b/;NON g(RWP-Lt!hY/Jf#;g74.`r8UvTEuU0,,<A]0AlY,lMys' );
define( 'SECURE_AUTH_SALT', 'Ry!vXl/M]MZR!.:V-E5<e-Vov=z0RC_WQDSWb+&fcd5g}z4p`V!B?y1PMXs4gRFH' );
define( 'LOGGED_IN_SALT',   ':@99U6FrT{eamv8C[Y(QZ).4BX^ZlEM2X{zEX/tCFidfW2t3!DHY&d~W$4H.!rD%' );
define( 'NONCE_SALT',       'u0ZmFHl_r}n,3&WeKY906Q?}9C{/xY|8$o+f1/?V0P+%7U<e|}TGQ&53tILUGr{h' );

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько сайтов в одну базу данных, если использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix = 'yvs_';

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Разработчикам плагинов и тем настоятельно рекомендуется использовать WP_DEBUG
 * в своём рабочем окружении.
 *
 * Информацию о других отладочных константах можно найти в Кодексе.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Инициализирует переменные WordPress и подключает файлы. */
require_once( ABSPATH . 'wp-settings.php' );
