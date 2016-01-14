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

namespace WP_API_Sidebars\Controllers;

use InvalidArgumentException;
use WP_REST_Controller;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

/**
 * Class Sidebars_Controller
 *
 * @package WP_API_Sidebars\Controllers
 */
class Sidebars_Controller extends WP_REST_Controller {
    /**
     * Registers the controllers routes
     *
     * @return null
     */
    public function register_routes() {
        register_rest_route( 'wp/v2', '/sidebars', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [ $this, 'get_items' ],
                'permission_callback' => [ $this, 'get_items_auth' ],
                'args'                => [],
            ],
        ] );

        register_rest_route( 'wp/v2', '/sidebars/(?P<id>[\w-]+)', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [ $this, 'get_item' ],
                'permission_callback' => [ $this, 'get_item_auth' ],
                'args'                => [
                    'id' => [
                        'description' => 'A sidebar id',
                        'type' => 'string',
                        'validate_callback' => function( $sidebar_id ) {
                            return ! is_null( $this->get_sidebar( $sidebar_id ) );
                        }
                    ],
                ],
            ],
        ] );
    }

    /**
     * Returns a list of registered sidebars
     *
     * @param WP_REST_Request $request
     *
     * @return WP_REST_Response
     */
    public function get_items( $request ) {
        // do type checking here as the method declaration must be compatible with parent
        if ( ! $request instanceof WP_REST_Request ) {
            throw new InvalidArgumentException( __METHOD__ . ' expects an instance of WP_REST_Request' );
        }

        global $wp_registered_sidebars;

        $sidebars = [];

        foreach ( (array) $wp_registered_sidebars as $slug => $sidebar ) {
            $sidebars[] = $sidebar;
        }

        return new WP_REST_Response( $sidebars, 200 );
    }

    /**
     * Validates the user to the route
     *
     * @param WP_REST_Request $request
     *
     * @return WP_Error|bool
     */
    public function get_items_auth( $request ) {
        // do type checking here as the method declaration must be compatible with parent
        if ( ! $request instanceof WP_REST_Request ) {
            throw new InvalidArgumentException( __METHOD__ . ' expects an instance of WP_REST_Request' );
        }

        return true;
    }

    /**
     * Returns the given sidebar
     *
     * @param WP_REST_Request $request
     *
     * @return WP_REST_Response
     */
    public function get_item( $request ) {
        // do type checking here as the method declaration must be compatible with parent
        if ( ! $request instanceof WP_REST_Request ) {
            throw new InvalidArgumentException( __METHOD__ . ' expects an instance of WP_REST_Request' );
        }

        $sidebar = $this->get_sidebar( $request->get_param( 'id' ) );

        ob_start();

        dynamic_sidebar( $request->get_param( 'id' ) );

        $sidebar['rendered'] = ob_get_clean();

        return new WP_REST_Response( $sidebar, 200 );
    }

    /**
     * Validates the user to the route
     *
     * @param WP_REST_Request $request
     *
     * @return WP_Error|bool
     */
    public function get_item_auth( $request ) {
        // do type checking here as the method declaration must be compatible with parent
        if ( ! $request instanceof WP_REST_Request ) {
            throw new InvalidArgumentException( __METHOD__ . ' expects an instance of WP_REST_Request' );
        }

        return true;
    }

    /**
     * Returns the given sidebar or false if not found
     *
     * @param string $sidebar_id
     *
     * @return array|null
     */
    protected function get_sidebar( $sidebar_id ) {
        global $wp_registered_sidebars;

        foreach ( (array) $wp_registered_sidebars as $id => $sidebar ) {
            if ( $id === $sidebar_id ) {
                return $sidebar;
            }
        }

        return null;
    }
}

