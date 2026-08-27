# CLAUDE.md

Guidance for AI coding agents working in this repository.

## Project

Easy!Appointments is an open source web scheduler (PHP 8.2+, GPL-3.0). It runs on a vendored, patched
CodeIgniter 3 in `system/`, which is framework code and should not be edited.

## Layout

- `application/controllers` — page controllers; `application/controllers/api/v1` holds the REST API (`*_api_v1.php`).
- `application/models` — one model per entity (`Appointments_model.php`, `Providers_model.php`, ...).
- `application/libraries` — domain services (`Availability`, `Google_sync`, `Caldav_sync`, `Notifications`,
  `Permissions`, `Webhooks_client`, `Timezones`, ...).
- `application/core` — framework overrides, all prefixed `EA_` (`EA_Controller`, `EA_Model`, `EA_Loader`, ...).
- `application/helpers` — procedural helpers loaded globally (`validation_helper`, `permission_helper`, ...).
- `application/migrations` — numbered schema migrations; add the next number, never edit an existing one.
- `application/language` — 40+ translations; English is the source, the rest follow it.
- `application/views` — `pages`, `layouts`, `components`, `emails`, `errors`.
- `application/config` — `config.php`, `routes.php`, `database.php`, `email.php`, `google.php`, `constants.php`.
- `assets/js` — `pages`, `components`, `http` (one HTTP client per controller), `utils`, `layouts`.
- `assets/css` — SCSS sources (`backend`, `frontend`, `general`) compiled next to their output.
- `tests/Unit` — PHPUnit tests; `docker/` and `docker-compose.yml` — local stack; `docs/` — user documentation.

## Conventions

- Configuration lives in `config.php` (copy of `config-sample.php`), not in environment variables.
- Console commands run through `php index.php console <command>`: `install`, `migrate`, `seed`, `backup`, `sync`,
  `cleanup`, `help`.
- Compiled `*.min.js` and `*.min.css` files are committed. Run `npm start` (gulp watch) or `npm run build` after
  touching sources, and commit both the source and the generated file.
- Formatting is Prettier (`printWidth` 120, 4 spaces, single quotes) plus `.editorconfig`. Match the file you edit.
- Every new API endpoint needs a matching entry in `openapi.yml`.

## Tests

- `composer test` or `vendor/bin/phpunit` runs the unit suite. CI runs the same on PHP 8.3.
- Cover non-trivial library and helper logic with a test in `tests/Unit`.

## Changelog

- Add user facing changes to the `[Unreleased]` section of `CHANGELOG.md`, under `Added`, `Changed` or `Fixed`.
- Keep every changelog line at 120 characters or less, wrapping longer entries.
- Write entries for end users, describing the effect of the change, and append the issue number when there is one.

## Commits

- Do not add a `Co-Authored-By: Claude ...` trailer to commit messages.
- Do not reference Claude, or any AI assistant, as an author or co-author of a commit.

## Branches

- Do not create a new branch unless the maintainer explicitly asks for one. Commit
  the work to the branch that is already checked out.
