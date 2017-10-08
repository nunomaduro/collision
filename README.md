## About Collision

Collision was created and maintained by [Nuno Maduro](https://github.com/nunomaduro). Collision is an error handler framework for console/command-line PHP applications.

- Build on top of the [Whoops](https://github.com/filp/whoops).
- Supports [Laravel](https://github.com/laravel/laravel) Artisan Tool.
- Built with [PHP 7](https://php.net) using modern coding standards.

## Installation & Usage

> **Requires [PHP 7.1+](https://php.net/releases/)**

Require Collision project using [Composer](https://getcomposer.org):

```bash
composer require nunomaduro\collision
```

Register the handler in your code (On Laravel is automatic):

```php
(\NunoMaduro\Collision\Provider)->register();
```

## Contributing

Thank you for considering to contribute to Collision. All the contribution guidelines are mentioned [here](CONTRIBUTING.md).

## Stay In Touch

You can have a look at the [CHANGELOG](CHANGELOG.md) & [Releases](https://github.com/collision/collision/releases) for constant updates & detailed information about the changes. You can also follow the twitter account for latest announcements or just come say hi!: [@enunomaduro](https://twitter.com/enunomaduro)

## License

Collision is an open-sourced software licensed under the [MIT license](LICENSE.md).
