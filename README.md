# Filament Connection Indicator

[![Latest Version on Packagist](https://img.shields.io/packagist/v/syofyanzuhad/filament-connection-indicator.svg?style=flat-square)](https://packagist.org/packages/syofyanzuhad/filament-connection-indicator)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/syofyanzuhad/filament-connection-indicator/tests.yml?branch=5.x&label=tests&style=flat-square)](https://github.com/syofyanzuhad/filament-connection-indicator/actions?query=workflow%3Atests+branch%3A5.x)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/syofyanzuhad/filament-connection-indicator/fix-code-style.yml?branch=5.x&label=code%20style&style=flat-square)](https://github.com/syofyanzuhad/filament-connection-indicator/actions?query=workflow%3Afix-code-style+branch%3A5.x)
[![GitHub PHPStan Action Status](https://img.shields.io/github/actions/workflow/status/syofyanzuhad/filament-connection-indicator/phpstan.yml?branch=5.x&label=phpstan&style=flat-square)](https://github.com/syofyanzuhad/filament-connection-indicator/actions?query=workflow%3Aphpstan+branch%3A5.x)
[![Total Downloads](https://img.shields.io/packagist/dt/syofyanzuhad/filament-connection-indicator.svg?style=flat-square)](https://packagist.org/packages/syofyanzuhad/filament-connection-indicator)

<img width="2752" height="1536" alt="filament-connection" class="filament-hidden" src="https://github.com/user-attachments/assets/8db2b719-bdec-428e-9f9f-33acf613363c" />

A **zero-dependency, client-side** Filament plugin that renders a real-time pulsing signal dot in your Filament panel (defaulting before the user menu). It uses the browser's native **Network Information API** and `navigator.onLine` to reflect connection quality without any WebSocket polling or server round-trips.

## Signal States

| Status | Color | Condition |
|--------|-------|-----------|
| `checking` | Gray `#9ca3af` | Initial check / rechecking |
| `online` | Green `#22c55e` | Online, effectiveType = `4g` (or API unsupported) |
| `moderate` | Yellow `#eab308` | effectiveType = `3g` |
| `slow` | Orange `#f97316` | effectiveType = `2g` / `slow-2g` |
| `offline` | Red `#ef4444` | `navigator.onLine === false` |

<img width="318" height="192" alt="image" src="https://github.com/user-attachments/assets/f3ce62c9-ac16-4217-9fa3-f45655988aa9" />
<img width="324" height="191" alt="image" src="https://github.com/user-attachments/assets/084fd886-3d27-485a-8059-8a3ff01527b6" />
<img width="318" height="194" alt="image" src="https://github.com/user-attachments/assets/5a50b87d-1a09-4c31-ae93-bf2a31414d30" />
<img width="321" height="191" alt="image" src="https://github.com/user-attachments/assets/7933fdbd-ad09-4145-b995-3cc7c0dc79b2" />

## Installation

You can install the package via composer:

```bash
composer require syofyanzuhad/filament-connection-indicator
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="connection-indicator-config"
```

Optionally, you can publish the views using:

```bash
php artisan vendor:publish --tag="connection-indicator-views"
```

## Usage

Register the plugin in your Filament Panel Provider (e.g. `AdminPanelProvider.php`):

```php
use Syofyanzuhad\ConnectionIndicator\ConnectionIndicatorPlugin;
use Filament\View\PanelsRenderHook;

public function panel(Panel $panel): Panel
{
    return $panel
        ->default()
        ->id('admin')
        ->plugins([
            ConnectionIndicatorPlugin::make(),
        ]);
}
```

### UI Styles

The indicator supports two visual styles:
- **`dot`** (default) — Pulsing circular dot with smooth ping animation.
<img width="314" height="188" alt="image" src="https://github.com/user-attachments/assets/70cc9ce7-e9e2-46bf-a89e-6f26e82c3b38" />

- **`bars`** — 4-tier vertical signal bars (cellular / Wi-Fi style) that fill dynamically based on connection quality, and shows an offline strike line when disconnected.
<img width="318" height="192" alt="image" src="https://github.com/user-attachments/assets/f3ce62c9-ac16-4217-9fa3-f45655988aa9" />

You can set the style fluently on the plugin:

```php
// Use vertical signal bars
ConnectionIndicatorPlugin::make()
    ->bars() // or ->style('bars')

// Or explicitly use the pulsing dot
ConnectionIndicatorPlugin::make()
    ->dot() // or ->style('dot')
```

### Custom Render Hook

By default, the indicator is rendered before the user menu (`PanelsRenderHook::USER_MENU_BEFORE`). You can customize where it appears:

```php
ConnectionIndicatorPlugin::make()
    ->renderHook(PanelsRenderHook::TOPBAR_END)
```

## Configuration

This is the contents of the published config file (`config/connection-indicator.php`):

```php
return [
    /*
    |--------------------------------------------------------------------------
    | Indicator UI Style
    |--------------------------------------------------------------------------
    | Supported options: 'dot', 'bars'
    | Default: 'dot'
    */
    'style' => 'dot',

    /*
    |--------------------------------------------------------------------------
    | Tooltip Labels
    |--------------------------------------------------------------------------
    */
    'labels' => [
        'checking' => 'Checking...',
        'online'   => 'Online',
        'moderate' => 'Slow Connection',
        'slow'     => 'Very Slow',
        'offline'  => 'Offline',
    ],

    /*
    |--------------------------------------------------------------------------
    | Poll Interval (ms)
    |--------------------------------------------------------------------------
    | How often the client re-checks the connection in the background.
    | Default: 30 000 ms (30 s).
    */
    'poll_interval' => 30000,
];
```

## Browser Compatibility

| API | Availability | Fallback |
|-----|-------------|----------|
| `navigator.onLine` | All modern browsers | — |
| `navigator.connection` (Network Information API) | Chrome / Edge / Android WebView | Gracefully falls back to `online` status |
| `navigator.connection.effectiveType` | Chrome 61+, not Safari / Firefox | Falls back to `online` if missing |
| `navigator.connection.rtt` | Chrome 61+, not Safari / Firefox | Omitted from tooltip if `null` |

The component **degrades gracefully**: on Safari and Firefox, it still tracks online and offline events in real-time even without network speed metrics.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Syofyan Zuhad](https://github.com/syofyanzuhad)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
