<?php

namespace WPShop\ClearfyPro;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Logger {

    protected $log_file;
    protected $log_dir;
    protected $enabled = false;

    const MAX_FILE_SIZE = 5242880; // 5 MB
    const MAX_FILES     = 5;

    public function __construct( $filename = 'clearfy-pro.log' ) {

        $this->enabled = defined( 'WP_DEBUG' ) && WP_DEBUG;

        if ( ! $this->enabled ) {
            return;
        }

        $upload_dir = wp_upload_dir();

        $this->log_dir = trailingslashit( $upload_dir['basedir'] ) . 'clearfy/logs/';
        $this->log_file = $this->log_dir . sanitize_file_name( $filename );

        if ( ! file_exists( $this->log_dir ) ) {
            wp_mkdir_p( $this->log_dir );
        }

        $this->maybe_create_htaccess();
    }

    /**
     * Main log method
     */
    public function log( $message, array $context = [] ) {

        if ( ! $this->enabled ) {
            return;
        }

        $this->maybe_rotate_logs();

        $date = current_time( 'Y-m-d H:i:s' );

        if ( is_array( $message ) || is_object( $message ) ) {
            $message = wp_json_encode( $message, JSON_UNESCAPED_UNICODE );
        }

        if ( ! empty( $context ) ) {
            $message .= ' | Context: ' . wp_json_encode( $context, JSON_UNESCAPED_UNICODE );
        }

        $line = sprintf( "[%s] %s\n", $date, $message );

        file_put_contents( $this->log_file, $line, FILE_APPEND | LOCK_EX );
    }

    /**
     * Rotate logs if file too large
     */
    protected function maybe_rotate_logs() {

        if ( ! $this->enabled ) {
            return;
        }

        if ( ! file_exists( $this->log_file ) ) {
            return;
        }

        if ( filesize( $this->log_file ) < self::MAX_FILE_SIZE ) {
            return;
        }

        // Удаляем самый старый лог
        $oldest = $this->log_file . '.' . self::MAX_FILES;
        if ( file_exists( $oldest ) ) {
            unlink( $oldest );
        }

        // Сдвигаем остальные
        for ( $i = self::MAX_FILES - 1; $i >= 1; $i-- ) {
            $src = $this->log_file . '.' . $i;
            $dst = $this->log_file . '.' . ( $i + 1 );

            if ( file_exists( $src ) ) {
                rename( $src, $dst );
            }
        }

        // Текущий лог → .1
        rename( $this->log_file, $this->log_file . '.1' );
    }

    /**
     * Create .htaccess to protect logs
     */
    protected function maybe_create_htaccess() {

        if ( ! $this->enabled ) {
            return;
        }

        $htaccess = $this->log_dir . '.htaccess';

        if ( file_exists( $htaccess ) ) {
            return;
        }

        $rules = <<<HTACCESS
<IfModule mod_authz_core.c>
    Require all denied
</IfModule>

<IfModule !mod_authz_core.c>
    Deny from all
</IfModule>
HTACCESS;

        file_put_contents( $htaccess, $rules, LOCK_EX );
    }
}
