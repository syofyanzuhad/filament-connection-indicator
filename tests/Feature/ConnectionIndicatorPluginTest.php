<?php

use Filament\View\PanelsRenderHook;
use Syofyanzuhad\ConnectionIndicator\ConnectionIndicatorPlugin;

it('registers the plugin with the correct id', function () {
    $plugin = ConnectionIndicatorPlugin::make();

    expect($plugin->getId())->toBe('connection-indicator');
});

it('uses USER_MENU_BEFORE hook by default', function () {
    $plugin = ConnectionIndicatorPlugin::make();

    expect($plugin->renderHook)->toBe(PanelsRenderHook::USER_MENU_BEFORE);
    expect($plugin->getRenderHook())->toBe(PanelsRenderHook::USER_MENU_BEFORE);
});

it('allows overriding the render hook', function () {
    $plugin = ConnectionIndicatorPlugin::make()
        ->renderHook(PanelsRenderHook::TOPBAR_END);

    expect($plugin->renderHook)->toBe(PanelsRenderHook::TOPBAR_END);
    expect($plugin->getRenderHook())->toBe(PanelsRenderHook::TOPBAR_END);
});

it('defaults to dot style', function () {
    $plugin = ConnectionIndicatorPlugin::make();

    expect($plugin->getStyle())->toBe('dot');
});

it('allows setting style via style method', function () {
    $plugin = ConnectionIndicatorPlugin::make()
        ->style('bars');

    expect($plugin->getStyle())->toBe('bars');
});

it('provides fluent bars and dot helper methods', function () {
    $plugin = ConnectionIndicatorPlugin::make()->bars();
    expect($plugin->getStyle())->toBe('bars');

    $plugin->dot();
    expect($plugin->getStyle())->toBe('dot');
});

it('can render blade view with dot style', function () {
    $view = view('connection-indicator::connection-indicator', ['style' => 'dot'])->render();

    expect($view)->toContain('ci-ping');
});

it('can render blade view with bars style', function () {
    $view = view('connection-indicator::connection-indicator', ['style' => 'bars'])->render();

    expect($view)->toContain('<svg');
    expect($view)->toContain('barLevel');
});
