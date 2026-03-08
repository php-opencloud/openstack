# AGENTS.md

## Project Scope

This repository is the `php-opencloud/openstack` SDK: a PHP client for multiple OpenStack services. The public entry point is `src/OpenStack.php`, which creates versioned service objects through shared builder logic in `src/Common/`.

The project follows semantic versioning and still supports PHP `^7.2.5 || ^8.0`. Keep changes narrowly scoped and avoid API breaks unless the task explicitly requires them.

## Repository Layout

- `src/`
  - `Common/`: shared API, resource, service, auth, error, JSON-schema, and transport infrastructure.
  - `<Service>/<version>/`: versioned SDK surface for each OpenStack service. Most service folders contain:
    - `Api.php`: declarative operation definitions (`method`, `path`, `params`, optional `jsonKey`)
    - `Params.php`: parameter schemas and wire-format metadata
    - `Service.php`: top-level user-facing service methods
    - `Models/`: resource classes with behavior and hydration rules
- `tests/unit/`: mocked unit tests, usually mirroring the `src/` namespace layout.
- `tests/sample/`: integration-style tests that execute files from `samples/`.
- `samples/`: runnable examples; these double as integration-test inputs.
- `doc/`: Sphinx/reStructuredText documentation.
- `.github/workflows/`: CI definitions for formatting, unit tests, and integration tests.

## Architecture Conventions

When adding or changing SDK functionality, preserve the existing layering:

1. Define or update the REST operation in `Api.php`.
2. Add or reuse parameter definitions in `Params.php`.
3. Expose the behavior from `Service.php` if it is part of the top-level service API.
4. Implement resource behavior in `Models/*` when the operation belongs on a model instance.
5. Add unit tests and, for user-facing flows, a sample plus sample test when practical.

Specific patterns used throughout the codebase:

- `Service.php` methods are intentionally thin. They usually create a model, populate it, or delegate to `enumerate(...)`.
- Resource classes commonly extend `OpenStack\Common\Resource\OperatorResource`, set `$resourceKey`, `$resourcesKey`, and `$markerKey`, and use `execute(...)` plus `populateFromResponse(...)`.
- API field renames are handled with `$aliases` and `Alias` objects instead of ad hoc mapping code.
- Operation option arrays are documented with `{@see \Namespace\Api::methodName}` docblocks. Keep those references accurate when signatures change.
- Reuse shared abstractions in `src/Common/` before introducing service-specific helpers.

## PHP Compatibility Rules

The SDK is tested against PHP `7.2` through `8.4` in CI. Do not introduce syntax or standard-library dependencies that require newer PHP versions than `7.2.5`.

In practice, avoid:

- union types
- attributes
- constructor property promotion
- enums
- `match`
- `readonly`
- typed properties
- named arguments in code examples or tests

Follow the surrounding file for `declare(strict_types=1);`. Many `src/` files use it, but not every file in the repository does.

## Testing Expectations

There are no Composer scripts in this repository; run tools directly from `vendor/bin`.

Primary local checks:

```bash
composer install
vendor/bin/parallel-lint --exclude vendor .
vendor/bin/phpunit --configuration phpunit.xml.dist
vendor/bin/php-cs-fixer fix --dry-run --diff
```

Additional checks when relevant:

```bash
composer normalize
vendor/bin/phpunit --configuration phpunit.sample.xml.dist
```

Notes:

- CI runs unit tests against both lowest and highest dependency sets, so avoid relying on the latest transitive behavior only.
- Integration tests require a live OpenStack environment, the environment variables from `env_test.sh.dist`, and an image named `cirros`.
- `php-cs-fixer` is configured for `src/`, `samples/`, and `.php-cs-fixer.dist.php`; tests are not auto-formatted by that config, so keep test edits manually consistent with surrounding code.

## Unit Test Patterns

Unit tests usually extend `tests/unit/TestCase.php`.

Follow the existing test style:

- set `rootFixturesDir` in `setUp()` when the test uses fixture responses
- use `mockRequest(...)` to assert HTTP method, path, body, headers, and returned response
- store larger or realistic HTTP responses as `.resp` files under a nearby `Fixtures/` directory
- mirror the production namespace and folder layout where possible

Prefer adding focused tests around the exact operation being changed instead of broad cross-service rewrites.

## Samples And Integration Coverage

`samples/` are executable examples and are also exercised by `tests/sample/`. When you add a new user-facing capability, consider whether it should have:

- a sample under the matching service/version folder in `samples/`
- a corresponding sample test under `tests/sample/`
- documentation in `doc/services/` if the feature is part of the supported public workflow

All code snippets used in the docs must live in `samples/` rather than being maintained only inline in `.rst` files, and they must be covered by the sample test suite.

Sample tests typically create a temporary PHP file from a template and `require_once` it, so keep samples self-contained and readable.

When adding sample tests, prefer reusing resources created earlier in the same test file instead of provisioning duplicate ones. In practice, `testCreate` should return the created resource, dependent tests should consume it via `@depends`, and cleanup should happen in the final `testDelete`.

## Documentation

User docs live in `doc/` and use Sphinx plus reStructuredText. If a change affects public behavior, examples, or supported options, update docs as needed.

Typical doc build:

```bash
pip install -r doc/requirements.txt
make -C doc html
```

## Change Heuristics

- Prefer small, service-scoped changes over broad refactors.
- Preserve public method names and option shapes unless the task explicitly calls for a breaking change.
- Keep docblocks accurate for public APIs and option arrays.
- Reuse existing fixtures, sample patterns, and helper methods before inventing new ones.
- If `composer.json` changes, run `composer normalize` because CI auto-normalizes that file.

