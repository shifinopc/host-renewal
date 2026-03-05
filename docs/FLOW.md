# Host Renewal App – Application Flow & Reference

This document describes how the application works end-to-end and lists known issues or gaps so you can find and fix problems quickly.

---

## 1. Authentication

| Route | Method | Controller | Purpose |
|-------|--------|------------|---------|
| `/login` | GET | `AuthController@showLoginForm` | Login page (guest only) |
| `/login` | POST | `AuthController@login` | Submit credentials |
| `/logout` | POST | `AuthController@logout` | Log out (auth only) |

- All app routes except login are under `auth` middleware.
- Admin-only routes (Servers, Reports, Settings) use `admin` middleware.

---

## 2. Dashboard

| Route | Method | Controller | Purpose |
|-------|--------|------------|---------|
| `/` | GET | `DashboardController@index` | Dashboard |

- Shows counts: servers, customers, domains, active domains, expiring in 30/7 days.
- Links to main sections.

---

## 3. Servers (Admin)

| Route | Method | Controller | Purpose |
|-------|--------|------------|---------|
| `servers` | GET | `ServerController@index` | List servers |
| `servers/create` | GET | `ServerController@create` | Create form |
| `servers` | POST | `ServerController@store` | Store server |
| `servers/{id}/edit` | GET | `ServerController@edit` | Edit form |
| `servers/{id}` | PUT/PATCH | `ServerController@update` | Update server |
| `servers/{id}` | DELETE | `ServerController@destroy` | Delete server |

- Servers are referenced by domains (hosting provider).

---

## 4. Customers

| Route | Method | Controller | Purpose |
|-------|--------|------------|---------|
| `customers` | GET | `CustomerController@index` | List + Add/Edit modals |
| `customers/create` | GET | `CustomerController@create` | Full-page create |
| `customers` | POST | `CustomerController@store` | Store customer |
| `customers/{id}` | GET | `CustomerController@show` | Customer detail + domains |
| `customers/{id}/edit` | GET | `CustomerController@edit` | Full-page edit |
| `customers/{id}` | PUT | `CustomerController@update` | Update customer |
| `customers/{id}` | DELETE | `CustomerController@destroy` | Delete customer |

**Customer fields**

- Basic: name, company, email, phone, address.
- Tax: `tax_preference` (taxable/exempt), `business_type`, `gstin`, `place_of_supply`, `tax_exempt_reason`.
- Used in proforma GST logic when the customer is linked to a domain.

---

## 5. Domains

| Route | Method | Controller | Purpose |
|-------|--------|------------|---------|
| `domains` | GET | `DomainController@index` | List domains (filters: q, status, server_id, customer_id) |
| `domains/create` | GET | `DomainController@create` | Create form |
| `domains` | POST | `DomainController@store` | Store domain |
| `domains/{id}` | GET | `DomainController@show` | Domain detail |
| `domains/{id}/edit` | GET | `DomainController@edit` | Edit form |
| `domains/{id}` | PUT | `DomainController@update` | Update domain |
| `domains/{id}` | DELETE | `DomainController@destroy` | Delete domain |
| `domains/{domain}/details` | GET | `DomainController@details` | Domain detail (alternate) |
| `domains/{domain}/renew/modal` | GET | `DomainController@renewModal` | Renewal modal content |
| `domains/{domain}/renew` | POST | `DomainController@renew` | Create renewal |
| `domains/{domain}/payments` | GET | `PaymentController@index` | Payments for domain |
| `domains/{domain}/payments/modal` | GET | `PaymentController@indexModal` | Payments list modal |
| `domains/{domain}/payments/create/modal` | GET | `PaymentController@createModal` | Add payment modal |
| `domains/{domain}/proforma/create/modal` | GET | `PaymentController@createProformaModal` | Create proforma modal (for this domain) |
| `domains/{domain}/payments` | POST | `PaymentController@store` | Add payment (invoice) |
| `domains/{domain}/payments/{payment}` | DELETE | `PaymentController@destroy` | Delete payment |

- Each domain belongs to one customer and optionally one server.
- Domain detail shows renewals and payments (invoices only; proformas are not listed here).

---

## 6. Payments (Invoices – Non‑proforma)

