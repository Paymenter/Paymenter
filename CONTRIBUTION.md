# Tech STACK
* **Front-end**: TailwindCSS & Flowbite
* **Back-end**: Laravel
* **Database**: MySQL

# How to run
`npm run dev`
Runs the front-end.
`php artisan serve` Runs the back-end.

# How to build
`npm run build`
Builds the front-end.

# How to run tests
`php artisan test`

# Contributing - translation
### Creating a new language
1. Create folders and files for yur language.
```bash
mkdir lang/LANGUAGE
cp lang/en/* lang/LANGUAGE
cp lang/en.json lang/LANGUAGE.json
```
2. Start translating in your newly created files!
3. Create a PR with your changes

### Add missing translation keys
1. Update the blade file missing the translation(s).
2. Update the lang files to reflect the new translation key(s) by running the lang script.
```bash
npx laravel-language-extractor --theme default 
```
