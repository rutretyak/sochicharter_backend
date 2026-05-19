<?php

namespace WPShop\ClearfyPro;

class RobotsTxt {

    /**
     * @var \Clearfy_Plugin_Options
     */
    protected $plugin_options;

    public function __construct( \Clearfy_Plugin_Options $plugin_options ) {
        $this->plugin_options = $plugin_options;
    }

    public function init() {
        add_action( 'admin_notices', [ $this, 'admin_notice_sitemap_domain_mismatch' ] );
    }

    public function render( $output = '' ) {
        if ( isset( $this->plugin_options->options['robots_txt_text'] ) && ! empty( $this->plugin_options->options['robots_txt_text'] ) ) {
            return $this->plugin_options->options['robots_txt_text'];
        }

        $site_url = get_home_url();

        $output  = 'User-agent: *' . PHP_EOL;
        $output .= 'Disallow: /wp-admin' . PHP_EOL;
        $output .= 'Disallow: /wp-includes' . PHP_EOL;
        $output .= 'Disallow: /wp-content/plugins' . PHP_EOL;
        $output .= 'Disallow: /wp-content/cache' . PHP_EOL;
        $output .= 'Disallow: /wp-json/' . PHP_EOL;
        $output .= 'Disallow: /xmlrpc.php' . PHP_EOL;
        $output .= 'Disallow: /readme.html' . PHP_EOL;
        $output .= 'Disallow: /*?' . PHP_EOL;
        $output .= 'Disallow: /?s=' . PHP_EOL;
        $output .= 'Disallow: /?customize_changeset_uuid=' . PHP_EOL;
        $output .= 'Allow: /wp-includes/*.css' . PHP_EOL;
        $output .= 'Allow: /wp-includes/*.js' . PHP_EOL;
        $output .= 'Allow: /wp-content/plugins/*.css' . PHP_EOL;
        $output .= 'Allow: /wp-content/plugins/*.js' . PHP_EOL;
        $output .= 'Allow: /*.css' . PHP_EOL;
        $output .= 'Allow: /*.js' . PHP_EOL . PHP_EOL;

        if ( function_exists( 'get_headers' ) ) {
            $get_headers = @get_headers( $site_url . '/sitemap.xml', 1 );

            if ( is_array( $get_headers ) && isset( $get_headers[0] ) && preg_match( '#200 OK#i', $get_headers[0] ) ) {
                $output .= 'Sitemap: ' . $site_url . '/sitemap.xml' . PHP_EOL;
            } elseif ( is_array( $get_headers ) && isset( $get_headers['Location'] ) && ! empty( $get_headers['Location'] ) ) {
                $output .= 'Sitemap: ' . $get_headers['Location'] . PHP_EOL;
            }
        }

        return $output;
    }

    public function has_sitemap_domain_mismatch( $robots_text = null ) {
        if ( $robots_text === null ) {
            $robots_text = isset( $this->plugin_options->options['robots_txt_text'] ) ? (string) $this->plugin_options->options['robots_txt_text'] : '';
        }

        if ( $robots_text === '' ) {
            return false;
        }

        if ( stripos( $robots_text, 'Sitemap:' ) === false ) {
            return false;
        }

        $site_host = wp_parse_url( home_url(), PHP_URL_HOST );
        $site_host = $this->normalize_host( $site_host );
        if ( empty( $site_host ) ) {
            return false;
        }

        preg_match_all( '/^\s*Sitemap:\s*(\S+)\s*$/mi', $robots_text, $matches );
        if ( empty( $matches[1] ) || ! is_array( $matches[1] ) ) {
            return false;
        }

        foreach ( $matches[1] as $sitemap_url ) {
            $sitemap_host = wp_parse_url( trim( (string) $sitemap_url ), PHP_URL_HOST );
            $sitemap_host = $this->normalize_host( $sitemap_host );
            if ( empty( $sitemap_host ) ) {
                continue;
            }

            if ( $sitemap_host !== $site_host ) {
                return true;
            }
        }

        return false;
    }

    private function normalize_host( $host ) {
        $host = strtolower( trim( (string) $host ) );
        if ( strpos( $host, 'www.' ) === 0 ) {
            $host = substr( $host, 4 );
        }

        return $host;
    }

    public function admin_notice_sitemap_domain_mismatch() {
        if ( ! is_admin() ) {
            return;
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        if ( ! $this->plugin_options->get_option( 'right_robots_txt' ) ) {
            return;
        }

        if ( ! $this->has_sitemap_domain_mismatch() ) {
            return;
        }

        echo '<div class="notice notice-warning"><p>' .
            esc_html__( 'The domain in robots.txt Sitemap does not match the current site domain. Check and update robots.txt settings.', $this->plugin_options->text_domain ) .
            '</p></div>';
    }
}
