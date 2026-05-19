<?php

/**
 * Class Clearfy_Sanitize
 */
class Clearfy_Sanitize {

    /**
     * Clearfy_Sanitize constructor.
     */
    public function __construct() {
        add_filter( 'sanitize_title', array( $this, 'sanitize_title' ), 9, 3 );
        add_filter( 'sanitize_file_name', array( $this, 'sanitize_file_name' ) );
    }


    /**
     * General sanitize
     *
     * @param $title
     *
     * @return mixed|string
     */
    public function sanitize( $title ) {

        $title = html_entity_decode( $title, ENT_QUOTES, 'utf-8' );
        $title = strtr( $title, $this->get_utf() );

        // Разрешаем только ascii + разделители
        $title = preg_replace( "/[^A-Za-z0-9-_.]/", '-', $title );
        $title = preg_replace( '~([=+.-])\\1+~' , '\\1', $title );

        // Схлопнем повторные дефисы и обрежем края
        $title = preg_replace( '/-{2,}/', '-', $title );
        $title = trim( $title, '-._' );

        // Если получилось пусто — сигнал, что транслитерации не хватило
        if ( $title === '' ) {
            return '';
        }

        if ( apply_filters( 'clearfy_transliteration_lower_title', true ) ) {
            $title = strtolower( $title );
        }

        return $title;

    }


    /**
     * Sanitize title
     *
     * @param $title
     *
     * @return mixed|string
     */
    public function sanitize_title( $title, $raw_title = '', $context = '' ) {

        if ( ! $title ) {
            return $title;
        }

        // #112 _wp_old_slug redirect bug
        if ( 'query' === $context ) {
            return $title;
        }

        // if WC attribute -- return title
        if ( $this->is_wc_attribute( $title ) ) {
            return $title;
        }

        // Предотвращаем пустые slug, если, например, иврит и мы не смогли транслитерировать
        $sanitized = $this->sanitize( $title );

        // Если не смогли сделать нормальный slug — НЕ ЛОМАЕМ.
        // Возвращаем то, что WordPress уже сделал (может быть иврит и т.п.)
        if ( $sanitized === '' ) {
            return $title;
        }

        $sanitized = str_replace('.', '-', $sanitized);
        $sanitized = preg_replace('/-{2,}/', '-', $sanitized);
        $sanitized = trim( $sanitized, '-' );

        // ещё один предохранитель на случай, если вдруг осталось только "-"
        if ( $sanitized === '' ) {
            return $title;
        }


        return $sanitized;
    }


    /**
     * Sanitize filename
     *
     * @param $title
     *
     * @return mixed|string
     */
    public function sanitize_file_name( $title ) {
        return $this->sanitize( $title );
    }


    /**
     * Check wc attribute
     */
    protected function is_wc_attribute( $title ) {

        // check wc activated
        if ( ! function_exists( 'wc_get_attribute_taxonomies' ) ) {
            return false;
        }

        $title = str_replace( 'pa_', '', $title );

        $attribute_taxonomies = wc_get_attribute_taxonomies();

        foreach ( $attribute_taxonomies as $attribute_taxonomy ) {
            if ( $attribute_taxonomy->attribute_name == $title ) {
                return true;
            }
        }

        return false;
    }



