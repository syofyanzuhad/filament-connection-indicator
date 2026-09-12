# Filament Connection Signal Indicator — Plugin Development Guide

> [!NOTE]
> Reverse-engineered from the live implementation in `erp-intimurnijaya`  
> Component: [`connection-indicator.blade.php`](file:///Users/macbookpro/Herd/erp-intimurnijaya/resources/views/components/connection-indicator.blade.php)  
> Registration: [`AdminPanelProvider.php`](file:///Users/macbookpro/Herd/erp-intimurnijaya/app/Providers/Filament/AdminPanelProvider.php#L97-L100)

---

## 1. Overview

A **zero-dependency, client-side** Filament v4 plugin that renders a pulsing signal-dot in the panel's user menu area (via `PanelsRenderHook::USER_MENU_BEFORE`). It uses the browser's native **Network Information API** and `navigator.onLine` to reflect real connection quality — no WebSocket polling, no server round-trips.

### States

| Status | Color | Condition |
|--------|-------|-----------|
| `checking` | Gray `#9ca3af` | Initial / rechecking |
| `online` | Green `#22c55e` | Online, effectiveType = `4g` |
| `moderate` | Yellow `#eab308` | effectiveType = `3g` |
| `slow` | Orange `#f97316` | effectiveType = `2g` / `slow-2g` |
| `offline` | Red `#ef4444` | `navigator.onLine === false` |

---

## 2. Package Structure

```
filament-connection-indicator/
├── composer.json
├── src/
│   ├── ConnectionIndicatorPlugin.php      ← Plugin entry point
│   └── ConnectionIndicatorServiceProvider.php
├── resources/
│   └── views/
│       └── connection-indicator.blade.php ← Alpine.js component
├── config/
│   └── connection-indicator.php           ← Optional config (hook position, locale labels)
└── README.md
```

---

## 3. `composer.json`

```json
{
    "name": "your-vendor/filament-connection-indicator",
    "description": "A real-time connection signal indicator for Filament panels.",
    "type": "library",
    "license": "MIT",
    "require": {
        "php": "^8.1",
        "filament/filament": "^4.0"
    },
    "autoload": {
        "psr-4": {
            "YourVendor\\ConnectionIndicator\\": "src/"
        }
    },
    "extra": {
        "laravel": {
            "providers": [
                "YourVendor\\ConnectionIndicator\\ConnectionIndicatorServiceProvider"
            ]
        }
    }
}
```

---

## 4. Service Provider

```php
<?php

namespace YourVendor\ConnectionIndicator;

use Illuminate\Support\ServiceProvider;

class ConnectionIndicatorServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'connection-indicator');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/connection-indicator'),
            __DIR__.'/../config/connection-indicator.php' => config_path('connection-indicator.php'),
        ], 'connection-indicator');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/connection-indicator.php',
            'connection-indicator'
        );
    }
}
```

---

## 5. Plugin Class

```php
<?php

namespace YourVendor\ConnectionIndicator;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;

class ConnectionIndicatorPlugin implements Plugin
{
    protected string $renderHook = PanelsRenderHook::USER_MENU_BEFORE;

    // ─── Fluent API ───────────────────────────────────────────────────────────

    public static function make(): static
    {
        return app(static::class);
    }

    /**
     * Override the Filament render hook where the indicator is injected.
     * Defaults to PanelsRenderHook::USER_MENU_BEFORE.
     */
    public function renderHook(string $hook): static
    {
        $this->renderHook = $hook;

        return $this;
    }

    // ─── Plugin Contract ─────────────────────────────────────────────────────

    public function getId(): string
    {
        return 'connection-indicator';
    }

    public function register(Panel $panel): void
    {
        // No panel-level registration needed.
    }

    public function boot(Panel $panel): void
    {
        FilamentView::registerRenderHook(
            $this->renderHook,
            fn (): View => view('connection-indicator::connection-indicator'),
        );
    }
}
```

---

## 6. Blade View

> Based directly on the production component. Inline `x-data` keeps the
> component fully self-contained — no external Alpine component registration needed.

```blade
<div
    x-data="{
        status: 'checking',
        connectionType: '',
        rtt: null,
        init() {
            this.checkConnection()
            window.addEventListener('online',  () => this.checkConnection())
            window.addEventListener('offline', () => this.status = 'offline')
            if (navigator.connection) {
                navigator.connection.addEventListener('change', () => this.checkConnection())
            }
            setInterval(() => this.checkConnection(), 30000)
        },
        checkConnection() {
            if (!navigator.onLine) {
                this.status = 'offline'
                return
            }
            if (navigator.connection) {
                const conn = navigator.connection
                this.connectionType = conn.effectiveType || ''
                this.rtt = conn.rtt || null
                this.status = (conn.effectiveType === 'slow-2g' || conn.effectiveType === '2g')
                    ? 'slow'
                    : conn.effectiveType === '3g' ? 'moderate' : 'online'
            } else {
                this.status = 'online'
            }
        },
        getColor() {
            return {
                checking: '#9ca3af',
                online:   '#22c55e',
                moderate: '#eab308',
                slow:     '#f97316',
                offline:  '#ef4444',
            }[this.status]
        },
        getTooltip() {
            const labels = {
                checking: 'Checking...',
                online:   'Online',
                moderate: 'Slow Connection',
                slow:     'Very Slow',
                offline:  'Offline',
            }
            let tip = labels[this.status]
            if (this.connectionType && this.status !== 'offline') {
                tip += ` (${this.connectionType.toUpperCase()})`
            }
            if (this.rtt) tip += ` • ${this.rtt}ms`
            return tip
        },
    }"
    style="position: relative; width: 12px; height: 12px; cursor: pointer; display: inline-block;"
    :title="getTooltip()"
    @click="checkConnection()"
>
    {{-- Pulse ring (hidden when offline) --}}
    <span
        x-show="status !== 'offline'"
        :style="'position: absolute; width: 12px; height: 12px; border-radius: 50%; opacity: 0.75; animation: ci-ping 1s cubic-bezier(0, 0, 0.2, 1) infinite; background-color: ' + getColor()"
    ></span>

    {{-- Solid dot --}}
    <span
        :style="'position: absolute; width: 12px; height: 12px; border-radius: 50%; background-color: ' + getColor()"
    ></span>
</div>

<style>
    @keyframes ci-ping {
        75%, 100% {
            transform: scale(2);
            opacity: 0;
        }
    }
</style>
```

> [!IMPORTANT]
> The keyframe was renamed from `ping` to `ci-ping` to avoid colliding with
> Tailwind CSS's built-in `animate-ping` keyframe when the plugin is installed
> in Tailwind v4 projects.

---

## 7. Config File

```php
<?php

// config/connection-indicator.php
return [
    /*
    |--------------------------------------------------------------------------
    | Tooltip Labels
    |--------------------------------------------------------------------------
    | Override per locale or publish this file with `vendor:publish`.
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

> [!TIP]
> Wire the config into the Blade view via `@json(config('connection-indicator.labels'))`  
> and `{{ config('connection-indicator.poll_interval') }}` to make labels translatable.

---

## 8. Registering in a Filament Panel

```php
use YourVendor\ConnectionIndicator\ConnectionIndicatorPlugin;
use Filament\View\PanelsRenderHook;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            ConnectionIndicatorPlugin::make(),

            // Optionally override the hook position:
            // ConnectionIndicatorPlugin::make()
            //     ->renderHook(PanelsRenderHook::TOPBAR_END),
        ]);
}
```

---

## 9. Browser API Compatibility Notes

| API | Availability | Fallback |
|-----|-------------|----------|
| `navigator.onLine` | All modern browsers | — |
| `navigator.connection` (Network Information API) | Chrome / Edge / Android WebView | Gracefully falls back to `online` status |
| `navigator.connection.effectiveType` | Chrome 61+, not Safari / Firefox | Falls back to `online` if missing |
| `navigator.connection.rtt` | Chrome 61+, not Safari / Firefox | Omitted from tooltip if `null` |

The component **degrades gracefully**: on Safari / Firefox, it still tracks online/offline events but won't display `3g`/`4g` granularity.

---

## 10. Testing

```php
// tests/Feature/ConnectionIndicatorPluginTest.php

