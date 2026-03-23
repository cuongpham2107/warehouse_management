
# ASGL Warehouse Management — Copilot Instructions

## Big Picture & Architecture
- **Laravel-based** system for warehouse operations: Receiving Plans, Check-in, Outbound Shipping.
- **Core flows:**
  - Receiving Plan → Check-in (assign Pallet/location) → Outbound/Shipping (pick, load, depart)
  - Device integration: PDA (barcode/manual), forklift computers (location assignment)
- **Domain structure:**
  - `app/Models/`: Eloquent models (Crate, Pallet, Shipment, ReceivingPlan, etc.)
  - `app/Enums/`: Status enums (e.g., `ShipmentStatus`, `CrateStatus`) with UI helpers (label, color, icon)
  - `app/States/`: State machine classes for workflow transitions (see `ShippingRequestState`, `ShipmentItemState`)
  - `app/Filament/Resources/`: Filament admin resources for CRUD, bulk actions, workflow steps
  - `app/Http/Controllers/`: RESTful controllers for web/device APIs

## Developer Workflows
- **Build/Serve:**
  - `php artisan serve` — start dev server
  - `php artisan migrate` — run migrations
- **Testing:**
  - `phpunit` (see `phpunit.xml`)
- **Database:**
  - SQLite for local dev (`database/database.sqlite`)
  - Migrations: `database/migrations/` (includes stored procedures/triggers for MySQL)
- **Front-end:**
  - Vite for asset bundling (`vite.config.js`)

## Project-Specific Patterns
- **Bulk Actions:** Excel import/export for Receiving Plans, Crates (see Filament actions/resources)
- **Device Workflows:** PDA/forklift logic via dedicated controllers, status checks, barcode/manual entry, offline sync
- **Status/State Machines:**
  - Enums encapsulate workflow states and UI mapping
  - State machine classes (e.g., `ShippingRequestState`, `ShipmentItemState`) define allowed transitions and helpers
- **Custom Actions:**
  - Activate/Close/Duplicate plans, Assign/Move locations, Approve/Reject requests, Print POD, Track shipments, etc.
- **Reporting:** Inventory, movement, performance reports; dashboard filtering/export

## Integration Points
- **Filament Admin:** Custom resources/widgets for advanced admin workflows
- **Device APIs:** Endpoints for PDA/forklift (barcode/manual, sync, offline)
- **Notifications:** Alerts/escalations for workflow events

## Conventions & Examples
- **Enums:** Always provide UI helpers (label, color, icon, badge class)
- **Controllers:** RESTful, grouped by domain (ReceivingPlan, Crate, Shipment, etc.)
- **Filament:** Use Filament's action/resource patterns for bulk/workflow operations
- **Testing:** Feature/unit tests in `tests/Feature/`, `tests/Unit/`

## Key Files & Directories
- `app/Enums/ShipmentStatus.php`, `app/Enums/CrateStatus.php`: Status enums with UI helpers
- `app/States/ShippingRequestState.php`, `app/States/ShipmentItemState.php`: State machine classes
- `app/Models/ReceivingPlan.php`, `app/Models/Crate.php`, etc.: Core domain models
- `app/Filament/Resources/`: Filament admin resources for CRUD/workflow actions
- `routes/web.php`, `routes/console.php`: Web/console routes
- `README.md`: Detailed workflow/action breakdown

---
**For new features:**
- Follow workflow/state patterns in enums and state machines
- Reference Filament actions/resources for UI consistency
- Device integration: endpoints must support barcode/manual entry and sync logic
- Reporting: use dashboard/export conventions
