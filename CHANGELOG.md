
# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased](https://github.com/KongHack/Utilities)



## [2.3.1](https://github.com/KongHack/Utilities/releases/tag/2.3.1)
- @Mitch Patch starImplode and starExplode Docblocks



## [2.3.0](https://github.com/KongHack/Utilities/releases/tag/2.3.0)

### Added

- GitHub Actions quality checks on PHP 8.4 and 8.5, with tag-driven release creation and release metadata validation.
- Weekly Dependabot checks for Composer lockfile and non-major GitHub Actions updates.
- Docker Compose development tooling using the same PHP image as CI.
- Composer scripts for syntax checks, PHPStan, PHPCS, PHPUnit, and the combined quality suite.
- PHPStan level 6, PSR-12, and PHPUnit configuration with characterization coverage for the core utilities.

### Changed

- Applied PSR-12 formatting and static-analysis type documentation across the package while retaining legacy public method names.
- Reworked cURL form posts to use RFC 3986 query encoding and removed shell execution from raw POST helpers.
- Removed the obsolete Cloudflare challenge helper.

### Fixed

- Corrected interval formatting for zero months and one-month intervals combined with years.
- Compare parsed dates rather than raw date strings in `timeAgo()`.
- Return the final value from associative arrays in `getLastArrayValue()`.
- Accept valid falsey JSON output such as `0` in `JSONTrait::json_encode()`.
- Kept thumbnail generation compatible with PHP 8.4 and PHP 8.5 GD return types.


## [2.2.5](https://github.com/KongHack/Utilities/releases/tag/2.2.5)
- @GameCharmer Make `starExplode` a bit more flexible



## [2.2.4](https://github.com/KongHack/Utilities/releases/tag/2.2.4)
- @GameCharmer `FancyArrayTrait`



## [2.2.3](https://github.com/KongHack/Utilities/releases/tag/2.2.3)
- @GameCharmer Base64URL encode/decode in Str trait



## [2.2.2](https://github.com/KongHack/Utilities/releases/tag/2.2.2)
- @GameCharmer new `secondsToTimeElapsed` method in Time trait



## [2.2.1](https://github.com/KongHack/Utilities/releases/tag/2.2.1)
- @GameCharmer Type Hints and more PHP 8.4 updates



## [2.2.0](https://github.com/KongHack/Utilities/releases/tag/2.2.0)
- @GameCharmer PHP 8.4



## [2.1.3](https://github.com/KongHack/Utilities/releases/tag/2.1.3)
- @GameCharmer Update Time Trait with new methods
- @GameCharmer new JSON Trait



## [2.1.2](https://github.com/KongHack/Utilities/releases/tag/2.1.2)
- @GameChamer Add `getNamePieces` to `Str` trait.



## [2.1.1](https://github.com/KongHack/Utilities/releases/tag/2.1.1)
- @GameCharmer Added DoubleMetaphone class



## [2.1.0](https://github.com/KongHack/Utilities/releases/tag/2.1.0)
- @GameCharmer Update Minimum PHP Version to 8.1
- @GameCharmer Add `searchSplit` method



## [2.0.0](https://github.com/KongHack/Utilities/releases/tag/2.0.0)
 - @GameCharmer modernize getIP method.  Replace DIE with a custom exception
 - @GameCharmer remove globals trait
 - @GameCharmer move all Traits into Traits namespace
 - @GameCharmer Various cleanup and modernization items



## [1.2.6](https://github.com/KongHack/Utilities/releases/tag/1.2.6)
 - @GameCharmer Added convertAscii function to Str



## [1.2.5](https://github.com/KongHack/Utilities/releases/tag/1.2.5)
 - @GameCharmer Added imageTTFCenter to Image Trait
 - @GameCharmer Added Colors trait for simple color manipulation



## [1.2.4](https://github.com/KongHack/Utilities/releases/tag/1.2.4)
 - @GameCharmer added more string functions
 - @GameCharmer added Globals trait
 - @GameCharmer added Time trait



## [1.2.3](https://github.com/KongHack/Utilities/releases/tag/1.2.3)
 - @GameCharmer last release before moving to github

