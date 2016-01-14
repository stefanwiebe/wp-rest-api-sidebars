<?php

/**
 * An extension for the WP REST API that exposes endpoints for sidebars and widgets.
 *
 * PHP version 5.4.0
 *
 * Copyright (C) 2015  Martin Pettersson
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author    Martin Pettersson <martin_pettersson@outlook.com>
 * @copyright 2015 Martin Pettersson
 * @license   GPLv2
 * @link      https://github.com/martin-pettersson/wp-rest-api-sidebars
 */

namespace WP_API_Sidebars;

use WP_Autoloader;

/**
 * Class Sidebars
 *
 * @package WP_API_Sidebars
 */
final class Sidebars {
    /**
     * The plugins autoloader
     *
     * @var WP_Autoloader
     */
    private $autoloader;

    /**
     * A single instance of this class
     *
     * @var Sidebars
     */
    private static $instance = null;

    /**
     * @constructor
     *
     * @param WP_Autoloader $autoloader
     */
    private function __construct( WP_Autoloader $autoloader ) {
        $this->autoloader = $autoloader;
    }

    /**
     * Returns a single instance of this class
     *
     * @param WP_Autoloader|null $loader
     *
     * @return Sidebars
     */
    public static function get_instance( WP_Autoloader $loader = null ) {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self( $loader );
        }

        return self::$instance;
    }

    /**
     * Returns the plugins autoloader
     *
     * @return WP_Autoloader
     */
    public function get_autoloader() {
        return $this->autoloader;
    }

    /**
     * Runs at the "plugins_loaded" hook
     *
     * @return null
     */
    public function load() {
        add_action( 'rest_api_init', function() {
            $sidebars_controller = new Controllers\Sidebars_Controller;

            // register controller routes
            $sidebars_controller->register_routes();
        } );
    }
}

