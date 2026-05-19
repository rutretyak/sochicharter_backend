<?php

class Clearfy_Plugin_Admin {
    /**
     * Option name
     *
     * @var string
     */
    protected $option_name = 'clearfy_option';

    /**
     * All options
     *
     * @var mixed|void
     */
    protected $options;

    /**
     * Plugin path
     *
     * @var string
     */
    protected $plugin_path;

    /**
     * Link to settings page
     *
     * @var string
     */
    protected $settings_link;

    /**
     * Settings migrate
     *
     * @var
     */
    protected $settings_migrate;

    /**
     * Plugin Options
     *
     * @var Clearfy_Plugin_Options
     */
    protected $plugin_options;


    /**
     * Clearfy_Plugin_Admin constructor.
     *
     * @param Clearfy_Plugin_Options $plugin_options
     */
    public function __construct( Clearfy_Plugin_Options $plugin_options ) {

        $this->plugin_options = $plugin_options;

        $this->settings_link    = admin_url( 'options-general.php?page=clearfy' );

        $this->options = get_option($this->option_name);

        /**
         * Admin menu and settings
         */
        add_action( 'admin_menu', array( $this, 'create_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_clearfy_settings' ) );

        /**
         * Add css and js files
         */
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );


        // plugin settings link
        add_filter( 'plugin_action_links_' . $this->plugin_options->plugin_path, array( $this, 'plugin_add_settings_link' ) );

        /**
         * License activate
         */
        add_action( 'admin_init', array( $this, 'activate_license' ) );

        /**
         * Settings Migrate
         */
        require_once dirname(__FILE__) . '/../inc/class-settings-migrate.php';
        $this->settings_migrate = new Clearfy_Settings_Migrate( $this->plugin_options, array( $this->option_name, 'redirect_manager' ) );
        $this->settings_migrate->init();
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    0.9.5
     */
    public function enqueue_styles() {
        wp_enqueue_style( $this->plugin_options->plugin_name, plugin_dir_url(__FILE__) . 'css/clearfy-admin.css', array(), $this->plugin_options->version, 'all' );
        wp_enqueue_style( 'wp-color-picker' );
    }


    /**
     * Register the JavaScript for the admin area.
     *
     * @since    0.9.5
     */
    public function enqueue_scripts() {
        // выводим только на странице плагина и на странице редактирования юзера
        if ( get_current_screen() && in_array( get_current_screen()->id, [ 'toplevel_page_clearfy', 'profile', 'user-edit' ] ) ) {
            $local_avatar_size = (int) apply_filters( 'clearfy/local_avatars/size', 200 );
            if ( $local_avatar_size < 1 ) {
                $local_avatar_size = 200;
            }
            $code_editor_settings = wp_enqueue_code_editor( array( 'type' => 'text/html' ) );
            if ( ! empty( $code_editor_settings ) ) {
                wp_enqueue_script( 'wp-theme-plugin-editor' );
                wp_enqueue_style( 'wp-codemirror' );
            }

            $enqueue_script_deps = apply_filters( 'clearfy_enqueue_script_deps', array( 'jquery', 'wp-color-picker' ) );
            wp_enqueue_script( $this->plugin_options->plugin_name, plugin_dir_url( $this->plugin_options->plugin_path ) . 'assets/js/clearfy-admin.js', $enqueue_script_deps, $this->plugin_options->version, false );
            wp_localize_script( $this->plugin_options->plugin_name, 'clearfy_settings', [
                'color_picker_enable' => apply_filters( 'clearfy_admin_color_picker_enable', true ),
                'local_avatar_size' => $local_avatar_size,
                'code_editor_settings' => $code_editor_settings,
                'i18n' => [
                    'choose_avatar' => __( 'Choose avatar', $this->plugin_options->text_domain ),
                    'select' => __( 'Select', $this->plugin_options->text_domain ),
                    'crop' => __( 'Crop', $this->plugin_options->text_domain ),
                    'redirect_cycle' => __( 'Cyclic redirect: this URL points to itself.', $this->plugin_options->text_domain ),
                    'redirect_duplicate' => __( 'Duplicate redirect: this source URL is used more than once.', $this->plugin_options->text_domain ),
                ],
            ] );
            wp_enqueue_media();
        }
        wp_enqueue_media();
    }


