# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.6.0] 2022-06-07

### Breaking

- The `Sluggable` trait does not exploits the `booted` method anymore. Tt uses `bootSluggable` now ([#18](https://github.com/khalyomede/laravel-eloquent-uuid-slug/issues/18)).

## [0.5.1] 2022-06-07

### Fixed

- You can now call `$model->replicate()` without this method call raising an exception ([#15](https://github.com/khalyomede/laravel-eloquent-uuid-slug/issues/15)).

## [0.5.0] 2022-06-06

### Added

- New `Khalyomede\EloquentUuidSlug\Rules\ExistsBySlug` validation rule ([#14](https://github.com/khalyomede/laravel-eloquent-uuid-slug/issues/14)).

### Breaking

- Dropped support for PHP 8.0 since [symfony/console](https://github.com/symfony/console) bumped their minimum version to PHP 8.1, and [laravel/framework](https://github.com/laravel/framework) requires this package, new Laravel installation will not work with PHP 8.0 anymore.

## [0.4.0] 2022-04-03

### Added

- New `Sluggable::findBySlug(string)` and `Sluggable::findBySlugOrFail(string)` methods ([#10](https://github.com/khalyomede/laravel-eloquent-uuid-slug/issues/10)).
- New `Sluggable::dropSlugColumn(Blueprint)`, `Sluggable::addUnconstrainedSlugColumn(Blueprint)` and `Sluggable::fillEmptySlugs()` methods ([#2](https://github.com/khalyomede/laravel-eloquent-uuid-slug/issues/2)).


## [0.3.0] 2022-02-12

### Added

- Support for Laravel 9 ([#7](https://github.com/khalyomede/laravel-eloquent-uuid-slug/issues/7)).

## [0.2.0] 2021-12-29

### Added

- Support for PHP 8.1 ([#5](https://github.com/khalyomede/laravel-eloquent-uuid-slug/issues/5)).

## [0.1.0] 2021-08-22

### Added

- First working version.
