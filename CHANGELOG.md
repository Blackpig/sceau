# Changelog

All notable changes to `blackpig-creatif/sceau` are documented in this file.

## v2.0.2 - 2026-11-05

### Added

- `SceauPlugin::settingsPage()` to replace the built-in SEO settings page with a subclass. Only the given class is registered.

### Fixed

- The SEO settings page was registered twice per panel (once explicitly, once by directory discovery of the Grimoire documentation pages). Apps extending the page got two **SEO Settings** navigation items sharing one route, and the unscoped base page could own it. Documentation pages are now registered from an explicit list.
- Selecting the **FAQ Page** schema type without custom schema data threw a `TypeError` when rendering the page head. `FaqSchema` now produces no output in that case (FAQ schemas come from Atelier FAQ blocks or custom schema data).
- Test suite: removed a stale migration stub include that failed every test, and rewrote unit tests written against fields that no longer exist on `SeoData`.
