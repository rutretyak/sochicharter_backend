<?php

use WPShop\ClearfyPro\Logger;

/**
 * Class Clearfy_Indexnow
 *
 * @package     Wpshop
 */

class Clearfy_Indexnow {

    const OPTION_INDEXNOW_KEY = 'indexnow_key';
    const OPTION_INDEXNOW_POST_TYPES = 'indexnow_post_types';

    protected $plugin_options;

    protected $log_option = 'clearfy_indexnow_log';
    public $log_limit;


    public $messages = [];


    protected $logger;

    public function __construct( Clearfy_Plugin_Options $plugin_options ) {
        $this->plugin_options = $plugin_options;

        $this->messages = [
            200 => 'OK',
            202 => __( 'Accepted', $this->plugin_options->text_domain ),
            403 => __( 'Invalid key', $this->plugin_options->text_domain ),
            405 => __( 'Method not allowed', $this->plugin_options->text_domain ),
            422 => __( 'Invalid request', $this->plugin_options->text_domain ),
            429 => __( 'Too Many Requests', $this->plugin_options->text_domain ),
        ];

        $this->log_limit = apply_filters( 'clearfy_indexnow_log_limit', 100 );

        $this->logger = new Logger();
    }

    public function init() {
        $this->is_key_file();
        $this->check_key();
    }

    public function is_key_file() {
        add_action( 'pre_get_posts', function ( $query ) {
            if ( ! is_admin() && $query->is_main_query() ) {
                global $wp;
                if ( preg_match( '/(.+?)\.txt$/ui', $wp->request, $match ) ) {
                    if ( $this->plugin_options->options[ self::OPTION_INDEXNOW_KEY ] == $match[1] ) {
                        echo $this->plugin_options->options[ self::OPTION_INDEXNOW_KEY ];
                        die();
                    }
                }
            }
        } );
    }

    public function get_log() {
        $log = get_option( $this->log_option );
        if ( empty( $log ) ) {
            $log = [];
        }

        return $log;
    }

    public function add_log( $post_id, $code, $message = '' ) {
        $log = $this->get_log();

        $log[] = [
            'post_id' => $post_id,
            'status'  => $code,
            'message' => $message,
            'time'    => current_time('timestamp'),
        ];

        // limit
        $count = count( $log );
        if ( $count > $this->log_limit ) {
            $log = array_slice( $log, ( $count - $this->log_limit ) );
        }

        update_option( $this->log_option, $log, false );
    }


    public function send() {
        $post_types = $this->get_selected_post_types();
        $post_types = (array) apply_filters( 'clearfy_indexnow_post_types', $post_types );
        $post_types = array_unique( array_filter( array_map( 'sanitize_key', $post_types ) ) );

        foreach ( $post_types as $post_type ) {
            add_action( "save_post_{$post_type}", function ( $post_ID, $post, $update ) {
                if ( wp_is_post_revision( $post ) ) {
                    return;
                }

                if ( wp_is_post_autosave( $post ) ) {
                    return;
                }

                // если пост не доступен к просмотру на сайте -- выходим
                if ( function_exists( 'is_post_publicly_viewable' ) && ! is_post_publicly_viewable( $post ) ) {
                    return;
                }

                if ( $post->post_status != 'publish' ) {
                    return;
                }


                // задержка, чтобы не слать часто, 30 сек по умолчанию, можно поменять через хук clearfy_indexnow_delay
                $check_delay = get_transient( 'clearfy_indexnow_send_' . $post_ID );
                if ( ! empty( $check_delay ) ) {
                    return;
                }


                $url    = get_permalink( $post );
                $result = $this->send_yandex( $url );
                set_transient( 'clearfy_indexnow_send_' . $post_ID, 1, apply_filters( 'clearfy_indexnow_delay', 30 ) );

                if ( is_wp_error( $result ) ) {
                    foreach ( $result->errors as $code => $message ) {
                        $this->add_log( $post_ID, $code, $message );
                    }
                } else {
                    $this->add_log( $post_ID, 200 );
                }

            }, 10, 3 );
        }

    }

