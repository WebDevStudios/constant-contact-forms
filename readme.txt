=== Constant Contact Forms ===
Contributors:      constantcontact, webdevstudios, tw2113, znowebdev, ggwicz, ravedev, oceas, dcooney
Tags: capture, contacts, constant contact, constant contact form, constant contact newsletter, constant contact official, contact forms, email, form, forms, marketing, mobile, newsletter, opt-in, plugin, signup, subscribe, subscription, widget
Requires at least: 5.2.0
Tested up to:      5.7.0
Stable tag:        1.11.0
License:           GPLv3
License URI:       http://www.gnu.org/licenses/gpl-3.0.html
Requires PHP:      5.6

The official Constant Contact plugin adds a contact form to your WordPress site to quickly capture information from visitors.

== Description ==

##Work smarter, not harder. The Constant Contact Way
Create branded emails, build a website, sell online, and make it easy for people to find you—all from one place.

https://www.youtube.com/watch?v=Qqb0_zcRKnM

**Constant Contact Forms** is the easiest way to connect your WordPress website with your Constant Contact account.

-  Effortlessly create sign-up forms to convert your site visitors into mailing list contacts.
-  Customize data fields, so you can tailor the type of information you collect from your users.
-  Captured email addresses will be automatically added to the Constant Contact email lists of your choosing.

**BONUS**: If you have a Constant Contact account, all new email addresses that you capture will be automatically added to the Constant Contact email lists of your choosing. Not a Constant Contact customer? Sign up for a [Free Trial](https://go.constantcontact.com/signup.jsp) right from the plugin.


##How To Get Started.

1. Signup for a [Free Trial](http://www.constantcontact.com/index?pn=miwordpress). ( Existing Constant Contact users can skip this step).
2. Follow [first-time setup instructions](https://knowledgebase.constantcontact.com/articles/KnowledgeBase/10054-WordPress-Integration-with-Constant-Contact).
3. [Create your first form](https://knowledgebase.constantcontact.com/articles/KnowledgeBase/18059-Create-a-Wordpress-Form?q=create%20a%20form%20wordpress&pnx=1&lang).
4. [Add a form anywhere on your website](https://knowledgebase.constantcontact.com/articles/KnowledgeBase/30850-Add-a-Form-Created-with-the-Constant-Contact-Plugin-to-a-WordPress-Page-or-Blog-Post?lang).
5. Watch as your visitors turn into lifetime contacts!

== Screenshots ==
1. Adding a New form when connected to Constant Contact account.
2. Viewing All Forms
3. Lists Page
4. Settings page
5. Basic Form

== Changelog ==

= 1.11.0 =

- Updated: New admin styling to update the overall look and feel of the plugin in the WordPress admin.
- Added: Plugins etting to disable the loading on Constant Contact plugin CSS on the frontend.
- Added: Native browser validatio for phone number (tel) field input type.
- Fixed: Amended admin notification for Bad Request results.

= 1.10.1 =
* Fixed: Removed accidental extra parentheses on a function call.

= 1.10.0 =
* Added: Toggle to our Forms Block to display Form title or not.
* Added: Support for displaying a form in Beaver Builder.
* Added: Support for displaying a form in Elementor.
* Fixed: PHP Notices about usage of deprecated functions.
* Updated: Touched up styles for the Constant Contact Forms Block.

= 1.9.1 =
* Fixed: Removing duplicate IDs on form submit button.
* Fixed: Preventing `Bad Request` request errors from rendering an admin notice.
* Fixed: Various other admin and front-end fixes.

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
[HELP: Install the Constant Contact Forms Plugin for WordPress to Gather Sign-Ups and Feedback](https://knowledgebase.constantcontact.com/articles/KnowledgeBase/10054-WordPress-Integration-with-Constant-Contact)

#### Constant Contact Forms Options
[HELP: Add email opt-in to a WordPress Form created with the Constant Contact plugin](http://knowledgebase.constantcontact.com/articles/KnowledgeBase/18260-WordPress-Constant-Contact-Forms-Options)

#### Frequently Asked Questions
[HELP: Enable Logging in the Constant Contact Forms for WordPress Plugin](https://knowledgebase.constantcontact.com/articles/KnowledgeBase/18491-Enable-Logging-in-the-Constant-Contact-Forms-for-WordPress-Plugin)

#### Constant Contact List Addition Issues
[HELP: Troubleshooting List Addition Issues in the Constant Contact Forms Plugin for WordPress](https://knowledgebase.constantcontact.com/articles/KnowledgeBase/18539-WordPress-Constant-Contact-List-Addition-Issues)

#### cURL error 60: SSL certificate problem
[HELP: WordPress cURL Error 60: SSL Certificate Problem](https://knowledgebase.constantcontact.com/articles/KnowledgeBase/18159-WordPress-Error-60)

#### Add Google reCAPTCHA to Constant Contact Forms
[HELP: Add Google reCAPTCHA to Your WordPress Sign-up Form to Prevent Spam Entries](http://knowledgebase.constantcontact.com/articles/KnowledgeBase/17880)

#### How do I include which custom fields labels are which custom field values in my Constant Contact Account?
You can add this to your active theme or custom plugin: `add_filter( 'constant_contact_include_custom_field_label', '__return_true' );`. Note: custom fields have a max length of 50 characters. Including the labels will subtract from the 50 character total available.

#### Which account level access is needed to connect my WordPress account to Constant Contact?
You will need to make the connection to Constant Contact using the credentials of the account owner. Campaign manager credentials will not have enough access.


== Upgrade Notice ==
-  None
