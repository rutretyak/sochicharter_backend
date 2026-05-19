<?php

// Файл с примерами использования хуков для нашей документации

/**
 * [en] Enable 404 error instead of redirect
 * [ru] Включаем 404 ошибку вместо перенаправления
 */
add_filter( 'clearfy/disable_feeds/is_redirect', '__return_false' );


/**
 * [en] Enable RSS feeds for any post types
 * [ru] Включаем для записей любых типов постов работу RSS-фидов
 */
add_filter( 'clearfy/disable_feeds/is_feed', function ( $is_feed ) {
    // is_category(), is_tag(), is_tax() и т.д.
    if ( is_singular() ) {
        return false;
    }

    return $is_feed;
} );


/**
 * [en] Disable content protection for specific pages
 * [ru] Отключаем защиту контента для определенных страниц
 *
 * @param bool $enable
 * @param string $type
 */
add_filter( 'clearfy/content_protection/enable', function ( $enable, $type ) {

    // [en] Example for a specific page with ID 437
    // [ru] Пример для определенной страницы с ID 437
    if ( is_page( 437 ) ) {
        return false;
    }

    // [en] Example for a specific category with ID 7
    // [ru] Пример для определенной категории с ID 7
    if ( in_category( 7 ) ) {
        return false;
    }

    // [ru] Дополнительно можно отключить только одну функцию защиты от копирования, например, выделение текста
    // [ru] $type может быть: context_menu, text_selection, disable_hotkeys, source_link
    // [en] You can also disable only one content protection function, for example, text selection
    // [en] $type can be: context_menu, text_selection, disable_hotkeys, source_link
    if ( 'text_selection' === $type && is_single( 219 ) ) {
        return false;
    }

    return $enable;
}, 10, 2 );


/**
 * [en] Fully remove the "ver" query arg from assets
 * [ru] Полностью удаляем параметр "ver" у CSS/JS файлов
 */
add_filter( 'clearfy/assets/is_remove_versions_fully', '__return_true' );


/**
 * [en] Change local avatar crop size (width and height)
 * [ru] Изменяем размер локального аватара (ширина и высота)
 */
add_filter( 'clearfy/local_avatars/size', function () {
    return 160;
} );


/**
 * [en] Allow editors to view frontend during maintenance mode
 * [ru] Разрешаем редакторам видеть фронтенд в режиме реконструкции
 */
add_filter( 'clearfy/maintenance_mode/view_capability', function () {
    return 'edit_others_posts';
} );


/**
 * [en] Change maintenance page title
 * [ru] Изменяем заголовок страницы режима реконструкции
 */
add_filter( 'clearfy/maintenance_mode/title', function ( $title ) {
    return 'Site under maintenance';
} );


/**
 * [en] Disable external links hiding for specific pages/categories and contexts
 * [ru] Отключаем скрытие внешних ссылок для отдельных страниц/рубрик и контекстов
 *
 * @param bool $enabled
 * @param string $context content|comment|comment_author
 */
add_filter( 'clearfy/external_links/is_enabled', function ( $enabled, $context ) {
    if ( is_page( 437 ) ) {
        return false;
    }

    if ( 'content' === $context && in_category( 7 ) ) {
        return false;
    }

    return $enabled;
}, 10, 2 );


/**
 * [en] Change allowed post types for content conversion
 * [ru] Изменяем типы записей, где скрываются ссылки в контенте
 */
add_filter( 'clearfy/external_links/post_types', function ( $post_types ) {
    return [ 'post', 'page' ];
} );


/**
 * [en] Add URL patterns that should never be converted
 * [ru] Добавляем шаблоны ссылок, которые не нужно конвертировать
 */
add_filter( 'clearfy/external_links/excluded_patterns', function ( $patterns ) {
    $patterns[] = 'my-partner.com';
    $patterns[] = '/go/';

    return $patterns;
} );


/**
 * [en] Allow editors and higher to bypass Clearfy Cloud protection
 * [ru] Разрешаем редакторам и выше обходить защиту Clearfy Cloud
 */
add_filter( 'clearfy/cloud_protection/bypass_capability', function () {
    return 'edit_others_posts';
} );


/**
 * [en] Example: bypass protection for all logged-in users
 * [ru] Пример: обходить защиту для всех авторизованных пользователей
 */
add_filter( 'clearfy/cloud_protection/bypass_logged_in', function ( $bypass ) {
    return $bypass;
} );


/**
 * [en] Disable Clearfy Cloud protection globally (example)
 * [ru] Полностью отключить защиту Clearfy Cloud (пример)
 */
add_filter( 'clearfy/cloud_protection/enabled', function ( $enabled ) {
    return $enabled;
} );
