const fs = require('fs').promises;
const path = require('path');
const { minify } = require('terser');

// Read package information dynamically from package.json
const packageJson = require('./package.json');
const version = packageJson.version;
const phpversion = packageJson.phpversion;
const modxversion = packageJson.modxversion;
const currentYear = new Date().getFullYear();
const startYear = parseInt(packageJson.startYear) || currentYear;
const yearRange = currentYear > startYear ? `${startYear}-${currentYear}` : `${startYear}`;
const dateStr = new Date().toISOString()
    .split('T')[0];

const copyrightRegex = new RegExp(`Copyright ${startYear}(-\\d{4})? by`, 'g');
const copyrightReplace = `Copyright ${yearRange} by`;
const apiRegex = new RegExp(`&copy; ${startYear}(-\\d{4})?`, 'g');
const apiReplace = `&copy; ${yearRange}`;
const banner = `/*!\n * ${packageJson.fullname} - ${packageJson.description}\n * Version: ${packageJson.version}\n * Build date: ${dateStr}\n */\n`;

let versionParts = version.split('-');
let versionNumber = versionParts[0];
let versionRelease = versionParts[1] || 'pl';
let versionFull = versionNumber + '-' + versionRelease;

// Helper: Replace string/regex in a file
async function replaceInFile(filePath, replacements, message = 'file') {
    try {
        let content = await fs.readFile(filePath, 'utf8');
        replacements.forEach(({ search, replace }) => {
            content = content.replace(search, replace);
        });
        await fs.writeFile(filePath, content, 'utf8');
        console.log(`✓ Updated ${message}: ${filePath}`);
    } catch (e) {
        console.warn(`⚠ Error updating ${filePath}`);
    }
}

// Helper: Ensure directory structure exists
async function ensureDirExists(dirPath) {
    await fs.mkdir(dirPath, { recursive: true });
}

// Helper function to compile scripts
async function compileScripts(files, dest, filename) {
    console.log('Compiling scripts...');
    const fileContents = await Promise.all(
        files.map(f => fs.readFile(f, 'utf8'))
    );
    const minified = await minify(fileContents.join('\n'), {
        mangle: true,
        compress: true,
        format: {
            comments: false,
        },
    });
    const finalCode = banner + minified.code;
    await ensureDirExists(dest);
    filename = filename.replace(/(\.\w+)$/i, '.min$1');
    await fs.writeFile(path.join(dest, filename), finalCode, 'utf8');
}

async function taskBump() {
    console.log(`Bump (with version ${versionFull} and daterange: ${yearRange})...`);
    const copyrightFiles = [
        'core/components/loginas/model/loginas/loginas.class.php',
        'core/components/loginas/src/LoginAs.php',
    ];
    for (const file of copyrightFiles) {
        await replaceInFile(file, [{
            search: copyrightRegex,
            replace: copyrightReplace
        }], 'copyright in');
    }
    await replaceInFile('core/components/loginas/src/LoginAs.php', [{
        search: /version = '\d+\.\d+\.\d+-?[0-9a-z]*'/ig,
        replace: `version = '${version}'`
    }], 'version in');
    const docFiles = [
        'zensical.toml',
    ];
    for (const file of docFiles) {
        await replaceInFile(file, [{
            search: apiRegex,
            replace: apiReplace
        }], 'daterange in');
    }
    await replaceInFile('docs/index.md', [{
        search: /[*-] MODX Revolution \d.\d.*/g,
        replace: '* MODX Revolution ' + modxversion + '+'
    }, {
        search: /[*-] PHP (v)?\d.\d.*/g,
        replace: '* PHP ' + phpversion + '+'
    }], 'requirements in');
    await replaceInFile('core/components/loginas/composer.json', [{
        search: /"php": "\d.\d.*?"/g,
        replace: '"php": "' + phpversion + '"'
    }, {
        search: /"php": ">=\d.\d.*?"/g,
        replace: '"php": ">=' + phpversion + '"'
    }], 'requirements in');
    const buildFiles = [
        '_build/config.json',
        'core/components/loginas/composer.json',
    ];
    for (const file of buildFiles) {
        await replaceInFile(file, [{
            search: /"version": "\d+\.\d+\.\d+-?[0-9a-z]*"/ig,
            replace: `"version": "${version}"`
        }], 'version in');
    }
}

async function taskMgrScripts() {
    await compileScripts([
        'source/js/mgr/loginas.js',
        'source/js/mgr/helper/combo.js',
        'source/js/mgr/helper/menu.js'
    ], 'assets/components/loginas/js/mgr/', 'loginas.js');
}

async function main() {
    const action = process.argv[2];
    if (action === 'bump') {
        await taskBump();
    } else if (action === 'scripts') {
        await taskMgrScripts();
    } else {
        // Default: Run all tasks
        await taskBump();
        await taskMgrScripts();
    }
    console.log('Done!');
}

main().catch((e) => {
    console.error(e);
    process.exitCode = 1;
});
