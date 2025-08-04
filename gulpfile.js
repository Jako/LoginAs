const gulp = require('gulp'),
    composer = require('gulp-uglify/composer'),
    concat = require('gulp-concat'),
    format = require('date-format'),
    header = require('@fomantic/gulp-header'),
    replace = require('gulp-replace'),
    uglifyjs = require('uglify-js'),
    uglify = composer(uglifyjs, console),
    pkg = require('./_build/config.json');

const banner = '/*!\n' +
    ' * <%= pkg.name %> - <%= pkg.description %>\n' +
    ' * Version: <%= pkg.version %>\n' +
    ' * Build date: ' + format("yyyy-MM-dd", new Date()) + '\n' +
    ' */';
const year = new Date().getFullYear();

let phpversion;
let modxversion;
pkg.dependencies.forEach(function (dependency, index) {
    switch (pkg.dependencies[index].name) {
        case 'php':
            phpversion = pkg.dependencies[index].version.replace(/>=/, '');
            break;
        case 'modx':
            modxversion = pkg.dependencies[index].version.replace(/>=/, '');
            break;
    }
});

const scriptsMgr = function () {
    return gulp.src([
        'source/js/mgr/loginas.js',
        'source/js/mgr/helper/combo.js',
        'source/js/mgr/helper/menu.js'
    ])
        .pipe(concat('loginas.min.js'))
        .pipe(uglify())
        .pipe(header(banner + '\n', {pkg: pkg}))
        .pipe(gulp.dest('assets/components/loginas/js/mgr/'))
};
gulp.task('scripts', gulp.series(scriptsMgr));

const bumpCopyright = function () {
    return gulp.src([
        'core/components/loginas/model/loginas/loginas.class.php',
        'core/components/loginas/src/LoginAs.php',
    ], {base: './'})
        .pipe(replace(/Copyright 2025(-\d{4})? by/g, 'Copyright ' + (year > 2025 ? '2025-' : '') + year + ' by'))
        .pipe(gulp.dest('.'));
};
const bumpVersion = function () {
    return gulp.src([
        'core/components/loginas/src/LoginAs.php',
    ], {base: './'})
        .pipe(replace(/version = '\d+\.\d+\.\d+-?[0-9a-z]*'/ig, 'version = \'' + pkg.version + '\''))
        .pipe(gulp.dest('.'));
};
const bumpDocs = function () {
    return gulp.src([
        'mkdocs.yml',
    ], {base: './'})
        .pipe(replace(/&copy; 2025(-\d{4})?/g, '&copy; ' + (year > 2025 ? '2025-' : '') + year))
        .pipe(gulp.dest('.'));
};
const bumpRequirements = function () {
    return gulp.src([
        'docs/index.md',
    ], {base: './'})
        .pipe(replace(/[*-] MODX Revolution \d.\d.*/g, '* MODX Revolution ' + modxversion + '+'))
        .pipe(replace(/[*-] PHP (v)?\d.\d.*/g, '* PHP ' + phpversion + '+'))
        .pipe(gulp.dest('.'));
};
const bumpComposer = function () {
    return gulp.src([
        'core/components/loginas/composer.json',
    ], {base: './'})
        .pipe(replace(/"version": "\d+\.\d+\.\d+-?[0-9a-z]*"/ig, '"version": "' + pkg.version + '"'))
        .pipe(gulp.dest('.'));
};
gulp.task('bump', gulp.series(bumpCopyright, bumpVersion, bumpDocs, bumpRequirements, bumpComposer));

// Default Task
gulp.task('default', gulp.series('bump', 'scripts'));