    public function sanitize_existing_slugs() {

        global $wpdb;

        $posts = $wpdb->get_results("SELECT ID, post_name FROM {$wpdb->posts} WHERE post_name REGEXP('[^A-Za-z0-9\-]+') AND post_status IN ('publish', 'future', 'private')");
        foreach ( (array) $posts as $post ) {
            $sanitized_name = $this->sanitize_title( urldecode( $post->post_name ) );

            // если вернулось то же самое или пусто — пропускаем
            if ( $post->post_name === $sanitized_name || empty( $sanitized_name ) ) {
                continue;
            }

            // сохраняем прошлый адрес
            add_post_meta($post->ID, '_wp_old_slug', $post->post_name, true);

            // сохраняем через wp_update_post, чтобы WP сам уникализировал slug
            wp_update_post([
                'ID'        => $post->ID,
                'post_name' => $sanitized_name,
            ]);
        }

        $terms = $wpdb->get_results("SELECT term_id, slug FROM {$wpdb->terms} WHERE slug REGEXP('[^A-Za-z0-9\-]+') ");
        foreach ( (array) $terms as $term ) {

            $sanitized_slug = $this->sanitize_title(urldecode($term->slug));

            // если вернулось то же самое или пусто — пропускаем
            if ( $term->slug === $sanitized_slug || empty( $sanitized_slug ) ) {
                continue;
            }

            // сохраняем прошлый адрес
            $history = (array) get_term_meta( $term->term_id, '_clearfy_old_slugs', true );
            $history[] = $term->slug;
            $history = array_values( array_unique( array_filter( $history ) ) );
            update_term_meta( $term->term_id, '_clearfy_old_slugs', $history );

            $wpdb->update($wpdb->terms, array( 'slug' => $sanitized_slug ), array( 'term_id' => $term->term_id ));

        }

    }



