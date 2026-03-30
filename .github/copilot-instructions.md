# Repository Instructions

## General Guidelines

* Always follow the existing coding style and conventions of this repository.
* Prefer consistency with existing patterns over introducing new ones.
* Keep code clean, readable, and maintainable.

## Security Best Practices

* Never introduce vulnerabilities such as:

    * SQL injection
    * XSS (Cross-Site Scripting)
    * CSRF issues
    * Insecure deserialization
* Always validate and sanitize user input.
* Use parameterized queries or ORM protections for database access.
* Avoid exposing sensitive data (API keys, tokens, passwords).
* Follow the principle of least privilege.
* Ensure proper error handling without leaking internal details.

## Code Quality

* Write small, focused, and testable functions.
* Add comments only when necessary to explain non-obvious logic.
* Avoid duplication; reuse existing utilities when possible.
* Ensure proper typing (if applicable).

## Changelog

* Whenever a task is completed, update `CHANGELOG.md`:

    * Add a new entry under the current version or create a new version.
    * Include a short, clear description of the change.
    * Use a consistent format (e.g., Added, Fixed, Improved).

## Commits

* After completing a task:

    * Stage all relevant changes.
    * Create a clear commit message describing the change.
    * Use conventional commit style when possible:
        * `feat:`
        * `fix:`
        * `chore:`
        * `refactor:`
* Commit messages should be concise but descriptive.


## Automation

* If automation is available:

    * Automatically update the changelog.
    * Automatically commit changes after task completion.
* Ensure automation does not overwrite or remove unrelated work.

## Scope Discipline

* Only modify files relevant to the task.
* Do not refactor unrelated parts of the codebase unless necessary.

## Documentation

* Update relevant documentation when behavior changes.
* Keep README and usage instructions accurate.

## Final Check

Before finishing:

* Code builds successfully.
* No linting or formatting errors.
* No obvious security issues.
* Changelog updated.
* Changes committed.
