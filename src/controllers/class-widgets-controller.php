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
use WP_Widget;
use WP_Error;
use WP_API_Sidebars\Sidebars;

/**
 * Class Widgets_Controller
 *
 * @package WP_API_Sidebars\Controllers
 */
class Widgets_Controller extends WP_REST_Controller {
    /**
     * Registers the controllers routes
     *
     * @return null
     */
    public function register_routes() {
        register_rest_route( Sidebars::ENDPOINT_NAMESPACE, '/widgets', [
            [
                'methods'  => WP_REST_Server::READABLE,
                'callback' => [ $this, 'get_items' ],
            ],
        ] );
    }

    /**
     * Returns a list of registered widgets
     *
     * @global array $wp_registered_widgets
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

        global $wp_registered_widgets;

        $widgets = [];

        foreach ( (array) $wp_registered_widgets as $key => $widget ) {
            if ( isset( $widget['callback'][0] ) && $widget['callback'][0] instanceof WP_Widget ) {
                $widget_instance = $widget['callback'][0];

                $unique_widget = ! in_array( $widget_instance->id_base, array_map( function ( $widget ) {
                    return $widget['id_base'];
                }, $widgets ) );

                // only push unique widgets as we are not interested in the instances
                if ( $unique_widget ) {
                    $widget['name'] = $widget_instance->name;
                    $widget['id_base'] = $widget_instance->id_base;
                    $widget['option_name'] = $widget_instance->option_name;
                    $widget['instances'] = 0;
                    $widget['sidebars'] = [];

                    // list the sidebars this widget has instances in, and the instance id's
                    foreach ( (array) wp_get_sidebars_widgets() as $sidebar_id => $sidebar_widgets ) {
                        foreach ( $sidebar_widgets as $widget_id ) {
                            if ( preg_match( "/({$widget['id_base']}-\d)/", $widget_id, $match ) ) {
                                if ( ! isset( $widget['sidebars'][ $sidebar_id ] ) ) {
                                    $widget['sidebars'][ $sidebar_id ] = [];
                                }

                                ++$widget['instances'];
                                $widget['sidebars'][ $sidebar_id ][] = $widget_id;
                            }
                        }
                    }

                    unset( $widget['id'] );
                    unset( $widget['params'] );
                    unset( $widget['callback'] );

                    $widgets[] = $widget;
                }
            }
        }

        return new WP_REST_Response( $widgets, 200 );
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

        // ...
    }
}

