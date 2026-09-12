<?php

namespace Syofyanzuhad\ConnectionIndicator;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;

class ConnectionIndicatorPlugin implements Plugin
{
    public string $renderHook = PanelsRenderHook::USER_MENU_BEFORE;

    // ─── Fluent API ───────────────────────────────────────────────────────────

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
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

    public function getRenderHook(): string
    {
        return $this->renderHook;
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
