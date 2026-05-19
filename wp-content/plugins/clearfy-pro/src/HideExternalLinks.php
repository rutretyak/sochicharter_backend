<?php

namespace WPShop\ClearfyPro;

class HideExternalLinks {

    /**
     * @var \Clearfy_Plugin_Options
     */
    protected $plugin_options;

    public function __construct( \Clearfy_Plugin_Options $plugin_options ) {
        $this->plugin_options = $plugin_options;
    }

    public function init() {
        if ( $this->is_content_enabled() ) {
            add_filter( 'the_content', [ $this, 'content_convert_links_pseudo' ], 99 );
        }

        if ( $this->plugin_options->get_option( 'comment_text_convert_links_pseudo' ) ) {
            add_filter( 'comment_text', [ $this, 'comment_text_convert_links_pseudo' ], 99 );
        }

        if ( $this->plugin_options->get_option( 'pseudo_comment_author_link' ) ) {
            add_filter( 'get_comment_author_link', [ $this, 'pseudo_comment_author_link' ], 100, 3 );
        }

        if ( $this->is_any_hide_mode_enabled() ) {
            add_action( 'wp_head', [ $this, 'add_pseudo_link_style' ] );
            add_action( 'wp_footer', [ $this, 'add_pseudo_link_scripts' ] );
        }
    }

    public function comment_text_convert_links_pseudo( $comment_text ) {
        if ( ! $this->is_context_enabled( 'comment' ) ) {
            return $comment_text;
        }

        return $this->convert_links_pseudo( $comment_text, 'comment' );
    }

    public function content_convert_links_pseudo( $content ) {
        if ( ! is_singular() ) {
            return $content;
        }

        if ( ! $this->is_context_enabled( 'content' ) ) {
            return $content;
        }

        if ( ! $this->is_content_post_type_allowed() ) {
            return $content;
        }

        if ( $this->is_post_excluded() ) {
            return $content;
        }

        return $this->convert_links_pseudo( $content, 'content' );
    }

    public function convert_links_pseudo( $text, $context = 'comment' ) {
        $class = $this->get_pseudo_link_class();

        return preg_replace_callback( '/<a\b[^>]*>.*?<\/a>/is', function ( $matches ) use ( $context, $class ) {
            $link_html = $matches[0];
            if ( ! preg_match( '/href\s*=\s*([\'"])(.*?)\1/i', $link_html, $href_match ) ) {
                return $link_html;
            }

            $href = trim( html_entity_decode( $href_match[2], ENT_QUOTES, get_bloginfo( 'charset' ) ) );
            if ( ! $this->should_convert_url( $href, $context ) ) {
                return $link_html;
            }

            $target = '';
            if ( preg_match( '/target\s*=\s*([\'"])(.*?)\1/i', $link_html, $target_match ) ) {
                $target = trim( $target_match[2] );
            }

            $content = preg_replace( '/^<a\b[^>]*>|<\/a>$/i', '', $link_html );
            $encoded_url = base64_encode( $href );
            $data_target = $target !== '' ? ' data-target="' . esc_attr( $target ) . '"' : '';

            return '<span class="' . esc_attr( $class ) . '" data-uri="' . esc_attr( $encoded_url ) . '"' . $data_target . '>' . $content . '</span>';
        }, $text );
    }

    public function pseudo_comment_author_link( $return, $author, $comment_ID ) {
        if ( ! $this->is_context_enabled( 'comment_author' ) ) {
            return $return;
        }

        $url = get_comment_author_url( $comment_ID );
        $author_name = get_comment_author( $comment_ID );

        if ( empty( $url ) || 'http://' === $url || ! $this->should_convert_url( $url, 'comment_author' ) ) {
            return $author_name;
        }

        $encoded_url = base64_encode( $url );
        $class = $this->get_pseudo_link_class();

        return '<span class="' . esc_attr( $class ) . '" data-uri="' . esc_attr( $encoded_url ) . '">' . esc_html( $author_name ) . '</span>';
    }

    public function add_pseudo_link_style() {
        $class = $this->get_pseudo_link_class();
        $color = $this->get_color_option( 'pseudo_links_color', '#0058cf' );
        $hover_color = $this->get_color_option( 'pseudo_links_hover_color', '#2900cf' );
        $underline = $this->plugin_options->get_option( 'pseudo_links_underline' ) ? 'underline' : 'none';
        $hover_underline = $this->plugin_options->get_option( 'pseudo_links_hover_underline' ) ? 'underline' : 'none';

        $styles  = '.' . $class . '{color:' . $color . ';cursor:pointer;text-decoration:' . $underline . ';}';
        $styles .= '.' . $class . ':hover{color:' . $hover_color . ';text-decoration:' . $hover_underline . ';}';

        echo '<style>' . apply_filters( 'clearfy_pseudo_links_style', $styles ) . '</style>';
    }

