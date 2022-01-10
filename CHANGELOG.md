# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

## [5.11.0] - 2022-01-10
### Added
- Adds multiple symfony packages as `ignoreFiles` ([#196](https://github.com/nunomaduro/collision/pull/196), [#202](https://github.com/nunomaduro/collision/pull/202))
- Allow to extend and customize test command envs ([#201](https://github.com/nunomaduro/collision/pull/201))

## [5.10.0] - 2021-09-20
### Added
- PHP 8.1 support ([5594255](https://github.com/nunomaduro/collision/commit/559425531ddcc0acdf79e3759ad85fa808172934))

## [5.9.0] - 2021-08-26
### Added
- The capability to force console syntax highlighting in the env ([#193](https://github.com/nunomaduro/collision/pull/193))

## [5.8.0] - 2021-08-13
### Added
- Support for Pest Parallel tests ([#190](https://github.com/nunomaduro/collision/pull/190))

## [5.7.0] - 2021-08-12
### Added
- `-p` shortcut to run tests in parallel ([4463030](https://github.com/nunomaduro/collision/commit/44630308e3bce25435423f9c0292a6e15e740722))
- Colored diff between expected and actual ([#187](https://github.com/nunomaduro/collision/pull/187))

## [5.6.0] - 2021-06-26
### Changed
- Ignores more paths on exceptions ([#182](https://github.com/nunomaduro/collision/pull/182), [#184](https://github.com/nunomaduro/collision/pull/184), [#185](https://github.com/nunomaduro/collision/pull/185))

### Fixed
- Slow printer because of sleep ([2815a0b](https://github.com/nunomaduro/collision/commit/2815a0bc8021a67d48c5a1a0fd80bd852aa4da39))

## [5.5.0] - 2021-06-22
### Changed
- Ignores more paths on exceptions ([#180](https://github.com/nunomaduro/collision/pull/180))

## [5.4.0] - 2021-04-09
### Changed
- Remove Lumen support ([8e003f0](https://github.com/nunomaduro/collision/commit/8e003f0a20adeff692c505beb6ea58e122b1d921))
- Do not render editor when file is unknown ([501d25e](https://github.com/nunomaduro/collision/commit/501d25effafe1e26b26bd7e513a6df0cad5730d4))
- Truncate strings to 1000 characters when formatting stacktraces ([#177](https://github.com/nunomaduro/collision/pull/177))

## [5.3.0] - 2021-01-25
### Added
- Ports Parallel Testing to Laravel 8

## [5.2.0] - 2021-01-13
### Added
- Support to Parallel Testing

## [5.1.0] - 2020-10-29
### Added
- Support to PHP 8

## [5.0.2] - 2020-08-27
### Fixed
- [Reverts] Prevents from being installation in Laravel < 8 versions

## [5.0.1] - 2020-08-27
### Fixed
- Prevents from being installation in Laravel < 8 versions

## [5.0.0] - 2020-08-25

## [5.0.0-BETA5] - 2020-08-09
### Fixed
- Frame filtering with windows paths ([#140](https://github.com/nunomaduro/collision/pull/140))
- Emit output with `beStrictAboutOutputDuringTests` enabled ([#130](https://github.com/nunomaduro/collision/pull/130))

## [5.0.0-BETA4] - 2020-06-25
### Changed
- Makes `artisan test` command PHPUNit 9 only ([ac6032d](https://github.com/nunomaduro/collision/commit/ac6032dd5546104ce9ae4143f46f391729bfc2ef))

## [5.0.0-BETA3] - 2020-06-22
### Changed
- Makes `artisan test` command Laravel 8 only ([ba26119](https://github.com/nunomaduro/collision/commit/ba26119149a7e42fbace9c09584596d313a535fd))
- Icons on printer ([#122](https://github.com/nunomaduro/collision/pull/122))

## [5.0.0-BETA2] - 2020-06-05
### Fixed
- Lowercasing names on Pest ([5e853c5](https://github.com/nunomaduro/collision/commit/5e853c54ceba7e6abf608957b944d20bb5d5ea6c))

## [5.0.0-BETA1] - 2020-05-11
### Added
- Uses Pest as test runner on `TestCommand` in Laravel
- Improvements on PHPUnit Printer Console UI
- Adds support to `stop-on-failure` in printer and test command
- Improvements on exception handler output

### Changed
- Makes some classes final & internal that will lead for a refactor in the `v6` version.

### Removed
- Support for PHP 7.2
- Support for PHPUnit 8

## [4.2.0] - 2020-04-04
### Changed
- Removes the dependency JakubOnderka@PhpConsoleColor ([9b430e4](https://github.com/nunomaduro/collision/commit/9b430e44467e7186f5a7e48c8cdeac0571817286))

### Fixed
- Missing first folder of relative path ([9af85c2](https://github.com/nunomaduro/collision/commit/9af85c29de1e7c3d9d2d0442a8db31fdfa63cb50), [fa81cd2](https://github.com/nunomaduro/collision/commit/fa81cd2d0eb636da010545acc17fcaba3ee26789))

## [4.1.3] - 2020-03-07
### Fixed
- Missing support for `.env.testing` ([#96](https://github.com/nunomaduro/collision/pull/96))

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

[Unreleased]: https://github.com/nunomaduro/collision/compare/v5.6.0...HEAD
[5.6.0]: https://github.com/nunomaduro/collision/compare/v5.5.0...v5.6.0
[5.5.0]: https://github.com/nunomaduro/collision/compare/v5.4.0...v5.5.0
[5.4.0]: https://github.com/nunomaduro/collision/compare/v5.3.0...v5.4.0
[5.3.0]: https://github.com/nunomaduro/collision/compare/v5.2.0...v5.3.0
[5.2.0]: https://github.com/nunomaduro/collision/compare/v5.1.0...v5.2.0
[5.1.0]: https://github.com/nunomaduro/collision/compare/v5.0.2...v5.1.0
[5.0.2]: https://github.com/nunomaduro/collision/compare/v5.0.1...v5.0.2
[5.0.1]: https://github.com/nunomaduro/collision/compare/v5.0.0...v5.0.1
[5.0.0]: https://github.com/nunomaduro/collision/compare/v5.0.0-BETA5...v5.0.0
[5.0.0-BETA5]: https://github.com/nunomaduro/collision/compare/v5.0.0-BETA4...v5.0.0-BETA5
[5.0.0-BETA4]: https://github.com/nunomaduro/collision/compare/v5.0.0-BETA3...v5.0.0-BETA4
[5.0.0-BETA3]: https://github.com/nunomaduro/collision/compare/v5.0.0-BETA2...v5.0.0-BETA3
[5.0.0-BETA2]: https://github.com/nunomaduro/collision/compare/v5.0.0-BETA1...v5.0.0-BETA2
[5.0.0-BETA1]: https://github.com/nunomaduro/collision/compare/v4.2.0...v5.0.0-BETA1
[4.2.0]: https://github.com/nunomaduro/collision/compare/v4.1.3...v4.2.0
[4.1.3]: https://github.com/nunomaduro/collision/compare/v4.1.2...v4.1.3
[4.1.2]: https://github.com/nunomaduro/collision/compare/v4.1.1...v4.1.2
[4.1.1]: https://github.com/nunomaduro/collision/compare/v4.1.0...v4.1.1
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