| Route | Method | Controller | Purpose |
|-------|--------|------------|---------|
| `payments` | GET | `PaymentController@all` | All invoices (filters, pagination) |
| `payments/export` | GET | `PaymentController@export` | CSV export (invoices only) |
| `payments/add` | POST | `PaymentController@quickStore` | Quick add payment (invoice) |
| `payments/reports` | GET | `PaymentController@reports` | Revenue summary + charts |
| `payments/{payment}/invoice` | GET | `PaymentController@invoice` | View invoice HTML or PDF |
| `payments/{payment}/receipt` | GET | `PaymentController@receipt` | Receipt view |

- Only records with `type` = `invoice` or `null` are treated as invoices; proformas are separate.

---

## 7. Proforma Invoices

### 7.1 Routes

| Route | Method | Controller | Purpose |
|-------|--------|------------|---------|
| `proforma-invoices` | GET | `PaymentController@proformaIndex` | List proformas |
| `proforma-invoices/create` | GET | `PaymentController@createProforma` | Create modal content (global) |
| `proforma-invoices` | POST | `PaymentController@storeProforma` | Create proforma |
| `proforma-invoices/{payment}/edit` | GET | `PaymentController@editProformaModal` | Edit form content (for modal) |
| `proforma-invoices/{payment}` | PUT | `PaymentController@updateProforma` | Update proforma |
| `proforma-invoices/{payment}/view` | GET | `PaymentController@proformaPanel` | Sidebar preview HTML |
| `payments/{payment}/invoice?download=1` | GET | `PaymentController@invoice` | Download proforma PDF |

### 7.2 Creating a proforma

- **From list**: “New proforma” opens global modal (`modal-create-global`). Fields: Domain, Amount, Invoice date, **Invoice type (With tax / Without tax)**, Method, Reference no.
- **From domain detail**: “New proforma” opens domain-specific modal (`modal-create`). Domain is fixed; same other fields.
- **Store** (`storeProforma`):
  - Validates: domain_id, amount, payment_date, method, reference_no.
  - Resolves `is_taxable`: if form sends `is_taxable` (1/0) → use it; else default = company has Tax/VAT or GSTIN **and** customer is taxable.
  - Creates `Payment` with `type = 'proforma'`, `status = 'draft'`, `is_taxable` set.

### 7.3 Listing proformas

- **Controller** (`proformaIndex`): Filters by `type = 'proforma'`, orders by **id** descending. Adds to each payment: `tax_amount`, `total_amount`, `apply_gst` (computed from company tax, customer preference, `payment->is_taxable`), 18% GST.
- **View** (`proforma/index.blade.php`): Table columns – Proforma no., Date, Customer, Domain, Amount, Tax amount, Total, Action (Edit, View proforma).
- **Edit**: “Edit” opens modal; content loaded via GET `proforma-invoices/{id}/edit`. Form submits PUT to `proforma-invoices/{id}`. Editable: amount, payment_date, method, reference_no, invoice type (With/Without tax).

### 7.4 Sidebar preview

- “View proforma” loads HTML from `proformaPanel` into sidebar.
- **View** (`proforma/panel.blade.php`): Uses `payment->is_taxable` (with fallback to customer taxable) and company tax settings to set `applyGst`. If `applyGst`: show Subtotal, GST @ 18%, Total and customer GSTIN in Billed to. If not: only Subtotal and Total, no GST row, no GSTIN in Billed to.

### 7.5 PDF download

- “Download PDF” uses `payments/{id}/invoice?download=1` → `proforma/pdf.blade.php` (DomPDF).
- Same GST logic as panel: `applyGst` from company + `payment->is_taxable` + customer. With tax: company/customer GSTIN and Tax/VAT in their blocks; GST row in totals. Without tax: no GST row, no GST IDs on that proforma.

### 7.6 Payment model (proforma)

- `payments.type` = `'proforma'`, `status` = `'draft'`.
- `payments.is_taxable`: `true` = with tax, `false` = without tax, `null` = legacy (fallback to customer).

---

## 8. Settings (Admin)

| Route | Method | Controller | Purpose |
|-------|--------|------------|---------|
| `settings` | GET | `SettingsController@index` | Settings page (tabs) |
| `settings` | POST | `SettingsController@update` | Save settings + file uploads |

