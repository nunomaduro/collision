# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

## [v7.9.0 (2023-09-19)](https://github.com/nunomaduro/collision/compare/v7.7.0...v7.9.0)
### Added
- `dontReportDuplicates` support to exception handler in Laravel

### Fixed
- `reportable` return type to exception handler in Laravel
- `renderable` return type to exception handler in Laravel

## [v7.7.0 (2023-06-29)](https://github.com/nunomaduro/collision/compare/v7.6.0...v7.7.0)
### Added
- `reportable` support to exception handler in Laravel
- `renderable` support to exception handler in Laravel

## [v7.6.0 (2023-06-15)](https://github.com/nunomaduro/collision/compare/v7.5.2...v7.6.0)
### Added
- Usage with PHPUnit 10.2.2
- Support for unexpected output

## [v7.5.2 (2023-04-22)](https://github.com/nunomaduro/collision/compare/v7.5.1...v7.5.2)
### Fix
- Usage with PHPUnit 10.1.2

## [v7.5.1 (2023-04-22)](https://github.com/nunomaduro/collision/compare/v7.5.0...v7.5.1)
### Fix
- Usage with PHPUnit 10.1.2

## [v7.5.0 (2023-04-14)](https://github.com/nunomaduro/collision/compare/v7.4.0...v7.5.0)
### Added
- Support for PHPUnit test runner deprecations

## [v7.4.0 (2023-03-31)](https://github.com/nunomaduro/collision/compare/v7.3.3...v7.4.0)
### Added
- Allows exceptions to be renderable on editor

## [v7.3.3 (2023-03-21)](https://github.com/nunomaduro/collision/compare/v7.3.2...v7.3.3)
### Chore
- Adds method specific for Pest

## [v7.3.2 (2023-03-21)](https://github.com/nunomaduro/collision/compare/v7.3.1...v7.3.2)
### Fixed
- Usage of result on Pest

## [v7.3.1 (2023-03-21)](https://github.com/nunomaduro/collision/compare/v7.3.0...v7.3.1)
### Fixed
- Bad regex on previous improved Laravel stacktrace

## [v7.3.0 (2023-03-21)](https://github.com/nunomaduro/collision/compare/v7.2.0...v7.3.0)
### Added
- Improved Laravel stacktrace

### Chore
- Bumps dependencies

## [v7.2.0 (2023-03-19)](https://github.com/nunomaduro/collision/compare/v7.1.2...v7.2.0)
### Added
- `--without-databases` option on the Laravel `test` Artisan command

## [v7.1.2 (2023-03-14)](https://github.com/nunomaduro/collision/compare/v7.1.1...v7.1.2)
### Fixed
- Displaying of uncovered lines

## [v7.1.1 (2023-03-13)](https://github.com/nunomaduro/collision/compare/v7.1.0...v7.1.1)
### Fixed
- PHPUnit `>10.0.16` support

## [v7.1.0 (2023-03-03)](https://github.com/nunomaduro/collision/compare/v7.0.5...v7.1.0)
### Added
- Support for `displayDetailsOnIncompleteTests`, `displayDetailsOnSkippedTests`, `displayDetailsOnTestsThatTriggerDeprecations`, `displayDetailsOnTestsThatTriggerErrors`, `displayDetailsOnTestsThatTriggerNotices`, `displayDetailsOnTestsThatTriggerWarnings`
- Better coverage output for `TestCommand`

### Fixed
- Parallel mode when cache directory is not available

## [v7.0.5 (2023-02-19)](https://github.com/nunomaduro/collision/compare/v7.0.4...v7.0.5)
### Added
- Support for better exception handling on `pestphp/pest` exceptions

### Fixed
- Requirement of `sebastian/environment` dependency

## [v7.0.4 (2023-02-17)](https://github.com/nunomaduro/collision/compare/v7.0.3...v7.0.4)
### Fixed
- Colors not being displayed on Windows

## [v7.0.3 (2023-02-16)](https://github.com/nunomaduro/collision/compare/v7.0.2...v7.0.3)
### Fixed
- Source of file on Windows

## [v7.0.2 (2023-02-11)](https://github.com/nunomaduro/collision/compare/v7.0.1...v7.0.2)
### Added
- Better `todo` output

## [v7.0.1 (2023-02-08)](https://github.com/nunomaduro/collision/compare/v7.0.0...v7.0.1)
### Changed
- Bumps dependencies

## [v7.0.0 (2023-02-07)](https://github.com/nunomaduro/collision/compare/v6.4.0...v7.x)
### Added
- PHPUnit 10.x and Pest 2.x support
- `--compact` printer
- `--profile` option to display top ten slow tests

### Removed
- PHPUnit 9.x and Pest 1.x support
