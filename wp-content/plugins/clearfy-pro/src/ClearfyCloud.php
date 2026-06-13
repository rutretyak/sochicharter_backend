<?php

namespace WPShop\ClearfyPro;

class ClearfyCloud {

    const OPTION_NAME_DATA = 'clearfy_cloud_data';
    const OPTION_LAST_FEED_SYNC = 'clearfy_cloud_feed_last_sync';
    const OPTION_LAST_FEED_STATUS = 'clearfy_cloud_feed_last_status';
    const OPTION_TABLES_VERSION = 'clearfy_cloud_tables_version';
    const TABLES_VERSION = '2';
    const LIMIT_SEND = 5;
    const API_URL = 'https://clearfypro.com/api-clearfy/';
    const CRON_HOOK_FEED_SYNC = 'clearfy_cloud_cron_feed_sync';
    const FEED_TTL_HOURS = 72;
    const CAPTCHA_COOKIE = 'clearfy_cloud_challenge';
    const CAPTCHA_COOKIE_TTL = DAY_IN_SECONDS;

    public function init() {

        // отключаем временно защиту вообще из-за ложных срабатываний у клиентов
        return;

        if ( ! clearfy_get_option( 'cloud_protection' ) ) {
            return;
        }

        $this->maybe_create_tables();

        add_filter( 'cron_schedules', [ $this, 'register_cron_schedules' ] );
        add_action( self::CRON_HOOK_FEED_SYNC, [ $this, 'sync_feed' ] );

        if ( ! wp_next_scheduled( self::CRON_HOOK_FEED_SYNC ) ) {
            wp_schedule_event( time() + 60, 'clearfy_six_hours', self::CRON_HOOK_FEED_SYNC );
        }

        // Применяем защиту максимально рано в жизненном цикле WP.
        add_action( 'init', [ $this, 'handle_request_protection' ], 0 );

        add_action( 'template_redirect', [ $this, 'save_404' ] );
        add_action( 'wp_ajax_nopriv_clearfy_cloud_update_data', [ $this, 'clearfy_cloud_update_data' ] );
        add_action( 'wp_ajax_clearfy_cloud_update_data', [ $this, 'clearfy_cloud_update_data' ] );
        add_action( 'wp_footer', [ $this, 'catch_404_footer' ] );

        if ( is_admin() ) {
            $this->send_data_to_api();
            $this->maybe_sync_feed_if_due();
        }
    }

    public function register_cron_schedules( $schedules ) {
        if ( ! isset( $schedules['clearfy_six_hours'] ) ) {
            $schedules['clearfy_six_hours'] = [
                'interval' => 6 * HOUR_IN_SECONDS,
                'display'  => 'Every 6 hours (Clearfy Cloud)',
            ];
        }

        return $schedules;
    }

    public function get_cloud_data() {
        $data = get_option( self::OPTION_NAME_DATA, [] );

        return is_array( $data ) ? $data : [];
    }

    public function save_404() {
        if ( ! is_404() ) {
            return;
        }

        $url = $this->get_relative_path();
        $whitelist = $this->get_whitelist();

        if ( $this->is_whitelist_extentions( $url ) ) {
            return;
        }

        if ( ! in_array( $url, $whitelist, true ) ) {
            $this->save_data_to_send( $url );
        }
    }

    public function catch_404_footer() {
        if ( ! is_404() ) {
            return;
        }

        $url = $this->get_relative_path();
        $nonce = wp_create_nonce( 'clearfy_ajax_nonce' );
        $ajax_url = admin_url( 'admin-ajax.php' );
        $data = [
            'url'      => $url,
            'nonce'    => $nonce,
            'ajax_url' => $ajax_url,
        ];

        $json_data = wp_json_encode( $data );

        echo "<script>(function(){var d={$json_data};var f=new FormData();f.append('action','clearfy_cloud_update_data');f.append('url',d.url);f.append('nonce',d.nonce);fetch(d.ajax_url,{method:'POST',credentials:'same-origin',body:f}).then(function(r){return r.json();}).catch(function(){});})();</script>";
    }

    public function clearfy_cloud_update_data() {
        check_ajax_referer( 'clearfy_ajax_nonce', 'nonce' );

        $url = isset( $_POST['url'] ) ? sanitize_text_field( wp_unslash( $_POST['url'] ) ) : '';
        if ( empty( $url ) ) {
            wp_send_json_error( 'URL is empty' );
        }

        $data = $this->get_cloud_data();
        foreach ( $data as $key => $item ) {
            if ( isset( $item['url'] ) && $item['url'] === $url ) {
                unset( $data[ $key ] );
                break;
            }
        }

        update_option( self::OPTION_NAME_DATA, array_values( $data ) );
        $this->send_data_to_api();

        wp_send_json_success();
    }

