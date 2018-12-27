# Constant Contact Forms

## Steps for local development setup

Not optimal, but for the moment, it works. Feedback and help appreciated.

1. Download the repo.
1. Run `composer install --no-dev`
	1. There will be a composer error related to TGMPA
1. Navigate to the `/vendor/webdevstudios/wds-shortcodes/` folder and run that file's composer file with `composer install --no-dev`
1. Navigate back to root of plugin folder and run `composer dump-autoload`
	1. This will generate the needed autoloader file.
