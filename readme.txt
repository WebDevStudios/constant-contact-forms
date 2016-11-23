=== Constant Contact Forms ===
Contributors:      constantcontact
Tags:
Requires at least: 4.0.0
Tested up to:      4.6.1
Stable tag:        1.1.0
License:           GPLv2
License URI:       http://www.gnu.org/licenses/gpl-2.0.html

Add a contact form to your WordPress site and quickly capture information from visitors.

== Description ==

**Constant Contact Forms** makes it fast and easy to capture visitor information right from your WordPress site. Whether you’re looking to collect email addresses, contact info, event sign-ups, or visitor feedback, you can customize your forms with data fields that work best for you. Best of all, this plugin is available to all WordPress users, even if you don’t have a Constant Contact account.

https://www.youtube.com/watch?v=MhxtAlpZzJw

**Constant Contact Forms** allows you to:

* Create forms that are clear, simple, and mobile-optimized for every device.
* Choose forms that automatically select the theme and style of your WordPress site.
* Customize data fields, so you can tailor the type of information you collect.

BONUS: If you have a Constant Contact account, all new email addresses that you capture will be automatically added to the Constant Contact email lists of your choosing. Not a Constant Contact customer? Sign up for a Free Trial right from the plugin.

**Constant Contact Forms** requires a PHP version of 5.4 or higher. You will not be able to use if on a lower version. Talk to your system administrator or hosting company if you are not sure what version you are on.

== Screenshots ==
1. Adding a New form
2. Viewing All Forms
3. Lists Page
4. Basic Form

== Changelog ==

= 1.2.0 =
* Added: Reveal shortcode for newly published form in admin notice and popup for non-connected accounts.

= 1.1.0 =
* Added: Widget that allows you to select a form to display.
* Added: Small metabox in form editor showing shortcode to use for current form.
* Added: Field and filter for text shown on the rendered submit button.
* Added: Developers: Inline documentation of actions and filters.
* Fixed: Loading position of Constant Contact stylesheet. Should now load in `<head>`.
* Fixed: Removed redundant "Add form" button on Constant Contact form editor TinyMCE.
* Fixed: Removed required attribute for Address line 2 when line 1 is required.
* Updated: Labels in Constant Contact Form list around none available and none in trash.

= 1.0.3 =
* Fixed: Improperly placed content for Constant Contact API requests for phone and website fields.
* Updated: Default text for admin email subject line and email footer copy.

= 1.0.2 =
* Update copyright information.
* Remove Form Options for users who aren't connected to Constant Contact.
* Clean up API fields.

= 1.0.1 =
* Fixed: issue with PHP 5.5+ syntax when we need 5.4+
* Added: Prevention of plugin loading for users below PHP version 5.4 to avoid incompatibility issues.

= 1.0.0 =
* Initial Release
