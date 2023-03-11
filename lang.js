const fs = require('fs');
const path = require('path');

const readDir = (dir) => {
    const files = fs.readdirSync(dir);
    let fileList = [];
    files.forEach((file) => {
        if (fs.statSync(path.join(dir, file)).isDirectory()) {
            fileList = fileList.concat(readDir(path.join(dir, file)));
        }
        else {
            fileList.push(path.join(dir, file));
        }
    });
    return fileList;
};
const themeDir = path.join(__dirname, '/themes/default/views');
const themeFiles = readDir(themeDir);
var lang = require('./lang/en.json');
console.log('Looping through theme files...')
themeFiles.forEach((file) => {
    file = file.replace(themeDir, '');
    const fileContent = fs.readFileSync(path.join(themeDir, file), 'utf8');
    const matches = fileContent.match(/{{ __\('(.*?)'\) }}/g);
    if (matches) {
        matches.forEach((match) => {
            const key = match.replace(/{{ __\('(.*?)'\) }}/, '$1');
            if (!lang[key]) {
                lang[key] = key;
            }
        });
    }
});
lang = Object.keys(lang).sort((a, b) => a.localeCompare(b, undefined, { sensitivity: 'base' })).reduce((r, k) => (r[k] = lang[k], r), {});

fs.writeFileSync(path.join(__dirname, '/lang/en.json'), JSON.stringify(lang, null, 4));

console.log('Writing en.json file...')

// Loop through all the files in the lang directory and if the file is outdated, update it
const langDir = path.join(__dirname, '/lang');
const langFiles = readDir(langDir);
langFiles.forEach((file) => {
    // If the file isn't a .json file, skip it
    if (!file.endsWith('.json')) { 
        return;
    }
    // If the file is en.json, skip it
    if (file.endsWith('en.json')) {
        return;
    }
    // Read the file
    existingLang = require(file);
    // Loop through all the keys in the en.json file
    Object.keys(lang).forEach((key) => {
        // If the key doesn't exist in the existing language file, add it
        if (!existingLang[key]) {
            existingLang[key] = key;
        }
    });
    // Sort the keys
    existingLang = Object.keys(existingLang).sort((a, b) => a.localeCompare(b, undefined, { sensitivity: 'base' })).reduce((r, k) => (r[k] = existingLang[k], r), {});
    // Write the file
    fs.writeFileSync(file, JSON.stringify(existingLang, null, 4));
    console.log(`Updating ${file}...`);
});

console.log('Done!')


