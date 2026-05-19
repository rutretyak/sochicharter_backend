<?php

/**
 * Class Plugin_Options
 *
 * @version     1.1
 * @updated     2018-05-23
 * @package     Clearfy_Plugin_Options
 */
class Clearfy_Plugin_Options {

    public $plugin_slug = 'clearfy_pro';

    public $plugin_name = 'clearfy-pro';

    public $text_domain = 'clearfy-pro';

    public $version = '1.0.0';

    public array $api_urls = [];

    public $plugin_path = '';

    public $options;

    public $default_options;

    public $option_name;

	protected $_init_defaults = false;



    /**
     * Set default options
     *
     * @param array $default_options
     */
//    public function set_default_options( $default_options = array() ) {
//        $this->default_options = $default_options;
//    }

	protected function init_default_options(  ) {
		if ( ! $this->_init_defaults ) {
			$this->default_options = apply_filters( 'clearfy_options_defaults', array(
				'cookie_message_text'               => __( 'This website uses cookies to improve user experience. By continuing to use the site, you consent to the use of cookies.', 'clearfy-pro' ),
				'cookie_message_position'           => 'bottom',
				'cookie_message_button_text'        => 'OK',
				'cookie_message_color'              => '#555555',
				'cookie_message_background'         => '#ffffff',
				'cookie_message_button_background'  => '#4b81e8',
				'maintenance_mode_html'             => '<div style="max-width:720px;margin:10vh auto;padding:2rem 1.5rem;text-align:center;font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Arial,sans-serif;line-height:1.6;color:#1f2937;">'
					. PHP_EOL . '<h1 style="margin:0 0 1rem;font-size:2rem;">' . esc_html__( 'Site under maintenance', 'clearfy-pro' ) . '</h1>'
					. PHP_EOL . '<p style="margin:0 0 .5rem;font-size:1.0625rem;">' . esc_html__( 'We are currently updating the site and will be back soon.', 'clearfy-pro' ) . '</p>'
					. PHP_EOL . '<p style="margin:0;font-size:.95rem;color:#6b7280;">' . esc_html__( 'Thank you for your patience.', 'clearfy-pro' ) . '</p>'
					. PHP_EOL . '</div>',
				'hide_external_links_post_types'    => [ 'post' ],
				'pseudo_links_class'                => 'pseudo-clearfy-link',
				'pseudo_links_color'                => '#0058cf',
				'pseudo_links_hover_color'          => '#2900cf',

				'login_attempts_allowed_retries'    => 5,
				'login_attempts_allowed_lockouts'   => 3,
				'login_attempts_lockout_duration'   => 15,
				'login_attempts_long_duration'      => 24,
			) );
			$this->_init_defaults = true;
		}
	}


    public function get_default_option( $name ) {
        $this->init_default_options();
        if ( array_key_exists( $name, $this->default_options ) ) {
            return $this->default_options[$name];
        }
        return null;
    }


    public function get_option( $name = '', $default = false ) {

		$this->init_default_options();

        if ( isset( $this->options[$name] ) ) {
            if ( $default && empty( $this->options[$name] ) ) {
                return $default;
            } else {
                return $this->options[ $name ];
            }
        } else {
            if ( ! empty( $this->default_options[$name] ) ) {
                return $this->default_options[$name];
            } else {
                if ( $default ) {
                    return $default;
                } else {
                    return false;
                }
            }
        }
    }

}
