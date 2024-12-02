# Constant Contact Forms

## Tooling Overview
Since we want to add in support for Gutenberg,  webpack has been integrated into the workflow. It is now
responsible for transpiling and bundling all JavaScript files. You can also write `JSX` out of the box! 

## EntryPoints
- `assets/js/ctct-plugin-admin` - Contains all the original JavaScript for the admin area.
- `assets/js/ctct-plugin-frontend` - Contains all original JavaScript for the frontend.
- `src` - Contains all related block JavaScript. Note that both the `block` and `admin` JS only
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
- `npm run build` - will build all /src/ assets.
- `npm run styles` - will compile and compress assets/sass source file into the resulting css and minified css.
- `npm run styles:compile` - will compile assets/sass source file into the resulting unminified css
- `npm run styles:compress` - will compress the resulting css from the compile script.
- `npm run stylesadmin` - will compile and compress admin assets/sass source file into the resulting css and minified css.
- `npm run stylesadmin:compile` - will compile admin assets/sass source file into the resulting unminified css
- `npm run stylesadmin:compress` - will compress the resulting admin css from the compile script.

## Steps for local development setup

1. Download the repo.
1. Run `composer install`
1. Check PHP compatibility with the Composer script `composer run compat`
1. Lint code with the Composer script `composer run lint`