    protected function save_data_to_send( $url ) {
        $data = $this->get_cloud_data();
        $data[] = [
            'host'       => isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '',
            'url'        => $url,
            'ip_address' => $this->get_ip(),
            'user_agent' => isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '',
            'timestamp'  => current_time( 'mysql' ),
        ];

        $data = array_slice( $data, -200 );
        update_option( self::OPTION_NAME_DATA, $data );
    }

    public function send_data_to_api() {
        $data = $this->get_cloud_data();
        if ( empty( $data ) || count( $data ) < self::LIMIT_SEND ) {
            return;
        }

        $license_token = $this->get_license_token_or_key();
        $post_data = [
            'host'  => isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : parse_url( home_url(), PHP_URL_HOST ),
            'data'  => $data,
            'token' => $license_token ?: '',
        ];

        wp_remote_post(
            self::API_URL,
            [
                'body'    => wp_json_encode( $post_data ),
                'timeout' => 10,
                'headers' => [ 'Content-Type' => 'application/json' ],
            ]
        );

        update_option( self::OPTION_NAME_DATA, [] );
    }

    public function sync_feed() {
        global $wpdb;

        $token_or_key = $this->get_license_token_or_key();
        $feed_url = add_query_arg(
            [
                'action' => 'feed',
                'token'  => $token_or_key,
            ],
            self::API_URL
        );

        $response = wp_remote_get(
            $feed_url,
            [
                'timeout' => 10,
                'headers' => [ 'Accept' => 'application/json' ],
            ]
        );

        if ( is_wp_error( $response ) ) {
            update_option( self::OPTION_LAST_FEED_STATUS, 'network_error' );
            return;
        }

        $body = wp_remote_retrieve_body( $response );
        $json = json_decode( $body, true );

        if ( ! is_array( $json ) || empty( $json['success'] ) || ! isset( $json['ips'] ) || ! is_array( $json['ips'] ) ) {
            update_option( self::OPTION_LAST_FEED_STATUS, 'invalid_response' );
            return;
        }

        $table = $this->get_table_cloud_ips();
        $now_gmt = gmdate( 'Y-m-d H:i:s' );
        $expires_at = gmdate( 'Y-m-d H:i:s', time() + ( self::FEED_TTL_HOURS * HOUR_IN_SECONDS ) );

        foreach ( $json['ips'] as $item ) {
            $ip = isset( $item['ip_address'] ) ? trim( (string) $item['ip_address'] ) : '';
            $action = isset( $item['recommended_action'] ) ? strtolower( trim( (string) $item['recommended_action'] ) ) : 'block';

            if ( ! filter_var( $ip, FILTER_VALIDATE_IP ) ) {
                continue;
            }
            if ( ! in_array( $action, [ 'block', 'captcha' ], true ) ) {
                $action = 'block';
            }

            $wpdb->replace(
                $table,
                [
                    'ip_address' => $ip,
                    'action'     => $action,
                    'source'     => 'cloud',
                    'updated_at' => $now_gmt,
                    'expires_at' => $expires_at,
                ],
                [ '%s', '%s', '%s', '%s', '%s' ]
            );
        }

        $this->purge_expired_cloud_ips();
        update_option( self::OPTION_LAST_FEED_SYNC, time() );
        update_option( self::OPTION_LAST_FEED_STATUS, 'success' );
    }

    protected function maybe_sync_feed_if_due() {
        $last_sync = (int) get_option( self::OPTION_LAST_FEED_SYNC, 0 );
        if ( $last_sync > 0 && ( time() - $last_sync ) < ( 6 * HOUR_IN_SECONDS ) ) {
            return;
        }

        $this->sync_feed();
    }

    public function handle_request_protection() {
        if ( ! $this->is_protection_enabled() ) {
            return;
        }

        if ( is_admin() || wp_doing_ajax() ) {
            return;
        }
        if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
            return;
        }
        if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
            return;
        }
        $request_uri = isset( $_SERVER['REQUEST_URI'] ) ? (string) $_SERVER['REQUEST_URI'] : '';
        $request_path = strtolower( (string) parse_url( $request_uri, PHP_URL_PATH ) );
        $request_method = isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( (string) $_SERVER['REQUEST_METHOD'] ) : 'GET';

        // Защищаем только страничные запросы, не ассеты.
        $extension = strtolower( (string) pathinfo( $request_path, PATHINFO_EXTENSION ) );
        $page_extensions = [ '', 'php', 'html', 'htm' ];
        if ( ! in_array( $extension, $page_extensions, true ) ) {
            return;
        }

        if ( ! in_array( $request_method, [ 'GET', 'POST' ], true ) ) {
            return;
        }

        if ( $this->can_bypass_protection_for_current_user() ) {
            return;
        }

        $ip = $this->get_ip();
        if ( ! filter_var( $ip, FILTER_VALIDATE_IP ) ) {
            return;
        }

        if ( $this->is_ip_allowlisted( $ip ) ) {
            return;
        }

        $rule = $this->get_active_cloud_rule_for_ip( $ip );
        if ( empty( $rule ) || empty( $rule['action'] ) ) {
            return;
        }

