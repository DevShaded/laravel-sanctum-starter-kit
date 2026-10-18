# Template setup to-do

Skills are planned separately and are outside this checklist.

- [x] Document and verify setup from a fresh clone using Docker, without requiring host PHP or Composer: install dependencies before invoking Sail, create `.env`, configure local user/group IDs, start services, generate the initial application key, run migrations, and run quality checks.
- [x] Remove machine-specific defaults and instructions: replace the absolute path in `README.md` and document deriving `WWWUSER` and `WWWGROUP` locally instead of relying on the current hardcoded IDs.
- [x] Replace booking-specific names with template defaults, or provide a rename checklist covering Composer metadata, application name, database name, and Docker image. Ensure projects created from the template can run alongside each other with distinct ports and appropriate image names.
- [x] Make `composer setup` safe to rerun: preserve an existing `APP_KEY` and generate one only when missing.
- [x] Add dependency auditing to CI. First decide how to handle the abandoned transitive dependency `symplify/rule-doc-generator-contracts`, required by `driftingly/rector-laravel`; the audit currently reports no vulnerability advisories but exits unsuccessfully for abandonment.
- [x] Configure scheduled dependency updates for Composer packages, GitHub Actions, and Docker images where supported.
- [x] Add the intended `LICENSE` file and ensure it matches the license declared in `composer.json` (currently MIT).
- [x] Document the existing local API documentation endpoints, `/docs/api` and `/docs/api.json`, including their production access restrictions.
- [x] Document deployment essentials: production environment configuration, application key management, migrations, queue workers, and scheduler operation.
- [x] Decide on expired-token pruning and schedule `sanctum:prune-expired` when using expiring tokens.
- [x] Initialize or connect the Git repository, verify that secrets and generated files are excluded, and confirm the intended template files are tracked. Initialized locally; environment files, dependencies, credentials, and generated caches are excluded.
- [ ] Run GitHub Actions on the published repository, configure appropriate required checks, and enable the GitHub template repository setting when ready.
