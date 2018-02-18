# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [2.0.0] - 2018-02-18
### Added
- Phpunit adapter: Support to PHPUnit 7.
- Syntax Highlighting

### Removed
- Phpunit adapter: Drops support to PHPUnit 6.

## [1.1.22] - 2018-02-07
### Fixed
- Phpunit adapter: Fixes usage with `laravel-ide-helper`.

## [1.1.20] - 2018-01-18
### Fixed
- Phpunit adapter: Respects `--stop-on-failure` and `--stop-on-error` options.

### Changed
- Phpunit adapter: Exception render place.

## [1.1.19] - 2018-01-10
### Removed
- Laravel adapter: Removes unused code.

## [1.1.18] - 2017-12-22
### Changed
- PHPUnit adapter: Don't render warnings.

## [1.1.17] - 2017-12-19
### Changed
- Laravel adapter: Don't register Collision on testing env.

## [1.1.16] - 2017-12-17
### Changed
- Laravel adapter: Defaults to renderForConsole when exception is an `Symfony\Component\Console\Exception\ExceptionInterface`.

## [1.1.15] - 2017-12-17
### Changed
- Laravel adapter: Ignores exception details when exception implements `Symfony\Component\Console\Exception\ExceptionInterface`.

## [1.1.12] - 2017-12-11
### Added
- Adds support to `symfony/console` version 4.0 ([#28](https://github.com/nunomaduro/collision/pull/28))

## [1.1.5] - 2017-10-17
### Added
- Adds support to `symfony/console` version 2.8 ([#16](https://github.com/nunomaduro/collision/pull/16))

## [1.1.0] - 2017-10-10
### Added
- Adds `phpunit` adapter

## [1.0.0] - 2017-10-08
### Added
- Adds first version
