=== WP REST API Sidebars ===
Contributors: njetskive
Donate link: http://example.com/
Tags: api, json, REST, rest-api, sidebar, sidebars, widget, widgets
Requires at least: 4.4
Tested up to: 4.4.1
Stable tag: 0.1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

An extension for the WP REST API that exposes endpoints for sidebars and widgets.

== Description ==

An extension for the WP REST API that exposes endpoints for sidebars and widgets.

*note: this plugin is under heavy development and will receive frequent feature updates (including documentation) so stay tuned and checkout the [github][] repo for the latest updates*
[github]: https://github.com/martin-pettersson/wp-rest-api-sidebars

= Currently supported endpoints =

**/wp-json/wp-rest-api-sidebars/v1/sidebars** *returns a list of registered sidebars*

<pre><code>
[
    {
        "name": "Sidebar Name",
        "id": "sidebar-id",
        "description": "Sidebar description...",
        "class": "sidebar-class",
        "before_widget": "<aside id=\"%1$s\" class=\"widget %2$s\">",
        "after_widget": "<\/aside>",
        "before_title": "<h1 class=\"widget-title\">",
        "after_title": "<\/h1>"
    }
]
</code></pre>

**/wp-json/wp-rest-api-sidebars/v1/sidebars/{id}** *returns the given sidebar*

<pre><code>
{
    "name": "Sidebar Name",
    "id": "sidebar-id",
    "description": "Sidebar description...",
    "class": "sidebar-class",
    "before_widget": "<aside id=\"%1$s\" class=\"widget %2$s\">",
    "after_widget": "<\/aside>",
    "before_title": "<h1 class=\"widget-title\">",
    "after_title": "<\/h1>",
    "rendered": "<aside id=\"widget-id-1\" class=\"widget widget_widget-id\">...",
    "widgets": [
        {
            "name": "Widget Name",
            "id": "widget-name-1",
            "classname": "widget_widget_name",
            "description": "Widget description",
            "rendered": "<aside id=\"widget-names-1\" class=\"widget widget_widget_name\">..."
        }
    ]
}
</code></pre>

== Installation ==

Get the content to the plugin directory and activate the plugin

note: to enable you to use any version/branch of the [WP REST API][] plugin during this rapid development phase there is currently no good way to check if it is active. Therefore make sure that it is before you activate this plugin.

[WP REST API]: https://wordpress.org/plugins/rest-api

== Changelog ==

= 0.1.1 =
* Now using a plugin specific endpoint namespace: /wp-rest-api-sidebars/v1
* Including widgets in the /sidebars/{id} response

== Upgrade Notice ==

= 0.1.1 =
This release changes the endpoint namespace and therefore breaks compatibility with any active clients.

