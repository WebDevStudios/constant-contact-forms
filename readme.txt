=== Constant Contact Forms ===
Contributors:      constantcontact, webdevstudios, tw2113, znowebdev, ggwicz, ravedev, oceas, dcooney, newyorkerlaura
Tags: constant contact, constant contact official, marketing, newsletter, contacts
Requires at least: 6.4.0
Tested up to:      6.9
Stable tag:        2.18.0
License:           GPLv3
License URI:       http://www.gnu.org/licenses/gpl-3.0.html
Requires PHP:      8.1

The official Constant Contact plugin adds a contact form to your WordPress site to quickly capture information from visitors.

== Description ==

## Work smarter, not harder. The Constant Contact Way
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

= 2.18.0 =
* Added: Revised refresh process to be more permissible of failures that are not expired refresh token related. Thanks JoeyYax.
* Updated: "Connect now" screen UI.
* Updated: details and visuals for the embed block.
* Updated: small visuals for WP 7.0.
* Updated: Extra error handling from empty API responses.
* Updated: internal code organization.
* Fixed: errors when deleting a form.
* Fixed: JS errors from CAPTCHA settings UI hiding, elsewhere in admin.

= 2.17.0 =
* Added: Hide UI of non-selected Captcha services until selected for usage.
* Added: Details regarding list status in Constant Contact account, to our forms list.
* Added: Email status or address destination column to Forms list table.
* Added: Messaging regarding user accounts on connect screen if a non-production install.
* Fixed: Issues around website field type not saving to custom field
* Updated: Moved custom field cap to 50 to match allowed contact field limit.
* Updated: Minor UI details around accessibility, wording, capitalization, visual spacing.

= 2.16.2 =
* Fixed: Errors regarding Captcha services
* Fixed: Warnings about array offsets.

= 2.16.1 =
* Updated: Amended some approaches in authentication process after previous release caused issues.
* Updated: return values for more precise troubleshooting
* Updated: delete code flag for manual reconnection on plugin deactivation.

= 2.16.0 =
* Added: Cloudflare Turnstile support
* Fixed: PHP warnings about name values from connected Constant Contact account.
* Updated: Revised API refresh token process to try and take a more active approach instead of just WP Cron based.
* Updated: Logging messages and data for troubleshooting API issues.
* Updated: Default language values for CAPTCHA services. Let the service autodetect instead of force English.
* Updated: Moved messaging about DISABLE_WP_CRON out of a notification and into Constant Contact Forms area top bar.

= 2.15.2 =
* Fixed: Fatal errors regarding strings and addition vs concatenation.

= 2.15.1 =
* Fixed: Compatibility issues around Monolog logger and other plugins using different versions.
* Fixed: PHP notice around custom fields if not managing to connect.
* Fixed: CMB2 Attached Post potential conflict with other plugins.
* Updated: aria-label wording for better compliance.

= 2.15.0 =
* Added: Moves PHP minimum requirement to version 8.1 or higher.
* Added: Anniversary and birthday form fields.
* Added: Max length limit to Form builder and our custom field inputs.
* Added: List display of existing custom fields from your Constant Contact Accout at bottom of form builder.
* Added: Reminder to set a list for a form, when connected.
* Fixed: Label style application for some positions.
* Updated: Adjusted logic regarding version 2.0.0 "major upgrade" admin notification.
* Updated: Show messaging in "Opt in" setting tab when not connected.
* Updated: Log library version.
* Updated: Improved log timestamp formats to make more visual sense.
* Updated: Removed internationalization files to rely on wordpress.org translations.

= 2.14.2 =
* Fixed: errors regarding Google reCAPTCHA v3 javascript variables.

= 2.14.1 =
* Fixed: Dashicon getting escaped instead of displaying, in custom menu spot.

= 2.14.0 =
* Fixed: Issues with Google reCAPTCHA version 3 and forms submitted without page refresh.
* Fixed: Logic around notification display in case manual intervention is needed.
* Added: Per-form field setting for a max-width value as a percentage.
* Added: Ability to choose the address type for address field. Example: home, work, other.
* Updated: Small visual indicators and wording for if connection issues exist.
* Updated: Visual details around "required" field indicators on forms, as well as accessibility improvements.
* Updated: Various PHP warnings and notices.

