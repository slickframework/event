# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]
## [v1.2.0] - 2025-11-25

### Added
- Added compatibility with **PHP 8.4**, including updates to the CI workflow and
  composer configuration.

### Changed
- Updated project dependencies to versions compatible with PHP 8.4, including
  **phpspec** and **PHP_CodeSniffer**.
- Updated the CI workflow to run exclusively on **PHP 8.4**.
- Increased the minimum PHP version requirement according to the updated compatibility matrix.

### Removed
- Removed the PHP 8.2 test workflow from the CI pipeline.

### Notes
- This release ensures full support for PHP 8.4 and aligns the testing and development
  environment with the latest PHP ecosystem.


## [v1.1.1] - 2025-06-17
### Fixed
- Fixed an issue in AttributeListenerProvider where event bindings were incorrectly assumed
  to be nested arrays, resulting in listeners not being invoked for dispatched events.

## [v1.1.0] - 2025-06-17
### Added
- Adding a Scrutinizer CI configuration for static analysis
- Attribute-based listener discovery

### Changed
- Updating the PHP version to 8.2
- Switching from Travis CI to GitHub Actions for CI
- Updating dependencies and dev dependencies to their latest versions

## [v1.0.0] - 2020-04-26
### Added
- PSR-14 interfaces as a dependency
- `Event`, `EventGenerator`, `EventListerner` and `Event dispatcher` interfaces
- PSR-14 `EventDispatcherInterface` implementation

[Unreleased]: https://github.com/slickframework/event/compare/v1.2.0...HEAD
[v1.2.0]: https://github.com/slickframework/event/compare/v1.1.1...v1.2.0
[v1.1.1]: https://github.com/slickframework/event/compare/v1.1.0...v1.1.1
[v1.1.0]: https://github.com/slickframework/event/compare/v1.0.0...v1.1.0
[v1.0.0]: https://github.com/slickframework/event/compare/be7a44d...v1.0.0