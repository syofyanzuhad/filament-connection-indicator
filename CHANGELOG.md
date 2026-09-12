# Changelog

All notable changes to `filament-connection-indicator` will be documented in this file.

## v1.0.0 - 2026-09-12

### What's Changed

- Initial release of **Filament Connection Indicator**
- Real-time client-side connection status using browser Network Information API & `navigator.onLine`
- Support for two visual UI styles:
  - `dot`: Pulsing circular signal dot
  - `bars`: 4-tier vertical signal bars with dynamic fill and offline strike line
  
- Fluent plugin API (`->bars()`, `->dot()`, `->style()`, `->renderHook()`)
- Configurable tooltip labels and polling intervals
- Zero-dependency client-side architecture (no WebSocket or server round-trips)

## 1.0.0 - 2026-09-12

- initial release
- client-side connection quality indicator using Network Information API and `navigator.onLine`
- support for `dot` (pulsing indicator) and `bars` (vertical signal bars) UI styles
- configurable tooltip labels and polling interval
