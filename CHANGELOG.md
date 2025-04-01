= 2.9.0 =

* Added: hCAPTHCA support.
* Updated: Moved global styles and opt-in settings to dedicated tabs.
* Updated: Removed old "bypass cron job" setting.
* Updated: "Click to copy" code behind the scenes.

= 2.8.0 =

* Added: Form frontend preview when working with Forms Block and block is not selected.
* Added: Ability to toggle frontend visibility for a form's description field. Allows to use as admin-only description.
* Added: Missed signup connection attempts count and reCAPTCHA status to Constant Contact Forms's Site Health Panel.
* Added: Quick links to duplicate a chosen form.
* Fixed: Touched up CSS for the WP Admin and RTL based languages.
* Updated: Converted majority of Constant Contact Forms admin-side Javascript away from jQuery base.
* Updated: amended admin email notifications to not promote email marketing, if site owner is already a Constant Contact
  account holder.
* Updated: CMB2 and Encryption internal libraries.

= 2.7.0 =

* Fixed: Issues around opt-in options not showing until a list was chosen and the form saved.
* Fixed: PHP warnings with login/register/comment signup integrations.
* Added: Extra form submission catching if connection issues detected. Will notify administrators right away when
  detected as well.
* Updated: Dismissable admin notice recommending to update the Constant Contact Forms plugin when one is available.

= 2.6.1 =

* Fixed: Issues around opt-in option display that were introduced with version 2.6.0.
* Fixed: Issues regarding lists display in admin emails after user signup.
* Updated: Removed unused images and changed some.
* Updated: Social sharing icons in plugin list page.

= 2.6.0 =

* Updated: Amended the list selection process for a given form. Should not break existing forms, and now you can make
  use of drag-n-drop to order the list selection when offering multiple lists.
* Updated: reworked the underpinning of the Constant Contact block to be more in line with modern WordPress coding
  patterns, including block.json based. Should not break existing forms.
* Updated: Show general List metabox regardless of connected status. Messaging will reflect connection state.
* Added: New duration timing for a review request and displayed notification.

= 2.5.0 =

* Updated: Better handling of email notifications around spam submission attemptes.

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

* Added: Background catch for new contact API requests that fail due to need to re-authenticate. Requests will be
  re-tried once newly reconnected.
* Added: Site health integration to help with debugging and troubleshooting.
* Updated: Removed reliance on WP Cron for sending submissions. All API submission should be run right away from now
  on. "Bypass cron" setting negated.
* Updated: Amended "address" field to allow for choosing which address components to use as well as allow requiring only
  certain components.

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

* Updated: Plugin has been migrated to use Constant Contact API version 3.0. This will require new authentication
  workflow.
* Updated: Addressed security issues with regards to opt-in notification.
* Updated: Added support to check for DISABLE_WP_CRON constant usage and bypass cron scheduling if true.
* Fixed: moved "Edit form" link to outside the `<form>` markup.
* Fixed: Custom color choices were not applying to all parts of form text output.

= 1.14.1 =

* Fixed: Backport of security issue originally fixed in 2.0.0 release.

= 1.14.0 =

* Fixed: Issues around email submissions with "some+value@email.com" based addresses.
* Fixed: Compatibility with Elementor Widget registration
* Added: Notice regarding upcoming API changes in a later major plugin version.
* Added: Compatibility with Cleantalk Spam Protect
* Added: Extra compatibility with Akismet Spam protection
* Updated: CMB2 internal library to 2.10.1

= 1.13.0 =

* Fixed: get_optin_list_options() defaults to an empty array instead of an empty string
* Fixed: Prevent "CTCT has experienced issues" notifications for "503 Service Not Available" errors
* Fixed: Fixes plugin sometimes causing errors due to trailing commas
* Changed: Move styles inline for honeypot field to ensure field is hidden when option to disable CSS output is used
* Updated: Mask API keys in error logs
* Updated: Mask phone numbers in logs
* Updated: Add noopener noreferrer to blank links
* Updated: Refactor multiple translated strings
* Updated: Added error messages on form submissions upon API faliures

= 1.12.0 =
 * Added: “Limit 500 Characters” description below textarea fields
 * Added: CSS class selector to the div wrapping the list checkboxes
 * Added: Force email notifications if no list is selected for a form
 * Added: Multi-select list options to "advanced optin" settings
 * Added: New setting to override default opt-in text
 * Added: Two new filters to override state and zipcode labels
 * Changed: Change `<small>` to `<sub>` for form disclaimer
 * Fixed: Email field browser validation when form submits via AJAX
 * Fixed: Erroneous placeholder attribute on submit button
 * Fixed: Incomplete "ctct-label-" CSS class on submit button
 * Updated: Addressed limits and issues regarding list management
 * Updated: Better ensured security

= 1.11.0 =
* Updated: New admin styling to update the overall look and feel of the plugin in the WordPress admin.
* Updated: Amend honeypot field input for accessibility purposes.
* Added: Plugin setting to disable the loading on Constant Contact plugin CSS on the frontend.
* Added: Native browser validation for phone number (tel) field input type.
* Fixed: Amended admin notification for Bad Request results.

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
