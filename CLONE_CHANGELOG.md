# Clone Changelog

Notes for the "hide provider selection" change. This file is not committed.

## Changelog

### Added

- Booking setting "Display Provider Selection" (`display_provider_selection`) under Settings / Booking, enabled by default so existing installations behave as before.
- Migration `070_add_display_provider_selection_setting.php`, which inserts the setting with the value `1` and removes it on downgrade.
- English translations `display_provider_selection` and `display_provider_selection_hint`. Other languages still need translating.
- Unit tests for the migration and the new translation keys.

### Changed

- When the setting is disabled, the booking page hides the provider field once a service is chosen.
    - If one provider can serve the service, that provider is used.
    - If several can, "Any Provider" is selected automatically, even when "Display Any Provider Option" is off.
    - A previously chosen provider is kept when that provider can still serve the newly selected service.
- `Booking::index()` passes `display_provider_selection` to the page script variables (default `1` when the setting is missing).
- The setting is saved through the existing generic `data-field` handling of the booking settings page, with no controller changes.

### Files

- `application/controllers/Booking.php`
- `application/language/english/translations_lang.php`
- `application/migrations/070_add_display_provider_selection_setting.php`
- `application/views/pages/booking_settings.php`
- `assets/js/pages/booking.js`
- `tests/Unit/Migration/DisplayProviderSelectionSettingMigrationTest.php`
- `CHANGELOG.md` (`[Unreleased]` entry)

### Notes

- The `?provider=` URL parameter still preselects a provider while the field is hidden.
- The compiled `*.min.js` files are gitignored in this checkout, so only `assets/js/pages/booking.js` is committed.

---

## [feat] Let administrators hide provider selection and book with any provider by default

### Purpose

Customers currently have to pick both a service and a provider before they can continue booking. Many businesses do not need customers to choose a provider, and the extra step adds friction. This change adds an administrator setting that removes the provider field from the booking page. When it is off, the customer picks a service and the system books with "Any Provider", which makes the flow shorter and simpler. The setting is on by default, so existing installations behave exactly as before until an administrator turns it off.

### Summary of changes and consequences

- New setting `display_provider_selection` under Settings / Booking, enabled by default. Existing booking pages are unchanged after upgrading.
- New migration `070_add_display_provider_selection_setting.php` creates the setting with the value `1`. Upgrading requires running migrations, which the normal update process does.
- When the setting is off, the provider dropdown is hidden on the booking page. Customers only choose a service and move on to the date and time step.
- When the setting is off and several providers can serve the service, "Any Provider" is selected automatically, even if "Display Any Provider Option" is off. The two settings are independent, and hiding the provider field takes precedence.
- When the setting is off and only one provider can serve the service, that provider is selected automatically, so nothing changes for the customer.
- `Booking::index()` passes the setting to the page scripts, defaulting to `1` when it is missing. A missing row never hides the field.
- English translations were added. Other languages show the raw translation keys on the settings page until they are translated.
- Unit tests cover the migration and the translation keys. The JavaScript behaviour has no automated test because the repository has no JS test runner.
- `CHANGELOG.md` has a new `[Unreleased]` entry describing the setting for end users.

### Detailed explanation

#### 1. Database migration

`application/migrations/070_add_display_provider_selection_setting.php` follows the pattern of the earlier `display_any_provider` migration (`019`).

- `up()` inserts a row into `settings` with `name = display_provider_selection` and `value = 1`, only if no such row exists. Re-running the migration or a manually pre-created row will not be overwritten.
- `down()` deletes the row.
- Consequence: the default of `1` keeps the current behaviour. The migration version is taken from the highest numbered file in `application/migrations`, so no config change was needed.

#### 2. Settings page

`application/views/pages/booking_settings.php` has a new switch with id `display-provider-selection` and `data-field="display_provider_selection"`. It sits between the "Any Provider" and "Login button" switches and uses the same markup as them.

- Consequence: the existing generic settings JavaScript loads and saves it through `data-field`, so no controller or model change was required. Saving the form stores `1` or `0` in the `settings` table.

#### 3. Translations

`application/language/english/translations_lang.php` gains `display_provider_selection` ("Display Provider Selection") and `display_provider_selection_hint`. The hint explains that disabling the setting hides the provider field and selects "Any Provider" automatically.

- Consequence: English is the source language per `CLAUDE.md`. The other translation files were not touched, so they lack these keys and show the key names for the new switch until translated.

#### 4. Booking controller

`application/controllers/Booking.php` adds `display_provider_selection` to the `script_vars` passed to the page, read with `setting('display_provider_selection', '1')`.

- Consequence: the front end receives the value as a script variable. The `'1'` default protects installations where the row is missing, for example before migrations have run.

#### 5. Booking page JavaScript

`assets/js/pages/booking.js`, in the service `change` handler:

- The provider field's wrapper is hidden when no service is chosen or when `display_provider_selection` is off.
- The "Any Provider" option is added when more than one provider can serve the service and either `display_any_provider` is on or provider selection is hidden.
- If provider selection is hidden and more than one provider can serve the service, "Any Provider" is selected, unless the previously chosen provider can still serve the new service, in which case it is kept.
- The existing validation in the Next button handler is unchanged. Because a value is always selected when the field is hidden, a customer is not blocked by a "service and provider missing" error.
- Consequences:
    - The provider is still submitted as `any-provider`, so the existing server-side logic that picks an available provider is reused with no change.
    - The confirmation step and the header still show "Any Provider".
    - The `?provider=` URL parameter still preselects a provider even when the field is hidden. A link to a specific provider keeps booking with that provider.
    - Manage mode (rescheduling) sets the appointment's existing provider through the existing code path and is not changed.

#### 6. Tests

`tests/Unit/Migration/DisplayProviderSelectionSettingMigrationTest.php` uses a fake database object and a stub `EA_Migration` base class. It checks that `up()` inserts the setting enabled, that `up()` does not overwrite an existing row, that `down()` removes it, and that the English translation keys exist.

- Consequence: the full suite went from 95 to 99 passing tests.

#### 7. Changelog

`CHANGELOG.md` has a new `[Unreleased]` / `Added` entry written for end users on a single line, per `CLAUDE.md`.

### Verification

- `composer test`: 99 tests pass (7 deprecations that were already present).
- `php -l` passed for the changed PHP files.
- Browser check against a local MariaDB database with two providers:
    - Setting on: the provider dropdown shows "Please Select", "Any Provider" and both providers, as before.
    - Setting off: the provider field is hidden, "Any Provider" is preselected, and the Next button leads to the time step with available hours listed.
    - Admin switch: toggling the switch on the booking settings page and saving changed the stored value from `0` to `1`.
- Not tested: a single-provider service with the field hidden, the `?provider=` URL parameter, and manage mode.

### Known limitations and follow-ups

- Only English strings were added.
- `npm run build` fails because `gulpfile.js` calls `del.sync`, which `del` v8 does not provide. This predates this change. Assets were built locally with a temporary `del@6` install that is not committed.
- No `openapi.yml` change was needed because no API endpoint was added or changed.

### Commits

1. `feat: add display_provider_selection setting migration`
2. `feat: add provider selection switch to booking settings`
3. `feat: hide provider field and default to any provider on booking page`
4. `test: cover display_provider_selection migration and translations`
5. `docs: add changelog entry for provider selection setting`