    /**
     * Set utf
     */
    protected function get_utf() {

        $table = [
            'Ä' => 'Ae',
            'ä' => 'ae',
            'Æ' => 'Ae',
            'æ' => 'ae',
            'À' => 'A',
            'à' => 'a',
            'Á' => 'A',
            'á' => 'a',
            'Â' => 'A',
            'â' => 'a',
            'Ã' => 'A',
            'ã' => 'a',
            'Å' => 'A',
            'å' => 'a',
            'ª' => 'a',
            'ₐ' => 'a',
            'ā' => 'a',
            'Ć' => 'C',
            'ć' => 'c',
            'Ç' => 'C',
            'ç' => 'c',
            'Ð' => 'D',
            'đ' => 'd',
            'È' => 'E',
            'è' => 'e',
            'É' => 'E',
            'é' => 'e',
            'Ê' => 'E',
            'ê' => 'e',
            'Ë' => 'E',
            'ë' => 'e',
            'ₑ' => 'e',
            'ƒ' => 'f',
            'ğ' => 'g',
            'Ğ' => 'G',
            'Ì' => 'I',
            'ì' => 'i',
            'Í' => 'I',
            'í' => 'i',
            'Î' => 'I',
            'î' => 'i',
            'Ï' => 'Ii',
            'ï' => 'ii',
            'ī' => 'i',
            'ı' => 'i',
            'I' => 'I',
            'Ñ' => 'N',
            'ñ' => 'n',
            'ⁿ' => 'n',
            'Ò' => 'O',
            'ò' => 'o',
            'Ó' => 'O',
            'ó' => 'o',
            'Ô' => 'O',
            'ô' => 'o',
            'Õ' => 'O',
            'õ' => 'o',
            'Ø' => 'O',
            'ø' => 'o',
            'ₒ' => 'o',
            'Ö' => 'Oe',
            'ö' => 'oe',
            'Œ' => 'Oe',
            'œ' => 'oe',
            'ß' => 'ss',
            'Š' => 'S',
            'š' => 's',
            'ş' => 's',
            'Ş' => 'S',
            'Ù' => 'U',
            'ù' => 'u',
            'Ú' => 'U',
            'ú' => 'u',
            'Û' => 'U',
            'û' => 'u',
            'Ü' => 'Ue',
            'ü' => 'ue',
            'Ý' => 'Y',
            'ý' => 'y',
            'ÿ' => 'y',
            'Ž' => 'Z',
            'ž' => 'z',
            '⁰' => '0',
            '¹' => '1',
            '²' => '2',
            '³' => '3',
            '⁴' => '4',
            '⁵' => '5',
            '⁶' => '6',
            '⁷' => '7',
            '⁸' => '8',
            '⁹' => '9' ,
            '₀' => '0',
            '₁' => '1',
            '₂' => '2',
            '₃' => '3',
            '₄' => '4',
            '₅' => '5',
            '₆' => '6',
            '₇' => '7',
            '₈' => '8',
            '₉' => '9',
            '±' => '-',
            '×' => 'x',
            '₊' => '-',
            '₌' => '=',
            '⁼' => '=',
            '⁻' => '-',
            '₋' => '-',
            '–' => '-',
            '—' => '-',
            '‑' => '-',
            '․' => '.',
            '‥' => '..',
            '…' => '...',
            '‧' => '.',
            ' ' => '-',
            ' ' => '-',
            'А' => 'A',
            'Б' => 'B',
            'В' => 'V',
            'Г' => 'G',
            'Д' => 'D',
            'Е' => 'E',
            'Ё' => 'YO',
            'Ё' => 'yo', // #114
            'Ж' => 'ZH',
            'З' => 'Z',
            'И' => 'I',
            'Й' => 'Y',
            'Й' => 'Y',  // #114
            'К' => 'K',
            'Л' => 'L',
            'М' => 'M',
            'Н' => 'N',
            'О' => 'O',
            'П' => 'P',
            'Р' => 'R',
            'С' => 'S',
            'Т' => 'T',
            'У' => 'U',
            'Ф' => 'F',
            'Х' => 'H',
            'Ц' => 'TS',
            'Ч' => 'CH',
            'Ш' => 'SH',
            'Щ' => 'SCH',
            'Ъ' => '',
            'Ы' => 'Y',
            'Ь' => '',
            'Э' => 'E',
            'Ю' => 'YU',
            'Я' => 'YA',
            'а' => 'a',
            'б' => 'b',
            'в' => 'v',
            'г' => 'g',
            'д' => 'd',
            'е' => 'e',
            'ё' => 'yo',
            'ё' => 'yo', // #114
            'ж' => 'zh',
            'з' => 'z',
            'и' => 'i',
            'й' => 'y',
            'й' => 'y',  // #114
            'к' => 'k',
            'л' => 'l',
            'м' => 'm',
            'н' => 'n',
            'о' => 'o',
            'п' => 'p',
            'р' => 'r',
            'с' => 's',
            'т' => 't',
            'у' => 'u',
            'ф' => 'f',
            'х' => 'h',
            'ц' => 'ts',
            'ч' => 'ch',
            'ш' => 'sh',
            'щ' => 'sch',
            'ъ' => '',
            'ы' => 'y',
            'ь' => '',
            'э' => 'e',
            'ю' => 'yu',
            'я' => 'ya',

            // ukrainian
            'і' => 'i',
            'І' => 'i',
            'ї' => 'i',
            'Ї' => 'i',
            'є' => 'e',
            'Є' => 'e',
            'ґ' => 'g',
            'Ґ' => 'g',

            // georgian
            'ა' => 'a',
            'ბ' => 'b',
            'გ' => 'g',
            'დ' => 'd',
            'ე' => 'e',
            'ვ' => 'v',
            'ზ' => 'z',
            'თ' => 'th',
            'ი' => 'i',
            'კ' => 'k',
            'ლ' => 'l',
            'მ' => 'm',
            'ნ' => 'n',
            'ო' => 'o',
            'პ' => 'p',
            'ჟ' => 'zh',
            'რ' => 'r',
            'ს' => 's',
            'ტ' => 't',
            'უ' => 'u',
            'ფ' => 'ph',
            'ქ' => 'q',
            'ღ' => 'gh',
            'ყ' => 'qh',
            'შ' => 'sh',
            'ჩ' => 'ch',
            'ც' => 'ts',
            'ძ' => 'dz',
            'წ' => 'ts',
            'ჭ' => 'tch',
            'ხ' => 'kh',
            'ჯ' => 'j',
            'ჰ' => 'h',

            // tatar
            'ә' => 'e',
            'Ә' => 'E',
            'ү' => 'u',
            'Ү' => 'U',
            'ң' => 'n',
            'Ң' => 'N',
            'җ' => 'zh',
            'Җ' => 'ZH',
            'ө' => 'o',
            'Ө' => 'O',
            'һ' => 'h',
            'Һ' => 'H',

            // armenian
            'և' => 'ev',
            'ու' => 'u',
            'Ա' => 'A',
            'Բ' => 'B',
            'Գ' => 'G',
            'Դ' => 'D',
            'Ե' => 'Ye',
            'Զ' => 'Z',
            'Է' => 'E',
            'Ը' => 'Eh',
            'Թ' => 'Th',
            'Ժ' => 'Zh',
            'Ի' => 'I',
            'Լ' => 'L',
            'Խ' => 'X',
            'Ծ' => 'Tc',
            'Կ' => 'K',
            'Հ' => 'H',
            'Ձ' => 'Dz',
            'Ղ' => 'Gh',
            'Ճ' => 'Tch',
            'Մ' => 'M',
            'Յ' => 'Y',
            'Ն' => 'N',
            'Շ' => 'Sh',
            'Ո' => 'Vo',
            'Չ' => 'Ch',
            'Պ' => 'P',
            'Ջ' => 'J',
            'Ռ' => 'R',
            'Ս' => 'S',
            'Վ' => 'V',
            'Տ' => 'T',
            'Ր' => 'R',
            'Ց' => 'C',
            'Փ' => 'Ph',
            'Ք' => 'Kh',
            'Օ' => 'O',
            'Ֆ' => 'F',
            'ա' => 'a',
            'բ' => 'b',
            'գ' => 'g',
            'դ' => 'd',
            'ե' => 'e',
            'զ' => 'z',
            'է' => 'e',
            'ը' => 'eh',
            'թ' => 'th',
            'ժ' => 'zh',
            'ի' => 'i',
            'լ' => 'l',
            'խ' => 'x',
            'ծ' => 'tc',
            'կ' => 'k',
            'հ' => 'h',
            'ձ' => 'dz',
            'ղ' => 'gh',
            'ճ' => 'tch',
            'մ' => 'm',
            'յ' => 'y',
            'ն' => 'n',
            'շ' => 'sh',
            'ո' => 'o',
            'չ' => 'ch',
            'պ' => 'p',
            'ջ' => 'j',
            'ռ' => 'r',
            'ս' => 's',
            'վ' => 'v',
            'տ' => 't',
            'ր' => 'r',
            'ց' => 'c',
            'փ' => 'ph',
            'ք' => 'kh',
            'օ' => 'o',
            'ֆ' => 'f',

            // serbian
            "Ђ" => "Dj",
            "Ј" => "J",
            "Љ" => "LJ",
            "Њ" => "NJ",
            "Ћ" => "C",
            "Џ" => "Dz",
            "ђ" => "dj",
            "ј" => "j",
            "љ" => "lj",
            "њ" => "nj",
            "ћ" => "c",
            "џ" => "dz",

            // kazakh
            'ғ' => 'g',
            'Ғ' => 'G',
            'қ' => 'k',
            'Қ' => 'K',
            'ұ' => 'u',
            'Ұ' => 'U',

            // other
            'ў' => 'l',
            'Ў' => 'L',
            'ѓ' => 'g',
            'Ѓ' => 'G',
        ];

        $locale = get_locale();

        // ukrainian
        if ( $locale == 'uk' || $locale == 'uk_ua' || $locale == 'uk_UA' ) {
            $table = array_merge( $table, [
                'и' => 'y',
                'И' => 'Y',
                'г' => 'h',
                'Г' => 'H',
            ] );
        }

        // bulgarian
        if ( $locale == 'bg' || $locale == 'bg_bg' || $locale == 'bg_BG' ) {
            $table = array_merge( $table, [
                'ъ' => 'a',
                'Ъ' => 'A',
                'щ' => 'sht',
                'Щ' => 'SHT',
            ] );
        }

        // serbian
        if ( $locale == 'sr_RS' ) {
            $table = array_merge( $table, [
                "ж" => "z",
                "Ж" => "Z",
                "ч" => "c",
                "Ч" => "C",

                "Ња" => "Nja",
                "Ње" => "Nje",
                "Њи" => "Nji",
                "Њо" => "Njo",
                "Њу" => "Nju",
                "Ља" => "Lja",
                "Ље" => "Lje",
                "Љи" => "Lji",
                "Љо" => "Ljo",
                "Љу" => "Lju",
                "Џа" => "Dza",
                "Џе" => "Dze",
                "Џи" => "Dzi",
                "Џо" => "Dzo",
                "Џу" => "Dzu",
            ] );
        }

        $table = apply_filters( 'clearfy_transliteration_table', $table );

        return $table;

    }

}
