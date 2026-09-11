# Project Progress & Context Log

## Phase 1: Foundational Database Schema
- [x] Initialized `context.md` to track changes.
- [x] Created `create_organizations_table` migration.
- [x] Created `create_credentials_table` migration.
- [x] Created `create_source_connections_table` migration.
- [x] Created `create_destination_connections_table` migration.
- [x] Created `create_migration_jobs_table` migration.
- [x] Created `create_endpoint_registry_table` migration.
- [x] Created `create_audit_events_table` migration.

## Phase 2: Eloquent Models
- [x] Created `Organization` model with relationships.
- [x] Created `Credential` model with relationships.
- [x] Created `SourceConnection` model with relationships.
- [x] Created `DestinationConnection` model with relationships.
- [x] Created `MigrationJob` model with JSON casting and relationships.
- [x] Created `EndpointRegistry` model with JSON casting.
- [x] Created `AuditEvent` model with JSON casting and relationships.

## Phase 3: Migration Adapters Base
- [x] Created `SourceAdapterInterface`.
- [x] Created `DestinationAdapterInterface`.
- [x] Created `TwentyISourceAdapter` utilizing Laravel Http client.
- [x] Created `CPanelDestinationAdapter` utilizing Laravel Http client.

## Phase 4: Migration Orchestrator & State Machine
- [x] Created `JobStatus` Enum covering all 19 job states.
- [x] Created `MigrationOrchestrator` service to handle state transitions.

## Phase 5: API Routes & Controllers
- [x] Created `MigrationJobController` to expose REST endpoints.
- [x] Updated `routes/web.php` with `/api/migration-jobs` routes.

## Phase 6: Data Plane Workers
- [x] Created `FileTransferWorker` Laravel Job.
- [x] Created `DatabaseTransferWorker` Laravel Job.

## Phase 7: Frontend Web UI
- [x] Created `WebMigrationController` to handle browser requests.
- [x] Built stunning Dark Mode layout in `layouts.app`.
- [x] Built `dashboard.blade.php` to list jobs.
- [x] Built `migrations.create.blade.php` form for initializing jobs.

## Phase 8: Data Discovery (20i)
- [x] Implemented `TwentyISourceAdapter::getInventory()` to pull 20i package list.
- [x] Updated `MigrationOrchestrator` to automatically process from `DISCOVERING` to `AWAITING_MAPPING`.
- [x] Updated UI to show a "View Packages" button for discovered jobs.

## Phase 9: Package Mapping & Selection
- [x] Added GET and POST routes for `/migrations/{id}/map`.
- [x] Implemented `showMapping()` and `submitMapping()` in `WebMigrationController`.
- [x] Created `map.blade.php` UI to list packages with checkboxes.
- [x] Updated Orchestrator state transitions to properly push from `AWAITING_MAPPING` to `PREFLIGHT`.

## Phase 10: cPanel Provisioning (WHM API)
- [x] Implemented `CPanelDestinationAdapter::provisionAccount()` to hit WHM `createacct`.
- [x] Implemented Orchestrator loop to generate secure usernames and passwords for selected domains.
- [x] Updated Orchestrator to save provisioning results and transition to `INITIAL_TRANSFER`.

## Feature: Real-Time Job Logs
- [x] Added `logs` JSON column to `migration_jobs` via Laravel migration.
- [x] Created `MigrationOrchestrator::logMessage()` to save detailed steps.
- [x] Built `logs.blade.php`, a terminal-style UI to view logs in real-time.

## Phase 11: SFTP File Transfer
- [x] Implemented `MigrationOrchestrator::handleInitialTransfer()`.
- [x] Added logic to connect to source and destination FTPs.
- [x] Built real-time file streaming logic with chunked transfer logs.

## Phase 12: Verification and DB Transfer
- [x] Implemented `MigrationOrchestrator::handleVerifyingInitial()` for file integrity.
- [x] Implemented `MigrationOrchestrator::handleDbTransfer()` for DB extraction, creation, import, and wp-config patching.

## Phase 13: Final Sync and Completion
- [x] Implemented `MigrationOrchestrator::handleFinalSync()` for delta rsyncs and HTTP health checks.
- [x] Set job to `COMPLETED` and updated dashboard UI to show success badge.

## Phase 14: cpmove Architecture Pivot
- [x] Refactored Orchestrator to use `cpmove` backup restoration instead of manual UAPI commands.
- [x] Implemented `MigrationOrchestrator::handlePackaging()` to compile native cPanel backups.
- [x] Implemented `MigrationOrchestrator::handleTransferring()` for SFTP `.tar.gz` transfer.
- [x] Implemented `MigrationOrchestrator::handleRestoring()` to use WHM `restore_queue_add_task`.
- [x] Updated `JobStatus` Enum for new states.
