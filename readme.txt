=== Constant Contact Forms ===
Contributors:      constantcontact, webdevstudios, tw2113, znowebdev, ggwicz, ravedev
Tags: capture, contacts, constant contact, constant contact form, constant contact newsletter, constant contact official, contact forms, email, form, forms, marketing, mobile, newsletter, opt-in, plugin, signup, subscribe, subscription, widget
Requires at least: 5.2.0
Tested up to:      5.3.2
Stable tag:        1.8.1
License:           GPLv3
License URI:       http://www.gnu.org/licenses/gpl-3.0.html
Requires PHP:      5.6

The official Constant Contact plugin adds a contact form to your WordPress site to quickly capture information from visitors.

== Description ==

**Constant Contact Forms** makes it fast and easy to capture visitor information right from your WordPress site. Whether you’re looking to collect email addresses, contact info, or visitor feedback, you can customize your forms with data fields that work best for you. Best of all, this plugin is available to all WordPress users, even if you don’t have a Constant Contact account.

https://www.youtube.com/watch?v=MhxtAlpZzJw

**Constant Contact Forms** allows you to:

* Create forms that are clear, simple, and mobile-optimized for every device.
* Choose forms that automatically select the theme and style of your WordPress site.
* Customize data fields, so you can tailor the type of information you collect.