= 2.13.0 =
* Fixed: Details around address data retrieval from Constant Contact, used in disclosure text.
* Updated: Added extra theme compatibility for popular themes.
* Updated: Removed "edit" link on frontend form display to avoid potential confusion.
* Updated: Adjusted details around cron jobs related to API token tasks.
* Added: Admin area "edit form" link output for chosen form in Constant Contact Forms Block.
* Added: Keywords for Elementor widget integration.
* Added: Dedicated color picker for form title display.
* Added: Ability to display form horizontally when using just the email field.

== Upgrade Notice ==
* Various UI enhancements in builder and settings, bug fixes, list/email statuses for forms, messaging regarding non-production URLs with connections.

== Frequently Asked Questions ==

#### Installation and Setup
[HELP: Install the Constant Contact Forms Plugin for WordPress to Gather Sign-Ups and Feedback](https://knowledgebase.constantcontact.com/email-digital-marketing/articles/KnowledgeBase/10054-Install-the-Constant-Contact-Forms-plugin-for-WordPress-to-gather-sign-ups-and-feedback?lang=en_US)

#### Constant Contact Forms Options
[HELP: Add email opt-in to a WordPress Form created with the Constant Contact plugin](https://knowledgebase.constantcontact.com/email-digital-marketing/articles/KnowledgeBase/18260-Add-email-opt-in-to-a-WordPress-Form-created-with-the-Constant-Contact-plugin?lang=en_US)

#### Frequently Asked Questions
[HELP: Enable Logging in the Constant Contact Forms for WordPress Plugin](https://knowledgebase.constantcontact.com/email-digital-marketing/articles/KnowledgeBase/18491-Enable-logging-in-the-Constant-Contact-Forms-for-WordPress-Plugin?lang=en_US)

#### Constant Contact List Addition Issues
[HELP: Troubleshooting List Addition Issues in the Constant Contact Forms Plugin for WordPress](https://knowledgebase.constantcontact.com/email-digital-marketing/articles/KnowledgeBase/18539-Troubleshooting-list-addition-issues-in-the-Constant-Contact-Forms-Plugin-for-WordPress?lang=en_US)

#### cURL error 60: SSL certificate problem
[HELP: WordPress cURL Error 60: SSL Certificate Problem](https://knowledgebase.constantcontact.com/email-digital-marketing/articles/KnowledgeBase/18159-WordPress-cURL-error-60-SSL-certificate-problem?lang=en_US)

#### Add Google reCAPTCHA to Constant Contact Forms
[HELP: Add Google reCAPTCHA to Your WordPress Sign-up Form to Prevent Spam Entries](https://knowledgebase.constantcontact.com/email-digital-marketing/articles/KnowledgeBase/17880-Add-Google-reCAPTCHA-to-your-WordPress-sign-up-form-to-prevent-spam-entries?lang=en_US)

#### How do I include which custom fields labels are which custom field values in my Constant Contact Account?
You can add this to your active theme or custom plugin: `add_filter( 'constant_contact_include_custom_field_label', '__return_true' );`. Note: custom fields have a max length of 50 characters. Including the labels will subtract from the 50 character total available.

#### Which account level access is needed to connect my WordPress account to Constant Contact?
You will need to make the connection to Constant Contact using the credentials of the account owner. Campaign manager credentials will not have enough access.

#### Error: Please select at least one list to subscribe to.
Some users are experiencing errors when upgrading from an older version of the plugin. If you are receiving an error "Please select at least one list to subscribe to" on your form submissions we recommend "Sync Lists with Constant Contact", this can be found in your admin dashboard Contact Form > Lists. If problem still persists we recommend recreating the form from scratch.

#### Version 2.0.x
Version 2.0.0 of this plugin is a significant release, including both security and feature updates. After updating to version 2.0.0, you will be required to reconnect the plugin to your Constant Contact account & reselect the lists associated with your forms.
