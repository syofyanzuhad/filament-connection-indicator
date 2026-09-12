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

    public ?string $style = null;

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

    /**
     * Set the indicator visual style ('dot' or 'bars').
     */
    public function style(string $style): static
    {
        $this->style = $style;

        return $this;
    }

    public function dot(): static
    {
        return $this->style('dot');
    }

    public function bars(): static
    {
        return $this->style('bars');
    }

    public function getStyle(): string
    {
        return $this->style ?? config('connection-indicator.style', 'dot');
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
            fn (): View => view('connection-indicator::connection-indicator', [
                'style' => $this->getStyle(),
            ]),
        );
    }
}
