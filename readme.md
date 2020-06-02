# Constant Contact Forms

## Tooling Overview
Since we want to add in support for Gutenberg,  webpack has been integrated into the workflow. It is now
responsible for transpiling and bundling all JavaScript files. You can also write `JSX` out of the box! 

## EntryPoints
- `ctct-plugin-admin` - Contains all the original JavaScript for the admin area.
- `ctct-plugin-admin` - Contains all original JavaScript for the frontend.
- `ctct-plugin-gutenberg` - Contains all related Gutenberg JavaScript. Note that both the `gutenberg` and `admin` JS only
load on new page/post pages in the admin.

## Getting Started
1. Edit `plugin-config.js` to match your local environment.
    - `localURL` - this should match your local development URL.
    - `watchURL` - should either be `https://localhost:3000` or `http://localhost:3000` depending on whether you have
    SSL running or not.
    - `publicJS` - this is just the public JS path and should not need updated.
2. Run `npm install`.
3. Run `npm run watch`.
4. Open up `localhost:3000` to vew the site.

**NOTE:** If you use Local by Flywheel and Browsersync seems to be really slow, you can go into
LBF->Preferences->Advanced menu and toggle on the `IPv6 Loopback` option. This should speed things up.

**Another Tip:** If you make the LBF setting change above and your local site loads with just
`It Works` on the page, just toggle the `IPv6` option off.

## Start Scripts
- `npm run watch` - will watch all JS/SCSS files and reload when changed.
- `npm run dev` - will build all CSS files plus non-minified JS files.
- `npm run build` - will build all CSS files plus minified JS files.

## Steps for local development setup

1. Download the repo.
1. Run `composer install`
