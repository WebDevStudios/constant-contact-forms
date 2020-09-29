=== Constant Contact Forms ===
Contributors:      constantcontact, webdevstudios, tw2113, znowebdev, ggwicz, ravedev
Tags: capture, contacts, constant contact, constant contact form, constant contact newsletter, constant contact official, contact forms, email, form, forms, marketing, mobile, newsletter, opt-in, plugin, signup, subscribe, subscription, widget
Requires at least: 5.2.0
Tested up to:      5.5.1
Stable tag:        1.9.0
License:           GPLv3
License URI:       http://www.gnu.org/licenses/gpl-3.0.html
Requires PHP:      5.6

The official Constant Contact plugin adds a contact form to your WordPress site to quickly capture information from visitors.

== Description ==

**Constant Contact Forms** makes it fast and easy to capture visitor information right from your WordPress site. Whether you’re looking to collect email addresses, contact info, or visitor feedback, you can customize your forms with data fields that work best for you. Best of all, this plugin is available to all WordPress users, even if you don’t have a Constant Contact account.

https://www.youtube.com/watch?v=Qqb0_zcRKnM

**Constant Contact Forms** allows you to:

* Create forms that are clear, simple, and mobile-optimized for every device.
* Choose forms that automatically select the theme and style of your WordPress site.
* Customize data fields, so you can tailor the type of information you collect.

BONUS: If you have a Constant Contact account, all new email addresses that you capture will be automatically added to the Constant Contact email lists of your choosing. Not a Constant Contact customer? Sign up for a [Free Trial](http://www.constantcontact.com/index?pn=miwordpress) right from the plugin.

**Constant Contact Forms** requires a PHP version of 5.6 or higher. You will not be able to use if on a lower version. Talk to your system administrator or hosting company if you are not sure what version you are on.

== Screenshots ==
1. Adding a New form when connected to Constant Contact account.
2. Viewing All Forms
3. Lists Page
4. Settings page
5. Basic Form

== Changelog ==

= 1.9.0 =
* Added: Ability for site owners to multiple lists for users to choose which to sign up for.
* Fixed: Issues around enabling list signup on user registration.
* Fixed: Unintended markup in email notification text.
* Fixed: Inappropriate or unneeded markup around hidden fields.
* Updated: Max length values for first/last name fields.
* Updated: Deprecated some functions in order to get consistent naming across all our code.

= 1.8.8 =
* Updated: Added extra security output escaping of custom input values

= 1.8.7 =
* Improved: Implemented recommendations for improved accessibility.
* Improved: Avoid duplicate ID attributes when more than one form is present on a page.
* Fixed: Submit button targeting for recaptcha v2.

= 1.8.6 =
* Fixed: Removed invalid property being sent to the API, causing rejected requests.
* Fixed: Failure to log API errors for support purposes.

= 1.8.5 =
* Added: Forced email notifications to admin when Constant Contact API request fails on attempted form submission.
* Fixed: Addressed issues with plugin error logging and addressed false-positive error messaging.
* Fixed: Cleaned up style minification task to allow for unminified version of stylesheet.
* Updated: Updated support error messaging in admin to reference tab structure of plugin settings.

= 1.8.4 =
* Fixed: Compatibility issue with PHP 5.6.

= 1.8.3 =
* Fixed: Potential compatibility issues around Gutenberg block.
* Fixed: Conflicts with multiple reCAPTCHAs on different Constant Contact Forms from the same page.
* Fixed: Conflicts with multiple reCAPTCHAs on the same Constant Contact Form on the page multiple times.
* Fixed: Removed incorrect usage of WordPress nonces on user submissions to forms.
* Fixed: Address missed logging enabling for cases that potentially lead to missing debugging information.

= 1.8.2 =
* Updated: Amended logging location for more hopefully more consistent write-ability and smoother support requests.

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

#### Which account level access is needed to connect my WordPress account to Constant Contact?
You will need to make the connection to Constant Contact using the credentials of the account owner. Campaign manager credentials will not have enough access.
