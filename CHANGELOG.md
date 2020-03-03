# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

## [4.1.2] - 2020-03-03
### Fixed
- `phpunit.xml` envs variables being ignored ([235132d](https://github.com/nunomaduro/collision/commit/235132db538b68892cd164948bac6caa7893a4ad))

## [4.1.1] - 2020-02-26
### Fixed
- `WARN` title being shown on failures

## [4.1.0] - 2020-02-04
### Added
- Adds `test` artisan Command to laravel adapter
- Support to phpunit 9 to phpunit adapter

## [4.0.1] - 2020-01-22
### Fixed
- Required version of phpunit

## [4.0.0] - 2020-01-22
### Added
- Better visuals
- Support to Laravel 7
- Better PHPUnit adapter

## [3.0.0] - 2019-03-07
### Added
- Support to Lumen ([#55](https://github.com/nunomaduro/collision/pull/55))
- Support to PHPUnit 8 ([#60](https://github.com/nunomaduro/collision/pull/60))

## [2.1.1] - 2018-11-21
### Added
- Support to `jakub-onderka/php-console-highlighter:0.4` ([#57](https://github.com/nunomaduro/collision/pull/57))

## [2.1.0] - 2018-10-03
### Added
- Method `shouldReport` to Exception Handler ([#56](https://github.com/nunomaduro/collision/pull/56))

## [2.0.3] - 2018-06-17
### Fixes
- Ensure that `Highlighter::class` receives a string on the `highlight` method ([3da3e13](https://github.com/nunomaduro/collision/commit/3da3e13db3e269b63298b8afa4b509da07181c9a))

## [2.0.2] - 2018-03-21
### Added
- Possibility of open file on the specified line ([#45](https://github.com/nunomaduro/collision/pull/45))

## [2.0.1] - 2018-03-20
### Added
- Laravel adapter: Defer service provider ([#46](https://github.com/nunomaduro/collision/pull/46))

## [2.0.0] - 2018-02-18
### Added
- Phpunit adapter: Support to PHPUnit 7
- Syntax Highlighting

### Removed
- Phpunit adapter: Drops support to PHPUnit 6

## [1.1.22] - 2018-02-07
### Fixed
- Phpunit adapter: Fixes usage with `laravel-ide-helper`

## [1.1.20] - 2018-01-18
### Fixed
- Phpunit adapter: Respects `--stop-on-failure` and `--stop-on-error` options

### Changed
- Phpunit adapter: Exception render place

## [1.1.19] - 2018-01-10
### Removed
- Laravel adapter: Removes unused code

## [1.1.18] - 2017-12-22
### Changed
- PHPUnit adapter: Don't render warnings

## [1.1.17] - 2017-12-19
### Changed
- Laravel adapter: Don't register Collision on testing env

## [1.1.16] - 2017-12-17
### Changed
- Laravel adapter: Defaults to renderForConsole when exception is an `Symfony\Component\Console\Exception\ExceptionInterface`

## [1.1.15] - 2017-12-17
### Changed
- Laravel adapter: Ignores exception details when exception implements `Symfony\Component\Console\Exception\ExceptionInterface`

## [1.1.12] - 2017-12-11
### Added
- Adds support to `symfony/console` version 4.0 ([#28](https://github.com/nunomaduro/collision/pull/28))

## [1.1.5] - 2017-10-17
### Added
- Adds support to `symfony/console` version 2.8 ([#16](https://github.com/nunomaduro/collision/pull/16))

## [1.1.0] - 2017-10-10
### Added
- Adds `phpunit` adapter

## 1.0.0 - 2017-10-08
### Added
- Adds first version

[Unreleased]: https://github.com/nunomaduro/collision/compare/v4.1.0...HEAD
[4.1.0]: https://github.com/nunomaduro/collision/compare/v4.0.1...v4.1.0
[4.0.1]: https://github.com/nunomaduro/collision/compare/v4.0.0...v4.0.1
[4.0.0]: https://github.com/nunomaduro/collision/compare/v3.0.0...v4.0.0
[3.0.0]: https://github.com/nunomaduro/collision/compare/v2.1.1...v3.0.0
[2.1.1]: https://github.com/nunomaduro/collision/compare/v2.1.0...v2.1.1
[2.1.0]: https://github.com/nunomaduro/collision/compare/v2.0.3...v2.1.0
[2.0.3]: https://github.com/nunomaduro/collision/compare/v2.0.2...v2.0.3
[2.0.2]: https://github.com/nunomaduro/collision/compare/v2.0.1...v2.0.2
[2.0.1]: https://github.com/nunomaduro/collision/compare/v2.0.0...v2.0.1
[2.0.0]: https://github.com/nunomaduro/collision/compare/v1.1.22...v2.0.0
[1.1.22]: https://github.com/nunomaduro/collision/compare/v1.1.20...v1.1.22
[1.1.20]: https://github.com/nunomaduro/collision/compare/v1.1.19...v1.1.20
[1.1.19]: https://github.com/nunomaduro/collision/compare/v1.1.18...v1.1.19
[1.1.18]: https://github.com/nunomaduro/collision/compare/v1.1.17...v1.1.18
[1.1.17]: https://github.com/nunomaduro/collision/compare/v1.1.16...v1.1.17
[1.1.16]: https://github.com/nunomaduro/collision/compare/v1.1.15...v1.1.16
[1.1.15]: https://github.com/nunomaduro/collision/compare/v1.1.12...v1.1.15
[1.1.12]: https://github.com/nunomaduro/collision/compare/v1.1.5...v1.1.12
[1.1.5]: https://github.com/nunomaduro/collision/compare/v1.1.0...v1.1.5
[1.1.0]: https://github.com/nunomaduro/collision/compare/v1.0.0...v1.1.0
