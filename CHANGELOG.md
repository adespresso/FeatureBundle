# Changelog

All Notable changes to `adespresso/feature-bundle` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [Unreleased]
### Fixed

  * Moved from deprecated `Symfony\Component\Security\Core\SecurityContextInterface` to `Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface`

## [1.1.0] - 2016-06-01
### Added

  * CS fixer configuration
  * Editorconfig configuration
  * Automatic PHPDoc generation using TravisCI
  * Included DI testing utility
  * Added the contribution guidelines file, with a first items proposal
  * Added phpunit as a dependency to run tests independently

### Changed

  * Updated minimum PHP version to 5.5
  * Required dependency-injection 2.8+ due to xsd
  * Ordered composer configuration
  * Fixed coding style
  * Improved documentation

### Fixed
  * Fix required for correct method usage
  * FQCN usage + attribute initialization
  * Attribute mapping name based on default strategy
  * PHPDoc
  * Removed not required asterisk
  * Migration initialization from cw_feature.* to ae_feature.*
  * Remove unused Configuration

[Unreleased]: https://github.com/olivierlacan/keep-a-changelog/compare/1.1.0...HEAD
[1.1.0]: https://github.com/olivierlacan/keep-a-changelog/compare/1.0.0...1.1.0