    /**
     * Add settings link in plugins list
     *
     * @param $links
     * @return mixed
     */
    public function plugin_add_settings_link( $links ) {
        $settings_link = '<a href="' . $this->settings_link . '">' . __( 'Settings' ) . '</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }

    /**
     * Add plugin settings menu link
     */
    public function create_admin_menu() {
        add_menu_page( 'Clearfy Settings', 'Clearfy Pro', 'manage_options', 'clearfy', array( $this, 'admin_page_display' ), $this->get_menu_svg(), "99.42" );

        /**
         * Change name
         */
        global $submenu;
        if ( isset( $submenu['clearfy'] ) && current_user_can( 'manage_options' ) ) {
            $submenu['clearfy'][0][0] = 'Основные';
        }
    }

    /**
     * Returns a base64 URL for the svg for use in the menu
     *
     * @return string
     */
    private function get_menu_svg() {
        $icon_svg = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiBwcmVzZXJ2ZUFzcGVjdFJhdGlvPSJ4TWlkWU1pZCIgd2lkdGg9IjM3NiIgaGVpZ2h0PSIzMDIiIHZpZXdCb3g9IjAgMCAzNzYgMzAyIj4KICA8ZGVmcz4KICAgIDxzdHlsZT4KCiAgICAgIC5jbHMtMiB7CiAgICAgICAgZmlsbDogIzAwMDAwMDsKICAgICAgfQogICAgPC9zdHlsZT4KICA8L2RlZnM+CiAgPHBhdGggZD0iTTM3Ni4yNDIsODguOTMyIEMzNzYuMjQyLDg4LjkzMiAzNzEuOTIwLDkyLjE4MSAzNzEuOTIwLDkyLjE4MSBDMzcxLjkyMCw5Mi4xODEgMzcyLjA2OSw5Mi4zMTMgMzcyLjA2OSw5Mi4zMTMgQzM3Mi4wNjksOTIuMzEzIDE4Ny4zODUsMzAwLjgyOSAxODcuMzg1LDMwMC44MjkgQzE4Ny4zODUsMzAwLjgyOSAxODYuNjk3LDMwMS45NjEgMTg2LjY5NywzMDEuOTYxIEMxODYuNjk3LDMwMS45NjEgMTg2LjU0MCwzMDEuNzgzIDE4Ni41NDAsMzAxLjc4MyBDMTg2LjU0MCwzMDEuNzgzIDE4Ni4zODIsMzAxLjk2MSAxODYuMzgyLDMwMS45NjEgQzE4Ni4zODIsMzAxLjk2MSAxODUuNjk2LDMwMC44MzEgMTg1LjY5NiwzMDAuODMxIEMxODUuNjk2LDMwMC44MzEgMC4wMTEsOTEuMzEzIDAuMDExLDkxLjMxMyBDMC4wMTEsOTEuMzEzIDEuMDcyLDkwLjQ5NiAxLjA3Miw5MC40OTYgQzEuMDcyLDkwLjQ5NiAwLjI1Nyw4OS45MzIgMC4yNTcsODkuOTMyIEMwLjI1Nyw4OS45MzIgNjEuODIwLDEuMDAwIDYxLjgyMCwxLjAwMCBDNjEuODIwLDEuMDAwIDYyLjQ4NywxLjQ2MiA2Mi40ODcsMS40NjIgQzYyLjQ4NywxLjQ2MiA2Mi40MTUsLTAuMDAwIDYyLjQxNSwtMC4wMDAgQzYyLjQxNSwtMC4wMDAgMzE0LjA4NCwtMC4wMDAgMzE0LjA4NCwtMC4wMDAgQzMxNC4wODQsLTAuMDAwIDMxNC4wMTMsMS40NjIgMzE0LjAxMywxLjQ2MiBDMzE0LjAxMywxLjQ2MiAzMTQuNjgwLDEuMDAwIDMxNC42ODAsMS4wMDAgQzMxNC42ODAsMS4wMDAgMzc2LjI0Miw4OC45MzIgMzc2LjI0Miw4OC45MzIgWk0zMDcuMDM0LDI2LjAxMCBDMzA3LjAzNCwyNi4wMTAgMjcyLjY5NCw3OC42NzAgMjcyLjY5NCw3OC42NzAgQzI3Mi42OTQsNzguNjcwIDM0My40ODgsNzguNjcwIDM0My40ODgsNzguNjcwIEMzNDMuNDg4LDc4LjY3MCAzMDcuMDM0LDI2LjAxMCAzMDcuMDM0LDI2LjAxMCBaTTMzOC42MTIsOTkuMTkzIEMzMzguNjEyLDk5LjE5MyAyNjIuMjE2LDk5LjE5MyAyNjIuMjE2LDk5LjE5MyBDMjYyLjIxNiw5OS4xOTMgMjExLjg3MiwyNDIuNjg1IDIxMS44NzIsMjQyLjY4NSBDMjExLjg3MiwyNDIuNjg1IDMzOC42MTIsOTkuMTkzIDMzOC42MTIsOTkuMTkzIFpNMTg2LjU0MCwyNTIuOTA1IEMxODYuNTQwLDI1Mi45MDUgMjQwLjQ2OCw5OS4xOTMgMjQwLjQ2OCw5OS4xOTMgQzI0MC40NjgsOTkuMTkzIDEzMi42MTEsOTkuMTkzIDEzMi42MTEsOTkuMTkzIEMxMzIuNjExLDk5LjE5MyAxODYuNTQwLDI1Mi45MDUgMTg2LjU0MCwyNTIuOTA1IFpNMTYxLjIwNywyNDIuNjg2IEMxNjEuMjA3LDI0Mi42ODYgMTEwLjg2NCw5OS4xOTMgMTEwLjg2NCw5OS4xOTMgQzExMC44NjQsOTkuMTkzIDM0LjQ2OCw5OS4xOTMgMzQuNDY4LDk5LjE5MyBDMzQuNDY4LDk5LjE5MyAxNjEuMjA3LDI0Mi42ODYgMTYxLjIwNywyNDIuNjg2IFpNMzMuMDExLDc4LjY3MCBDMzMuMDExLDc4LjY3MCAxMDAuMzg2LDc4LjY3MCAxMDAuMzg2LDc4LjY3MCBDMTAwLjM4Niw3OC42NzAgNjcuNzA0LDI4LjU1NCA2Ny43MDQsMjguNTU0IEM2Ny43MDQsMjguNTU0IDMzLjAxMSw3OC42NzAgMzMuMDExLDc4LjY3MCBaTTg2Ljk2NiwyMC41MjMgQzg2Ljk2NiwyMC41MjMgMTIwLjE2Miw3MS40MjggMTIwLjE2Miw3MS40MjggQzEyMC4xNjIsNzEuNDI4IDE2NC4xMjEsMjAuNTIzIDE2NC4xMjEsMjAuNTIzIEMxNjQuMTIxLDIwLjUyMyA4Ni45NjYsMjAuNTIzIDg2Ljk2NiwyMC41MjMgWk0xNDEuMDIyLDc4LjY3MCBDMTQxLjAyMiw3OC42NzAgMjMyLjA1Nyw3OC42NzAgMjMyLjA1Nyw3OC42NzAgQzIzMi4wNTcsNzguNjcwIDE4Ni41NDAsMjUuOTYwIDE4Ni41NDAsMjUuOTYwIEMxODYuNTQwLDI1Ljk2MCAxNDEuMDIyLDc4LjY3MCAxNDEuMDIyLDc4LjY3MCBaTTIwOC45NTgsMjAuNTIzIEMyMDguOTU4LDIwLjUyMyAyNTIuOTE4LDcxLjQyOCAyNTIuOTE4LDcxLjQyOCBDMjUyLjkxOCw3MS40MjggMjg2LjExMywyMC41MjMgMjg2LjExMywyMC41MjMgQzI4Ni4xMTMsMjAuNTIzIDIwOC45NTgsMjAuNTIzIDIwOC45NTgsMjAuNTIzIFoiIGlkPSJwYXRoLTEiIGNsYXNzPSJjbHMtMiIgZmlsbC1ydWxlPSJldmVub2RkIi8+Cjwvc3ZnPgo=';

        return $icon_svg;
    }


    /**
     * Register settings
     */
    public function register_clearfy_settings() {
        register_setting( 'clearfy_settings', $this->option_name, array( $this, 'sanitize_clearfy_options' ) );
    }

    public function sanitize_clearfy_options( $options ) {

        // indexnow sanitize // Key must consist of a-Z0-9 or '-'
        if ( isset( $options['indexnow_key'] ) ) {
            $options['indexnow_key'] = preg_replace( '/[^a-zA-Z0-9\-]/', '', $options['indexnow_key'] );
        }
        if ( isset( $options['indexnow_post_types'] ) && is_array( $options['indexnow_post_types'] ) ) {
            $options['indexnow_post_types'] = array_values( array_unique( array_filter( array_map( 'sanitize_key', $options['indexnow_post_types'] ) ) ) );
        } else {
            $options['indexnow_post_types'] = [];
        }
        if ( isset( $options['hide_external_links_post_types'] ) && is_array( $options['hide_external_links_post_types'] ) ) {
            $options['hide_external_links_post_types'] = array_values( array_unique( array_filter( array_map( 'sanitize_key', $options['hide_external_links_post_types'] ) ) ) );
        } else {
            $options['hide_external_links_post_types'] = [];
        }
        if ( isset( $options['hide_external_links_excluded_post_ids'] ) ) {
            $options['hide_external_links_excluded_post_ids'] = preg_replace( '/[^0-9,\s]/', '', (string) $options['hide_external_links_excluded_post_ids'] );
        }
        if ( isset( $options['pseudo_links_class'] ) ) {
            $options['pseudo_links_class'] = sanitize_html_class( (string) $options['pseudo_links_class'] );
        }
        foreach ( [ 'pseudo_links_color', 'pseudo_links_hover_color' ] as $color_key ) {
            if ( isset( $options[ $color_key ] ) ) {
                $color = (string) $options[ $color_key ];
                if ( ! preg_match( '/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/', $color ) ) {
                    unset( $options[ $color_key ] );
                }
            }
        }

        return $options;
    }




    public function activate_license() {
        if ( empty( $_POST['clearfy_license_submit'] ) ) {
            return false;
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            return false;
        }

        if ( empty( $_POST['clearfy_activate_license_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['clearfy_activate_license_nonce'] ) ), 'clearfy_activate_license' ) ) {
            update_option( 'license_error', 'Ошибка проверки запроса. Обновите страницу и попробуйте снова.' );
            return false;
        }

        $license_input = '';
        if ( ! empty( $_POST['clearfy_license_input'] ) ) {
            $license_input = trim( (string) wp_unslash( $_POST['clearfy_license_input'] ) );
        } elseif ( ! empty( $_POST['clearfy_license_key'] ) ) {
            // Legacy field name fallback.
            $license_input = trim( (string) wp_unslash( $_POST['clearfy_license_key'] ) );
        }

        if ( $license_input !== '' ) {

            delete_option( Clearfy_Plugin::CHECK_UPDATE_OPTION );

            $license = $license_input;

            $api_params = array(
                'action'    => 'activate_license',
                'license'   => $license,
                'item_name' => $this->plugin_options->plugin_name, // НЕ urlencode — на сервере ты сам sanitize’ишь
                'version'   => $this->plugin_options->version,
                'version_wp'  => get_bloginfo( 'version' ),
                'version_php' => PHP_VERSION,
                'type'      => 'plugin',
                'ip'        => $this->get_ip(),
                'hash'      => md5( (string) get_option( 'admin_email' ) ),
                'url'       => home_url(),
                'locale'    => get_locale(), // <-- ключевой параметр для нового JSON
            );

            $result = $this->request_api( $api_params );

            if ( empty( $result['ok'] ) ) {
                /** @var WP_Error $err */
                $err = $result['error'];
                update_option( 'license_error', 'Ошибка запроса: ' . $err->get_error_message() );
                $this->redirect_after_license_submit();
            }

            $license_data = trim( (string) $result['body'] );

            // 1) Новый формат: JSON
            $json = json_decode( $license_data, true );

            if ( is_array( $json ) && isset( $json['success'] ) ) {
                if ( ! empty( $json['success'] ) ) {
                    $token = trim( (string) ( $json['token'] ?? '' ) );
                    if ( $token === '' ) {
                        update_option( 'license_error', 'License token is empty in API response.' );
                        $this->redirect_after_license_submit();
                    }

                    update_option( Clearfy_Plugin::LICENSE_TOKEN_OPTION, $token );
                    update_option( 'license_verify', time() + ( WEEK_IN_SECONDS * 4 ) );
                    delete_option( 'license_error' );
                    $this->redirect_after_license_submit();
                }

                // Ошибка в новом формате
                $message = $json['message'] ?? ( $json['code'] ?? 'Unknown error' );
                update_option( 'license_error', (string) $message );
                $this->redirect_after_license_submit();
            }

            // 2) Старый формат: начинается с "ok"
            if ( mb_substr( $license_data, 0, 2 ) === 'ok' ) {
                update_option( 'license_verify', time() + ( WEEK_IN_SECONDS * 4 ) );
                delete_option( 'license_error' );
                $this->redirect_after_license_submit();
            }

            // 3) Непонятный ответ
            update_option( 'license_error', $license_data ?: 'Unknown response from license server.' );
            $this->redirect_after_license_submit();
        }

        return false;
    }

    private function redirect_after_license_submit() {
        $redirect_url = add_query_arg(
            array(
                'page' => 'clearfy',
            ),
            admin_url( 'options-general.php' )
        );

        wp_safe_redirect( $redirect_url );
        exit;
    }

    private function request_api( array $api_params ) {
        $urls = array();

        // Source of truth for license API endpoints.
        if ( ! empty( $this->plugin_options->api_urls ) && is_array( $this->plugin_options->api_urls ) ) {
            $urls = $this->plugin_options->api_urls;
        }

        // если совсем пусто — подстрахуемся
        if ( empty( $urls ) ) {
            $urls = array( 'https://wpshop.ru/api/' );
        }

        $last_error = null;

        foreach ( $urls as $url ) {
            $response = wp_remote_post( $url, array(
                'timeout'     => 20,      // 15 часто ок, но лучше 20 под CF/хостинги
                'sslverify'   => true,    // лучше true; иначе вы ловите странные сетевые баги и риски
                'redirection' => 3,
                'headers'     => array(
                    'Accept' => 'application/json',
                ),
                'body'        => $api_params,
            ) );

            if ( is_wp_error( $response ) ) {
                $last_error = $response;
                continue;
            }

            $code = (int) wp_remote_retrieve_response_code( $response );
            $body = (string) wp_remote_retrieve_body( $response );

            // Даже если код не 200, иногда API шлёт полезный JSON — вернём как есть
            return array(
                'ok'   => true,
                'code' => $code,
                'body' => $body,
                'url'  => $url,
            );
        }

        return array(
            'ok'    => false,
            'error' => $last_error,
        );
    }




    /**
     * Display admin plugin page
     */
    public function admin_page_display() {
        $options = get_option($this->option_name);
        $license_token = get_option(Clearfy_Plugin::LICENSE_TOKEN_OPTION);
        $license_key = get_option(Clearfy_Plugin::LICENSE_KEY_OPTION);
        $license_verify = get_option('license_verify');
        $license_error = get_option('license_error');

        // 27600
        ?>

        <div class="wrap wpshop clearfy clearfy-settings-page js-clearfy">

            <h1>Clearfy Pro</h1>

            <div class="wpshopbiz-plugin-info">
                <img src="https://cdn.wpshop.ru/plugins/clearfy/logo-mini.png" alt="">
            </div>

            <?php if ( ( empty( $license_token ) && empty( $license_key ) ) || empty( $license_verify ) || ! empty( $license_error ) ): ?>
            <div class="clearfy-settings-box">
            <form method="post">
                <?php wp_nonce_field( 'clearfy_activate_license', 'clearfy_activate_license_nonce' ); ?>
                <input type="hidden" name="clearfy_license_submit" value="1">
                <table class="form-table">

                    <tr>
                        <th scope="row"><label for="clearfy_license_key">Лицензионный ключ</label></th>
                        <td>
                            <input name="clearfy_license_input" id="clearfy_license_key" type="text" class="regular-text" value="">
                            <?php if (!empty($license_error)): ?>
                                <p class="description danger"><?php echo $license_error ?></p>
                            <?php endif; ?>

                            <p><?php echo sprintf( __( 'To activate the plugin, enter the license key that you receive after payment in the letter or in <a href="%s" target="_blank" rel="noopener">personal account</a>.', $this->plugin_options->text_domain ), 'https://wpshop.ru/dashboard' ); ?></p>
                        </td>
                    </tr>
                </table>

                <?php submit_button(); ?>
            </form>
            </div>
            <?php else: ?>




            <h3><?php _e( 'Settings', $this->plugin_options->text_domain ) ?></h3>


            <div class="wpshop-cols">

                <div class="pseudo-button js-clearfy-enable"><?php _e( 'Enable all', $this->plugin_options->text_domain ) ?></div>
                <div class="pseudo-button pseudo-button__green js-clearfy-recommend"><?php _e( 'Enable recommended', $this->plugin_options->text_domain ) ?></div>
                <div class="pseudo-button pseudo-button__gray js-clearfy-disable"><?php _e( 'Disable all', $this->plugin_options->text_domain ) ?></div>

                <p><?php _e( 'For default we recommend enable only recommended settings.<br>If you are expert - you can configure manually.', $this->plugin_options->text_domain ) ?></p>
                <p><strong><?php _e( 'Don\'t forget to save settings', $this->plugin_options->text_domain ) ?></strong></p>

                <div class="clearfy-settings-cols">
                <div class="clearfy-settings-col clearfy-settings-col--left">

                    <form method="post" action="options.php" class="js-clearfy-form">

                        <?php settings_fields( 'clearfy_settings' ); ?>

                        <h2 class="wpshop-tab-wrapper js-wpshop-tab-wrapper">
                            <a class="wpshop-tab wpshop-tab-active" id="tab-clearfy_general" href="#clearfy_general"><?php _e( 'General', $this->plugin_options->text_domain ) ?></a>
                            <a class="wpshop-tab" id="tab-clearfy_clear" href="#clearfy_clear"><?php _e( 'Code', $this->plugin_options->text_domain ) ?></a>
                            <a class="wpshop-tab" id="tab-clearfy_seo" href="#clearfy_seo"><?php _e( 'SEO', $this->plugin_options->text_domain ) ?></a>
                            <a class="wpshop-tab" id="tab-clearfy_double" href="#clearfy_double"><?php _e( 'Duplicate', $this->plugin_options->text_domain ) ?></a>
                            <a class="wpshop-tab" id="tab-clearfy_security" href="#clearfy_security"><?php _e( 'Security', $this->plugin_options->text_domain ) ?></a>
                            <a class="wpshop-tab" id="tab-clearfy_modules" href="#clearfy_modules"><?php _e( 'Modules', $this->plugin_options->text_domain ) ?></a>
                            <a class="wpshop-tab" id="tab-clearfy_more" href="#clearfy_more"><?php _e( 'Additionally', $this->plugin_options->text_domain ) ?></a>
                            <a class="wpshop-tab" id="tab-clearfy_redirect" href="#clearfy_redirect"><?php _e( 'Redirect', $this->plugin_options->text_domain ) ?></a>
                            <a class="wpshop-tab" id="tab-clearfy_404" href="#clearfy_404"><?php _e( '404', $this->plugin_options->text_domain ) ?></a>
                            <a class="wpshop-tab" id="tab-clearfy_indexnow" href="#clearfy_indexnow"><?php _e( 'IndexNow', $this->plugin_options->text_domain ) ?></a>
                        </h2>

                        <div id="clearfy_general" class="wpshop-tab-in js-wpshop-tab-item active">

                            <div class="option-field-header"><?php _e( 'Instructions', $this->plugin_options->text_domain ) ?></div>

                            <div class="wpshop-widgets wpshop-widgets--docs">
                                <div class="wpshop-widget">

                                    <div class="wpshop-widget__icon">
                                        <img src="<?php echo plugins_url( 'admin/images/widget-docs.svg', $this->plugin_options->plugin_path ) ?>" alt="">
                                    </div>
                                    <div class="wpshop-widget__header"><a href="https://support.wpshop.ru/docs/plugins/clearfy-pro/?utm_source=plugin&utm_medium=clearfy&utm_campaign=instruction" target="_blank" rel="noopener"><?php _e( 'Documentation', $this->plugin_options->text_domain ) ?></a></div>
                                    <div class="wpshop-widget__description"><?php _e( 'If you have a question about our product - perhaps the answer is already in our documentation.', $this->plugin_options->text_domain ) ?></div>

                                </div><!--.wpshop-widget-->
                                <div class="wpshop-widget wpshop-widget--color-purple">

                                    <div class="wpshop-widget__icon">
                                        <img src="<?php echo plugins_url( 'admin/images/widget-qa.svg', $this->plugin_options->plugin_path ) ?>" alt="">
                                    </div>
                                    <div class="wpshop-widget__header"><a href="https://support.wpshop.ru/fag_tag/clearfy-pro/?utm_source=plugin&utm_medium=clearfy&utm_campaign=instruction" target="_blank" rel="noopener"><?php _e( 'FAQ', $this->plugin_options->text_domain ) ?></a></div>
                                    <div class="wpshop-widget__description"><?php _e( 'Section of frequently asked questions and their answers. You can quickly find an answer to your question.', $this->plugin_options->text_domain ) ?></div>

                                </div><!--.wpshop-widget-->
                                <div class="wpshop-widget wpshop-widget--color-red">

                                    <div class="wpshop-widget__icon">
                                        <img src="<?php echo plugins_url( 'admin/images/widget-video.svg', $this->plugin_options->plugin_path ) ?>" alt="">
                                    </div>
                                    <div class="wpshop-widget__header"><a href="https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/" target="_blank" rel="noopener"><?php _e( 'Video tutorials', $this->plugin_options->text_domain ) ?></a></div>
                                    <div class="wpshop-widget__description"><?php _e( 'Video tutorials on the plugin and its functions. Subscribe to the channel so you don\'t miss it.', $this->plugin_options->text_domain ) ?></div>

                                </div><!--.wpshop-widget-->
                            </div>

                            <div class="wpshop-widget">
                                <?php _e( 'Our pages', $this->plugin_options->text_domain ) ?>:
                                <a href="https://vk.com/wpshop" target="_blank" rel="noopener" class="wpshop-widget-social-icon wpshop-widget-social-icon--vk"></a>
                                <a href="https://t.me/wpshop" target="_blank" rel="noopener" class="wpshop-widget-social-icon wpshop-widget-social-icon--telegram"></a>

                                <a href="https://wpshop.ru/partner?utm_source=plugin&utm_medium=clearfy&utm_campaign=instruction" target="_blank" rel="noopener" class="wpshop-widget-partners"><?php _e( 'Affiliate program', $this->plugin_options->text_domain ) ?></a>
                                <a href="https://wpshop.ru/?utm_source=plugin&utm_medium=clearfy&utm_campaign=instruction" target="_blank" rel="noopener" class="wpshop-widget-partners">WPShop.ru</a>
                            </div>


                            <p><?php _e( 'For quick start just enable Recommended settings and click Save. But we recommend watch all possible Clearfy Pro features.', $this->plugin_options->text_domain ) ?></p>
                            <p><?php _e( 'Bloggers need attention to RSS feeds. If you use them, do not disable it.', $this->plugin_options->text_domain ) ?></p>
                            <p><?php _e( 'Just enable needed settings and click save. All done.', $this->plugin_options->text_domain ) ?></p>
                            <p><?php _e( 'Any questions? Send message to our technical support.', $this->plugin_options->text_domain ) ?></p>


                            <div class="option-field-header"><?php _e( 'License key', $this->plugin_options->text_domain ) ?></div>

                            <p><?php _e( 'You can remove your license key by clicking the button below. Warning! After removing the key, the plugin will no longer work.', $this->plugin_options->text_domain ) ?></p>

                            <p>
                                <span class="button js-clearfy-remove-license" data-nonce="<?php echo wp_create_nonce( 'clearfy_remove_license_nonce' ) ?>"><?php _e( 'Remove license key', $this->plugin_options->text_domain ) ?></span>
                            </p>


                            <div class="option-field-header"><?php _e( 'Questions, changelog', $this->plugin_options->text_domain ) ?></div>
                            <p><?php printf( __( 'FAQ and changelog you can find in <a href="%s">our knowledge base</a>.', $this->plugin_options->text_domain ), 'https://support.wpshop.ru/docs/plugins/clearfy-pro/changelog/' ) ?></p>


                            <div class="option-field-header"><?php _e( 'Export / Import settings', $this->plugin_options->text_domain ) ?></div>

                            <div class="wpshop-export-settings">
                                <label for="export_settings">Export:</label>
                                <textarea id="export_settings" class="large-text code" rows="3" onmouseover="this.select()"><?php echo $this->settings_migrate->export() ?></textarea>
                                <p class="description"><?php _e( 'Copy this code to any text file to save all site settings.', $this->plugin_options->text_domain ) ?></p>
                            </div>

                            <div class="wpshop-import-settings">
                                <label for="import_settings">Import:</label>
                                <textarea id="import_settings" name="import_settings" class="large-text code" rows="3"></textarea>
                                <input type="hidden" name="import_settings_name" value="<?php echo $this->plugin_options->plugin_name ?>">
                                <p class="description"><?php _e( 'Warning! Old settings will be deleted before importing!', $this->plugin_options->text_domain ) ?></p>
                                <span class="button js-import-settings-clearfy" data-nonce="<?php echo wp_create_nonce( 'wpshop_plugin_import_settings' ) ?>"><?php _e( 'Import', $this->plugin_options->text_domain ) ?></span>
                            </div>

                            <div class="option-field-header"><?php _e( 'Team WPShop.ru', 'clearfy' ) ?></div>

                            <p>
                                <?php _e( 'We thank you for purchasing Clearfy Pro!', $this->plugin_options->text_domain ) ?>
                                <br>
                                <?php _e( 'Our goal is to make a powerful plugin that will be among the first must-have plugins for WP.', $this->plugin_options->text_domain ) ?>
                            </p>
                            <a href="https://wpshop.ru/?utm_source=wp-admin&utm_medium=plugin&utm_campaign=clearfy" target="_blank"><img src="https://cdn.wpshop.ru/logotype.png" alt="WPShop"></a>

                        </div>
                        <div id="clearfy_clear" class="wpshop-tab-in js-wpshop-tab-item">
                            <div class="option-field-header"><?php _e( 'Clear code', $this->plugin_options->text_domain ) ?></div>

                            <div class="option-field">
                                <label class="option-field-label" for="disable_json_rest_api">
                                    <?php _e( 'Disable JSON REST API', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'rest-api' ) ?>
                                    <span class="clearfy-recommend"><?php _e( 'Recommended', $this->plugin_options->text_domain ) ?></span>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('disable_json_rest_api') ?>
                                    <p class="description"><?php _e( 'WP 4.4 and up create technical pages /wp-json/, which successfully indexing search engines like Google and reduce rank and positions of site.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php printf( __( 'Remove REST API links from %s and create redirect on front.', $this->plugin_options->text_domain ), '<code>&lt;head&gt;</code>' ) ?></p>


                                    <?php
                                    $wp_rest_server     = rest_get_server();
                                    $all_namespaces     = $wp_rest_server->get_namespaces();
                                    if ( ! empty( $all_namespaces ) ) {

                                        echo '<p><span class="button js-clearfy-rest-api-list-button">';
                                        _e('Show registered path\'s REST API', $this->plugin_options->text_domain);
                                        echo '</span></p>';

                                        echo '<pre class="clearfy-rest-api-list js-clearfy-rest-api-list" style="display: none;">';
                                        foreach ( $all_namespaces as $namespace ) {
                                            $namespaces = explode( '/', $namespace );
                                            echo $namespaces[0] . PHP_EOL;
                                        }
                                        echo '</pre>';
                                    }
                                    ?>

                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="disable_emoji">
                                    <?php _e( 'Disable Emoji', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'disable-emoji' ) ?>
                                    <span class="clearfy-recommend"><?php _e( 'Recommended', $this->plugin_options->text_domain ) ?></span>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('disable_emoji') ?>
                                    <p class="description"><?php _e( 'WP 4.2 and up add support Emoji smiles in source code for old browsers. It use external JavaScript library which slowly page and create request to external resources.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php printf( __( 'Removes Emoji from %s', $this->plugin_options->text_domain ), '<code>&lt;head&gt;</code>' ) ?></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="remove_dns_prefetch">
                                    <?php _e( 'Delete dns-prefetch', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'remove-dns-prefetch' ) ?>
                                    <span class="clearfy-recommend"><?php _e( 'Recommended', $this->plugin_options->text_domain ) ?></span>
                                </label>
                                <div class="option-field-body">
	                                <?php $this->display_select( 'remove_dns_prefetch', [
                                        'no' => __( 'Do not delete', $this->plugin_options->text_domain ),
                                        'all' => __( 'Delete all', $this->plugin_options->text_domain ),
                                        'selected' => __( 'Selected', $this->plugin_options->text_domain ),
	                                ] ) ?>
	                                <div>
		                                <?php $this->display_textarea( 'remove_dns_prefetch_urls', [] ) ?>
                                    </div>
                                    <p class="description"><?php printf( __( 'Since version 4.6.1 WordPress add new links in section %s like this: %s', $this->plugin_options->text_domain ), '<code>&lt;head&gt;</code>', '&lt;link rel=\'dns-prefetch\' href=\'//s.w.org\'&gt;' ) ?></p>
                                    <p class="description"><?php printf( __( 'You can delete all dns-prefetch links or optionally, just put domains in textarea. One domain on new line. Example: s.w.org', $this->plugin_options->text_domain ), '<code>&lt;head&gt;</code>', '&lt;link rel=\'dns-prefetch\' href=\'//s.w.org\'&gt;' ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php printf( __( 'Removes dns-prefetch links from %s section', $this->plugin_options->text_domain ), '<code>&lt;head&gt;</code>' ) ?></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="remove_jquery_migrate">
                                    <?php _e( 'Remove jquery-migrate.min.js', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'remove-jquery-migrate' ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('remove_jquery_migrate') ?>
                                    <p class="description"><?php _e( 'File jquery-migrate.min.js require for old version of jQuery before 1.9.х. In most cases it unnecessary file to load.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php printf( __( 'Delete including jquery-migrate.min.js from %s', $this->plugin_options->text_domain ), '<code>&lt;head&gt;</code>' ) ?></p>
                                    <p class="description"><span class="dashicons dashicons-warning wpshop-warning-color"></span> <?php _e( 'Check your site after enable this setting', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="remove_rsd_link">
                                    <?php _e( 'Delete RSD link', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'rsd-link' ) ?>
                                    <span class="clearfy-recommend"><?php _e( 'Recommended', $this->plugin_options->text_domain ) ?></span>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('remove_rsd_link') ?>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="remove_wlw_link">
                                    <?php _e( 'Delete WLW Manifest link', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'wlw-link' ) ?>
                                    <span class="clearfy-recommend"><?php _e( 'Recommended', $this->plugin_options->text_domain ) ?></span>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('remove_wlw_link') ?>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="remove_shortlink_link">
                                    <?php printf( __( 'Delete shortlink %s', $this->plugin_options->text_domain ), '<code>/?p=</code>' ) ?>
                                    <?php $this->the_help_icon( 'shortlink' ) ?>
                                    <span class="clearfy-recommend"><?php _e( 'Recommended', $this->plugin_options->text_domain ) ?></span>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('remove_shortlink_link') ?>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="remove_adjacent_posts_link">
                                    <?php _e( 'Remove previous and next post links', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'next-prev-links' ) ?>
                                    <span class="clearfy-recommend"><?php _e( 'Recommended', $this->plugin_options->text_domain ) ?></span>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('remove_adjacent_posts_link') ?>
                                    <?php
                                    if ( version_compare( get_bloginfo( 'version' ), '5.6', '>=' ) ) {
                                        echo '<p class="description">' . __( 'You can not enable this option, because with WordPress 5.6 these links are removed from the core.', $this->plugin_options->text_domain ) . '</p>';
                                    }
                                    ?>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="remove_recent_comments_style">
                                    <?php _e( 'Remove .recentcomments styles', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'recentcomments' ) ?>
                                    <span class="clearfy-recommend"><?php _e( 'Recommended', $this->plugin_options->text_domain ) ?></span>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('remove_recent_comments_style') ?>
                                    <p class="description"><?php _e( 'By default for widget "recent comments" WordPress add styles to source code that you can\'t change, because to them apply !important.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php printf( __( 'Removes .recentcomments styles from %s', $this->plugin_options->text_domain ), '<code>&lt;head&gt;</code>' ) ?></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="insert_code_in_head">
                                    <?php printf( __( 'Code in %s', $this->plugin_options->text_domain ), '&lt;head&gt;' ) ?>
                                    <?php $this->the_help_icon( 'code-head' ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('insert_code_in_head') ?>
                                    <div style="border: 2px solid #c2c2c2; border-radius: 5px; margin-bottom: 1rem;">
                                        <div class="code-in-head">
                                            <?php $this->display_textarea( 'code_in_head' ) ?>
                                        </div>
                                    </div>
                                    <p class="description"><?php _e( 'This usually adds verification codes from the webmaster, retarget code or JS code, which should be executed first.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php printf( __( 'Insert the code before the closing %s', $this->plugin_options->text_domain ), '&lt;head&gt;' ) ?></p>
                                </div>
                            </div><!--.option-field-->


                            <div class="option-field">
                                <label class="option-field-label" for="insert_code_before_body">
                                    <?php echo sprintf( __( 'Code before %s', $this->plugin_options->text_domain ), '&lt;/body&gt;' ) ?>
                                    <?php $this->the_help_icon( 'code-body' ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('insert_code_in_body') ?>
                                    <div style="border: 2px solid #c2c2c2; border-radius: 5px; margin-bottom: 1rem;">
                                        <div class="code-in-body">
                                            <?php $this->display_textarea( 'code_in_body' ) ?>
                                        </div>
                                    </div>
                                    <p class="description"><?php _e( 'This usually adds counters, analytics and other JS scripts.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php printf( __( 'Insert the code before the closing %s', $this->plugin_options->text_domain ), '&lt;/body&gt;' ) ?></p>
                                </div>
                            </div><!--.option-field-->



                            <div class="option-field-header"><?php _e( 'Minify', $this->plugin_options->text_domain ) ?></div>

                            <div class="option-field auto-enable-false">
                                <label class="option-field-label" for="html_minify">
                                    <?php _e( 'Enable HTML minify', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'minify' ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('html_minify') ?>
                                    <p class="description"><?php _e( 'Reduces page weight about 20-30&#37 by removing line breaks, tabs, spaces, etc. Improve Google PageSpeed scores.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><?php _e( 'After turn on this settings - clear cache if you have.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><?php _e( 'JS scripts are not minified in code, because in 90&#37 of cases minifier braking them.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><?php _e( 'HTML comments are not deleted, because it can brake ad or analytics.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'Minify pages', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><span class="dashicons dashicons-warning wpshop-warning-color"></span> <?php _e( 'Check your site after enable this setting', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description wpshop-red-color"><small>* <?php _e( 'In some cases minifier can\'t work correct - please send report to our technical support.', $this->plugin_options->text_domain ) ?></small></p>
                                </div>
                            </div><!--.option-field-->

                        </div>
                        <div id="clearfy_seo" class="wpshop-tab-in js-wpshop-tab-item">
                            <div class="option-field-header">SEO</div>

                            <div class="option-field">
                                <label class="option-field-label" for="set_last_modified_headers">
                                    <?php _e( 'Automatically set Last Modified', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'last-modified' ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('set_last_modified_headers') ?>
                                    <div class="textarea-block">
                                        <?php _e( 'Exclude pages:', $this->plugin_options->text_domain ) ?>
                                        <?php $this->display_textarea_last_modified('last_modified_exclude') ?>
                                        <p class="description">
                                            <?php printf(__('You can specify a page mask, such as %s or %s. It will exclude all pages than contain string.', $this->plugin_options->text_domain), '/s=', '/cabinet/') ?>
                                            <br>
                                            <?php printf(
                                                __('If you set %s, all pages containing %s will be excluded, including %s, %s, %s, etc.', $this->plugin_options->text_domain),
                                                '<code>cart/</code>',
                                                '<code>cart/</code>',
                                                '<code>cart/process</code>',
                                                '<code>order-cart/</code>',
                                                '<code>check-cart/?get=action</code>'
                                            ) ?>
                                        </p>
                                    </div>
                                    <p class="description">
                                        <?php _e( 'WordPress does not know how to give the server response header Last Modified (date of last modification of the document) and give the correct response 304 Not Modified. And this header is very important for search engines. Its presence accelerates indexing, reduces the load and allows search engines to load more pages at a time in the index.', $this->plugin_options->text_domain ) ?>
                                        <?php if ( $this->is_cyrillic_location() ): ?>
                                        <a href="https://wpshop.ru/blog/last-modified-i-wordpress?utm_source=wp-admin&utm_medium=plugin&utm_campaign=clearfy" target="_blank">Подробнее в нашем блоге</a>.
                                        <?php endif; ?>
                                    </p>
                                    <p class="description">
                                        <strong>Clearfy Pro:</strong> <?php printf( __( 'Set for all posts, pages, archives (categories, tags, etc.) the header %s and returns the correct answer if the page has not been changed.', $this->plugin_options->text_domain ), '<code>Last Modified</code>' ) ?>
                                    </p>
                                    <p class="description danger">
                                        <small>* <?php printf(
                                                __( 'It does not work on your hosting, please, read %s.', $this->plugin_options->text_domain ),
                                                '<a href="https://support.wpshop.ru/faq/clearfy-last-modified-not-working/">' . _x( 'manual', 'last modified manual', $this->plugin_options->text_domain ) . '</a>'
                                            ) ?></small>
                                    </p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="if_modified_since_headers">
                                    <?php _e( 'Set header If-Modified-Since', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'if-modified-since' ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('if_modified_since_headers') ?>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="content_image_auto_alt">
                                    <?php _e( 'Automatically set the alt attribute', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'add-alt' ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('content_image_auto_alt') ?>
                                    <p class="description"><?php _e( 'The most of SEO specialists advise to fill alt attribute. If you missed or did not fill it, it will be automatically assigned and equal the title of article.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php printf( __( 'Add attribute %s to image without it.', $this->plugin_options->text_domain ), '<code>alt</code>' ) ?></p>
                                    <p class="description danger"><small>* <?php printf( __( 'Only works for images in content since it uses %s filter.', $this->plugin_options->text_domain ), 'the_content' ) ?></small></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="noindex_pagination">
                                    <?php _e( 'Noindex for pagination', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'noindex-pagination' ) ?>
                                    <span class="clearfy-recommend"><?php _e( 'Recommended', $this->plugin_options->text_domain ) ?></span>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('noindex_pagination') ?>
                                    <p class="description"><?php _e( 'Pagination pages are included in search engine results /page/2/, /page/3/, etc.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'Add noindex meta tag on pagination pages /page/2/, /page/3/, etc.', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="right_robots_txt">
                                    <?php _e( 'Create an optimized robots.txt', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'robots-txt' ) ?>
                                    <span class="clearfy-recommend"><?php _e( 'Recommended', $this->plugin_options->text_domain ) ?></span>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('right_robots_txt') ?>
                                    <?php if ( file_exists(ABSPATH . 'robots.txt') ) { ?>
                                            <p class="description danger"><strong><?php _e('Attention! A robots.txt was detected.', $this->plugin_options->text_domain); ?></strong>
                                            <br><?php _e('Make a backup of the current robots.txt file and delete it to make this feature work', $this->plugin_options->text_domain); ?></p>
                                    <?php } ?>
                                    <p class="description"><?php _e( 'After installing WP, there is no robots.txt file and you have to create it manually. We\'ve checked more then 30 different best examples to create the perfect robots.txt', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'Automatically creates the perfect robots.txt', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><?php _e('You can change your robots.txt in the box below:', $this->plugin_options->text_domain); ?></p>
                                    <p class="robots-text">
                                        <?php $this->display_textarea_robots('robots_txt_text') ?>
                                    </p>
                                    <p class="description"><?php _e( 'If you want to reset robots.txt to its original position, as with a new Clearfy Pro installation, clear the field above and save your settings.', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field auto-enable-false">
                                <label class="option-field-label" for="redirect_from_http_to_https">
                                    Редирект с http на https
                                    <?php $this->the_help_icon( 'http-https' ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('redirect_from_http_to_https') ?>
                                    <p class="description"><span class="dashicons dashicons-warning wpshop-warning-color"></span> <?php _e('Warning! Before you activate it, make sure that your site opens via https', $this->plugin_options->text_domain); ?></p>
                                    <p class="description"><?php _e( 'If your site uses an SSL certificate, check this box to enable redirection from http to https', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e('It redirects from http to https.', $this->plugin_options->text_domain); ?></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field-header">
                                <?php _e( 'Hide external links', $this->plugin_options->text_domain ) ?>
                            </div>

                            <div class="option-field">
                                <label class="option-field-label" for="hide_external_links_content">
                                    <?php _e( 'Hide in content', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'hide-external-links-content' ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('hide_external_links_content') ?>
                                    <p class="description"><?php _e( 'Converts external links in selected post types into pseudo links.', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label">
                                    <?php _e( 'Post types', $this->plugin_options->text_domain ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php
                                    $external_links_post_types = ( ! empty( $options['hide_external_links_post_types'] ) && is_array( $options['hide_external_links_post_types'] ) )
                                        ? $options['hide_external_links_post_types']
                                        : [ 'post' ];
                                    $post_types = get_post_types( [ 'public' => true ], 'objects' );
                                    $excluded_post_types = [ 'attachment', 'revision', 'nav_menu_item', 'custom_css', 'customize_changeset', 'oembed_cache', 'user_request', 'wp_block', 'wp_template', 'wp_template_part', 'wp_navigation', 'wp_global_styles' ];
                                    foreach ( $post_types as $post_type_key => $post_type_obj ) {
                                        if ( in_array( $post_type_key, $excluded_post_types, true ) ) {
                                            continue;
                                        }
                                        echo '<label>';
                                        echo '<input type="checkbox" name="clearfy_option[hide_external_links_post_types][]" value="' . esc_attr( $post_type_key ) . '" ' . checked( in_array( $post_type_key, $external_links_post_types, true ), true, false ) . '>';
                                        echo ' ' . esc_html( $post_type_obj->labels->name ) . '</label><br>';
                                    }
                                    ?>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="comment_text_convert_links_pseudo">
                                    <?php _e( 'Hide in comments text', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'hide-comment-links' ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('comment_text_convert_links_pseudo') ?>
                                    <p class="description"><?php _e( 'Converts external links in comment text into pseudo links.', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="pseudo_comment_author_link">
                                    <?php _e( 'Hide comment author links', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'hide-author-link' ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('pseudo_comment_author_link') ?>
                                    <p class="description"><?php _e( 'Converts comment author website links into pseudo links.', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="hide_external_links_excluded_urls">
                                    <?php _e( 'Exclude links', $this->plugin_options->text_domain ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_textarea( 'hide_external_links_excluded_urls', [ 'rows' => 5 ] ) ?>
                                    <p class="description"><?php _e( 'One pattern per line. If a URL contains this part, it will not be converted. You can specify a full URL or only part of an address (for example: example.com, /go/, partner-site).', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="hide_external_links_excluded_post_ids">
                                    <?php _e( 'Disable by post IDs', $this->plugin_options->text_domain ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_input_text( 'hide_external_links_excluded_post_ids' ) ?>
                                    <p class="description"><?php _e( 'Enter post IDs separated by commas. For these posts/pages, links will not be converted in content.', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field-header"><?php _e( 'Pseudo links settings', $this->plugin_options->text_domain ) ?></div>

                            <div class="option-field">
                                <label class="option-field-label" for="pseudo_links_class">
                                    <?php _e( 'Link class', $this->plugin_options->text_domain ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_input_text( 'pseudo_links_class', [ 'default' => 'pseudo-clearfy-link' ] ) ?>
                                    <p class="description"><?php _e( 'You can set your own unique class. Avoid overly generic names like "link".', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="pseudo_links_color">
                                    <?php _e( 'Link color', $this->plugin_options->text_domain ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_color( 'pseudo_links_color', [ 'default' => '#0058cf' ] ) ?>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="pseudo_links_underline">
                                    <?php _e( 'Underline', $this->plugin_options->text_domain ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('pseudo_links_underline') ?>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="pseudo_links_hover_color">
                                    <?php _e( 'Hover color', $this->plugin_options->text_domain ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_color( 'pseudo_links_hover_color', [ 'default' => '#2900cf' ] ) ?>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="pseudo_links_hover_underline">
                                    <?php _e( 'Underline on hover', $this->plugin_options->text_domain ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('pseudo_links_hover_underline') ?>
                                </div>
                            </div><!--.option-field-->


                            <?php if ( $this->is_yoast_seo_enabled() ) : ?>

                            <div class="option-field-header"><?php _e('For Yoast SEO plugin', $this->plugin_options->text_domain); ?></div>


                            <div class="option-field">
                                <label class="option-field-label" for="remove_last_item_breadcrumb_yoast">
                                    <?php _e( 'Remove last duplicate title in breadcrumbs WP SEO by Yoast', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'yoast-breadcrumbs-remove-last' ) ?>
                                    <span class="clearfy-recommend"><?php _e( 'Recommended', $this->plugin_options->text_domain ) ?></span>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('remove_last_item_breadcrumb_yoast') ?>
                                    <p class="description"><?php _e( 'Last element in breadcrubms in Yoast SEO plugin duplicate article title. Some SEO specialists thinks that it\'s worse for optimization.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy:</strong> <?php _e( 'Removes duplicate title in breadcrumbs WP SEO by Yoast', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="replace_last_item_breadcrumb_yoast_on_title">
                                    <?php _e( 'Replace the title of an entry with the title in WP SEO Yoast breadcrumbs', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'yoast-breadcrumbs-replace-title' ) ?>
                                    <span class="clearfy-recommend"><?php _e( 'Recommended', $this->plugin_options->text_domain ) ?></span>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('replace_last_item_breadcrumb_yoast_on_title') ?>
                                    <p class="description"><?php _e( 'In the last element of the breadcrumbs plugin Yoast SEO displays the name of the record. To avoid duplication, you can replace it with the title of the record.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'Replaces the name of the record with the title of the record in the breadcrumbs of the WP SEO Yoast plugin', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="yoast_remove_image_from_xml_sitemap">
                                    <?php printf( __( 'Remove tag %s from XML sitemap', $this->plugin_options->text_domain ), '&lt;image:image&gt;' ); ?>
                                    <?php $this->the_help_icon( 'yoast-xml-image' ) ?>
                                    <span class="clearfy-recommend"><?php _e( 'Recommended', $this->plugin_options->text_domain ) ?></span>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('yoast_remove_image_from_xml_sitemap') ?>
                                    <p class="description"><?php printf( __( 'Yandex.Webmaster is fighting the standard XML map from the Yoast plugin, because it has a specific tag %s Read more on our blog.', $this->plugin_options->text_domain ), '&lt;image:image&gt;' ); ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php printf( __( 'Remove tag %s from XML sitemap plugin Yoast SEO.', $this->plugin_options->text_domain ), '&lt;image:image&gt;' ); ?></p>
                                    <p class="description danger"><strong><?php _e('Warning!', $this->plugin_options->text_domain); ?></strong> <?php _e('After activation, turn off the sitemap and turn it back on to regenerate it.', $this->plugin_options->text_domain); ?></p>
                                    <p class="description danger"><small><?php _e('* On older versions of Yoast SEO may not work - update the Yoast plugin', $this->plugin_options->text_domain); ?></small></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="yoast_remove_head_comment">
                                    <?php printf( __( 'Delete a comment from a section %s', $this->plugin_options->text_domain ), '&lt;head&gt;' ); ?>
                                    <?php $this->the_help_icon( 'yoast-remove-comment' ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('yoast_remove_head_comment') ?>
                                    <p class="description">
                                        <?php printf( __( 'Yoast SEO plugin displays a comment of the kind %s in a section %s', $this->plugin_options->text_domain ), '&lt;!-- This site is optimized with the Yoast SEO plugin v3.1.1 - https://yoast.com/wordpress/plugins/seo/ --&gt;', '&lt;head&gt;' ); ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php printf( __( 'Removes the Yoast SEO plugin comment from the section %s.', $this->plugin_options->text_domain ), '&lt;head&gt;' ); ?></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="yoast_canonical_pagination">
                                    <?php _e( 'Canonical in pagination pages', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'yoast-paged-canonical' ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox( 'yoast_canonical_pagination' ) ?>
                                    <p class="description"><?php _e( 'Yoast SEO plugin displays canonical links /page/2/, /page/3/, etc. on pagination pages.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'Changes the canonical link to the homepage or the rubric itself.', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="yoast_application_ld_json">
                                    <?php _e( 'Remove application/ld+json', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'yoast-json-ld' ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox( 'yoast_application_ld_json' ) ?>
                                    <p class="description"><?php _e( 'JSON-LD is a markup format. Plugin outputs in the header information about the site and a link to the search using this format.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'Disables the output of application/ld+json code in the site header.', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->


                            <?php else: ?>

                                <div class="option-field-header">
                                    <?php printf( __( 'Settings for %s are hidden because the plugin is not active at the moment.', $this->plugin_options->text_domain ), 'Yoast SEO' ) ?>
                                </div>

                            <?php endif; ?>




                            <?php if ( $this->is_rank_math_enabled() ) : ?>

                            <div class="option-field-header"><?php _e('For Rank Math plugin', $this->plugin_options->text_domain); ?></div>

                            <div class="option-field">
                                <label class="option-field-label" for="rank_math_white_label">
                                    <?php _e( 'Enable white label', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'rank-math-white-label' ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox( 'rank_math_white_label' ) ?>
                                    <p class="description"><?php _e( 'Rank Math adds comments in HTML, links, social media links to source code.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'Remove all HTML comments, links, social media links about Rank Math.', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="rank_math_canonical_pagination">
                                    <?php _e( 'Canonical in pagination pages', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'rank-math-paged-canonical' ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox( 'rank_math_canonical_pagination' ) ?>
                                    <p class="description"><?php _e( 'Rank Math plugin displays canonical links /page/2/, /page/3/, etc. on pagination pages.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'Changes the canonical link to the homepage or the rubric itself.', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="rank_math_application_ld_json">
                                    <?php _e( 'Remove application/ld+json', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'rank-math-json-ld' ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox( 'rank_math_application_ld_json' ) ?>
                                    <p class="description"><?php _e( 'JSON-LD is a markup format. Plugin outputs in the header information about the site and a link to the search using this format.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'Disables the output of application/ld+json code in the site header.', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->

                            <?php else: ?>

                            <div class="option-field-header">
                                <?php printf( __( 'Settings for %s are hidden because the plugin is not active at the moment.', $this->plugin_options->text_domain ), 'Rank Math' ) ?>
                            </div>

                            <?php endif; ?>


                        </div>
                        <div id="clearfy_double" class="wpshop-tab-in js-wpshop-tab-item">
                            <div class="option-field-header"><?php _e( 'Duplicate pages', $this->plugin_options->text_domain ) ?></div>


                            <div class="option-field">
                                <label class="option-field-label" for="redirect_archives_date">
                                    <?php _e( 'Delete date archives', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'date-duplicates' ) ?>
                                    <span class="clearfy-recommend"><?php _e( 'Recommended', $this->plugin_options->text_domain ) ?></span>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('redirect_archives_date') ?>
                                    <p class="description"><?php _e( 'A huge number of duplicates in date archives. Imagine, besides the fact that your article will be displayed on the main page and in the category, you will get at least 3 doubles: in the archives by year, month and date, for example /2016/ /2016/02/ /2016/02/15.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'Deletes the date archives completely and sets a redirect.', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="redirect_archives_author">
                                    <?php _e( 'Delete user archives', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'user-duplicates' ) ?>
                                    <span class="clearfy-recommend"><?php _e( 'Recommended', $this->plugin_options->text_domain ) ?></span>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('redirect_archives_author') ?>
                                    <p class="description"><?php _e( 'If you are the only one filling the site, it\'s a must. Allows you to get rid of duplicates on user archives, such as /author/admin/.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'Deletes user archives completely and puts a redirect.', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="redirect_archives_tag">
                                    <?php _e( 'Delete tag archives', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'tag-duplicates' ) ?>
                                    <span class="clearfy-recommend"><?php _e( 'Recommended', $this->plugin_options->text_domain ) ?></span>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('redirect_archives_tag') ?>
                                    <p class="description"><?php _e( 'If you use tags only for the Related posts block or don\'t use them at all, it\'s better to close them to avoid duplicates.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'It redirects from the tag pages to the main page.', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="attachment_pages_redirect">
                                    <?php _e( 'Delete attachment pages', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'attachment-duplicates' ) ?>
                                    <span class="clearfy-recommend"><?php _e( 'Recommended', $this->plugin_options->text_domain ) ?></span>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('attachment_pages_redirect') ?>
                                    <p class="description"><?php _e( 'Each uploaded picture has its own page on the site, consisting of only one picture. Such pages are successfully indexed and create duplicates. The site may have thousands of pages of the same type of attachments.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'Deletes attachment pages and puts a redirect on the entry.', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="remove_single_pagination_duplicate">
                                    <?php _e( 'Remove duplicate pagination of posts', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'post-pagination-duplicates' ) ?>
                                    <span class="clearfy-recommend"><?php _e( 'Recommended', $this->plugin_options->text_domain ) ?></span>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('remove_single_pagination_duplicate') ?>
                                    <p class="description"><?php _e( 'In WordPress, any entry can be divided into parts (pages), each part has its own address. But this functionality is rarely used, but can cause you trouble. For example, to the address of any record of your blog can add a number, /privet-mir/1/ - will open the record itself, which will be a double. The number can be any number.', $this->plugin_options->text_domain ) ?></p>
                                    <?php if ( version_compare( get_bloginfo( 'version' ), '5.5', '>=' ) ) { echo '<p class="description">' . __( 'The option can not be included, because with WordPress 5.5 fixed in the core.', $this->plugin_options->text_domain ) . '</p>'; } ?>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'It redirects to the entry itself.', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="remove_replytocom">
                                    <?php _e( 'Delete ?replytocom', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'replytocom-duplicates' ) ?>
                                    <span class="clearfy-recommend"><?php _e( 'Recommended', $this->plugin_options->text_domain ) ?></span>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('remove_replytocom') ?>
                                    <p class="description"><?php _e( 'WordPress adds ?replytocom= to the Reply link in comments if tree comments are enabled', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'Deletes ?relpytocom and puts a redirect on the entry', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->


                        </div>
                        <div id="clearfy_security" class="wpshop-tab-in js-wpshop-tab-item">
                            <div class="option-field-header"><?php _e( 'Security', $this->plugin_options->text_domain ) ?></div>

                            <div class="option-field">
                                <label class="option-field-label" for="cloud_protection">
                                    <?php _e( 'Cloud site protection', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'cloud-protection' ) ?>
                                    <sup style="color: #143bd6; font-size: .8em;">🔥 beta</sup>
                                    <br>
                                    <span style="color: #165ff4;font-size: .9em;font-weight: 600;">Clearfy Cloud+</span><br>
                                    <span class="clearfy-recommend"><?php _e( 'Recommended', $this->plugin_options->text_domain ) ?></span>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('cloud_protection') ?>
                                    <p class="description"><?php _e('All websites are constantly under attack by bots looking for vulnerabilities. These include attempts to find security holes, authorization attempts, opportunities to upload viruses to the site, find backups, logs, and so on. To avoid this, we decided to launch a cloud-based protection service.', 'clearfy-pro'); ?></p>
                                    <p class="description"><?php printf( __( 'Сейчас сервис бесплатный, но в следующем обновлении будет работать в единой подписке за <a href="%s" target="_blank" rel="noopener">лимиты</a>.', 'clearfy-pro' ), 'https://wpshop.ru/limits' ); ?></p>
                                    <p class="description"><strong style="color: #165ff4;">Clearfy Cloud+:</strong> <?php _e( 'Включает облачную защиту сайта в бета-режиме. Собирает, анализирует и блокирует опасные запросы и ботов, ищущих уязвимости.', 'clearfy-pro' ) ?></p>
                                    <?php
                                    $cloud = new \WPShop\ClearfyPro\ClearfyCloud();
                                    $last_sync = $cloud->get_last_feed_sync();
                                    $cloud_ips = $cloud->get_cloud_ips_for_admin( 300 );
                                    $cloud_stats = $cloud->get_stats_summary_for_admin();
                                    $cloud_ips_count = is_array( $cloud_ips ) ? count( $cloud_ips ) : 0;
                                    ?>
                                    <p class="description">
                                        <strong><?php _e( 'Last bad IP feed update:', $this->plugin_options->text_domain ) ?></strong>
                                        <?php
                                        if ( $last_sync > 0 ) {
                                            echo esc_html( wp_date( 'd.m.Y H:i', $last_sync ) );
                                        } else {
                                            _e( 'Never', $this->plugin_options->text_domain );
                                        }
                                        ?>
                                    </p>
                                    <p class="description">
                                        <strong><?php _e( 'Bad IPs in current list:', $this->plugin_options->text_domain ) ?></strong>
                                        <?php echo (int) $cloud_ips_count; ?>
                                    </p>
                                    <div style="display:grid; grid-template-columns:repeat(3,minmax(180px,1fr)); gap:10px; margin:10px 0 12px;">
                                        <div style="border:1px solid #dcdcde; border-radius:8px; padding:10px 12px; background:#fff;">
                                            <div style="font-size:12px; color:#646970; margin-bottom:6px;"><?php _e( 'Last 24 hours', $this->plugin_options->text_domain ) ?></div>
                                            <div style="font-size:13px; line-height:1.5;">
                                                <div><?php _e( 'Blocked:', $this->plugin_options->text_domain ) ?> <strong><?php echo (int) $cloud_stats['24h']['blocked_count']; ?></strong></div>
                                                <div><?php _e( 'Captcha shown:', $this->plugin_options->text_domain ) ?> <strong><?php echo (int) $cloud_stats['24h']['captcha_shown_count']; ?></strong></div>
                                                <div><?php _e( 'Captcha passed:', $this->plugin_options->text_domain ) ?> <strong><?php echo (int) $cloud_stats['24h']['captcha_passed_count']; ?></strong></div>
                                                <div><?php _e( 'Captcha failed:', $this->plugin_options->text_domain ) ?> <strong><?php echo (int) $cloud_stats['24h']['captcha_failed_count']; ?></strong></div>
                                            </div>
                                        </div>
                                        <div style="border:1px solid #dcdcde; border-radius:8px; padding:10px 12px; background:#fff;">
                                            <div style="font-size:12px; color:#646970; margin-bottom:6px;"><?php _e( 'Last 30 days', $this->plugin_options->text_domain ) ?></div>
                                            <div style="font-size:13px; line-height:1.5;">
                                                <div><?php _e( 'Blocked:', $this->plugin_options->text_domain ) ?> <strong><?php echo (int) $cloud_stats['30d']['blocked_count']; ?></strong></div>
                                                <div><?php _e( 'Captcha shown:', $this->plugin_options->text_domain ) ?> <strong><?php echo (int) $cloud_stats['30d']['captcha_shown_count']; ?></strong></div>
                                                <div><?php _e( 'Captcha passed:', $this->plugin_options->text_domain ) ?> <strong><?php echo (int) $cloud_stats['30d']['captcha_passed_count']; ?></strong></div>
                                                <div><?php _e( 'Captcha failed:', $this->plugin_options->text_domain ) ?> <strong><?php echo (int) $cloud_stats['30d']['captcha_failed_count']; ?></strong></div>
                                            </div>
                                        </div>
                                        <div style="border:1px solid #dcdcde; border-radius:8px; padding:10px 12px; background:#fff;">
                                            <div style="font-size:12px; color:#646970; margin-bottom:6px;"><?php _e( 'All time', $this->plugin_options->text_domain ) ?></div>
                                            <div style="font-size:13px; line-height:1.5;">
                                                <div><?php _e( 'Blocked:', $this->plugin_options->text_domain ) ?> <strong><?php echo (int) $cloud_stats['all']['blocked_count']; ?></strong></div>
                                                <div><?php _e( 'Captcha shown:', $this->plugin_options->text_domain ) ?> <strong><?php echo (int) $cloud_stats['all']['captcha_shown_count']; ?></strong></div>
                                                <div><?php _e( 'Captcha passed:', $this->plugin_options->text_domain ) ?> <strong><?php echo (int) $cloud_stats['all']['captcha_passed_count']; ?></strong></div>
                                                <div><?php _e( 'Captcha failed:', $this->plugin_options->text_domain ) ?> <strong><?php echo (int) $cloud_stats['all']['captcha_failed_count']; ?></strong></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div><!--.option-field-->


                            <div class="option-field">
                                <label class="option-field-label" for="hide_wp_login">
                                    <?php _e( 'Hide wp-login.php', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'hide-wp-login' ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('hide_wp_login') ?>
                                    <p class="description"><?php _e( 'You can hide the login page wp-login.php, and replace its address with the new one.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description danger"><?php _e( 'Important: Be sure to memorize and write down the new login page. After activating this option - the old login page will be unavailable.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><?php _e( 'If possible, do not use the words login, sign-in, admin, dashboard.', $this->plugin_options->text_domain ) ?></p>
                                    <?php echo home_url() ?>/<?php if ( ! get_option( 'permalink_structure' ) ) echo '?' ?>
                                    <?php $this->display_input_text( 'hide_wp_login_new_slug' ) ?>
                                    <?php if ( get_option( 'permalink_structure' ) ) echo '/' ?>
                                    <p class="description">
                                        <?php _e( 'You can use the ones just generated for you:', $this->plugin_options->text_domain ) ?><br>
                                        <code><?php echo Clearfy_Hide_Admin::generate_login_page_slug(6) ?></code>
                                        <code><?php echo Clearfy_Hide_Admin::generate_login_page_slug(10) ?></code>
                                        <code><?php echo Clearfy_Hide_Admin::generate_login_page_slug(15) ?></code>
                                    </p>
                                    <p class="description danger"><?php _e( 'If you forget the address of the new page - you will have to either delete the plugin or use the hook from the ', $this->plugin_options->text_domain ) ?><a href="<?php echo $this->get_help_url( 'hide-wp-login-disable' ) ?>" target="_blank" rel="noopener noreferrer"><?php _e( 'documentation', $this->plugin_options->text_domain ) ?></a>.</p>
                                </div>
                            </div><!--.option-field-->


                            <div class="option-field">
                                <label class="option-field-label" for="protect_author_get">
                                    <?php _e( 'Remove the ability to know the username of the administrator', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'remove-author-get' ) ?>
                                    <span class="clearfy-recommend"><?php _e( 'Recommended', $this->plugin_options->text_domain ) ?></span>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('protect_author_get') ?>
                                    <p class="description"><?php printf( __( 'Have you changed your username from admin to another so that intruders do not know your login? Don\'t get excited, type in the address bar %s and you will be redirected to the author\'s page in 90&#37 of cases %s, thus giving away your login.', $this->plugin_options->text_domain ), '<code>yousite.com/?author=1</code>','<code>/author/alexey</code>' ); ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'Removes the ability to find out the username of the administrator', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="change_login_errors">
                                    <?php _e( 'Hide login errors', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'hide-login-errors' ) ?>
                                    <span class="clearfy-recommend"><?php _e( 'Recommended', $this->plugin_options->text_domain ) ?></span>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('change_login_errors') ?>
                                    <p class="description"><?php _e( 'WP by default shows if you entered the wrong username or the wrong password, which gives attackers to understand if a particular user exists on the site, and then start brute-forcing passwords.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( ' Changes the text of the error so that attackers can not pick up the login.', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="remove_x_pingback">
                                    <?php _e( 'Disable xmlrpc.php', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'disable-xml-rpc' ) ?>
                                    <span class="clearfy-recommend"><?php _e( 'Recommended', $this->plugin_options->text_domain ) ?></span>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('remove_x_pingback') ?>
                                    <p class="description"><?php _e( 'One of the reasons why your site on WP started to slow down is an attack on the site, when there are a lot of requests to the file xmlrpc.php, which is responsible for pingback\'s, remote access to WP. Through the file xmlrpc.php can go DDoS or Brutforce attack.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'It removes the link to xmlrpc.php in the server response, and closes the possibility of spamming the site with pingbacks.', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->


                            <?php
                            $class_login_attempts = new Clearfy_Login_Attempts( $this->plugin_options );
                            $class_login_attempts->clean(null);
                            ?>
                            <div class="option-field-header"><?php _e( 'Password brute force protection', $this->plugin_options->text_domain ) ?></div>

                            <div class="option-field">
                                <label class="option-field-label" for="login_attempts">
                                    <?php _e( 'Limit the number of attempts', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'login-attempts' ) ?>
                                    <br><small style="color: red">beta</small>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('login_attempts') ?>
                                    <p class="description"><?php _e( 'Bruteforcing or brute-forcing passwords to sites happens all the time. In addition to potential hacking, you get a constant load on the server.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description danger"><?php _e( 'If you forget your username/password and get locked out - ', $this->plugin_options->text_domain ) ?><a href="<?php echo $this->get_help_url( 'login-attempts-disable' ) ?>" target="_blank" rel="noopener noreferrer"><?php _e( 'this protection can be disabled', $this->plugin_options->text_domain ) ?></a>.</p>
                                    <p class="description"><?php _e( 'After ', $this->plugin_options->text_domain ) ?><?php $this->display_input_number('login_attempts_allowed_retries', [ 'default' => $this->plugin_options->get_default_option('login_attempts_allowed_retries') ] ) ?><?php _e( '  incorrect attempts to enter will be blocked for ', $this->plugin_options->text_domain ) ?><?php $this->display_input_number('login_attempts_lockout_duration',  [ 'default' => $this->plugin_options->get_default_option('login_attempts_lockout_duration') ] ) ?><?php _e( ' minutes.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><?php _e( 'After ', $this->plugin_options->text_domain ) ?><?php $this->display_input_number('login_attempts_allowed_lockouts', [ 'default' => $this->plugin_options->get_default_option('login_attempts_allowed_lockouts') ] ) ?><?php _e( ' blockings, access will be blocked for ', $this->plugin_options->text_domain ) ?><?php $this->display_input_number('login_attempts_long_duration',  [ 'default' => $this->plugin_options->get_default_option('login_attempts_long_duration') ] ) ?><?php _e( ' hours.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description">
                                        <strong>Clearfy Pro:</strong> <?php _e( 'Protects the site from password brute-forcing.', $this->plugin_options->text_domain ) ?>

                                        <?php
                                        if ( $class_login_attempts->get_total_lockouts() ) {
                                            echo __( 'It was already blocked: ', $this->plugin_options->text_domain ) . $class_login_attempts->get_total_lockouts();
                                        }
                                        ?>
                                    </p>
                                </div>
                            </div><!--.option-field-->


                            <div class="option-field">
                                <label class="option-field-label" for="login_attempts_whitelist">
                                    <?php _e( 'IP Whitelist', $this->plugin_options->text_domain ) ?>
                                </label>
                                <div class="option-field-body">
                                    <div class="textarea-block">
                                        <?php $this->display_textarea('login_attempts_whitelist') ?>
                                        <p class="description"><?php _e( 'Add here the IPs that will not be restricted. 1 line - 1 IP.', $this->plugin_options->text_domain ) ?></p>
                                    </div>
                                </div>
                            </div><!--.option-field-->


                            <div class="option-field">
                                <label class="option-field-label" for="login_attempts_blacklist">
                                    <?php _e( 'IP Blacklist', $this->plugin_options->text_domain ) ?>
                                </label>
                                <div class="option-field-body">
                                    <div class="textarea-block">
                                        <?php $this->display_textarea('login_attempts_blacklist') ?>
                                        <p class="description"><?php _e( 'Add here the IPs to be blocked. 1 line - 1 IP.', $this->plugin_options->text_domain ) ?></p>
                                    </div>
                                </div>
                            </div><!--.option-field-->

                            <?php if ( ! empty( $class_login_attempts->get_lockouts() ) && is_array( $class_login_attempts->get_lockouts() ) ) : ?>

                                <div class="option-field">
                                    <label class="option-field-label" for="">
                                        <?php _e( 'Current lockouts', $this->plugin_options->text_domain ) ?>
                                    </label>
                                    <div class="option-field-body">

                                        <table class="wpshop-table clearfy-table-404">
                                            <thead>
                                            <tr>
                                                <th>IP</th>
                                                <th><?php _e( 'Expired', $this->plugin_options->text_domain ) ?></th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            foreach ( $class_login_attempts->get_lockouts() as $lockout_ip => $lockout ) :
                                                $delta = ceil( ($lockout - time() ) / 60 );
                                                if ( $delta > 60 ) {
                                                    $delta = ceil( $delta / 60 ) . __( 'h.', $this->plugin_options->text_domain );
                                                } else {
                                                    $delta = $delta . __( 'm.', $this->plugin_options->text_domain );
                                                }
                                                ?>
                                            <tr>
                                                <td><?php echo $lockout_ip ?></td>
                                                <td><?php echo $delta ?></td>
                                                <td><span class="button js-clearfy-remove-lockout" data-ip="<?php echo esc_attr( $lockout_ip ) ?>" data-nonce="<?php echo wp_create_nonce( 'clearfy_remove_lockout_nonce' ) ?>"><?php _e( 'Remove', $this->plugin_options->text_domain ) ?></span></td>
                                            </tr>
                                            <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div><!--.option-field-->

                            <?php endif; ?>

                            <div class="option-field-header"><?php _e( 'Versions', $this->plugin_options->text_domain ) ?></div>

                            <div class="option-field">
                                <label class="option-field-label" for="remove_meta_generator">
                                    <?php _e( 'Remove meta generator', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'remove-meta-generator' ) ?>
                                    <span class="clearfy-recommend"><?php _e( 'Recommended', $this->plugin_options->text_domain ) ?></span>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('remove_meta_generator') ?>
                                    <p class="description"><?php _e( 'Allows attackers to find out the WP version installed on the site. This meta tag has no useful function.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php printf( __( 'Removes the meta tag from the section  %s', $this->plugin_options->text_domain ), '<code>&lt;head&gt;</code>' ); ?></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="remove_versions_styles">
                                    <?php _e( 'Remove version from styles', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'remove-version-styles' ) ?>
                                    <span class="clearfy-recommend"><?php _e( 'Recommended', $this->plugin_options->text_domain ) ?></span>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('remove_versions_styles') ?>
                                    <p class="description"><?php printf( __( 'WP, themes and plugins often include styles with the version of the file, plugin or engine, it looks like this: %s. First, this allows attackers to know the version of the plugin, the engine, and secondly, disables caching for these files, which reduces the time it takes to load the page.', $this->plugin_options->text_domain ), '<code>?ver=4.7.5</code>' ); ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'Deletes versions of styles', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="remove_versions_scripts">
                                    <?php _e( 'Remove the version from the scripts', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'remove-version-scripts' ) ?>
                                    <span class="clearfy-recommend"><?php _e( 'Recommended', $this->plugin_options->text_domain ) ?></span>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('remove_versions_scripts') ?>
                                    <p class="description"><?php printf( __( 'As with styles, scripts are connected by specifying the version of the file, plugin or engine, it looks like this: %s. Firstly, this allows attackers to know the version of the plugin, the engine, and secondly, disables caching for these files, which reduces the page load time.', $this->plugin_options->text_domain ), '<code>?ver=4.7.5</code>' ); ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'Removes versions from scripts', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->

                        </div>






                        <div id="clearfy_modules" class="wpshop-tab-in js-wpshop-tab-item">

                            <div class="option-field-header"><?php _e( 'Transliteration', $this->plugin_options->text_domain ) ?></div>

                            <div class="option-field">
                                <label class="option-field-label" for="sanitize_title">
                                    <?php _e( 'Transliteration of headers and files', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'transliteration' ) ?>
                                </label>
                                <div class="option-field-body">
			                        <?php $this->display_checkbox('sanitize_title') ?>
                                    <p class="description"><?php _e( 'Analogue to Rus To Lat, Cyr2Lat and other plugins. Transliteration of permanent links and file names. For example, the post "hello world!" becomes "privet-mir", and the file "picture.jpg" becomes "kartinka.jpg".', $this->plugin_options->text_domain ) ?></p>

                                    <p class="description"><?php _e( 'Attention!', $this->plugin_options->text_domain ) ?> <?php _e( 'The plugin does not automatically translate existing slugs. Click the button below to transliterate them', $this->plugin_options->text_domain ) ?>:</p>
                                    <p><span class="button js-clearfy-transliterate-existing-slugs" data-nonce="<?php echo wp_create_nonce( 'clearfy_transliterate_existing_slugs_nonce' ) ?>"><?php _e( 'Transliterate existing slugs', $this->plugin_options->text_domain ) ?></span></p>

                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'Performs transliteration of permanent links and downloadable files.', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->




                            <div class="option-field-header">
                                <?php _e( 'Avatars', $this->plugin_options->text_domain )?>
                                <?php $this->the_help_icon( 'avatars' ) ?>
                            </div>

                            <div class="option-field">
                                <label class="option-field-label" for="disable_gravatar">
                                    <?php _e( 'Disable gravatars', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'disable-gravatar' ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('disable_gravatar') ?>
                                    <p class="description"><?php _e( 'As avatars in WP are automatically displayed gravatars from gravatar.com, an unnecessary external resource to download.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><?php _e( 'You can specify the address of any of your images, or leave this field blank.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><?php printf( __( 'You can create our own unique avatar on %s', $this->plugin_options->text_domain ), '<a href="https://wpavatar.ru/?utm_source=plugin&utm_medium=clearfy&utm_campaign=disable_gravatar" target="_blank">WPAvatar ↗</a>' ) ?></p>

                                    <p>
                                        <span class="button js-upload-avatar"><?php _e( 'Upload avatar', $this->plugin_options->text_domain ) ?></span>
                                        <?php $this->display_input_text( 'disable_gravatar_avatar_url' ) ?>
                                    </p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'Disables gravatars and displays the default image as an avatar.', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="disable_gravatar">
                                    <?php _e( 'Enable local avatars', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'local-avatars' ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('local_avatars') ?>
                                    <p class="description"><?php _e( 'Enable the ability for registered users to upload their avatars on the user edit page.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'Adds image uploading for local avatars on profile edit page.', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->





                            <div class="option-field-header"><?php _e( 'Comments', $this->plugin_options->text_domain ) ?></div>

                            <div class="option-field">
                                <label class="option-field-label" for="disable_comments">
			                        <?php _e( 'Disables comments', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'disable-comments' ) ?>
                                </label>
                                <div class="option-field-body">
			                        <?php $this->display_checkbox('disable_comments') ?>
                                    <p class="description"><?php _e( 'Select post types to disable comments:', $this->plugin_options->text_domain ) ?></p>

			                        <?php

			                        $post_types = get_post_types([
				                        'public' => true,
				                        '_builtin' => true,
			                        ], 'objects');

			                        $disable_comments_post_types = ( ! empty( $options['disable_comments_post_types'] ) ) ? $options['disable_comments_post_types'] : [];
			                        foreach ($post_types as $key => $value) {
				                        echo '<p><label>';
				                        echo '<input type="checkbox" name="clearfy_option[disable_comments_post_types][]" value="' . esc_attr($key) . '" ' . checked(in_array($key, $disable_comments_post_types), true, false) . '>';
				                        echo $value->labels->name;
				                        echo '</label></p>';
			                        }
			                        ?>

                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'Fully or partially disables commenting.', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->


                            <div class="option-field">
                                <label class="option-field-label" for="disable_comments_xml_rpc">
			                        <?php _e( 'Disable XML-RPC comments', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'disable-comments-xml-rpc' ) ?>
                                </label>
                                <div class="option-field-body">
			                        <?php $this->display_checkbox('disable_comments_xml_rpc') ?>
                                    <p class="description"><?php _e( 'Comments in WordPress can be added through external applications using XML-RPC.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'Disables commenting via XML-RPC.', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->


                            <div class="option-field">
                                <label class="option-field-label" for="disable_comments_rest_api">
			                        <?php _e( 'Disable REST API comments', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'disable-comments-rest-api' ) ?>
                                </label>
                                <div class="option-field-body">
			                        <?php $this->display_checkbox('disable_comments_rest_api') ?>
                                    <p class="description"><?php _e( 'Comments in WordPress can be added through the REST API.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'Disables commenting via the REST API.', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->


                            <div class="option-field">
                                <label class="option-field-label" for="disable_comments_interface">
			                        <?php _e( 'Remove from interface', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'disable-comments-interface' ) ?>
                                </label>
                                <div class="option-field-body">
			                        <?php $this->display_checkbox('disable_comments_interface') ?>
                                    <p class="description"><?php _e( 'Remove comments from all menus, admin bar, widgets, feeds etc.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'Removes comments from interface.', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->



                            <div class="option-field">
                                <label class="option-field-label" for="remove_url_from_comment_form">
                                    <?php _e( 'Remove the "Site" field in the comment form', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'disable-site-field' ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('remove_url_from_comment_form') ?>
                                    <p class="description"><?php _e( 'Tired of spam comments? Visitors leaving "empty" comments for a link to your site?', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'Removes the "Site" field from the comment form.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description danger"><small><?php _e( '* Works with the standard commenting form, if your topic has a manually prescribed form - most likely will not work!', $this->plugin_options->text_domain ) ?></small></p>
                                </div>
                            </div><!--.option-field-->


                            <div class="option-field">
                                <label class="option-field-label" for="disable_comments_email_field">
                                    <?php _e( 'Hide email field', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'disable-comments-email-field' ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('disable_comments_email_field') ?>
                                    <p class="description"><?php _e( 'Removes the email field from the comment form.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'This option hides the email field completely and disables its validation.', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->


                            <div class="option-field">
                                <label class="option-field-label" for="disable_comments_save_ip">
                                    <?php _e( 'Disable IP address logging', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'disable-comments-save-ip' ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('disable_comments_save_ip') ?>
                                    <p class="description"><?php _e( 'Prevents WordPress from saving commenters’ IP addresses to the database. This helps protect users’ personal data and supports GDPR compliance.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'Also removes IP logging from custom forms and plugins.', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div>
                            <div class="option-field-header">
                                <?php _e( 'Content protection', $this->plugin_options->text_domain ) ?>
                                <?php $this->the_help_icon( 'content-protection' ) ?>
                            </div>

                            <div class="option-field">
                                <label class="option-field-label" for="copy_source_link">
                                    <?php _e( 'Link to source when copying', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'content-protection-source-link' ) ?>
                                </label>
                                <div class="option-field-body">
			                        <?php $this->display_checkbox('copy_source_link') ?>
                                    <div><?php $this->display_input_text('copy_source_link_text', array( 'default' => __( '<br>Source: %link%', $this->plugin_options->text_domain ) )) ?></div>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'Adds a link to the source of the article when copying text. Be sure to add the word: %link% it will be replaced by the link. &lt;br&gt; is a line break.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><?php _e( 'For example:', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description">&lt;br&gt;<?php _e( 'Source: %link%', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><?php _e( '- Read more at: %link%', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description">
                                        <a href="https://support.wpshop.ru/faq/clearfy-disable-content-protection/" target="_blank" rel="noopener">
                                            <?php _e( 'How do I disable copy protection for an individual page?', $this->plugin_options->text_domain ) ?>
                                        </a>
                                    </p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="disable_right_click">
                                    <?php _e( 'Disable the right mouse button', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'content-protection-right-click' ) ?>
                                </label>
                                <div class="option-field-body">
			                        <?php $this->display_checkbox('disable_right_click') ?>
                                    <p class="description"><?php _e( 'One way to combat text copying.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'Disables the right mouse button', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="disable_selection_text">
                                    <?php _e( 'Disable text selection', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'content-protection-selection' ) ?>
                                </label>
                                <div class="option-field-body">
			                        <?php $this->display_checkbox('disable_selection_text') ?>
                                    <p class="description"><?php _e( 'One way to combat text copying.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'Disables text selection on the page', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="disable_keystrokes">
                                    <?php _e( 'Disable hotkeys', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'content-protection-hotkeys' ) ?>
                                </label>
                                <div class="option-field-body">
			                        <?php $this->display_checkbox('disable_keystrokes') ?>
                                    <p class="description"><?php _e( 'One way to combat text copying.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'Disables key operation Ctrl+C, Ctrl+A, Ctrl+U, Ctrl+S, Ctrl+X, Ctrl+Shift+C', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->



                            <div class="option-field-header"><?php _e( 'Cookie notice', $this->plugin_options->text_domain ) ?></div>

                            <div class="option-field">
                                <label class="option-field-label" for="message_cookie">
                                    <?php _e( 'Cookie notice', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'cookies' ) ?>
                                </label>
                                <div class="option-field-body">
			                        <?php $this->display_checkbox('message_cookie') ?>
                                    <div class="textarea-block">
				                        <?php $this->display_textarea( 'cookie_message_text', array( 'default' => $this->plugin_options->get_default_option('cookie_message_text') ) ) ?>
                                        <p class="description"><?php _e( 'You can set the text for the cookie notification, for example:', $this->plugin_options->text_domain ) ?></p>
                                        <p class="description"><?php echo $this->plugin_options->get_default_option('cookie_message_text') ?></p>
                                    </div>
                                    <p class="description"><strong>Clearfy:</strong> <?php _e( 'Displays a notification at the bottom about the use of cookies on the site.', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="cookie_message_position">
                                    <?php _e( 'Position', $this->plugin_options->text_domain ) ?>
                                </label>
                                <div class="option-field-body">
			                        <?php $this->display_select( 'cookie_message_position', array(
				                        'bottom'    => __( 'Bottom', $this->plugin_options->text_domain ),
				                        'left'      => __( 'Left', $this->plugin_options->text_domain ),
				                        'right'     => __( 'Right', $this->plugin_options->text_domain ),
			                        ), array(
				                        'default' => $this->plugin_options->get_default_option('cookie_message_position')
			                        ) ) ?>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="cookie_message_color">
                                    <?php _e( 'Text color', $this->plugin_options->text_domain ) ?>
                                </label>
                                <div class="option-field-body">
			                        <?php $this->display_color( 'cookie_message_color', array( 'default' => $this->plugin_options->get_default_option('cookie_message_color') ) ) ?>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="cookie_message_background">
                                    <?php _e( 'Background Color', $this->plugin_options->text_domain ) ?>
                                </label>
                                <div class="option-field-body">
			                        <?php $this->display_color( 'cookie_message_background', array( 'default' => $this->plugin_options->get_default_option('cookie_message_background') ) ) ?>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="cookie_message_button_text">
                                    <?php _e( 'Button text', $this->plugin_options->text_domain ) ?>
                                </label>
                                <div class="option-field-body">
			                        <?php $this->display_input_text( 'cookie_message_button_text', array( 'default' => $this->plugin_options->get_default_option('cookie_message_button_text') ) ) ?>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="cookie_message_background">
                                    <?php _e( 'Button Color', $this->plugin_options->text_domain ) ?>
                                </label>
                                <div class="option-field-body">
			                        <?php $this->display_color( 'cookie_message_button_background', array( 'default' => $this->plugin_options->get_default_option('cookie_message_button_background') ) ) ?>
                                </div>
                            </div><!--.option-field-->


                            <div class="option-field-header">
                                <?php _e( 'Site under maintenance', $this->plugin_options->text_domain ) ?>
                                <?php $this->the_help_icon( 'maintenance-enable' ) ?>
                            </div>

                            <div class="option-field">
                                <label class="option-field-label" for="maintenance_mode_enable">
                                    <?php _e( 'Enable maintenance mode', $this->plugin_options->text_domain ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('maintenance_mode_enable') ?>
                                    <p class="description"><?php _e( 'Shows a temporary "Site under maintenance" page only on the frontend. The admin area is not affected. Users with the Editor access level can still view the site. The page returns HTTP status 503 (Service Unavailable), which tells search engines and monitoring systems that this is a temporary state.', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="maintenance_mode_allow_indexing">
                                    <?php _e( 'Allow indexing by robots', $this->plugin_options->text_domain ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('maintenance_mode_allow_indexing') ?>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="maintenance_mode_html">
                                    <?php _e( 'HTML content', $this->plugin_options->text_domain ) ?>
                                </label>
                                <div class="option-field-body">
                                    <div style="border: 2px solid #c2c2c2; border-radius: 5px; margin-bottom: 1rem;">
                                        <?php $this->display_textarea( 'maintenance_mode_html', array( 'rows' => 10, 'default' => $this->plugin_options->get_default_option( 'maintenance_mode_html' ) ) ) ?>
                                    </div>
                                    <p class="description"><?php _e( 'You can use your own HTML. By default, simple localized text with minimal style is used.', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->






                            <div class="option-field-header">
                                <?php _e( 'Hide entries', $this->plugin_options->text_domain ) ?>
                            </div>

                            <div class="option-field">
                                <label class="option-field-label" for="hide_posts_front">
                                    <?php _e( 'On the main page', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'hide-posts-home' ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_input_text('hide_posts_front' ) ?>
                                    <p class="description"><?php _e( 'Hide records from the main page, specify the IDs of records, separated by commas.', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="hide_posts_archive">
                                    <?php _e( 'In the archives', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'hide-posts-archives' ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_input_text('hide_posts_archive' ) ?>
                                    <p class="description"><?php _e( 'Hide the output of records from all archives (headings, labels, etc.), specify the record IDs separated by commas.', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="hide_posts_search">
                                    <?php _e( 'In search', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'hide-posts-search' ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_input_text('hide_posts_search' ) ?>
                                    <p class="description"><?php _e( 'Hide the records from the search page, specify the IDs of the records, separated by commas.', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->




                            <div class="option-field-header">
                                <?php _e( 'XML sitemap', $this->plugin_options->text_domain ) ?>
                                <?php $this->the_help_icon( 'xml-sitemap' ) ?>
                            </div>
                            <p><?php _e( 'These settings apply only to the standard XML sitemap, which appeared since WP 5.5.', $this->plugin_options->text_domain ) ?></p>
                            <p><?php _e( 'Built-in XML sitemap is located at /wp-sitemap.xml.', $this->plugin_options->text_domain ) ?></p>

                            <br><br>

                            <div class="option-field">
                                <label class="option-field-label" for="wp_sitemaps_xml_disable">
				                    <?php _e( 'Disable standard XML Sitemap', $this->plugin_options->text_domain ) ?>
                                </label>
                                <div class="option-field-body">
				                    <?php $this->display_checkbox('wp_sitemaps_xml_disable') ?>
                                    <p class="description"><?php _e( 'By default, since version 5.5 in WordPress appeared XML sitemap. If you use SEO plugins, you can disable the default map.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'Completely disables the built-in XML sitemap.', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="xml_disable_users">
				                    <?php _e( 'Disable users', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'xml-sitemap-users' ) ?>
                                    <span class="clearfy-recommend"><?php _e( 'Recommended', $this->plugin_options->text_domain ) ?></span>
                                </label>
                                <div class="option-field-body">
				                    <?php $this->display_checkbox('wp_sitemaps_xml_disable_users') ?>
                                    <p class="description"><?php _e( 'By default, the XML sitemap displays links to user pages. In 99&#37; of cases they are not needed and it is recommended to disable them.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'Disable user list in wp-sitemap-users-1.xml', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->

                        </div><!--tab-->


                        <div id="clearfy_more" class="wpshop-tab-in js-wpshop-tab-item">


                            <div class="option-field-header"><?php _e( 'Extras', $this->plugin_options->text_domain ) ?></div>


                            <div class="option-field">
                                <label class="option-field-label" for="sanitize_title">
                                    <?php _e( 'Disable Gutenberg', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'disable-gutenberg' ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('disable_gutenberg') ?>
                                    <p class="description"><?php _e( 'After upgrading WordPress to version 5.0, everyone had the Gutenberg editor turned on by default, which brought not a few problems and issues.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'Completely turns off Gutenberg.', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->


                            <div class="option-field">
                                <label class="option-field-label" for="disable_gutenberg_widgets">
                                    <?php _e( 'Disable Gutenberg widgets', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'disable-gutenberg-widgets' ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('disable_gutenberg_widgets') ?>
                                    <p class="description"><?php _e( 'In WordPress 5.8, the usual widgets have been replaced by Gutenberg, it is not very convenient and many do not need. Analogue to Classic Widgets.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'Returns classic widgets.', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->



                            <div class="option-field">
                                <label class="option-field-label" for="passive_listeners">
                                    <?php _e( 'PageSpeed passive listeners', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'passive-listeners' ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('passive_listeners') ?>
                                    <p class="description"><?php _e( 'Activate the option if Google PageSpeed is berating "Passive event listeners are not used to improve scrolling performance".', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><?php _e( 'Does not work: with Yandex.Market widget.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'Adds passive for scroll and touch-action listeners.', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->




                            <div class="option-field">
                                <label class="option-field-label" for="disable_feed">
                                    <?php _e( 'Disable RSS Feeds', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'disable-rss' ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('disable_feed') ?>
                                    <p class="description"><?php _e( 'The main hole from which they will parse your content is RSS feeds. For article sites, business card sites, corporate sites - disable it.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'Removes the link to the RSS feed from the &lt;head&gt; section, closes it and puts a redirect from all RSS feeds.', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="remove_unnecessary_link_admin_bar">
                                    <?php _e( 'Remove links to wordpress.org from the admin bar', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'remove-wordpress-admin-bar' ) ?>
                                    <span class="clearfy-recommend"><?php _e( 'Recommended', $this->plugin_options->text_domain ) ?></span>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('remove_unnecessary_link_admin_bar') ?>
                                    <p class="description"><?php _e( 'The first item in the top bar is the WordPress logo and external links to wordpress.org, documentation, and forums.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'Removes all links to wordpress.org from the toolbar.', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->





                            <div class="option-field">
                                <label class="option-field-label" for="disable_admin_bar">
                                    <?php _e( 'Disable admin bar', $this->plugin_options->text_domain ) ?>
                                    <?php $this->the_help_icon( 'disable-admin-bar' ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('disable_admin_bar') ?>
                                    <p class="description"><?php _e( 'By default, the top admin panel is shown for authorized users.', $this->plugin_options->text_domain ) ?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'Disable admin bar', $this->plugin_options->text_domain ) ?></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="disable_email_notification">
                                    <?php _e( 'Update notifications', $this->plugin_options->text_domain )?>
                                    <?php $this->the_help_icon( 'disable-email-notification' ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('disable_email_notification') ?>
                                    <p class="description"><?php _e( 'Since version 3.7, WordPress has learned how to automatically update itself and send you an email about the update each time. Since version 5.5, two more types of emails have been added.', $this->plugin_options->text_domain )?></p>
                                    <p class="description"><strong>Clearfy:</strong> <?php _e( 'Disables email notifications about automatic updates.', $this->plugin_options->text_domain )?></p>
                                </div>
                            </div><!--.option-field-->









                            <div class="option-field-header">
                                <?php _e( 'Widgets', $this->plugin_options->text_domain )?>
                                <?php $this->the_help_icon( 'widgets' ) ?>
                            </div>


                            <div class="option-field">
                                <label class="option-field-label" for="remove_unneeded_widget_page">
                                    <?php _e( 'Remove "Pages" widget', $this->plugin_options->text_domain )?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('remove_unneeded_widget_page') ?>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="remove_unneeded_widget_calendar">
                                    <?php _e( 'Remove "Calendar" widget', $this->plugin_options->text_domain )?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('remove_unneeded_widget_calendar') ?>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="remove_unneeded_widget_tag_cloud">
                                    <?php _e( 'Remove "Tag Cloud" widget', $this->plugin_options->text_domain )?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('remove_unneeded_widget_tag_cloud') ?>
                                    <p class="description"><?php _e( 'Widgets "Pages", "Calendar", "Label Cloud" create unnecessary requests to the database, and are now used very rarely, because "Pages" is easily replaced by the widget "Menu", and the other two only create duplicate pages.', $this->plugin_options->text_domain )?></p>
                                    <p class="description"><strong>Clearfy Pro:</strong> <?php _e( 'Disables these widgets, reducing the number of database queries.', $this->plugin_options->text_domain )?></p>
                                </div>
                            </div><!--.option-field-->



                            <div class="option-field-header">
                                <?php _e( 'Revisions', $this->plugin_options->text_domain )?>
                                <?php $this->the_help_icon( 'revisions' ) ?>
                            </div>


                            <div class="option-field">
                                <label class="option-field-label" for="revisions_disable">
                                    <?php _e( 'Disable revisions completely', $this->plugin_options->text_domain )?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('revisions_disable') ?>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="revision_limit">
                                    <?php _e( 'Limit the number of revisions', $this->plugin_options->text_domain )?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_input_number('revision_limit', [ 'min' => 1 ]) ?>

                                    <?php
                                    $check_config_revisions = file_get_contents( get_home_path() . 'wp-config.php' );
                                    if ( preg_match('/define(.+?)WP_POST_REVISIONS/', $check_config_revisions) ) {
                                        echo '<p class="description danger">' . __( 'Warning. In the file wp-config.php found a constant WP_POST_REVISIONS, it defines the number of revisions. Remove it, so you can change this value through the admin panel.', $this->plugin_options->text_domain ) . '</p>';
                                    }
                                    ?>
                                    <p class="description"><?php _e( 'When you save and update any record or page, a copy (revision) of it is created, which you can view or restore in the future. But over time, a large number of such revisions (and there can be dozens of them for each page) clog the database, wasting space and slowing down the work. Usually, it is sufficient to keep up to 3-5 recent revisions.', $this->plugin_options->text_domain )?></p>
                                </div>
                            </div><!--.option-field-->



                        </div>
                        <div id="clearfy_redirect" class="wpshop-tab-in js-wpshop-tab-item">
                            <div class="option-field-header">
                                <?php _e( 'Redirect Manager', 'clearfy' ) ?>
                                <?php $this->the_help_icon( 'redirect-manager' ) ?>
                            </div>

                            <div class="option-field">
                                <label class="option-field-label" for="protect_author_get">
                                    <?php _e( 'Redirect', $this->plugin_options->text_domain )?>
                                </label>
                                <div class="option-field-body">

                                    <?php
                                        $redirect_manager = new Clearfy_Redirect_Manager();
                                        echo $redirect_manager->show_fields();
                                    ?>

                                    <p class="description"><?php _e( '301 redirect from one address to another. For example, if the article is not accessible at the old address.', $this->plugin_options->text_domain )?></p>
                                    <p class="description"><?php _e( 'You can specify both internal and external links.', $this->plugin_options->text_domain )?></p>
                                    <p class="description"><?php printf( __( 'Put * to replace any number of characters. For example: %s.', $this->plugin_options->text_domain ), '/?product=*' ); ?></p>
                                </div>
                            </div><!--.option-field-->
                        </div>

                        <div id="clearfy_404" class="wpshop-tab-in js-wpshop-tab-item">
                            <div class="option-field-header">
                                <?php _e( '404', 'clearfy' ) ?>
                                <?php $this->the_help_icon( '404' ) ?>
                            </div>

                            <?php
                            require_once dirname(__FILE__) . '/../inc/class-logging.php';
                            $class_log = new Clearfy_Logging( $this->plugin_options, '404' );
                            ?>

                            <div class="option-field">
                                <label class="option-field-label" for="change_login_errors">
                                    <?php _e( 'Disable logging', $this->plugin_options->text_domain )?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('logging_off') ?>
                                    <p class="description"><?php _e( 'If you are sure that 404 error logging is not required - you can disable it completely.', $this->plugin_options->text_domain )?></p>
                                </div>
                            </div><!--.option-field-->

                            <p><?php _e( 'On this page you can see a log of the most recent requests for which a 404 error was returned.', $this->plugin_options->text_domain )?></p>
                            <p><?php _e( 'This information will help you properly configure redirects from articles in which you have changed the address, to find pictures, styles and scripts that do not open on the site, to monitor the security of the site, time to see the problem areas.', $this->plugin_options->text_domain )?></p>
                            <p><?php _e( 'At most, the most recent ', $this->plugin_options->text_domain )?><?php echo $class_log->get_limit() ?><?php _e( ' entries.', $this->plugin_options->text_domain )?></p>

                            <p><span class="button js-clearfy-clear-log" data-nonce="<?php echo wp_create_nonce( 'clearfy_clear_log_nonce' ) ?>"><?php _e( 'Clear log', $this->plugin_options->text_domain )?></span></p>

                            <?php
                            $logs = $class_log->read();
                            ?>

                            <?php if ( ! empty( $logs ) ): ?>
                                <table class="wpshop-table clearfy-table-404">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>url</th>
                                            <th>referer</th>
                                            <th>ip</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ( $logs as $log ): ?>
                                            <?php
                                            $referer = '';
                                            $referer_short = '';
                                            if ( ! empty( $log['referer'] ) ) {
                                                $referer = $log['referer'];
                                                $referer_short = parse_url( $log['referer'], PHP_URL_HOST );
                                            }
                                            ?>
                                            <tr>
                                                <td><?php echo date( 'd.m.Y H:i', (int) $log['date'] ) ?></td>
                                                <td>
                                                    <?php echo $log['message'] ?>
                                                </td>
                                                <td><?php echo ( ! empty( $referer_short ) ) ? '<a href="' . esc_attr( $referer ) . '" target="_blank" rel="noopener noreferrer">' . $referer_short . '</span>' : '' ?></td>
                                                <td><?php echo $log['ip'] ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>

                        </div>

                        <div id="clearfy_indexnow" class="wpshop-tab-in js-wpshop-tab-item">
                            <div class="option-field-header">
                                <?php _e( 'IndexNow', 'clearfy' ) ?>
                            </div>

                            <?php
                            $class_indexnow = new Clearfy_Indexnow( $this->plugin_options );
                            ?>

                            <div class="option-field">
                                <label class="option-field-label" for="indexnow_enable">
                                    <?php _e( 'Include IndexNow', $this->plugin_options->text_domain )?>
                                    <?php $this->the_help_icon( 'indexnow' ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_checkbox('indexnow_enable') ?>
                                    <p class="description"><?php _e( 'Automatically notify search engines of changes to the site.', $this->plugin_options->text_domain )?></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="indexnow_post_types">
                                    <?php _e( 'Post types', $this->plugin_options->text_domain )?>
                                </label>
                                <div class="option-field-body">
                                    <?php
                                    $indexnow_available_post_types = $class_indexnow->get_available_post_types();
                                    $indexnow_selected_post_types = $class_indexnow->get_selected_post_types();
                                    ?>

                                    <?php foreach ( $indexnow_available_post_types as $indexnow_post_type ): ?>
                                        <?php
                                        $indexnow_post_type_name = $indexnow_post_type->name;
                                        $indexnow_post_type_label = ! empty( $indexnow_post_type->labels->name ) ? $indexnow_post_type->labels->name : $indexnow_post_type_name;
                                        $indexnow_checked = in_array( $indexnow_post_type_name, $indexnow_selected_post_types, true ) ? ' checked' : '';
                                        ?>
                                        <label>
                                            <input type="checkbox" name="<?php echo esc_attr( $this->option_name ) ?>[indexnow_post_types][]" value="<?php echo esc_attr( $indexnow_post_type_name ) ?>"<?php echo $indexnow_checked ?>>
                                            <?php echo esc_html( $indexnow_post_type_label ) ?>
                                        </label><br>
                                    <?php endforeach; ?>

                                    <p class="description"><?php _e( 'Select which post types should trigger IndexNow when updated.', $this->plugin_options->text_domain )?></p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="indexnow_key">
                                    <?php _e( 'IndexNow key', $this->plugin_options->text_domain )?>
                                    <?php $this->the_help_icon( 'indexnow_key' ) ?>
                                </label>
                                <div class="option-field-body">
                                    <?php $this->display_input_text( 'indexnow_key' ) ?>
                                    <p class="description">
                                        <?php _e( 'It is generated automatically, you can change it to your own. The key can contain the characters a-z, A-Z , 0-9, -.', $this->plugin_options->text_domain )?><br>
                                        <?php _e( 'Length from 8 to 128 characters.', $this->plugin_options->text_domain )?><br>
                                        <?php _e( 'You can delete it to generate a new one automatically.', $this->plugin_options->text_domain )?>
                                    </p>
                                </div>
                            </div><!--.option-field-->

                            <div class="option-field">
                                <label class="option-field-label" for="indexnow_history">
                                    <?php _e( 'Dispatch History', $this->plugin_options->text_domain )?>
                                </label>
                                <div class="option-field-body">

                                    <p>
                                        <?php _e( 'Help:', $this->plugin_options->text_domain )?>
                                        <a href="https://support.wpshop.ru/faq/clearfy-pro-202/" target="_blank"><?php _e( 'What does code 202 mean', $this->plugin_options->text_domain ) ?></a>,
                                        <?php _e( 'which means ', $this->plugin_options->text_domain )?><a href="https://support.wpshop.ru/faq/clearfy-pro-403-invalid-key/" target="_blank">403 Invalid key</a>
                                        <br>
                                        <?php _e( 'The table shows the last ', $this->plugin_options->text_domain )?><?php echo $class_indexnow->log_limit ?> <?php _e( ' entries.', $this->plugin_options->text_domain )?>
                                    </p>

                                    <?php
                                    $indexnow_logs = $class_indexnow->get_log();
                                    $indexnow_logs = array_reverse($indexnow_logs);
                                    ?>

                                    <?php if ( ! empty( $indexnow_logs ) ): ?>
                                        <table class="wpshop-table clearfy-table-indexnow">
                                            <thead>
                                            <tr>
                                                <th>date</th>
                                                <th>post</th>
                                                <th>status</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach ( $indexnow_logs as $log ): ?>
                                                <?php
                                                ?>
                                                <tr>
                                                    <td><?php echo date( 'd.m.y H:i:s', $log['time'] ) ?></td>
                                                    <td><?php echo $log['post_id'] ?>: <a href="<?php echo get_edit_post_link( $log['post_id'] ) ?>"><?php echo $class_indexnow->get_the_title($log['post_id']) ?></a></td>
                                                    <td>
                                                        <span class="clearfy-indexnow-status clearfy-indexnow-status--<?php echo $log['status'] ?>"></span>
                                                        <?php echo $log['status'] ?>
                                                        <?php echo $class_indexnow->get_message_by_code( $log['status'] ) ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    <?php endif; ?>


                                </div>
                            </div><!--.option-field-->


                        </div>

                        <div class="clearfy-settings-save-container js-clearfy-settings-save-container">
                            <?php submit_button(); ?>
                        </div>

                    </form>

                </div><!--.wpshop-col-left-->


                <div class="clearfy-settings-col clearfy-settings-col--right">

                    <?php $this->display_widgets(); ?>

                </div>

                </div><!--.clearfy-settings-cols-->

            </div>


            <?php endif; //license key ?>

        </div>

        <?php
    }



    public function display_widgets() {
        ?>

        <div class="wpshop-widget">
            <?php _e( 'Plugin version', $this->plugin_options->text_domain ) ?>: <?php echo $this->plugin_options->version; ?>
        </div>

        <div class="wpshop-widget wpshop-widget-news">
            <div class="js-wpshop-news-block"></div>
            <script>
                const container = document.querySelector('.js-wpshop-news-block');

                if (container) {
                    const widgetProduct = 'clearfy_pro';
                    const widgetLocale = '<?php echo get_locale() ?>';

                    const cacheKey = 'wpshop_news_widget_' + widgetLocale + '_cache_v1';
                    const cacheTtl = 60 * 60 * 1000; // 1 час
                    const endpoint = `https://wpshop.tech/tools/product-widget/?${new URLSearchParams({
                        product: widgetProduct,
                        locale: widgetLocale,
                    }).toString()}`;

                    const renderNews = (html) => {
                        if (!html) {
                            return;
                        }
                        const newsBox = document.createElement('div');
                        newsBox.className = 'wpshop-settings-widget';
                        newsBox.innerHTML = html;
                        container.innerHTML = '';
                        container.appendChild(newsBox);
                    };

                    const readCache = () => {
                        try {
                            const raw = window.localStorage.getItem(cacheKey);
                            if (!raw) {
                                return null;
                            }
                            const parsed = JSON.parse(raw);
                            if (!parsed || typeof parsed !== 'object') {
                                return null;
                            }
                            if (!parsed.html || !parsed.cachedAt) {
                                return null;
                            }
                            return parsed;
                        } catch (error) {
                            return null;
                        }
                    };

                    const writeCache = (html) => {
                        try {
                            window.localStorage.setItem(cacheKey, JSON.stringify({
                                html,
                                cachedAt: Date.now()
                            }));
                        } catch (error) {
                            // Если localStorage недоступен — просто пропускаем кеш.
                        }
                    };

                    const cached = readCache();
                    const isFresh = cached && (Date.now() - Number(cached.cachedAt) < cacheTtl);

                    if (isFresh) {
                        renderNews(cached.html);
                    } else {
                        fetch(endpoint)
                            .then(response => response.json())
                            .then(data => {
                                if (data && data.html) {
                                    writeCache(data.html);
                                    renderNews(data.html);
                                    return;
                                }
                                if (cached && cached.html) {
                                    renderNews(cached.html);
                                }
                            })
                            .catch(error => {
                                if (cached && cached.html) {
                                    renderNews(cached.html);
                                    return;
                                }
                                console.error('Ошибка загрузки новостей:', error);
                            });
                    }
                }
            </script>
        </div>

        <?php
    }


    public function get_ip() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public function is_cyrillic_location() {
        if ( in_array( get_locale(), [ 'ru_RU', 'uk', 'bel', 'ba', 'kk', 'tg', 'tuk', 'uz_UZ'] ) ) {
            return true;
        }
        return false;
    }


    public function the_help_icon( $name ) {
        echo '<a href="' . $this->get_help_url( $name ) . '" target="_blank" rel="noopener" class="clearfy-ico-help" title="' . __( 'Help', $this->plugin_options->text_domain ) . '">?</a>';
    }

    public function get_help_url( $name ) {

        $urls = [
            // code
            'rest-api' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#rest-api',
            ],
            'disable-emoji' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#disable-emoji'
            ],
            'remove-dns-prefetch' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#remove-dns-prefetch',
            ],
            'remove-jquery-migrate' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#remove-jquery-migrate',
            ],
            'rsd-link' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#remove-rsd',
            ],
            'wlw-link' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#remove-wlw',
            ],
            'shortlink' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#shortlink',
            ],
            'next-prev-links' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#remove-next-prev-links',
            ],
            'recentcomments' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#recentcomments',
            ],
            'code-head' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#code-head',
            ],
            'code-body' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#code-body',
            ],
            'minify' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#minify',
            ],

            // seo
            'last-modified' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#last-modified',
            ],
            'if-modified-since' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#if-modified-since',
            ],
            'add-alt' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#add_alt',
            ],
            'hide-comment-links' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#hide-comment-links',
            ],
            'hide-author-link' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#hide-author-link',
            ],
            'hide-external-links-content' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#hide-external-links-content',
            ],
            'noindex-pagination' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#noindex-pagination',
            ],
            'robots-txt' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#robots-txt',
            ],
            'http-https' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#http-https',
            ],
            'yoast-breadcrumbs-remove-last' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#yoast-breadcrumbs-remove-last',
            ],
            'yoast-breadcrumbs-replace-title' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#yoast-breadcrumbs-replace-title',
            ],
            'yoast-xml-image' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#yoast-xml-image',
            ],
            'yoast-remove-comment' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#yoast-remove-comment',
            ],
            'yoast-paged-canonical' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#yoast-paged-canonical',
            ],
            'yoast-json-ld' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#yoast-json-ld',
            ],
            'rank-math-white-label' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#rank-math-white-label',
            ],
            'rank-math-paged-canonical' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#rank-math-paged-canonical',
            ],
            'rank-math-json-ld' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#rank-math-json-ld',
            ],

            // duplicates
            'date-duplicates' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#date-duplicates',
            ],
            'user-duplicates' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#user-duplicates',
            ],
            'tag-duplicates' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#tag-duplicates',
            ],
            'attachment-duplicates' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#attachment-duplicates',
            ],
            'post-pagination-duplicates' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#post-pagination-duplicates',
            ],
            'replytocom-duplicates' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#replytocom-duplicates',
            ],


            // security
            'cloud-protection' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/clearfy-cloud/#cloud-protection',
            ],
            'hide-wp-login' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#hide-wp-login',
            ],
            'hide-wp-login-disable' => [
                'default' => 'https://support.wpshop.ru/faq/clearfy-pro-disable-hide-admin/',
            ],
            'remove-author-get' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#remove-author-get',
            ],
            'hide-login-errors' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#hide-login-errors',
            ],
            'disable-xml-rpc' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#xml-rpc',
            ],
            'login-attempts' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#login-attempts',
            ],
            'login-attempts-disable' => [
                'default' => 'https://support.wpshop.ru/faq/clearfy-pro-disable-login-attempts/',
            ],
            'remove-meta-generator' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#remove-meta-generator',
            ],
            'remove-version-styles' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#remove-version-styles',
            ],
            'remove-version-scripts' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#remove-version-scripts',
            ],


            // modules
            'transliteration' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#transliteration',
            ],
            'disable-comments' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#disable-comments',
            ],
            'disable-comments-xml-rpc' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#disable-comments-xml-rpc',
            ],
            'disable-comments-rest-api' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#disable-comments-rest-api',
            ],
            'disable-comments-interface' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#disable-comments-interface',
            ],
            'content-protection' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#content-protection',
            ],
            'content-protection-source-link' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#content-protection-source-link',
            ],
            'content-protection-right-click' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#content-protection-right-click',
            ],
            'content-protection-selection' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#content-protection-selection',
            ],
            'content-protection-hotkeys' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#content-protection-hotkeys',
            ],
            'cookies' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#cookies',
            ],
            'maintenance-enable' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#maintenance-enable',
            ],
            'hide-posts-home' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#hide-posts-home',
            ],
            'hide-posts-archives' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#hide-posts-archives',
            ],
            'hide-posts-search' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#hide-posts-search',
            ],
            'xml-sitemap' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#xml-sitemap',
            ],
            'xml-sitemap-users' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#xml-sitemap-users',
            ],

            // additional
            'avatars' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#avatars',
            ],
            'disable-gutenberg' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#disable-gutenberg',
            ],
            'disable-gutenberg-widgets' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#disable-gutenberg-widgets',
            ],
            'passive-listeners' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#passive-listeners',
            ],
            'disable-rss' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#disable-rss',
            ],
            'disable-site-field' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#disable-site-field',
            ],
            'remove-wordpress-admin-bar' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#remove-wordpress-admin-bar',
            ],
            'disable-gravatar' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#disable-gravatar',
            ],
            'local-avatars' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#local-avatars',
            ],
            'disable-admin-bar' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#disable-admin-bar',
            ],
            'disable-email-notification' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#disable-email-notification',
            ],
            'widgets' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#widgets',
            ],
            'revisions' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#revisions',
            ],
            'redirect-manager' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#redirect-manager',
            ],
            '404' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#404',
            ],
            'indexnow' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#indexnow',
            ],
            'indexnow_key' => [
                'default' => 'https://support.wpshop.ru/docs/plugins/clearfy-pro/setting/#indexnow-key',
            ],
        ];

        if ( isset( $urls[ $name ] ) ) {
            if ( isset( $urls[ $name ][ get_locale() ] ) ) {
                return $urls[ $name ][ get_locale() ];
            } else if ( $urls[ $name ]['default'] ) {
                return $urls[ $name ]['default'];
            }
        }

        return '';
    }


    /**
     * Display option checkbox
     *
     * @param string $name
     */
    public function display_checkbox( $name ) {
        $checked = '';
        if (isset($this->options[$name]) && $this->options[$name] == 'on') $checked = ' checked';
        $string = '<span class="pseudo-checkbox'. $checked .'"></span> <input class="pseudo-checkbox-hidden" name="' . $this->option_name . '[' . $name . ']" type="checkbox" id="' . $name . '" value="on"'. $checked .'>';
        echo $string;
    }

    /**
     * Display input text field
     *
     * @param string $name
     * @param array $args
     */
    public function display_input_text( $name, $args = array() ) {
        $value = '';
        if (isset($this->options[$name]) && ! empty($this->options[$name])) $value = $this->options[$name];
        if ( empty( $value ) && ! empty( $args['default'] ) ) $value = $args['default'];
        $string = '<input name="' . $this->option_name . '[' . $name . ']" type="text" id="' . $name . '" value="'. esc_attr($value) .'"" class="regular-text">';
        echo $string;
    }

    /**
     * Display textarea field
     *
     * @param string $name
     */
    public function display_textarea_robots( $name ) {
        $value = '';
        if (isset($this->options[$name]) && ! empty($this->options[$name])) $value = $this->options[$name];
        if ( empty( $value ) ) {
            $robots_txt = new \WPShop\ClearfyPro\RobotsTxt( $this->plugin_options );
            $value = $robots_txt->render( '' );
        }
        $string = '<textarea name="' . $this->option_name . '[' . $name . ']" id="' . $name . '" class="regular-text">'. $value .'</textarea>';
        echo $string;
    }

	public function display_textarea_last_modified( $name ) {
        $value = '';
        if (isset($this->options[$name]) && ! empty($this->options[$name])) $value = $this->options[$name];
        $string = '<textarea name="' . $this->option_name . '[' . $name . ']" id="' . $name . '" class="regular-text" rows="4">'. $value .'</textarea>';
        echo $string;
    }

    public function display_textarea( $name, $args = array() ) {
        if ( isset( $this->options[$name] ) && ! empty( $this->options[$name] ) ) {
            $value = $this->options[$name];
        } else {
            $value = '';
        }
        if ( empty( $value ) && ! empty( $args['default'] ) ) $value = $args['default'];
        $rows = ( ! empty( $args['rows'] ) ) ? $args['rows'] : 4 ;
        $string = '<textarea name="' . $this->option_name . '[' . $name . ']" id="' . $name . '" class="regular-text" rows="' . $rows . '">'. $value .'</textarea>';
        echo $string;
    }

    public function display_color( $name, $args = array() ) {
        if ( isset( $this->options[$name] ) && ! empty( $this->options[$name] ) ) $value = $this->options[$name];
        if ( empty( $value ) && ! empty( $args['default'] ) ) $value = $args['default'];

        $string = '<input class="clearfy-color-input" type="text" name="' . $this->option_name . '[' . $name . ']" value="'. $value .'">';
        echo $string;

    }

    public function display_cookie_text_color( $name ) {
        $value = '#fff';
        if (isset($this->options[$name]) && ! empty($this->options[$name])) $value = $this->options[$name];

        $string = '<input class="clearfy-color-input" type="text" name="' . $this->option_name . '[' . $name . ']" value="'. $value .'" />';
        echo $string;

    }

    public function display_cookie_background_color( $name ) {
        $value = '#000';
        if (isset($this->options[$name]) && ! empty($this->options[$name])) $value = $this->options[$name];

        $string = '<input class="clearfy-color-input" type="text" name="' . $this->option_name . '[' . $name . ']" value="'. $value .'" />';
        echo $string;

    }

    /**
     * Display input number field
     *
     * @param $name
     * @param $step
     * @param $min
     * @param $max
     */
    public function display_input_number( $name , $args = [] ) {

        $args = wp_parse_args($args, [
            'step' => '',
            'min' => '',
            'max' => '',
            'default' => '',
        ]);

        $value = '';
        if ( isset( $this->options[ $name ] ) && ! empty( $this->options[ $name ] ) ) {
            $value = $this->options[ $name ];
        } elseif ( ! empty( $args['default'] ) ) {
            $value = $args['default'];
        }

        $string = '<input name="' . $this->option_name . '[' . $name . ']" type="number" ';
        if ( ! empty( $args['step'] ) ) {
            $string .= 'step="' . $args['step'] . '" ';
        }
        if ( ! empty( $args['min'] ) || $args['min'] === 0 ) {
            $string .= 'min="' . $args['min'] . '"  ';
        }
        if ( ! empty( $args['max'] ) ) {
            $string .= 'max="' . $args['max'] . '" ';
        }
        $string .= 'id="' . $name . '" value="' . esc_attr($value) . '"" class="small-text">';
        echo $string;
    }


    /**
     * Display select
     *
     * @param string $name
     * @param array $values
     */
    public function display_select( $name , $values, $args = array() ) {
        if (isset($this->options[$name]) && ! empty($this->options[$name])) $value = $this->options[$name];
        $string  = '<select name="' . $this->option_name . '[' . $name . ']" id="' . $name . '">';

        if (is_array( $values )) {
            foreach ($values as $key => $value) {
                $selected = '';
                if (isset($this->options[$name]) && $this->options[$name] == $key) $selected = ' selected';

                $string .= '<option value="' . $key . '"'. $selected .'>' . $value . '</option>';
            }
        }

        $string .= '</select>';
        echo $string;
    }


    public function is_rank_math_enabled() {
        return is_plugin_active('seo-by-rank-math/rank-math.php');
    }

    public function is_yoast_seo_enabled() {
        return is_plugin_active('wordpress-seo/wp-seo.php');
    }
}