    public function get_available_post_types() {
        $post_types = get_post_types(
            [
                'public'  => true,
                'show_ui' => true,
            ],
            'objects'
        );

        $excluded_post_types = [
            'attachment',
            'revision',
            'nav_menu_item',
            'custom_css',
            'customize_changeset',
            'oembed_cache',
            'user_request',
            'wp_block',
            'wp_template',
            'wp_template_part',
            'wp_navigation',
            'wp_global_styles',
            'wp_font_family',
            'wp_font_face',
        ];

        foreach ( $excluded_post_types as $excluded_post_type ) {
            if ( isset( $post_types[ $excluded_post_type ] ) ) {
                unset( $post_types[ $excluded_post_type ] );
            }
        }

        return $post_types;
    }

    public function get_selected_post_types() {
        $selected_post_types = isset( $this->plugin_options->options[ self::OPTION_INDEXNOW_POST_TYPES ] ) ? (array) $this->plugin_options->options[ self::OPTION_INDEXNOW_POST_TYPES ] : [];
        $selected_post_types = array_unique( array_filter( array_map( 'sanitize_key', $selected_post_types ) ) );

        if ( empty( $selected_post_types ) ) {
            $selected_post_types = [ 'post', 'page' ];
        }

        $available_post_types = array_keys( $this->get_available_post_types() );

        return array_values( array_intersect( $selected_post_types, $available_post_types ) );
    }


    /**
     * @param       $url
     * @param array $args
     *
     * @return bool|WP_Error
     */
    public function send_yandex( $url, $args = [] ) {
        $body = [
            'url' => $url,
            'key' => $this->plugin_options->options[ self::OPTION_INDEXNOW_KEY ],
        ];


        $this->logger->log('IndexNow: Send request to Yandex', $body);

        $args = wp_parse_args( $args, [
            'body' => $body,
            'sslverify' => false,
        ] );

        $response = wp_remote_get( 'https://yandex.com/indexnow', $args );

        $this->logger->log( 'IndexNow: Response from Yandex', $response );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $code = (int) wp_remote_retrieve_response_code( $response );
        if ( 200 !== $code ) {
            $result = wp_remote_retrieve_body( $response );
            $result = json_decode( $result, true );

            if ( json_last_error() != JSON_ERROR_NONE ) {
                return new WP_Error( 'json_error', json_last_error_msg() );
            }

            return new WP_Error( $code, ( ! empty( $result['message'] ) ) ? $result['message'] : $this->retrieve_response_header( $response ) );
        }

        return $response;
    }


    protected function retrieve_response_header( $response ) {
        if ( isset( $response['http_response'] ) ) {
            $r = $response['http_response'];
            if ( $r instanceof WP_HTTP_Requests_Response ) {
                $raw   = $r->get_response_object()->raw;
                $parts = explode( "\n", $raw );

                return current( $parts );
            }
        }

        return null;
    }


    public function get_the_title( $post_id, $limit = 25 ) {
        $title = get_the_title( $post_id );

        $len = ( function_exists( 'mb_strlen' ) ) ? mb_strlen( $title ) : strlen( $title );

        if ( $len > $limit ) {
            $title = ( function_exists( 'mb_substr' ) ) ? mb_substr( $title, 0, $limit ) : substr( $title, 0, $limit );
            $title .= '...';
        }

        return $title;
    }

    public function get_message_by_code( $code ) {
        if ( isset( $this->messages[ $code ] ) ) {
            return $this->messages[ $code ];
        }

        return '';
    }

    public function check_key() {
        if ( ! isset( $this->plugin_options->options[ self::OPTION_INDEXNOW_KEY ] ) || empty( $this->plugin_options->options[ self::OPTION_INDEXNOW_KEY ] ) ) {
            $clearfy_options = get_option( $this->plugin_options->option_name, [] );
            if ( empty( $clearfy_options[ self::OPTION_INDEXNOW_KEY ] ) ) {
                $clearfy_options[ self::OPTION_INDEXNOW_KEY ] = $this->generate_key();
            }
            update_option( $this->plugin_options->option_name, $clearfy_options );
        }
    }

    public function generate_key() {
        $length = mt_rand( 32, 120 );

        $characters        = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-';
        $characters_length = strlen( $characters );
        $random_string     = '';
        for ( $i = 0; $i < $length; $i ++ ) {
            $random_string .= $characters[ rand( 0, $characters_length - 1 ) ];
        }

        return $random_string;
    }

}
