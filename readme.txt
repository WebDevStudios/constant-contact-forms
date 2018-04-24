=== Constant Contact Forms ===
Contributors:      constantcontact
Tags: capture, contacts, constant contact, constant contact form, constant contact newsletter, constant contact official, contact forms, email, form, forms, marketing, mobile, newsletter, opt-in, plugin, signup, subscribe, subscription, widget
Requires at least: 4.0.0
Tested up to:      4.9.4
Stable tag:        1.3.7
License:           GPLv2
License URI:       http://www.gnu.org/licenses/gpl-2.0.html
Requires PHP:      5.4

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
= 1.3.7 =
* Added: Logging functionality to help aid with debugging and the plugin not working as needed or expected.
* Added: Passed form ID to filters related to including labels for custom fields.
* Fixed: Made sure some Constant Contact markup was only added to the page when in a Constant Contact area.
* Fixed: Issue with submitted custom field lengths when also including the original labels. Users were able to still go above 50 character limit.
* Fixed: Addressed issue with email bypassing when not needing to opt in.
* Updated: CMB2 library to version 2.3.0.
* Updated: Guzzle library to version 5.3.2.
* Updated: Code quality regarding translated text, namespaces, and returned value consistency.
* Updated: Added minimum PHP version to plugin readme for use on WordPress.org.
* Updated: Reduced cache time for account information so changes reflect in WP admin more quickly.
* Updated: Revised disclosure message on the form to be more GDPR compliant.

= 1.3.6 =
* Fixed: Validate and potentially create full urls for values passed into custom "redirect to url" values. Prevents unintended destinations from partial urls.
* Fixed: Error display for cron debugging was not showing correctly.
* Fixed: Added required indicators to all appropriate fields in address fieldset on frontend.
* Fixed: No form will be displayed if the form is not published.
* Added: Address line 2 is no longer considered a required field.
* Added: Plugin will now send email notifications if notifications are disabled but no Constant Contact list has been set for a form or opt-in is required but user does not opt in. Prevents possible lost submissions.
* Updated: Wording for "Disable email" setting to clarify its purpose and intent.
* Updated: Wording around some form builder fields.
* Updated: Reworded emails and conditionally added messages regarding issues with form that required sending the email.
* Updated: Adjusted plugin load order slightly to prevent conflicts with other plugins using GuzzleHTTP.

= 1.3.5 =
* Fixed: Prevent submission status message from displaying on all forms with multiple forms on same page.
* Fixed: Properly prevent submission via AJAX when required fields are not met.
* Fixed: Properly prevent AJAX submissions from incorrectly collecting data from all forms on a page that displays multiple Constant Contact forms.
* Fixed: Adjusted database query in Constant Contact Form lists display in conjunction with WordPress 4.8.2.
* Fixed: Invalid markup with form display and checkbox items.
* Fixed: Prevent possible issues with $_POST globals not being strings.
* Fixed: Addressed issues with Google reCAPTCHA validation and verification of submitting users via allow_url_fopen.
* Updated: Provided updated default values for button text, success message.
* Updated: Rearranged and updated labels form builder fields to provide better clarity.
* Updated: Added form's unique ID to form markup output for styling options and specific targeting.

= 1.3.4 =
* Fixed: Typo in code variable. Typo prevented forms with custom redirects from properly processing submission.

= 1.3.3 =
* Fixed: Issue with failed list additions in relation to spam prevention measures in 1.3.2. Sorry everyone.

= 1.3.2 =
* Added: More spam-preventive measures via timestamp comparison. Less than 5 seconds to fill in form and submit is rather bot-like.
* Updated: touched up markup around frontend form output. Fieldsets don't go in paragraph tags.
* Updated: Moved some inline styles away from honeypot and into frontend stylesheet.
* Fixed: Potential issues with API requests due to honeypot field.
* Fixed: HTML class output missing for textareas.

= 1.3.1 =
* Fixed: undefined index notice from helper-functions file.

= 1.3.0 =
* Added: Per-form AJAX submission support.
* Added: Display associated Constant Contact list in form listing.
* Added: Display Constant Contact list count in Constant Contact List listing.
* Added: HTML classes on form field wrappers for required fields.
* Added: Plenty of WordPress filters around available email fields.
* Added: UI field to customize text used to show successful submission.
* Added: UI field to specify URL to redirect user to, after successful submission.
* Added: Actions and filters after processing a form entry.
* Added: Filter email used in get_email method, which determines where to send submission notifications to.
* Added: Force a cursor pointer for submit buttons on frontend.
* Added: Debugging information around WP_CRON on settings page when "ctct-debug-server-check" GET parameter present.
* Added: Easily create a new form via the "New" menu area in the admin bar.
* Added: maxlength attribute to custom field inputs to match Constant Contact API restrictions.
* Fixed: mismatched textdomain that affected internationalization.
* Fixed: Resolved issue with field builder when Constant Contact Forms is network activated.
* Fixed: Prevent potential fatal errors for constant_contact function call.
* Fixed: Append determined classes for the checkbox field.
* Fixed: Increased the width for some settings text fields for better readability of content stored.
* Fixed: Possible API failures if custom field listed first.
* Updated: Output honeypot field regardless of reCAPTCHA status. Previously we did only reCAPTCHA if keys available. Else was honeypot.
* Updated: Improved text sent to ConstantContact.com around custom fields. Should better reflect which field each line is related to. See Frequently Asked Questions regarding some limits to this feature and how to enable.
* Updated: Improved return messages for submission failures.
* Updated: Upgraded to the latest version of CMB2.

