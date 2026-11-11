sho# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.0] - 2026-11-11

### Added

- Log into the manager as another manager user.
- Log each login in the manager log.
- Translations: cs, da, es, fr, it, nl, pt, ru, sv, uk.

### Changed

- List only the matching web or manager users.

### Fixed

- Fix the permission checks.
- Don't block the selected user.
- Redirect to the site start by default.
- Fix warnings and session issues.

## [1.0.2] - 2025-11-05

### Fixed

- Don't change the help_url in the manager javascript, since the script is loaded globally and the value will be used everywhere.

## [1.0.1] - 2025-08-14

### Fixed

- Don't select the first found user per default and change the searched value with the first found value (autoSelect = false)

## [1.0.0] - 2025-08-04

### Added

- Initial release
