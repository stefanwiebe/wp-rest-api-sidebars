<div class="error">
    <p>
        <strong>
            <?php echo esc_html( __( 'Error, incompatible environment!', 'wp-rest-api-sidebars' ) ); ?>
        </strong>
        <br>
        <i>
            <?php
                echo esc_html( sprintf(
                    __( 'The WP-API Sidebars plugin requires at least PHP version %s or greater (current: %s) and WordPress version %s or greater (current: %s), the plugin has been deactivated.', 'wp-rest-api-sidebars' ),
                    WP_REST_API_SIDEBARS_REQUIRED_PHP_VERSION,
                    PHP_VERSION,
                    WP_REST_API_SIDEBARS_REQUIRED_WP_VERSION,
                    WP_VERSION
                ) );
            ?>
        </i>
    </p>
</div>