    public function add_pseudo_link_scripts() {
        $class = $this->get_pseudo_link_class();
        $class_js = wp_json_encode( $class );
        echo '<script>(function(){';
        echo 'var cls=' . $class_js . ';';
        echo 'function decodeUri(value){if(!value){return"";}if(/^https?:\\/\\//i.test(value)){return value;}try{return atob(value);}catch(e){return value;}}';
        echo 'var links=document.querySelectorAll("."+cls);';
        echo 'for(var i=0;i<links.length;i++){links[i].addEventListener("click",function(e){var el=e.currentTarget;var url=decodeUri(el.getAttribute("data-uri"));if(!url){return;}var target=el.getAttribute("data-target");if(target==="_blank"){window.open(url,"_blank");return;}window.location.href=url;});}';
        echo '})();</script>';
    }

    protected function is_any_hide_mode_enabled() {
        return (bool) (
            $this->is_content_enabled()
            || $this->plugin_options->get_option( 'comment_text_convert_links_pseudo' )
            || $this->plugin_options->get_option( 'pseudo_comment_author_link' )
        );
    }

    protected function is_content_enabled() {
        return (bool) $this->plugin_options->get_option( 'hide_external_links_content' );
    }

    protected function is_context_enabled( $context ) {
        $enabled = true;
        if ( 'content' === $context ) {
            $enabled = $this->is_content_enabled();
        } elseif ( 'comment' === $context ) {
            $enabled = (bool) $this->plugin_options->get_option( 'comment_text_convert_links_pseudo' );
        } elseif ( 'comment_author' === $context ) {
            $enabled = (bool) $this->plugin_options->get_option( 'pseudo_comment_author_link' );
        }

        return (bool) apply_filters( 'clearfy/external_links/is_enabled', $enabled, $context );
    }

    protected function should_convert_url( $url, $context ) {
        if ( ! preg_match( '#^https?://#i', $url ) ) {
            return false;
        }

        $home = home_url();
        if ( strpos( $url, $home ) === 0 ) {
            return false;
        }

        foreach ( $this->get_excluded_url_patterns() as $pattern ) {
            if ( strpos( $url, $pattern ) !== false ) {
                return false;
            }
        }

        return (bool) apply_filters( 'clearfy/external_links/should_convert_url', true, $url, $context );
    }

    protected function is_content_post_type_allowed() {
        $post_types = $this->get_content_post_types();
        if ( empty( $post_types ) ) {
            return false;
        }

        return is_singular( $post_types );
    }

    protected function get_content_post_types() {
        $post_types = $this->plugin_options->get_option( 'hide_external_links_post_types' );
        if ( ! is_array( $post_types ) || empty( $post_types ) ) {
            $post_types = [ 'post' ];
        }

        return (array) apply_filters( 'clearfy/external_links/post_types', $post_types );
    }

    protected function is_post_excluded() {
        $post_id = get_queried_object_id();
        if ( ! $post_id ) {
            return false;
        }

        $excluded = $this->get_excluded_post_ids();
        if ( empty( $excluded ) ) {
            return false;
        }

        return in_array( (int) $post_id, $excluded, true );
    }

    protected function get_excluded_post_ids() {
        $ids_raw = (string) $this->plugin_options->get_option( 'hide_external_links_excluded_post_ids' );
        $parts = preg_split( '/[,\s]+/', $ids_raw );
        $ids = [];
        if ( is_array( $parts ) ) {
            foreach ( $parts as $id ) {
                $id = absint( $id );
                if ( $id > 0 ) {
                    $ids[] = $id;
                }
            }
        }

        $ids = array_values( array_unique( $ids ) );

        return (array) apply_filters( 'clearfy/external_links/excluded_post_ids', $ids );
    }

    protected function get_excluded_url_patterns() {
        $raw = (string) $this->plugin_options->get_option( 'hide_external_links_excluded_urls' );
        $items = preg_split( '/\r\n|\r|\n/', $raw );
        $patterns = [];
        if ( is_array( $items ) ) {
            foreach ( $items as $item ) {
                $item = trim( $item );
                if ( $item !== '' ) {
                    $patterns[] = $item;
                }
            }
        }

        return (array) apply_filters( 'clearfy/external_links/excluded_patterns', $patterns );
    }

    protected function get_color_option( $option_name, $default ) {
        $value = (string) $this->plugin_options->get_option( $option_name, $default );
        if ( ! preg_match( '/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/', $value ) ) {
            return $default;
        }

        return $value;
    }

    protected function get_pseudo_link_class() {
        $class = (string) $this->plugin_options->get_option( 'pseudo_links_class', 'pseudo-clearfy-link' );
        $class = sanitize_html_class( $class );
        if ( $class === '' ) {
            $class = 'pseudo-clearfy-link';
        }

        return $class;
    }
}
