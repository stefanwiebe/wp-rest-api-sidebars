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
        register_rest_route( 'wp-rest-api-sidebars/v1', '/sidebars', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [ $this, 'get_items' ],
            ],
        ] );

        register_rest_route( 'wp-rest-api-sidebars/v1', '/sidebars/(?P<id>[\w-]+)', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [ $this, 'get_item' ],
                'args'                => [
                    'id' => [
                        'description' => 'The id of a registered sidebar',
                        'type' => 'string',
                        'validate_callback' => function ( $sidebar_id ) {
                            return ! is_null( self::get_sidebar( $sidebar_id ) );
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
        $sidebar['widgets'] = self::get_widgets( $sidebar['id'] );

        return new WP_REST_Response( $sidebar, 200 );
    }

    /**
     * Returns the given sidebar or false if not found
     *
     * @global array $wp_registered_sidebars
     *
     * @param string $id
     *
     * @return array|null
     */
    public static function get_sidebar( $id ) {
        global $wp_registered_sidebars;

        if ( is_int( $id ) ) {
            $id = 'sidebar-' . $id;
        } else {
            $id = sanitize_title( $id );

            foreach ( (array) $wp_registered_sidebars as $key => $sidebar ) {
                if ( sanitize_title( $sidebar['name'] ) == $id ) {
                    return $sidebar;
                }
            }
        }

        foreach ( (array) $wp_registered_sidebars as $key => $sidebar ) {
            if ( $key === $id ) {
                return $sidebar;
            }
        }

        return null;
    }

    /**
     * Returns the given sidebars widgets
     *
     * @global array $wp_registered_widgets
     * @global array $wp_registered_sidebars
     *
     * @param string $sidebar_id
     *
     * @return array
     */
    public static function get_widgets( $sidebar_id ) {
        global $wp_registered_widgets, $wp_registered_sidebars;

        $widgets = [];
        $sidebars_widgets = wp_get_sidebars_widgets();

        if ( isset( $wp_registered_sidebars[ $sidebar_id ] ) && isset( $sidebars_widgets[ $sidebar_id ] ) ) {
            foreach ( (array) $sidebars_widgets[ $sidebar_id ] as $widget_id ) {
                // just to be sure
                if ( isset( $wp_registered_widgets[ $widget_id ] ) ) {
                    $widget = $wp_registered_widgets[ $widget_id ];

                    // get the widget output
                    if ( is_callable( $widget['callback'] ) ) {
                        // @note: everything up to ob_start is taken from the dynamic_sidebar function
                        $widget_parameters = array_merge(
                            [
                                array_merge( $wp_registered_sidebars[ $sidebar_id ], [
                                    'widget_id' => $widget_id,
                                    'widget_name' => $widget['name'],
                                ] )
                            ],
                            (array) $widget['params']
                        );

                        $classname = '';
                        foreach ( (array) $widget['classname'] as $cn ) {
                            if ( is_string( $cn ) )
                                $classname .= '_' . $cn;
                            elseif ( is_object( $cn ) )
                                $classname .= '_' . get_class( $cn );
                        }
                        $classname = ltrim( $classname, '_' );
                        $widget_parameters[0]['before_widget'] = sprintf( $widget_parameters[0]['before_widget'], $widget_id, $classname );

                        ob_start();

                        call_user_func_array( $widget['callback'], $widget_parameters );

                        $widget['rendered'] = ob_get_clean();
                    }

                    unset( $widget['callback'] );
                    unset( $widget['params'] );

                    $widgets[] = $widget;
                }
            }
        }

        return $widgets;
    }
}