**Tabs**

- **Company**: name, address, email, phone, website, Tax/VAT ID, GSTIN, logo. Remove logo/header/footer via “Remove” buttons.
- **Proforma invoice template**: invoice prefix, number length, next number; header/footer images.
- **Tax settings**: Read-only summary when company has Tax/VAT or GSTIN.

---

## 9. Reports (Admin)

| Route | Method | Controller | Purpose |
|-------|--------|------------|---------|
| `reports/expiring` | GET | `ReportController@expiring` | Domains expiring |
| `reports/revenue` | GET | `ReportController@revenue` | Revenue report |
| `reports/server-revenue` | GET | `ReportController@serverRevenue` | Revenue by server |
| `reports/expiring.csv` | GET | Same | CSV export |
| (same for revenue, server-revenue) | GET | Same | CSV exports |

---

## 10. Database / Migrations

- **Payments**: Ensure migration that adds `is_taxable` (nullable boolean) to `payments` has been run, e.g. `php artisan migrate`. Without it, “Without tax” cannot be stored and sidebar/PDF may show tax for proformas that should be without tax.
- **Customers**: Tax fields (`tax_preference`, `business_type`, `gstin`, `place_of_supply`, `tax_exempt_reason`) and **payments** `type`, `status`, `is_taxable` should be present from their migrations.

---

## 11. Known Issues & Gaps

Use this list to find and fix issues.

| Area | Issue / gap | Where to look |
|------|-------------|----------------|
| **Proforma – tax** | Old proformas created before `is_taxable` have `null`; they fall back to “taxable if customer is taxable”. If they were meant to be without tax, DB must be updated or recreated. | `panel.blade.php`, `pdf.blade.php`: `$invoiceTaxableFlag = $payment->is_taxable` |
| **Proforma – GST rate** | GST rate is hard-coded **18%** everywhere (listing, panel, PDF). No setting. | `PaymentController@proformaIndex`, `panel.blade.php`, `pdf.blade.php` |
| **Proforma – domain on edit** | Edit proforma form does not allow changing domain; domain is read-only. | `modal-edit.blade.php`, `updateProforma` |
| **Customers – validation** | `tax_exempt_reason` may not be required when `tax_preference = exempt` in all forms. | `CustomerController@store`, `@update`, create/edit views |
| **Listing order** | Proformas are ordered by `id` descending (newest first). If you need by date then invoice number, change `proformaIndex` query. | `PaymentController@proformaIndex` |
| **Payments list (domain)** | Domain payment list and modals show only invoices (`type` null or `invoice`), not proformas. | `PaymentController@index`, `indexModal` |
| **Invoice number** | Proforma and invoice share the same sequence (`invoice_next_number` in settings). | `Payment::generateNextInvoiceNumber()`, Settings |
| **PDF – place of supply** | Customer `place_of_supply` is stored but not used for CGST/SGST/IGST logic in PDF. | `pdf.blade.php` |
| **Edit proforma – success** | After update, page redirects to list; sidebar does not auto-refresh if it was open for that proforma. | Optional: add JS to close sidebar on submit |

---

## 12. Quick reference – key files

| Purpose | File(s) |
|--------|--------|
| Proforma list + modals | `resources/views/proforma/index.blade.php` |
| Create proforma (global) | `resources/views/proforma/partials/modal-create-global.blade.php` |
| Create proforma (per domain) | `resources/views/proforma/partials/modal-create.blade.php` |
| Edit proforma form | `resources/views/proforma/partials/modal-edit.blade.php` |
| Sidebar preview | `resources/views/proforma/panel.blade.php` |
| PDF layout | `resources/views/proforma/pdf.blade.php` |
| Proforma logic | `app/Http/Controllers/PaymentController.php` (proformaIndex, storeProforma, editProformaModal, updateProforma, proformaPanel), `invoice()` for PDF |
| Payment model | `app/Models/Payment.php` (fillable: is_taxable; casts: is_taxable boolean) |
| Settings | `resources/views/settings/index.blade.php`, `app/Http/Controllers/SettingsController.php` |
| Routes | `routes/web.php` |

---

*Last updated to include edit proforma flow and full application flow with known issues.*
