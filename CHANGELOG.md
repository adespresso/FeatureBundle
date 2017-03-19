# Changelog

All Notable changes to `adespresso/feature-bundle` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [Unreleased]
### Added

  * Added `features:create <parent> <name> [--enabled] [--role=<REQUIRED_ROLE>]` console command to create a feature from CLI
  * Added `features:disable <parent> <name>` console command to disable a feature from CLI
  * Added `features:enable <parent> <name> [--role=<REQUIRED_ROLE>]` console command to enable a feature from CLI

### Changed

  * Updated symfony dependencies to require at least v2.8

### Fixed

  * Moved from deprecated `Symfony\Component\Security\Core\SecurityContextInterface` to `Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface`
  * Don't use cache if no cache is provided from the EntityManager

### Removed

  * Removed friendsofsymfony/user-bundle dependency

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

[Unreleased]: https://github.com/adespresso/FeatureBundle/compare/1.1.0...HEAD
[1.1.0]: https://github.com/adespresso/FeatureBundle/compare/1.0.0...1.1.0
