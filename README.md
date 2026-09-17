# Utilities

![Packagist](https://img.shields.io/packagist/dm/gcworld/utilities.svg)
![Packagist](https://img.shields.io/packagist/dt/gcworld/utilities.svg)

![Packagist PHP](https://img.shields.io/packagist/php-v/gcworld/utilities.svg)
![Packagist](https://img.shields.io/packagist/v/gcworld/utilities.svg)
![GitHub](https://img.shields.io/github/tag/konghack/utilities.svg)

Reusable PHP utility traits and helpers shared across GCWorld applications.

### Version
2.3.0

## Requirements

- PHP 8.4 or newer
- cURL extension
- GD extension
- Composer

## Installation

Install the package through Composer:

```bash
composer require gcworld/utilities
```

## Usage

Most helpers are provided as traits. Add the traits needed by an application's utility or support class:

```php
<?php

namespace App\Support;

use GCWorld\Utilities\Traits\Str;
use GCWorld\Utilities\Traits\Time;

final class Utilities
{
    use Str;
    use Time;
}
```

The static helpers are then available on that class:

```php
$encoded = Utilities::base64url_encode('example');
$parts = Utilities::searchSplit('alpha "two words"');
$displayTime = Utilities::formatTime('14:30:00');
```

`DoubleMetaphone` is a standalone class rather than a trait:

```php
use GCWorld\Utilities\DoubleMetaphone;

$metaphone = new DoubleMetaphone('Smith');
$primary = $metaphone->getPrimary();
$secondary = $metaphone->getSecondary();
```

## Available utilities

| Utility | Purpose |
| --- | --- |
| `CLI` | ANSI foreground and background colors for terminal output. |
| `Colors` | Conversion between HTML RGB values and HSL components. |
| `Curl` | Simple GET, HEAD, form POST, and raw-body POST requests. |
| `FancyArrayTrait` | Recursive Bootstrap 3 rendering for arrays, objects, JSON, scalars, and binary UUID values. |
| `General` | General time, memory, reversible integer, IP, email, filesystem, and process helpers. |
| `Image` | Thumbnail generation and TrueType text centering. |
| `JSONTrait` | Defensive JSON decoding and exception-based JSON encoding. |
| `Str` | Base64URL, delimited-string, ASCII, search, and name helpers. |
| `Time` | Time zone lists, interval/date formatting, elapsed time, and relative time helpers. |
| `DoubleMetaphone` | Primary and secondary Double Metaphone phonetic codes. |

### HTTP form posts

`Curl::post()` and `Curl::postRaw()` encode form fields according to RFC 3986. Use `Curl::postStringRaw()` when the request body is already serialized.

The cURL helpers return an empty string when a request fails. Callers that need error details, status validation, headers, retries, or authentication policy should use a dedicated HTTP client.

### Client IP addresses

`General::getIP()` recognizes forwarding headers used by the GCWorld HAProxy environment. Those headers must be supplied or sanitized by a trusted proxy. The returned address is suitable for the package's established logging use case and should not be treated as an authorization boundary by itself.

## Development

The repository includes a Docker Compose environment based on the same PHP image used in CI:

```bash
./dc up -d
./dc exec php composer install
./dc exec php composer check
./dc down
```

Copy `docker-compose.override.yml.example` to `docker-compose.override.yml` when local Composer credentials or SSH access are required. The override file is ignored by Git.

The Composer quality suite runs:

- PHP syntax checks
- PHPStan level 6
- PHPCS with PSR-12
- PHPUnit characterization tests

GitHub Actions runs the suite on PHP 8.4 and 8.5. Semantic-version tags also validate the version metadata and create GitHub releases from the matching changelog entry.

## Compatibility

This package is used by multiple downstream products. Existing public method names—including legacy snake_case names—are retained to avoid breaking consumers.

## License

Proprietary.