BONUS: If you have a Constant Contact account, all new email addresses that you capture will be automatically added to the Constant Contact email lists of your choosing. Not a Constant Contact customer? Sign up for a [Free Trial](http://www.constantcontact.com/index?pn=miwordpress) right from the plugin.

**Constant Contact Forms** requires a PHP version of 5.4 or higher. You will not be able to use if on a lower version. Talk to your system administrator or hosting company if you are not sure what version you are on.

== Screenshots ==
1. Adding a New form when connected to Constant Contact account.
2. Viewing All Forms
3. Lists Page
4. Settings page
5. Basic Form

== Changelog ==

= 1.8.1 =
* Fixed: Google reCAPTCHA issues with jQuery dependencies.
* Fixed: Google reCAPTCHA undefined class errors when `allow_url_fopen` is disabled.
* Fixed: Array to string errors when API errors occurred.

= 1.8.0 =
* Added: Form and field IDs parameters to the `constant_contact_input_classes` filters.
* Added: Site owners will be notified if they have stray shortcodes or widgets using a newly deleted form.
* Added: Separated the settings page into tabs for better purpose organization.
* Updated: Reduced frequency of admin notifications for potentially momentary issues.
* Updated: Clarified details regarding "Redirect URL" setting.

= 1.7.0 =
* New - Added support for Google reCAPTCHA version 3
* Fix - Fixed with debug log deletion and dialog closing
* Fix - Updated a number of PHP and JavaScript dependencies

= 1.6.1 =
* Fixed: Issue with selecting forms in the widget.
* Fixed: Compatibility with other page builders and our Gutenberg integration.
* Updated: Revised wording and links for admin notice about potential issues.

= 1.6.0 =
* Addded: Uninstall routine to remove various options saved from use of the plugin, when uninstalling.
* Updated: Improved handling of potential fatal errors that caused sites to become unusable.
* Updated: Completely removed TinyMCE support in favor of Gutenberg block and copy/pasting existing shortcode output.
* Updated: Reviewed and improved on overall plugin accessibility.
* Updated: Hardened up sanitization around Google reCAPTCHA settings.
* Fixed: Inability to remove admin notices in some cases.
* Fixed: Addressed admin notice meant to show at a later time that showed right away.
* Fixed: Submission issues when multiple forms are on the same page and "no-refresh" option is used.
* Fixed: Add "show_title" attribute to List Column shortcode output.

= 1.5.3 =
* Fixed: Removed TGMPA library files that were causing some conflicts with premium themes or other plugins.
* Fixed: tweaked shortcode assets URL reference in bundled library for better compatibility with various hosting environments.

= 1.5.2 =
* Fixed: Javascript conflicts with Lodash and Underscores in conjunction with 1.5.0's Gutenberg support.

= 1.5.1 =
* Fixed: Issues with editor screen when no forms have been created yet.
* Fixed: Missed endpoint change for wp-json details with Contant Contact Gutenberg integration.

= 1.5.0 =
* Added: Gutenberg block. Easier to get a form set up on a Gutenberg powered post or page.
* Added: Ability to customize "We do not think you are human" spam messaging.
* Added: Ability to conditionally output a reCAPTCHA field for each form.
* Added: Better compatibility with WP-SpamShield plugin.
* Added: Quick button to reset a form's style customization selections.
* Added: Option to display form title with Constant Contact Forms output.
* Fixed: Added missing label placement options in settings page and per-form dropdown options.
* Updated: Ensure we have valid URLs when taking custom redirect values.
* Updated: Append custom textarea content to existing notes for updated contacts.
* Updated: Added some "alert" roles for better accessibility.
* Updated: Added logging of API request parameters before the request is made.
* Updated: Added logging around valid requests verifications when submitting a form.

= 1.4.5 =
* Fixed: Conflicts with custom textareas and notes inside of Constant Contact account when updating an existing contact.
* Fixed: Potential issues around reading Constant Contact Forms error logs when log file is potentially not readable.

= 1.4.4 =
* Fixed: Hardened reCAPTCHA and form processing from possible AJAX bypass.

= 1.4.3 =
* Fixed: Persistent spinner on Constant Contact Forms submit button when Google reCAPTCHA is anywhere on the page.
* Fixed: Better messaging around debug logging when unable to write to the intended log file.
* Updated: Changed the modal popup content for when we need to display Endurance Privacy Policy information.

= 1.4.2 =
* Fixed: Issue with mismatched meta key for per-form destination email address.
* Fixed: Ability to successfully submit a form with Google reCAPTCHA enabled, but when not validated, with a custom redirect URL is set.
* Fixed: Prevent errors if Debug Log location is not writeable by the plugin.

= 1.4.1 =
* Fixed: Issue with generic CSS selector causing other WordPress admin UI to be revealed unintentionally.
* Fixed: Issue with emails losing submitted information due to newly mismatched md5 hash values for each field.
* Updated: Re-added outlines styles in a couple of places in admin area for accessibility sake.
* Updated: Made form ID optional during contact addition method for site owners using plugin for comment/login page signups.

= 1.4.0 =
* Added: Various styling options during the form building process.
* Added: Initial Akismet integration to help aid with spam submissions.
* Added: Clear form fields after successful AJAX-based form submissions.
* Added: Clear success/error message after small delay, for AJAX-based form submissions.
* Added: WordPress action hooks before and after form output. Useful to add your own output for a given form.
* Added: Compatibility with "Call To Action" plugin.
* Added: Include custom field labels in email notifications.
* Added: Ability to customize who receives email notifications, per form.
* Added: Frontend form submit button disabled if hidden honeypot field has changed.
* Fixed: Consistently applied ctct_process_form_success filter to AJAX form submission success messages.
* Fixed: Prevent errors with Constant Contact social links and array_merge issues.
* Fixed: Prevent errors with array_key_exists() and the ctct_get_settings_option function.
* Fixed: Wording around associated lists for a form, in the WordPress admin.
* Fixed: Removed .gitignore files from /vendor folders.
* Fixed: Prevent potential PHP warnings and notices in various areas.
* Updated: Better support for emailing notifications to multiple recipiants.
* Updated: Better disabling of submit button during AJAX-based submissions.
* Updated: Tightened up form builder screen to not use so much space.

== Frequently Asked Questions ==

#### Installation and Setup
[https://knowledgebase.constantcontact.com/articles/KnowledgeBase/10054-WordPress-Integration-with-Constant-Contact](https://knowledgebase.constantcontact.com/articles/KnowledgeBase/10054-WordPress-Integration-with-Constant-Contact)

#### Constant Contact Forms Options
[http://knowledgebase.constantcontact.com/articles/KnowledgeBase/18260-WordPress-Constant-Contact-Forms-Options](http://knowledgebase.constantcontact.com/articles/KnowledgeBase/18260-WordPress-Constant-Contact-Forms-Options)

#### Frequently Asked Questions
[https://knowledgebase.constantcontact.com/articles/KnowledgeBase/18491-Enable-Logging-in-the-Constant-Contact-Forms-for-WordPress-Plugin](https://knowledgebase.constantcontact.com/articles/KnowledgeBase/18491-Enable-Logging-in-the-Constant-Contact-Forms-for-WordPress-Plugin)

#### Constant Contact List Addition Issues
[https://knowledgebase.constantcontact.com/articles/KnowledgeBase/18539-WordPress-Constant-Contact-List-Addition-Issues](https://knowledgebase.constantcontact.com/articles/KnowledgeBase/18539-WordPress-Constant-Contact-List-Addition-Issues)

#### cURL error 60: SSL certificate problem
[https://knowledgebase.constantcontact.com/articles/KnowledgeBase/18159-WordPress-Error-60](https://knowledgebase.constantcontact.com/articles/KnowledgeBase/18159-WordPress-Error-60)

#### Add Google reCAPTCHA to Constant Contact Forms
[http://knowledgebase.constantcontact.com/articles/KnowledgeBase/17880](http://knowledgebase.constantcontact.com/articles/KnowledgeBase/17880)

#### How do I include which custom fields labels are which custom field values in my Constant Contact Account?
You can add this to your active theme or custom plugin: `add_filter( 'constant_contact_include_custom_field_label', '__return_true' );`. Note: custom fields have a max length of 50 characters. Including the labels will subtract from the 50 character total available.
