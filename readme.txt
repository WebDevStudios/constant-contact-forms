=== Constant Contact Forms ===
Contributors:      constantcontact, webdevstudios, tw2113, znowebdev, ggwicz, ravedev, oceas, dcooney, newyorkerlaura
Tags: contacts, constant contact, constant contact form, constant contact newsletter, constant contact official, contact forms, forms, marketing, newsletter, signup
Requires at least: 5.2.0
Tested up to:      6.4.3
Stable tag:        2.4.4
License:           GPLv3
License URI:       http://www.gnu.org/licenses/gpl-3.0.html
Requires PHP:      7.4

The official Constant Contact plugin adds a contact form to your WordPress site to quickly capture information from visitors.

== Description ==

Please note: Version 2.0.0 of this plugin is a significant release, including both security and feature updates. After updating to version 2.0.0, you will be required to reconnect the plugin to your Constant Contact account & reselect the lists associated with your forms.

##Work smarter, not harder. The Constant Contact Way
Create branded emails, build a website, sell online, and make it easy for people to find you—all from one place.

https://www.youtube.com/watch?v=Qqb0_zcRKnM

**Constant Contact Forms** is the easiest way to connect your WordPress website with your Constant Contact account.

-  Effortlessly create sign-up forms to convert your site visitors into mailing list contacts.
-  Customize data fields, so you can tailor the type of information you collect from your users.
-  Captured email addresses will be automatically added to the Constant Contact email lists of your choosing.
-  Have immediate access to all your Constant Contact lists to integrate with your signup forms, right from your WordPress dashboard

Not a Constant Contact customer? Sign up for a [Free Trial](https://go.constantcontact.com/signup.jsp) right from the plugin.

##How To Get Started.

1. Signup for a [Free Trial](http://www.constantcontact.com/index?pn=miwordpress). ( Existing Constant Contact users can skip this step).
2. Follow [first-time setup instructions](https://knowledgebase.constantcontact.com/articles/KnowledgeBase/10054-WordPress-Integration-with-Constant-Contact).
3. [Create your first form](https://knowledgebase.constantcontact.com/articles/KnowledgeBase/18059-Create-a-Wordpress-Form?q=create%20a%20form%20wordpress&pnx=1&lang).
4. [Add a form anywhere on your website](https://knowledgebase.constantcontact.com/articles/KnowledgeBase/30850-Add-a-Form-Created-with-the-Constant-Contact-Plugin-to-a-WordPress-Page-or-Blog-Post?lang).
5. Watch as your visitors turn into lifetime contacts!

## Development

Development of Constant Contact Forms plugin occurs on [GitHub](https://github.com/WebDevStudios/constant-contact-forms). Please see the security policy there to report any security issues. General support should start on our [WordPress forums](https://wordpress.org/support/plugin/constant-contact-forms/)

== Screenshots ==
1. Adding a New form when connected to Constant Contact account.
2. Viewing All Forms
3. Lists Page
4. Settings page
5. Basic Form

== Changelog ==

= 2.4.4 =
* Fixed: Consistently set address kind to "home".

= 2.4.3 =
* Fixed: Hardened up details around error log files and access.

= 2.4.2 =
* Fixed: Extra early return check before we process form submissions.
* Updated: Accessibility around disabled submit buttons during form processing.

= 2.4.1 =
* Fixed: White font on light gray in admin page modal popups.
* Fixed: Prevent potential PHP fatal errors with access token fetching.

= 2.4.0 =
* Updated: Removed reliance on jQuery library for frontend scripts.
* Updated: Individual address fields and available required fields match up to what's included.
* Updated: Internal logging library.
* Updated: Moved disclosure messaging to outside of the generated `<form>` tag.
* Added: Include list name in sent admin emails.
* Fixed: Empty list information when creating a list in WordPress Dashboard.
* Fixed: Prevent fatal errors in functionality that checks for a note.

= 2.3.0 =
* Added: Background catch for new contact API requests that fail due to need to re-authenticate. Requests will be re-tried once newly reconnected.
* Added: Site health integration to help with debugging and troubleshooting.
* Updated: Removed reliance on WP Cron for sending submissions. All API submission should be run right away from now on. "Bypass cron" setting negated.
* Updated: Amended "address" field to allow for choosing which address components to use as well as allow requiring only certain components.

= 2.2.0 =
* Added: Admin notice if its been determined that the account connection needs human intervention.
* Updated: Revised and improved access token refreshing in the background.
* Updated: Removed Google Analytics opt-in option.
* Updated: Prevent Constant Contact WooCommerce lists from being imported to local lists.

= 2.1.0 =
* Fixed: Option saving process for Multisite installs.
* Fixed: Further touchups and fixes around reported security vulnerabilities.
* Fixed: Prevent potential issues with `lists` property and contact actions.
* Fixed: Compatibility with other oAuth2 based services. Specifically: Site Kit at this time.
* Updated: Obfuscated API values in debug logs.
* Updated: Adjusted account information lookup frequency. Reduced to every 12 hours.
* Updated: Adjusted and fixed up details around custom field usage and needing unique labels.

= 2.0.3 =
* Updated: Resolution for fatal errors regarding autoloading.
* Updated: Further security fixes for reported issues.

= 2.0.2 =
* Updated: Rate limits on API requests. Should help address list sync issues.
* Added: Notifications of issues with list fetching with API version 3. Shorter transient cache time.
* Added: More conditions to show "APIv3 upgrade needed" notice while needed.

= 2.0.1 =
* Fixed: Fatal error regarding objects and arrays upon update to 2.0.0

= 2.0.0 =
* Updated: Plugin has been migrated to use Constant Contact API version 3.0. This will require new authentication workflow.
* Updated: Addressed security issues with regards to opt-in notification.
* Updated: Added support to check for DISABLE_WP_CRON constant usage and bypass cron scheduling if true.
* Fixed: moved "Edit form" link to outside the `<form>` markup.
* Fixed: Custom color choices were not applying to all parts of form text output.

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

### Error: Please select at least one list to subscribe to.
Some users are experiencing errors when upgrading from an older version of the plugin. If you are receiving an error "Please select at least one list to subscribe to" on your form submissions we recommend "Sync Lists with Constant Contact", this can be found in your admin dashboard Contact Form > Lists. If problem still persists we recommend recreating the form from scratch.

== Upgrade Notice ==
* We will soon be releasing a version of Constant Contact Forms that has a major upgrade to our API behind the scenes. Information will be provided at the time to help ease the process. You should not need to recreate any existing forms, but will need to re-authenticate.
