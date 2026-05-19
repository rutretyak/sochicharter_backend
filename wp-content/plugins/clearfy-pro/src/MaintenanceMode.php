<?php

namespace WPShop\ClearfyPro;

class MaintenanceMode {

    /**
     * @var \Clearfy_Plugin_Options
     */
    protected $plugin_options;

    public function __construct( \Clearfy_Plugin_Options $plugin_options ) {
        $this->plugin_options = $plugin_options;
    }

    public function init() {
        add_action( 'template_redirect', [ $this, 'render' ], 0 );
    }

    public function render() {
        if ( is_admin() ) {
            return;
        }

        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            return;
        }

        if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
            return;
        }

        if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
            return;
        }

        if ( ! $this->plugin_options->get_option( 'maintenance_mode_enable' ) ) {
            return;
        }

        /**
         * [en] Capability that can bypass maintenance mode on the frontend.
         * [ru] Способность, при которой пользователь может обходить режим реконструкции на фронтенде.
         */
        $view_capability = apply_filters( 'clearfy/maintenance_mode/view_capability', 'edit_others_posts' );

        if ( is_user_logged_in() && current_user_can( $view_capability ) ) {
            return;
        }

        status_header( 503 );
        nocache_headers();

        $allow_indexing = ( 'on' === $this->plugin_options->get_option( 'maintenance_mode_allow_indexing' ) );
        $robots_content = $allow_indexing ? 'index,follow' : 'noindex,nofollow';

        if ( ! $allow_indexing ) {
            header( 'X-Robots-Tag: noindex, nofollow', true );
        }

        $content = $this->plugin_options->get_option( 'maintenance_mode_html' );
        if ( empty( $content ) ) {
            $content = __( 'Site under maintenance', $this->plugin_options->text_domain );
        }

        /**
         * [en] Maintenance page title.
         * [ru] Заголовок страницы режима реконструкции.
         */
        $page_title = apply_filters( 'clearfy/maintenance_mode/title', get_bloginfo( 'name' ) );

        echo '<!DOCTYPE html><html ' . get_language_attributes() . '><head><meta charset="' . esc_attr( get_bloginfo( 'charset' ) ) . '">';
        echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
        echo '<meta name="robots" content="' . esc_attr( $robots_content ) . '">';
        echo '<title>' . esc_html( $page_title ) . '</title></head><body>';
        echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo '</body></html>';
        exit;
    }
}