use YourVendor\ConnectionIndicator\ConnectionIndicatorPlugin;
use Filament\View\PanelsRenderHook;

it('registers the plugin with the correct id', function () {
    $plugin = ConnectionIndicatorPlugin::make();
    expect($plugin->getId())->toBe('connection-indicator');
});

it('uses USER_MENU_BEFORE hook by default', function () {
    $plugin = ConnectionIndicatorPlugin::make();
    expect($plugin->renderHook)->toBe(PanelsRenderHook::USER_MENU_BEFORE);
});

it('allows overriding the render hook', function () {
    $plugin = ConnectionIndicatorPlugin::make()
        ->renderHook(PanelsRenderHook::TOPBAR_END);

    expect($plugin->renderHook)->toBe(PanelsRenderHook::TOPBAR_END);
});
```

---

## 11. Roadmap / Enhancements

- [ ] **Livewire server-ping mode** — periodic HTTP ping to `/up` to confirm server reachability (not just client-side `navigator.onLine`).
- [ ] **RTT badge** — optionally render the round-trip time as a small text badge next to the dot.
- [ ] **Accessibility** — add `aria-label` bound to the same tooltip text for screen readers.
- [ ] **Translations** — ship `lang/` files so labels auto-resolve via `__()` without needing config overrides.
- [ ] **Dark mode dot border** — thin ring in dark mode to distinguish the dot from the background.
- [ ] **Publishable Tailwind preset** — replace inline styles with proper Tailwind utility classes once the plugin ships a Tailwind preset.
