# My Sales ERP Zota — Project Mindmap

> Reference map of the **Ultimate POS** fork `mysaleserp_zota` — every module, controller, table, and design rule. Use this to find the right file before making changes.

---

## 🏛 Root

- **Path:** `/Applications/XAMPP/xamppfiles/htdocs/mysaleserp_zota`
- **Stack:** Laravel 9 · PHP 8.2 · MariaDB 10.4 (DB `mysaleserp_zota`, user `root`, no pwd)
- **Frontend:** Tailwind CSS **with `tw-` prefix** (raw Bootstrap classes are mostly no-ops) · jQuery · DataTables · Gridstack
- **Modular:** [`nwidart/laravel-modules`](https://github.com/nWidart/laravel-modules) — each module is self-contained
- **Auth/Authz:** Spatie Permissions, Laravel Passport (OAuth)

---

## 🌐 Core App (`app/Http/Controllers/`)

### Auth & Install
- **Auth/**
  - `LoginController` — username-based login → `auth.login` (recently redesigned medical-green split-screen)
  - `RegisterController`
  - `ForgotPasswordController` / `ResetPasswordController`
  - `ConfirmPasswordController` / `VerificationController`
- **Install/**
  - `InstallController` — first-time setup wizard
  - `ModulesController` — Manage Modules: install/uninstall/delete

### POS / Sales
- `SellController`, `SellPosController`, `SellReturnController`
- `SalesOrderController`, `SalesCommissionAgentController`
- `TransactionPaymentController`, `CashRegisterController`

### Purchase
- `PurchaseController`, `PurchaseOrderController`
- `PurchaseRequisitionController`, `PurchaseReturnController`
- `CombinedPurchaseReturnController`

### Stock
- `StockAdjustmentController`, `StockTransferController`
- `ImportOpeningStockController`

### Products
- `ProductController` (CRUD + bulk + variations)
- `VariationTemplateController`
- `BrandController`, `UnitController`
- `ImportProductsController`

### Contacts
- `ContactController` (customers/suppliers, ledger, import, map)
- `CustomerGroupController`
- `ImportSalesController`

### Taxonomy & Pricing
- `TaxonomyController` (category hierarchy)
- `TaxRateController`, `GroupTaxController`
- `DiscountController`, `SellingPriceGroupController`, `LedgerDiscountController`

### Accounting (core stub — extended by Accounting module)
- `AccountController`, `AccountTypeController`, `AccountReportsController`

### Other core
- **Expenses:** `ExpenseController`, `ExpenseCategoryController`
- **Documents:** `DocumentAndNoteController`
- **Invoice:** `InvoiceLayoutController`, `InvoiceSchemeController`, `LabelsController`
- **Locations:** `BusinessLocationController`, `LocationSettingsController`
- **Users/Roles:** `UserController`, `ManageUserController` (sign-in-as-user), `RoleController`
- **Notifications:** `NotificationController`, `NotificationTemplateController`
- **Reports:** `ReportController` (P/L, sales, purchase, tax, stock, etc.)
- **Dashboard:** `DashboardConfiguratorController`
- **Backup:** `BackUpController`
- **Barcode / Warranty / TypesOfService / Printer / OpenAI**
- **Payment Gateways:** `MyFatoorahController`, `PesaPalController`

### Restaurant sub-app (`Controllers/Restaurant/`)
- `RestaurantController`, `TableController`, `OrderController`
- `BookingController`, `KitchenController`
- `ModifierSetsController`, `ProductModifierSetController`
- `DataController`

### Utils (`app/Utils/`)
`BusinessUtil`, `ContactUtil`, `ProductUtil`, `TaxUtil`, `ModuleUtil`, `InstallUtil`, `RestaurantUtil`, `Util`

### Core Models (`app/`)
`User`, `Business`, `BusinessLocation`, `Contact`, `Product`, `ProductVariation`, `Variation`, `VariationTemplate`, `VariationGroupPrice`, `VariationLocationDetails`, `Category`, `Brand`, `Unit`, `Transaction`, `TransactionSellLine`, `TransactionPayment`, `PurchaseLine`, `StockAdjustmentLine`, `Account`, `AccountType`, `AccountTransaction`, `CashRegister`, `CashDenomination`, `Discount`, `SellingPriceGroup`, `TaxRate`, `GroupSubTax`, `ExpenseCategory`, `DocumentAndNote`, `InvoiceLayout`, `InvoiceScheme`, `Currency`, `Barcode`, `CustomerGroup`, `Media`, `NotificationTemplate`, `PaymentAccount`, `Printer`, `ProductRack`, `ReferenceCount`, `System` (key/value), `Taxonomy`, `TransactionSellLinesPurchaseLines`, `TypesOfService`, `UserContactAccess`, `Warranty`

---

## 🧩 Modules (`Modules/`)

**24 total · 9 active · 13 enabled-but-unpinned · 2 disabled**

### ✅ ACTIVE (boots, routes, migrations, UI)

#### 📒 Accounting v0.8 (v2.0 schema)
- **Entities:** `accounting_account_types`, `accounting_accounts`, `accounting_accounts_transactions`, `accounting_acc_trans_mappings`, `accounting_budgets`
- **Controllers:** `Coa`, `JournalEntry`, `Transfer`, `Transaction`, `Budget`, `Report`, `Settings`, `AccountType`, `Reconcile`, `Accounting`
- **Listeners** (event-driven ledger mapping): `MapSellTransaction`, `MapPurchaseTransaction`, `MapPaymentTransaction`, `MapExpenseTransactions`
- **Pages:** dashboard, journal entries, transfers, transactions, budgets, reports, settings

#### 📦 AssetManagement v2.1
- **Entities:** `assets`, `asset_maintenances`, `asset_transactions`, `asset_warranties`
- **Controllers:** `Asset`, `Data`, `Install`
- **Pages:** asset CRUD, settings, dashboard

#### 🔌 Connector v2.1 (3rd-party API bridge)
- **14 API controllers** (`Api/Product`, `Sell`, `Contact`, `Brand`, `Category`, `Expense`, `Tax`, `Unit`, `User`, `Attendance`, etc.)
- `ClientController`, `ConnectorController`
- Has its own auth flow (`AuthConnectorServiceProvider`)
- **14 Transformers** (resource formatters)

#### 🤝 Crm v2.2
- **Entities:** `crm_campaigns`, `crm_call_logs`, `crm_contact_person_commissions`, `crm_followup_invoices`, `crm_lead_users`, `crm_marketplaces`, `crm_proposals`, `crm_proposal_templates`, `crm_schedules`, `crm_schedule_logs`, `crm_schedule_users`
- **Controllers:** `Lead`, `Campaign`, `Proposal`, `ProposalTemplate`, `Schedule`, `ScheduleLog`, `CallLog`, `ContactBooking`, `ContactLogin`, `CrmDashboard`, `CrmMarketplace`, `CrmSettings`, `Dashboard`, `ManageProfile`, `OrderRequest`, `Purchase`, `Report`, `Sell`, `Ledger`
- **Console:** `CreateRecursiveFollowup`, `SendScheduleNotification`
- **Notifications:** `ScheduleNotification`, `SendCampaignNotification`, `SendProposalNotification`
- **Middleware:** `CheckContactLogin`, `ContactSidebarMenu`
- Separate `contact_login` portal (customer-facing)

#### 👥 Essentials v4.0 (HRM)
- **Entities (×18):** `essentials_allowances_and_deductions`, `essentials_attendances`, `essentials_documents`, `essentials_document_shares`, `essentials_holidays`, `essentials_kb` (knowledge base), `essentials_kb_users`, `essentials_leave_types`, `essentials_leaves`, `essentials_messages`, `essentials_payroll_groups`, `essentials_payroll_group_transactions`, `essentials_reminders`, `essentials_shifts`, `essentials_todos`, `essentials_todo_comments`, `essentials_todos_users`, `essentials_user_allowance_and_deductions`, `essentials_user_sales_targets`, `essentials_user_shifts`
- **Controllers:** `Essentials`, `Attendance`, `Payroll`, `Shift`, `Document`, `DocumentShare`, `Reminder`, `ToDo`, `SalesTarget`, `KnowledgeBase`, `Dashboard`
- **Console:** `AutoClockOutUser`
- **Notifications:** leave, payroll, message, task, document share

#### 🛍 ProductCatalogue v1.0
- Public product catalogue (mini storefront)

#### 📊 Spreadsheet v1.0
- **Entities:** `sheet_spreadsheets`, `sheet_spreadsheet_shares`
- Share spreadsheets with users/roles/todos

#### 👑 Superadmin v4.0
- **Entities:** `packages`, `subscriptions`, `superadmin_communicator_logs`, `superadmin_frontend_pages`
- Multi-business SaaS control panel

### 🟡 ENABLED IN JSON (`true`) BUT NO VERSION PINNED
`Cms`, `Ecommerce`, `FieldForce`, `Manufacturing`, `Project`, `Repair`, `Hms` (Hotel Management), `InboxReport`, `CustomDashboard`, `Gym`, `ZatcaIntegrationKsa`, `Cheque`, `Woocommerce`
- Most are buy-only or stubbed; no DB schema by default

### ❌ DISABLED (`false` in `modules_statuses.json`)
- `AiAssistance` (OpenAI integration)
- `InventoryManagement`

---

## 🗄 Database (`mysaleserp_zota`)

**124 tables** total, grouped by domain:

| Group | Key Tables |
|---|---|
| **Identity** | `users`, `roles`, `permissions`, `role_has_permissions`, `model_has_permissions`, `password_resets`, `oauth_*` |
| **Tenant** | `business`, `business_locations`, `system` (key/value) |
| **Products** | `products`, `product_variations`, `variations`, `variation_templates`, `variation_value_templates`, `variation_location_details`, `variation_group_prices`, `product_locations`, `product_racks`, `media` |
| **Catalog** | `categories`, `brands`, `units`, `tax_rates`, `group_sub_taxes`, `barcodes`, `selling_price_groups`, `discount_variations`, `discounts`, `taxonomies`, `categorizables` |
| **Contacts** | `contacts`, `customer_groups`, `user_contact_access` |
| **Transactions** | `transactions`, `transaction_sell_lines`, `transaction_payments`, `transaction_sell_lines_purchase_lines`, `purchase_lines`, `stock_adjustment_lines`, `stock_adjustments_temp` |
| **Money** | `accounts`, `account_types`, `account_transactions`, `cash_registers`, `cash_register_transactions`, `cash_denominations`, `currencies`, `payment_accounts` |
| **Documents** | `document_and_notes`, `invoice_layouts`, `invoice_schemes`, `reference_counts`, `warranties`, `sell_line_warranties` |
| **Service** | `types_of_services`, `res_tables`, `bookings`, `res_product_modifier_sets` |
| **Config** | `notification_templates`, `printers`, `dashboard_configurations`, `expense_categories` |
| **System** | `sessions`, `subscriptions`, `packages`, `activity_log`, `migrations` |
| **Accounting v2** | `accounting_account_types`, `accounting_accounts`, `accounting_accounts_transactions`, `accounting_acc_trans_mappings`, `accounting_budgets` |
| **AssetManagement** | `assets`, `asset_maintenances`, `asset_transactions`, `asset_warranties` |
| **Crm** | `crm_call_logs`, `crm_campaigns`, `crm_contact_person_commissions`, `crm_followup_invoices`, `crm_lead_users`, `crm_marketplaces`, `crm_proposals`, `crm_proposal_templates`, `crm_schedules`, `crm_schedule_logs`, `crm_schedule_users` |
| **Essentials** | `essentials_*` (×18: attendances, leaves, payrolls, todos, shifts, kb, etc.) |
| **Spreadsheet** | `sheet_spreadsheets`, `sheet_spreadsheet_shares` |
| **Superadmin** | `superadmin_communicator_logs`, `superadmin_frontend_pages` |
| **Accounting v1 (residual)** | `account_types`, `account_transactions`, `account_detail_types`, `account_subtypes`, `chart_of_accounts`, `journal_entries`, `payment_details`, `payment_types`, `countries`, `transfers`, `budgets`, `branch_capital` |

---

## 🎨 Frontend Stack

```
public/css/
├── tailwind/app.css  ← PRIMARY (tw- prefixed utilities)
├── vendor.css        ← Bootstrap leftovers (only col-lg-6 works reliably)
├── init.css, app.css, gridstack.min.css, rtl.css
└── custom rx- design tokens in resources/views/layouts/auth2.blade.php
   (--rx-green-50..900, --rx-ink-50..900)
```

**Layout hierarchy (`resources/views/`):**
- `app.blade.php` — main authenticated app (topbar + sidebar)
- `partials/app.blade.php` — sidebar menu (registers module links)
- `auth2.blade.php` — login/register (recently redesigned — medical green split-screen)
- `restaurant/*` — kitchen/order display
- `Modules/*/Resources/views/` — per-module views

### 🔑 Frontend Rules
- ✅ Use: `tw-*` (Tailwind), `rx-*` (custom), inline `style="..."`
- ❌ Don't use: `d-flex`, `col-lg-6`, `d-lg-none`, `mb-3`, `align-items-center`, `justify-content-between` — they're no-ops in this project

---

## ⚙️ Key Config

| File | Purpose |
|---|---|
| `.env` | DB, APP, mail, queue, payments (PayPal/Paystack/PesaPal/MyFatoorah), OpenAI |
| `config/modules.php` | nwidart/laravel-modules (Modules dir, autoload) |
| `config/permission.php` | spatie roles/permissions |
| `config/constants.php` | Feature flags (`enable_recaptcha`, `allow_registration`, etc.) |
| `config/menus.php` | Sidebar menu registration |
| `modules_statuses.json` | Module on/off (23 entries) |
| `system` table (DB) | `{module}_version` + other system properties |

---

## 🔄 Auth Flow

1. `GET /login` → `LoginController::showLoginForm()` → `resources/views/auth/login.blade.php`
2. `POST /login` → `AuthenticatesUsers` trait → redirect to `/home`
3. `GET /home` → `HomeController::index()` (Dashboard)
4. `AdminSidebarMenu` middleware injects module nav links into sidebar
5. Module routes loaded from `Modules/*/Routes/web.php`
6. Permissions enforced via `spatie/laravel-permission` (User → Roles → Permissions)

---

## 🧰 Module Install Pattern (recurring)

For every module:
1. `module.json` declares name, version, providers
2. `Database/Migrations/*` create tables
3. `Http/Controllers/InstallController.php`
   - `index()` → `view('install.install-module')` **MUST** pass `{action_url, intruction_type, module_display_name}`
   - `install()` → runs `module:migrate`, `module:publish`, sets version
   - `uninstall()` → removes version
4. Install CLI (manual):
   ```bash
   php artisan module:migrate <Name> --force
   php artisan module:publish <Name>
   System::addProperty('{name}_version', config('{name}.module_version'))
   ```
5. Removal via `ModulesController::destroy()` (clears asset cache, removes version, sets `false` in `modules_statuses.json`, deletes module folder)

---

## 🔧 Common Tasks Quick Reference

| Task | Command / Location |
|---|---|
| Clear stale views | `php artisan view:clear && cache:clear && config:clear` |
| List modules | `cat modules_statuses.json` |
| Check installed versions | `SELECT * FROM system WHERE key LIKE '%_version';` |
| Enable/disable module | Edit `modules_statuses.json` (no artisan command) |
| Upload new module | `Manage Modules → Upload` then install via UI |
| Custom auth design | `resources/views/layouts/auth2.blade.php` + `auth/login.blade.php` |
| Sidebar items | `app/Http/helpers.php`, `config/menus.php`, module `nav.blade.php` |
| Permissions | `permissions` table seeded by `PermissionsTableSeeder` |
| Run core migrations | `php artisan migrate` |
| Run module migrations | `php artisan module:migrate <Name>` |
| Recheck user roles | `SELECT u.username, r.name FROM users u JOIN model_has_roles mhr ON mhr.model_id=u.id JOIN roles r ON r.id=mhr.role_id;` |

---

## 🧪 Engineering Conventions

- Modules use `nwidart/laravel-modules` — each in `Modules/<Name>/`
- Use `App\Utils\ModuleUtil::isModuleInstalled($name)` to check
- DB host pattern: `127.0.0.1` (XAMPP), user `root`, no password
- Multi-business: each business has its own `business_id` scoping on transactions
- All money fields use `decimal(22,4)` for precision
- Image storage: `storage/app/public/` linked to `public/storage/`
- Cache: `file` driver (not Redis by default in this XAMPP setup)
- Queue: `sync` driver (jobs run immediately; for prod, switch to `database`/`redis`)

## 🛑 Hard Constraints

- Shared installation views require `$module_display_name` and `$intruction_type` from the module's `InstallController`
- Auth layout `auth2.blade.php` uses a custom `rx-` grid and utility system to avoid framework conflicts
- DDL operations (e.g., `ALTER TABLE`) cannot be wrapped in `DB::transaction()` blocks here (MySQL DDL auto-commits)

## 📝 Lessons Learned

- Bootstrap utility classes like `d-lg-none` were no-ops in this project's layout — moved to custom `rx-` classes
- Stale compiled Blade views often cause "undefined function" errors → clear `view:clear` after UI changes
- Module `InstallController::index()` shipped without `$module_display_name` on multiple modules (Spreadsheet, Accounting, Crm, Connector, AiAssistance, ProductCatalogue) — must be added on install
- The `Destroy` module action had a `die()` placeholder — replaced with real uninstall + folder delete
