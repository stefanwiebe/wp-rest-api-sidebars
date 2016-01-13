# WP REST API Sidebars
An extension for the WP REST API that exposes endpoints for sidebars and widgets.

*note: this plugin is under heavy development and will receive frequent feature updates (including documentation) so stay tuned*

## Installation

Get the content to the plugin directory and activate the plugin

note: to enable you to use any version/branch of the [WP REST API][] plugin during this rapid development phase there is currently no good way to check if it is active. Therefore make sure that it is before you activate this plugin.

[WP REST API]: https://wordpress.org/plugins/rest-api

## Currently supported endpoints
**/wp-json/wp/v2/sidebars** *returns a list of registered sidebars*

```json
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
    }, ...
]
```

**/wp-json/wp/v2/sidebars/{id}** *returns the given sidebar*

```json
{
    "name": "Sidebar Name",
    "id": "sidebar-id",
    "description": "Sidebar description...",
    "class": "sidebar-class",
    "before_widget": "<aside id=\"%1$s\" class=\"widget %2$s\">",
    "after_widget": "<\/aside>",
    "before_title": "<h1 class=\"widget-title\">",
    "after_title": "<\/h1>",
    "rendered": "<aside id=\"widget-id-1\" class=\"widget widget_widget-id\">..."
}
```
