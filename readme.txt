=== Constant Contact Forms ===
Contributors:      constantcontact, webdevstudios, tw2113, znowebdev, ggwicz, ravedev, oceas, dcooney, newyorkerlaura
Tags: constant contact, constant contact official, marketing, newsletter, contacts
Requires at least: 6.4.0
Tested up to:      6.8
Stable tag:        2.15.0
License:           GPLv3
License URI:       http://www.gnu.org/licenses/gpl-3.0.html
Requires PHP:      8.1

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

= 2.15.0 =
* Added: Anniversary and birthday form fields.

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

= 2.12.0 =
* Fixed: Fatal errors around list creation within WordPress dashboard.
* Fixed: Touchups and style bugs around Forms block.
* Fixed: Require list selection if site has a connected account but no list is chosen for form.
* Added: Ability to select the heading level when showing form title.
* Updated: Touched up styles and wording in form editor.
* Updated: Adjusted Google reCAPTCHA version 3 token timing. Assigned upon submit instead of pageload, to help avoid 2 minute expiration issues.

= 2.11.3 =
* Fixed: Email notifications being sent even when toggled off.
* Updated: Wording in various metaboxes and some fuzzy/blurry icons.

= 2.11.2 =
* Fixed: PHP errors regarding passed variable types expecting array but getting string
* Fixed: Checkbox widths with TwentyTwentyOne theme.

= 2.11.1 =
* Updated: restored missed php file that was causing fatal errors.

= 2.11.0 =
* Added: hCaptcha data to Site Health Panel
* Updated: Lots of internal code cleanup
* Updated: Removed old Constant Contact SDK code.
* Updated: Hide disclosure text below form if not connected to Constant Contact.
* Updated: Internal, switch to wp_admin_notice() usage.

= 2.10.0 =
* Added: Use current displayed language with Google reCAPTCHA when using WPML or PolyLang.
* Fixed: Issues around language specifications for Google reCAPTCHA.
* Fixed: WordPress notices around textdomain loading.
* Fixed: Added aria-label to disclosure external links for better ADA compliance
* Updated: Amended processes regarding failing API communications when human intervention needed. Includes preventing excessive attempts to refresh tokens in states where the attempt will fail.
* Updated: Increased notification chances if human intervention needed.
* Updated: Register list post type for Constant Contact Lists even if not yet connected.
* Updated: Notice regarding list management details.

== Upgrade Notice ==
* Fixes issues around reCAPTCHA, details related to connection issues, and PHP notices. Adds ability to choose address type.

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

### Error: Please select at least one list to subscribe to.
Some users are experiencing errors when upgrading from an older version of the plugin. If you are receiving an error "Please select at least one list to subscribe to" on your form submissions we recommend "Sync Lists with Constant Contact", this can be found in your admin dashboard Contact Form > Lists. If problem still persists we recommend recreating the form from scratch.