= 1.2.5 =
* Fixed: Customized labels no longer reset to default when adding new fields.
* Added: Ability to bypass using WP_CRON when trying to have form entries sent to ConstantContact.com lists. If you're having trouble getting them sent, use this setting.
* Updated: Revised content for "Disconnect" page when connected, and have not created a form yet.

= 1.2.4 =
* Added: Google reCAPTCHA "I am human" checkbox support for forms. See https://www.google.com/recaptcha/intro/. Will fall back to honeypot prevention if not set up.
* Fixed: Stray quote mark in honeypot markup.
* Fixed: missing space after placeholder attribute for inputs.
* Fixed: Removed unintentional "Leave page" confirmation popup when saving settings.

= 1.2.3 =
* Fixed: Attempt to process forms that have provided a custom url via filter.
* Fixed: Clean up class attributes regarding validation errors in text inputs.

= 1.2.2 =
* Fixed: Conflicts with other plugins using the Constant Contact PHP SDK.
* Fixed: Added honeypot-style spam prevention on forms.
* Fixed: Removed anonymous function usage in widget to prevent potential errors.
* Fixed: Hardened up helper function in cases where internal function does not exist.
* Fixed: Issues with multiple custom textareas and the Constant Contact API. See the "Learn more" link/modal for some more information.
* Added: Potential admin notice requesting users to review plugin if they have not already.

= 1.2.1 =
* Fixed: Re-show sections of "Publish" metabox incorrectly hidden for post types outside Constant Contact Forms.
* Fixed: Issues with transparent background on frontend forms when input is valid.
* Fixed: Fatal errors on deactivation if user is on PHP 5.3 or lower.
* Fixed: PHP Warnings regarding missing parameters for maybe_log_mail_status().
* Updated: Bumped Guzzle to 5.3.1 for PHP7.1 compatibility.

= 1.2.0 =
* Added: Reveal shortcode for newly published form in admin notice and popup for non-connected accounts.
* Added: Classes for individual form inputs and textareas on rendered form.
* Added: Request to opt into some anonymous data tracking for Constant Contact's information usage.
* Added: Note about no forms being available in modal popup, if none available.
* Added: Ability to disable emails if Constant Contact account is connected and "disable email" option checked.
* Added: Necessary disclosure text to output on comment form and login/registration form when able to do advanced opt-in for list enrollment.
* Fixed: Possible issues with PHP 5.2 compatibility from the widget.
* Fixed: Prevent status message from displaying multiple times if multiple forms present on the page.
* Fixed: Ability to remove description values from various available form inputs.
* Updated: Changed field order in admin UI for creating Constant Contact form.
* Updated: Better compatibility with TwentyFourteen.

= 1.1.1 =
* Fixed: Made frontend form default to an empty action attribute to take care of occasional 404 errors after submission.
* Added: New filter on the default empty string from above, so others can provide their own redirect location if desired.

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

== Frequently Asked Questions ==

#### Constant Contact Forms Options
http://knowledgebase.constantcontact.com/articles/KnowledgeBase/18260-WordPress-Constant-Contact-Forms-Options

#### Frequently Asked Questions
https://knowledgebase.constantcontact.com/articles/KnowledgeBase/18491-WordPress-Frequently-Asked-Questions

#### Constant Contact List Addition Issues
https://knowledgebase.constantcontact.com/articles/KnowledgeBase/18539-WordPress-Constant-Contact-List-Addition-Issues

#### cURL error 60: SSL certificate problem
https://knowledgebase.constantcontact.com/articles/KnowledgeBase/18159-WordPress-Error-60

#### Add Google reCAPTCHA to Constant Contact Forms
http://knowledgebase.constantcontact.com/articles/KnowledgeBase/17880

#### How do I include which custom fields labels are which custom field values in my Constant Contact Account?
You can add this to your active theme or custom plugin: `add_filter( 'constant_contact_include_custom_field_label', '__return_true' );`. Note: custom fields have a max length of 50 characters. Including the labels will subtract from the 50 character total available.
