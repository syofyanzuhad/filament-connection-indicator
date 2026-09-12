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