//        if ( $_SERVER['HTTP_HOST'] === 'reboot.local:8888' ) {
//            $rule['action'] = 'captcha';
//        }

        if ( 'block' === $rule['action'] ) {
            $this->increment_daily_stat( 'blocked_count' );
            $this->render_block_page();
        }

        if ( 'captcha' === $rule['action'] ) {
            if ( $this->has_valid_challenge_cookie( $ip ) ) {
                return;
            }
            $this->render_challenge_page( $ip );
        }
    }

    protected function is_protection_enabled() {
        // Emergency switch in wp-config.php:
        // define( 'CLEARFY_CLOUD_PROTECTION_DISABLE', true );
        if ( defined( 'CLEARFY_CLOUD_PROTECTION_DISABLE' ) && CLEARFY_CLOUD_PROTECTION_DISABLE ) {
            return false;
        }

        /**
         * Global switch for Clearfy Cloud request protection.
         * Return false to disable all block/captcha checks.
         */
        return (bool) apply_filters( 'clearfy/cloud_protection/enabled', true );
    }

    protected function can_bypass_protection_for_current_user() {
        if ( ! is_user_logged_in() ) {
            return false;
        }

        /**
         * Capability that bypasses Clearfy Cloud protection.
         * Default: editor and higher.
         */
        $capability = (string) apply_filters( 'clearfy/cloud_protection/bypass_capability', 'edit_others_posts' );
        if ( $capability !== '' && current_user_can( $capability ) ) {
            return true;
        }

        /**
         * Additional custom bypass rule for logged-in users.
         */
        return (bool) apply_filters( 'clearfy/cloud_protection/bypass_logged_in', false );
    }

    protected function render_challenge_page( $ip ) {
        $is_post_attempt = ( 'POST' === strtoupper( $_SERVER['REQUEST_METHOD'] ?? '' ) && ! empty( $_POST['clearfy_cloud_challenge'] ) );

        if ( 'POST' === strtoupper( $_SERVER['REQUEST_METHOD'] ?? '' ) && ! empty( $_POST['clearfy_cloud_challenge'] ) ) {
            $nonce = isset( $_POST['clearfy_cloud_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['clearfy_cloud_nonce'] ) ) : '';
            $captcha_token = isset( $_POST['clearfy_cloud_captcha_token'] ) ? sanitize_text_field( wp_unslash( $_POST['clearfy_cloud_captcha_token'] ) ) : '';
            $captcha_answer = isset( $_POST['clearfy_cloud_captcha_answer'] ) ? absint( wp_unslash( $_POST['clearfy_cloud_captcha_answer'] ) ) : -1;
            $js_ready = isset( $_POST['clearfy_cloud_js_ready'] ) ? sanitize_text_field( wp_unslash( $_POST['clearfy_cloud_js_ready'] ) ) : '';
            $honeypot = isset( $_POST['clearfy_cloud_hp'] ) ? sanitize_text_field( wp_unslash( $_POST['clearfy_cloud_hp'] ) ) : '';
            $expected_answer = 0;
            if ( $captcha_token !== '' ) {
                $expected_answer = (int) get_transient( 'clearfy_cloud_captcha_' . md5( $ip . '|' . $captcha_token ) );
            }

            if ( wp_verify_nonce( $nonce, 'clearfy_cloud_challenge_' . $ip ) && $js_ready === '1' && $honeypot === '' && $expected_answer >= 0 && $captcha_answer === $expected_answer ) {
                $cookie_value = hash_hmac( 'sha256', $ip, wp_salt( 'auth' ) );
                delete_transient( 'clearfy_cloud_captcha_' . md5( $ip . '|' . $captcha_token ) );
                $this->increment_daily_stat( 'captcha_passed_count' );
                setcookie( self::CAPTCHA_COOKIE, $cookie_value, time() + self::CAPTCHA_COOKIE_TTL, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, is_ssl(), true );
                wp_safe_redirect( esc_url_raw( $_SERVER['REQUEST_URI'] ?? '/' ) );
                exit;
            }
        }

        if ( $is_post_attempt ) {
            $this->increment_daily_stat( 'captcha_failed_count' );
        }

        $this->increment_daily_stat( 'captcha_shown_count' );

        status_header( 403 );
        nocache_headers();
        $nonce = wp_create_nonce( 'clearfy_cloud_challenge_' . $ip );
        // Добавляем случайное смещение: "ровная поза" достигается в случайной точке слайдера,
        // а не на его крайних значениях min/max.
        $offset = random_int( 40, 320 );
        $target_angle = ( 360 - $offset ) % 360;
        $start_angle = random_int( 0, 359 );
        while ( abs( $start_angle - $target_angle ) < 25 ) {
            $start_angle = random_int( 0, 359 );
        }
        $captcha_token = wp_generate_password( 16, false, false );
        $expected_answer = $target_angle;
        set_transient( 'clearfy_cloud_captcha_' . md5( $ip . '|' . $captcha_token ), $expected_answer, 10 * MINUTE_IN_SECONDS );

        $title = esc_html__( 'Security check', 'clearfy-pro' );
        $subtitle = esc_html__( 'Please confirm you are a human.', 'clearfy-pro' );
        $rotate_text = esc_html__( 'Rotate the icon to the upright position.', 'clearfy-pro' );
        $button_text = esc_html__( 'Continue', 'clearfy-pro' );
        $content = '
            <h1 class="clearfy-cloud-title">' . esc_html( $title ) . '</h1>
            <p class="clearfy-cloud-subtitle">' . esc_html( $subtitle ) . '</p>
            <form method="post" class="clearfy-cloud-form">
                <input type="hidden" name="clearfy_cloud_challenge" value="1">
                <input type="hidden" name="clearfy_cloud_nonce" value="' . esc_attr( $nonce ) . '">
                <input type="hidden" name="clearfy_cloud_captcha_token" value="' . esc_attr( $captcha_token ) . '">
                <input type="hidden" name="clearfy_cloud_js_ready" id="clearfy-cloud-js-ready" value="0">
                <input type="hidden" name="clearfy_cloud_captcha_answer" id="clearfy-cloud-answer" value="-1">
                <div style="position:absolute;left:-9999px;opacity:0;"><label>Website<input type="text" name="clearfy_cloud_hp" value=""></label></div>
                <p class="clearfy-cloud-help">' . esc_html( $rotate_text ) . '</p>
                <div class="clearfy-cloud-captcha-figure" aria-hidden="true">
                    <svg width="110" height="110" viewBox="0 0 110 110" role="presentation"><g id="clearfy-cloud-figure" transform="rotate(' . (int) $start_angle . ' 55 55)"><circle cx="55" cy="24" r="10" fill="#f7f8fb"></circle><rect x="49" y="35" width="12" height="32" rx="4" fill="#f7f8fb"></rect><line x1="30" y1="48" x2="80" y2="48" stroke="#f7f8fb" stroke-width="8" stroke-linecap="round"></line><line x1="55" y1="67" x2="38" y2="95" stroke="#f7f8fb" stroke-width="8" stroke-linecap="round"></line><line x1="55" y1="67" x2="72" y2="95" stroke="#f7f8fb" stroke-width="8" stroke-linecap="round"></line></g></svg>
                </div>
                <input id="clearfy-cloud-slider" class="clearfy-cloud-slider" type="range" min="0" max="359" step="1" value="' . (int) $start_angle . '">
                <button id="clearfy-cloud-submit" class="clearfy-cloud-button" type="submit">' . esc_html( $button_text ) . '</button>
            </form>
            <script>(function(){var offset=' . (int) $offset . ';var slider=document.getElementById("clearfy-cloud-slider");var fig=document.getElementById("clearfy-cloud-figure");var js=document.getElementById("clearfy-cloud-js-ready");var ans=document.getElementById("clearfy-cloud-answer");js.value="1";function norm(v){v=parseInt(v,10)||0;v=((v%360)+360)%360;return v;}function sync(){var raw=norm(slider.value);var displayed=norm(raw+offset);fig.setAttribute("transform","rotate("+displayed+" 55 55)");ans.value=raw;}slider.addEventListener("input",sync);sync();})();</script>
        ';
        $this->render_cloud_page_shell( esc_html__( 'Verification', 'clearfy-pro' ), $content, $ip );
        exit;
    }

    protected function render_block_page() {
        status_header( 403 );
        nocache_headers();

        $title = esc_html__( 'Access denied by Clearfy Pro.', 'clearfy-pro' );
        $help_text = esc_html__( 'If you think this is a mistake, contact the site administrator.', 'clearfy-pro' );
        $help_url = 'https://support.wpshop.ru/faq/clearfy-pro-access-denied-cloud-protection/';
        $owner_text = sprintf(
            __( 'If you are the site owner, disable Cloud Protection temporarily using %s.', 'clearfy-pro' ),
            '<a href="' . esc_url( $help_url ) . '" target="_blank" rel="noopener">' . esc_html__( 'this guide', 'clearfy-pro' ) . '</a>'
        );
        $visitor_ip = $this->get_ip();
        $retry_text = esc_html__( 'Try again', 'clearfy-pro' );
        $content = '
            <h1 class="clearfy-cloud-title">' . esc_html( $title ) . '</h1>
            <p class="clearfy-cloud-subtitle">' . esc_html( $help_text ) . '</p>
            <p class="clearfy-cloud-help">' . wp_kses_post( $owner_text ) . '</p>
            <a class="clearfy-cloud-button clearfy-cloud-button--link" href="' . esc_url( $_SERVER['REQUEST_URI'] ?? '/' ) . '">' . esc_html( $retry_text ) . '</a>
        ';
        $this->render_cloud_page_shell( $title, $content, $visitor_ip );
        exit;
    }

    protected function render_cloud_page_shell( $page_title, $content, $ip ) {
        $ip_label = esc_html__( 'IP address:', 'clearfy-pro' );
        $protection_label = esc_html__( 'Protection by Clearfy Pro', 'clearfy-pro' );
        $safe_content = wp_kses(
            $content,
            [
                'div' => [ 'class' => true, 'style' => true, 'aria-hidden' => true ],
                'svg' => [ 'width' => true, 'height' => true, 'viewBox' => true, 'role' => true ],
                'g' => [ 'id' => true, 'transform' => true ],
                'circle' => [ 'cx' => true, 'cy' => true, 'r' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true ],
                'rect' => [ 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true, 'fill' => true ],
                'line' => [ 'x1' => true, 'y1' => true, 'x2' => true, 'y2' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-linecap' => true ],
                'path' => [ 'd' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-linecap' => true ],
                'h1' => [ 'class' => true ],
                'p' => [ 'class' => true ],
                'form' => [ 'method' => true, 'class' => true ],
                'input' => [ 'type' => true, 'name' => true, 'value' => true, 'id' => true, 'class' => true, 'min' => true, 'max' => true, 'step' => true ],
                'button' => [ 'id' => true, 'class' => true, 'type' => true ],
                'script' => [],
                'a' => [ 'href' => true, 'target' => true, 'rel' => true, 'class' => true ],
                'label' => [],
            ]
        );

        echo '<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>' . esc_html( $page_title ) . '</title><style>
            *,*::before,*::after{box-sizing:border-box;}
            body{margin:0;padding:clamp(1rem,2.4vw,1.75rem);min-height:100dvh;display:flex;align-items:center;justify-content:center;background:radial-gradient(circle at top,#2f313a 0%,#1f2129 65%,#1a1b22 100%);font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Arial,sans-serif;color:#f3f4f6;}
            .clearfy-cloud-card{width:100%;max-width:38rem;background:#2e3039;border:1px solid rgba(255,255,255,.16);border-radius:2rem;padding:clamp(1.75rem,3.8vw,2.75rem) clamp(1.25rem,3vw,2.2rem) clamp(1.125rem,2.6vw,1.85rem);box-shadow:0 1rem 3rem rgba(0,0,0,.32);}
            .clearfy-cloud-title{margin:0 0 .625rem;font-size:clamp(1.5rem,3.9vw,1.95rem);line-height:1.15;font-weight:700;color:#f5f6f7;}
            .clearfy-cloud-subtitle{margin:0 0 clamp(1rem,2.3vw,1.4rem);font-size:clamp(1rem,2.8vw,1.3rem);line-height:1.35;color:#bcc0cf;}
            .clearfy-cloud-help{margin:0 0 clamp(1rem,2vw,1.2rem);font-size:clamp(.95rem,2.3vw,1.12rem);line-height:1.45;color:#d6d8de;}
            .clearfy-cloud-help a{color:#e8e9ee;text-decoration:underline;}
            .clearfy-cloud-captcha-figure{display:flex;justify-content:center;align-items:center;margin:0 0 clamp(.75rem,1.9vw,1rem);}
            .clearfy-cloud-slider{width:100%;margin:0 0 clamp(.75rem,1.9vw,1rem);}
            .clearfy-cloud-form{margin-top:.25rem;}
            .clearfy-cloud-button{display:block;width:100%;margin-top:clamp(.85rem,2vw,1.1rem);border:0;border-radius:999px;padding:clamp(.75rem,1.9vw,.95rem) 1rem;background:#14151b;color:#fff;font-size:clamp(1.2rem,3vw,1.6rem);line-height:1.2;font-weight:500;cursor:pointer;text-align:center;text-decoration:none;}
            .clearfy-cloud-button:hover,.clearfy-cloud-button:focus{background:#0f1015;}
            .clearfy-cloud-button--link{box-sizing:border-box;}
            .clearfy-cloud-meta{margin-top:clamp(1.1rem,2.5vw,1.55rem);padding-top:clamp(.65rem,1.7vw,.85rem);border-top:1px solid rgba(255,255,255,.12);display:flex;gap:clamp(.7rem,2vw,1.25rem);flex-wrap:wrap;justify-content:center;font-size:clamp(.75rem,1.7vw,.875rem);color:#9fa4b6;}
        </style></head><body><div class="clearfy-cloud-card">' . $safe_content . '<div class="clearfy-cloud-meta"><span>' . esc_html( $protection_label ) . '</span><span>' . esc_html( $ip_label ) . ' ' . esc_html( $ip ) . '</span></div></div></body></html>';
    }

    protected function has_valid_challenge_cookie( $ip ) {
        if ( empty( $_COOKIE[ self::CAPTCHA_COOKIE ] ) ) {
            return false;
        }

        $expected = hash_hmac( 'sha256', $ip, wp_salt( 'auth' ) );

        return hash_equals( $expected, (string) $_COOKIE[ self::CAPTCHA_COOKIE ] );
    }

    protected function get_active_cloud_rule_for_ip( $ip ) {
        global $wpdb;
        $table = $this->get_table_cloud_ips();
        $now_gmt = gmdate( 'Y-m-d H:i:s' );

        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT ip_address, action, expires_at FROM {$table} WHERE ip_address = %s AND expires_at >= %s LIMIT 1",
                $ip,
                $now_gmt
            ),
            ARRAY_A
        );

        return is_array( $row ) ? $row : [];
    }

    protected function is_ip_allowlisted( $ip ) {
        global $wpdb;
        $table = $this->get_table_allowlist();
        $now_gmt = gmdate( 'Y-m-d H:i:s' );

        $row = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT ip_address FROM {$table} WHERE ip_address = %s AND (expires_at IS NULL OR expires_at >= %s) LIMIT 1",
                $ip,
                $now_gmt
            )
        );

        return ! empty( $row );
    }

    public function get_cloud_ips_for_admin( $limit = 200 ) {
        global $wpdb;
        $table = $this->get_table_cloud_ips();
        $limit = absint( $limit );
        if ( $limit < 1 ) {
            $limit = 200;
        }

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT ip_address, action, updated_at, expires_at FROM {$table} ORDER BY updated_at DESC LIMIT %d",
                $limit
            ),
            ARRAY_A
        );
    }

    public function get_last_feed_sync() {
        return (int) get_option( self::OPTION_LAST_FEED_SYNC, 0 );
    }

    public function get_stats_summary_for_admin() {
        return [
            '24h' => $this->get_stats_sum_last_days( 1 ),
            '30d' => $this->get_stats_sum_last_days( 30 ),
            'all' => $this->get_stats_sum_all_time(),
        ];
    }

    protected function get_stats_sum_last_days( $days ) {
        global $wpdb;
        $table = $this->get_table_stats_daily();
        $days = max( 1, absint( $days ) );
        $from_date = gmdate( 'Y-m-d', time() - ( ( $days - 1 ) * DAY_IN_SECONDS ) );

        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT
                    COALESCE(SUM(blocked_count),0) AS blocked_count,
                    COALESCE(SUM(captcha_shown_count),0) AS captcha_shown_count,
                    COALESCE(SUM(captcha_passed_count),0) AS captcha_passed_count,
                    COALESCE(SUM(captcha_failed_count),0) AS captcha_failed_count
                 FROM {$table}
                 WHERE stat_date >= %s",
                $from_date
            ),
            ARRAY_A
        );

        return is_array( $row ) ? $row : [
            'blocked_count' => 0,
            'captcha_shown_count' => 0,
            'captcha_passed_count' => 0,
            'captcha_failed_count' => 0,
        ];
    }

    protected function get_stats_sum_all_time() {
        global $wpdb;
        $table = $this->get_table_stats_daily();
        $row = $wpdb->get_row(
            "SELECT
                COALESCE(SUM(blocked_count),0) AS blocked_count,
                COALESCE(SUM(captcha_shown_count),0) AS captcha_shown_count,
                COALESCE(SUM(captcha_passed_count),0) AS captcha_passed_count,
                COALESCE(SUM(captcha_failed_count),0) AS captcha_failed_count
             FROM {$table}",
            ARRAY_A
        );

        return is_array( $row ) ? $row : [
            'blocked_count' => 0,
            'captcha_shown_count' => 0,
            'captcha_passed_count' => 0,
            'captcha_failed_count' => 0,
        ];
    }

    protected function purge_expired_cloud_ips() {
        global $wpdb;
        $table = $this->get_table_cloud_ips();
        $now_gmt = gmdate( 'Y-m-d H:i:s' );
        $wpdb->query( $wpdb->prepare( "DELETE FROM {$table} WHERE expires_at < %s", $now_gmt ) );
    }

    protected function maybe_create_tables() {
        static $done = false;
        if ( $done ) {
            return;
        }
        $done = true;
        if ( get_option( self::OPTION_TABLES_VERSION ) === self::TABLES_VERSION ) {
            return;
        }

        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $charset_collate = $wpdb->get_charset_collate();

        $table_cloud = $this->get_table_cloud_ips();
        $sql_cloud = "CREATE TABLE {$table_cloud} (
            ip_address varchar(45) NOT NULL,
            action varchar(20) NOT NULL DEFAULT 'block',
            source varchar(20) NOT NULL DEFAULT 'cloud',
            updated_at datetime NOT NULL,
            expires_at datetime NOT NULL,
            PRIMARY KEY  (ip_address),
            KEY action (action),
            KEY expires_at (expires_at)
        ) {$charset_collate};";

        $table_allow = $this->get_table_allowlist();
        $sql_allow = "CREATE TABLE {$table_allow} (
            ip_address varchar(45) NOT NULL,
            expires_at datetime NULL DEFAULT NULL,
            reason text NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (ip_address),
            KEY expires_at (expires_at)
        ) {$charset_collate};";

        $table_stats = $this->get_table_stats_daily();
        $sql_stats = "CREATE TABLE {$table_stats} (
            stat_date date NOT NULL,
            blocked_count bigint(20) unsigned NOT NULL DEFAULT 0,
            captcha_shown_count bigint(20) unsigned NOT NULL DEFAULT 0,
            captcha_passed_count bigint(20) unsigned NOT NULL DEFAULT 0,
            captcha_failed_count bigint(20) unsigned NOT NULL DEFAULT 0,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (stat_date)
        ) {$charset_collate};";

        dbDelta( $sql_cloud );
        dbDelta( $sql_allow );
        dbDelta( $sql_stats );
        update_option( self::OPTION_TABLES_VERSION, self::TABLES_VERSION );
    }

    protected function get_table_cloud_ips() {
        global $wpdb;
        return $wpdb->prefix . 'clearfy_cloud_ips';
    }

    protected function get_table_allowlist() {
        global $wpdb;
        return $wpdb->prefix . 'clearfy_cloud_allowlist';
    }

    protected function get_table_stats_daily() {
        global $wpdb;
        return $wpdb->prefix . 'clearfy_cloud_stats_daily';
    }

    protected function increment_daily_stat( $field ) {
        $allowed_fields = [ 'blocked_count', 'captcha_shown_count', 'captcha_passed_count', 'captcha_failed_count' ];
        if ( ! in_array( $field, $allowed_fields, true ) ) {
            return;
        }

        global $wpdb;
        $table = $this->get_table_stats_daily();
        $today = gmdate( 'Y-m-d' );
        $now = gmdate( 'Y-m-d H:i:s' );

        $sql = "INSERT INTO {$table} (stat_date, {$field}, updated_at) VALUES (%s, 1, %s)
                ON DUPLICATE KEY UPDATE {$field} = {$field} + 1, updated_at = VALUES(updated_at)";
        $wpdb->query( $wpdb->prepare( $sql, $today, $now ) );
    }

    private function get_relative_path() {
        $requested_uri = isset( $_SERVER['REQUEST_URI'] ) ? (string) $_SERVER['REQUEST_URI'] : '';
        $home_path = parse_url( home_url(), PHP_URL_PATH );
        $home_path = is_string( $home_path ) ? rtrim( $home_path, '/' ) : '';

        if ( $home_path && $home_path !== '/' ) {
            $relative_path = substr( $requested_uri, strlen( $home_path ) );
        } else {
            $relative_path = $requested_uri;
        }

        return ltrim( $relative_path, '/' );
    }

    private function is_whitelist_extentions( $url ) {
        $path = (string) parse_url( $url, PHP_URL_PATH );
        $normalized_path = '/' . ltrim( strtolower( trim( $path ) ), '/' );

        if (
            preg_match( '#/wp-content/(plugins|themes)/[^/]+/readme\.txt$#', $normalized_path )
            || preg_match( '#/readme\.txt$#', $normalized_path )
        ) {
            return true;
        }

        $extension = pathinfo( parse_url( $url, PHP_URL_PATH ), PATHINFO_EXTENSION );
        $basename = pathinfo( parse_url( $url, PHP_URL_PATH ), PATHINFO_BASENAME );

        $files = [ 'readme.txt', 'readme.html', 'license.txt', 'sitemap.xml', 'robots.txt', 'humans.txt' ];
        $extensions = [
            'jpg', 'jpeg', 'png', 'gif', 'svg', 'ico', 'bmp', 'webp', 'tiff', 'tif', 'ttf', 'eot', 'woff', 'woff2', 'otf', 'svgz',
            'm3u', 'mp3', 'mp4', 'wav', 'ogg', 'oga', 'webm', 'flac', 'aac', 'm4a', 'css', 'js', 'md', 'xml', 'map',
        ];

        if ( in_array( strtolower( $basename ), $files, true ) ) {
            return true;
        }

        return in_array( strtolower( $extension ), $extensions, true );
    }

    private function get_whitelist() {
        return [
            'apple-icon-60x60.png', 'apple-icon-76x76.png', 'apple-icon-152x152.png', 'apple-touch-icon.png',
            'apple-touch-icon-precomposed.png', 'apple-touch-icon-120x120.png', 'apple-touch-icon-120x120-precomposed.png',
            'apple-touch-icon-180x180.png', 'apple-touch-icon-167x167.png', 'apple-touch-icon-152x152-precomposed.png',
            'apple-touch-icon-144x144.png', 'apple-touch-icon-114x114.png', 'apple-touch-icon-76x76-precomposed.png',
            'apple-touch-icon-72x72.png', 'apple-touch-icon-60x60-precomposed.png', 'apple-touch-icon-57x57.png',
            'safari-pinned-tab.svg', 'favicon.svg', 'favicon.ico', 'favicon.png', 'favicon-16x16.png', 'favicon-32x32.png',
            'mstile-150x150.png', '.well-known/traffic-advice', '.well-known/assetlinks.json', '.well-known/dnt-policy.txt',
            '.well-known/change-password', '.well-known/apple-app-site-association', '.well-known/security.txt', '.well-known/gpc.json',
            'ads.txt', 'site.webmanifest', 'manifest.json', 'service-worker.js', 'robots.txt', 'humans.txt', 'browserconfig.xml',
            'sitemap_index.xml', 'sitemap.xml', 'sitemaps.xml', 'sitemap.xml.gz', 'crossdomain.xml', 'readme.html', 'license.txt', 'readme.txt',
        ];
    }

    private function get_ip() {
//        if ( $_SERVER['HTTP_HOST'] === 'reboot.local:8888' ) {
//            return '107.172.35.195';
//        }
        $remote_addr = isset( $_SERVER['REMOTE_ADDR'] ) ? (string) $_SERVER['REMOTE_ADDR'] : '';
        $trusted_proxies = [ '127.0.0.1', '::1' ];

        if ( in_array( $remote_addr, $trusted_proxies, true ) ) {
            if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
                $forwarded_ips = array_map( 'trim', explode( ',', (string) $_SERVER['HTTP_X_FORWARDED_FOR'] ) );
                for ( $i = count( $forwarded_ips ) - 1; $i >= 0; $i-- ) {
                    $ip = $forwarded_ips[ $i ];
                    if ( filter_var( $ip, FILTER_VALIDATE_IP ) && ! in_array( $ip, $trusted_proxies, true ) ) {
                        return $ip;
                    }
                }
            }
            if ( ! empty( $_SERVER['HTTP_X_REAL_IP'] ) && filter_var( (string) $_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP ) ) {
                return (string) $_SERVER['HTTP_X_REAL_IP'];
            }
        }

        if ( filter_var( $remote_addr, FILTER_VALIDATE_IP ) ) {
            return $remote_addr;
        }

        return 'UNKNOWN';
    }

    private function get_license_token_or_key() {
        $token = (string) get_option( \Clearfy_Plugin::LICENSE_TOKEN_OPTION );
        if ( $token !== '' ) {
            return $token;
        }

        return (string) get_option( \Clearfy_Plugin::LICENSE_KEY_OPTION );
    }
}
