=== Bonza Quote Management Plugin ===
Contributors: your-github-username
Tags: quotes, form, admin, approval, service requests, shortcode
Requires at least: 5.5
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A custom WordPress plugin to manage incoming service quote requests, admin approval workflows, and notifications.

== Description ==

Bonza Quote Management Plugin provides a frontend shortcode form and a backend admin dashboard to simulate quote request workflows. Users can submit service quote requests, and admins can manage their status.

This plugin was developed as part of a technical task to demonstrate WordPress plugin development skills using OOP, custom post types, and security best practices.

= Frontend Features =
- `[bonza_quote_form]` shortcode
- Fields: Name, Email, Service Type, Notes
- On submit, saves a custom post type with status 'pending'
- Displays a success message after submission

= Admin Features =
- Adds a new admin menu: "Bonza Quotes"
- Table view of submitted quotes
- Admin can approve or reject quotes via status dropdown

= Technical Highlights =
- Object-Oriented PHP structure
- Secure handling with sanitization and escaping
- Modular code structure for easy maintenance
- Hooks for developers: `bonza_quote_submitted`, `bonza_quote_status_updated`

= Bonus Features =
- Email notification to admin upon new quote submission

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/bonza-quote-manager` directory, or install via Git.
2. Activate the plugin through the ‘Plugins’ screen in WordPress.
3. Add the `[bonza_quote_form]` shortcode to any post or page.

== Frequently Asked Questions ==

= Does this plugin store quotes as posts or in a custom database table? =

It uses a custom post type called `bonza_quote`.

= Will it work with my theme? =

Yes. The plugin uses basic HTML elements that inherit your theme's styles.

== Screenshots ==

1. Frontend quote form
2. Admin quote management table

== Changelog ==

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.0.0 =
First stable release. Safe to install.

== License ==

This plugin is licensed under the GNU General Public License v2.0 or later.  
See [https://www.gnu.org/licenses/gpl-2.0.html](https://www.gnu.org/licenses/gpl-2.0.html) for full license text.
