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
