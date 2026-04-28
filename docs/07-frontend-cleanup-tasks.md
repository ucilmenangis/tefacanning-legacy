# Frontend Cleanup Tasks — Convert Native CSS to Tailwind

## Rule

**ALL `<style>` blocks must be removed.** Replace every native CSS class with Tailwind utility classes.

### Exceptions — KEEP native CSS (Tailwind can't do these):

1. `includes/header-admin.php` — sidebar accordion animation (3 lines: `max-height` transition). Tailwind can't animate `max-height`.
2. `views/pdf/order-report.php` — PDF template. DomPDF requires inline CSS.
3. `border-spacing` in order item tables — Tailwind CDN has no `border-spacing` utility. Keep as inline `style="border-collapse:separate;border-spacing:0 12px;"` on the `<table>` element.
4. Accordion animations in `customer/preorder.php` and `customer/edit-order.php` — same `max-height` transition as sidebar. Keep as a small `<style>` block (5 lines).
5. `.product-row.selected` in `customer/preorder.php` and `customer/edit-order.php` — JS-toggled state class. Keep as a small `<style>` block.
6. Dynamic `style=""` for chart canvas heights (`height:32px`) — keep those inline styles.

### Reference files (how it should look — 0 native CSS):
- `index.php` — pure Tailwind
- `customer/dashboard.php` — pure Tailwind

## Tailwind Config (already in header-admin.php)

```js
colors: {
    primary: '#E02424',
    accent:  '#F05252',
    dark:    '#9B1C1C',
    navy:    '#111827',
}
```

Use `text-primary`, `bg-primary`, `text-navy`, `bg-navy`, `text-dark`, etc.

---

## Progress Tracker

### Phase 1 — Auth Pages
- [x] `auth/login-admin.php`
- [x] `auth/login-customer.php`
- [x] `auth/register.php`
- [x] `auth/forgot-password.php`

### Phase 2 — Admin List Pages
- [x] `admin/orders.php`
- [x] `admin/products.php`
- [x] `admin/batches.php`
- [x] `admin/customers.php`

### Phase 3 — Admin Form Pages
- [x] `admin/edit-product.php`
- [x] `admin/create-product.php`
- [x] `admin/create-batch.php`
- [x] `admin/edit-customer.php`
- [x] `admin/create-order.php`
- [x] `admin/edit-order.php`
- [x] `admin/view-order.php`

### Phase 4 — Admin Special Pages
- [x] `admin/dashboard.php`
- [x] `admin/pengaturan.php`
- [x] `admin/activity-log.php`

### Phase 5 — Customer Pages
- [x] `customer/dashboard.php`
- [x] `customer/preorder.php`
- [x] `customer/orders.php`
- [x] `customer/edit-order.php`
- [x] `customer/profile.php`

---

## Phase 1 — Auth Pages (4 files, ~402 lines to remove)

All 4 files share 90%+ identical CSS. Convert each one.

### 1.1 `auth/login-admin.php` (~102 lines CSS → 0)

Remove `<style>` block. Replace classes:

| Native CSS | Tailwind Replacement |
|------------|---------------------|
| `body` background | Move to `<body>` tag: `bg-[#f8f9fb]` |
| `.auth-card` | `bg-white rounded-2xl shadow-[0_1px_3px_rgba(0,0,0,0.06),0_4px_24px_rgba(0,0,0,0.06)] border border-black/[0.04]` |
| `.form-input` | `w-full border border-gray-200 rounded-lg px-4 py-3 text-[13px] text-gray-700 bg-gray-50 outline-none transition-all focus:border-primary focus:ring-[3px] focus:ring-primary/10 focus:bg-white` |
| `.form-input::placeholder` | Add `placeholder:text-gray-400` alongside `.form-input` classes |
| `.input-wrapper` | `relative` |
| `.input-icon` | `absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-base pointer-events-none` |
| `.form-input.has-icon` | Same `.form-input` + `pl-[38px]` |
| `.toggle-password` | `absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors bg-transparent border-none cursor-pointer` |
| `.btn-primary` | `w-full bg-primary text-white font-bold py-3 rounded-lg transition-all hover:bg-dark active:scale-[0.98]` |
| `.form-label` | `text-[12px] font-semibold text-gray-500 mb-1.5 block` |
| `.checkbox-custom` | `w-4 h-4 accent-primary cursor-pointer` |
| `.alert-error` | `bg-red-50 border border-red-200 text-red-600 text-[12px] rounded-lg px-4 py-3 mb-4` |

### 1.2 `auth/login-customer.php` (~101 lines CSS → 0)

Same as login-admin. Same classes, same replacements. Extra class:

| Native CSS | Tailwind Replacement |
|------------|---------------------|
| `.link-red` | `text-primary font-semibold hover:text-dark transition-colors` |

### 1.3 `auth/register.php` (~108 lines CSS → 0)

Same pattern as login. Extra:

| Native CSS | Tailwind Replacement |
|------------|---------------------|
| `textarea.form-input` | Same `.form-input` Tailwind + `min-h-[100px] resize-none` |
| `.form-input.with-icon` | Same `.form-input` + `pl-11` |

### 1.4 `auth/forgot-password.php` (~91 lines CSS → 0)

Same pattern. Extra:

| Native CSS | Tailwind Replacement |
|------------|---------------------|
| `.alert-success` | `bg-emerald-50 border border-emerald-200 text-emerald-600 text-[12px] rounded-lg px-4 py-3 mb-4` |
| `.back-link` | `inline-flex items-center gap-2 text-gray-400 hover:text-primary transition-colors text-[13px]` |

---

## Phase 2 — Admin List Pages (4 files, ~150 lines to remove)

All 4 files share the same table/list layout pattern.

### Shared Component Replacements (apply to ALL 4 files)

| Native CSS | Tailwind Replacement |
|------------|---------------------|
| `.page-header` | `flex items-center justify-between mb-5` |
| `.breadcrumb` | `text-[12px] text-gray-400 mb-1` + `.breadcrumb span` → `text-gray-700` |
| `.btn-primary` | `inline-flex items-center gap-1.5 bg-primary text-white text-[13px] font-bold px-4 py-2 rounded-lg transition-colors hover:bg-dark` |
| `.table-wrap` | `bg-white border border-gray-100 rounded-xl overflow-visible` |
| `.table-toolbar` | `flex items-center justify-end gap-2 px-4 py-3 border-b border-gray-50` |
| `.mini-search` | `border border-gray-200 rounded-lg py-1.5 pl-[30px] pr-3 text-[12px] outline-none bg-gray-50 w-[200px] transition-colors focus:border-primary focus:bg-white` |
| `.icon-btn-sm` | `w-[30px] h-[30px] rounded-md border border-gray-200 bg-white inline-flex items-center justify-center text-gray-400 cursor-pointer transition-colors hover:bg-gray-50 hover:text-gray-700` |
| `.data-table` | `w-full text-left text-[12.5px] border-collapse` |
| `.data-table th` | `text-[11.5px] font-semibold text-gray-400 px-3.5 py-2.5 border-b border-gray-100 whitespace-nowrap bg-gray-50/50` |
| `.data-table td` | `px-3.5 py-3 border-b border-gray-50/50 text-gray-700 align-middle` |
| `.data-table tr:last-child td` | `border-b-0` |
| `.data-table tbody tr:hover td` | `bg-gray-50` |
| `.cb-cell` | `w-9` |
| `.cb` | `w-[15px] h-[15px] accent-primary cursor-pointer` |
| `.table-footer` | `px-4 py-3 border-t border-gray-50 flex items-center justify-between text-[12px] text-gray-400 gap-3 flex-wrap` |
| `.per-page-select` | `border border-gray-200 rounded-md px-2 py-1 text-[12px] outline-none bg-white appearance-none cursor-pointer` |

### 2.1 `admin/orders.php` (~42 lines CSS → 0)

Use shared replacements above. Extra:

| Native CSS | Tailwind Replacement |
|------------|---------------------|
| `.status-inline` | `inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold border` |
| `.action-pill` | `inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11.5px] font-semibold cursor-pointer transition-colors border-0 bg-transparent` |

### 2.2 `admin/products.php` (~36 lines CSS → 0)

Same shared replacements. Extra:

| Native CSS | Tailwind Replacement |
|------------|---------------------|
| `.stock-badge` | `inline-flex items-center justify-center min-w-[38px] px-2 py-0.5 rounded-full text-[11.5px] font-bold bg-emerald-50 text-emerald-600` |

### 2.3 `admin/batches.php` (~39 lines CSS → 0)

Same shared replacements. Extra:

| Native CSS | Tailwind Replacement |
|------------|---------------------|
| `.status-pill` | `inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold border` |
| `.pesanan-count` | `text-[11px] bg-blue-50 text-blue-600 border border-blue-100 px-2 py-0.5 rounded font-semibold` |

### 2.4 `admin/customers.php` (~36 lines CSS → 0)

Same shared replacements. Extra:

| Native CSS | Tailwind Replacement |
|------------|---------------------|
| `.order-badge` | `inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-600` |

---

## Phase 3 — Admin Form Pages (7 files, ~230 lines to remove)

All form pages share card/input/button patterns.

### Shared Form Replacements (apply to ALL form pages)

| Native CSS | Tailwind Replacement |
|------------|---------------------|
| `.card` | `bg-white border border-gray-100 rounded-xl p-6 mb-6` |
| `.card-title` | `text-[14px] font-bold text-gray-800 mb-4 flex items-center gap-2` |
| `.card-subtitle` | `text-[11px] text-gray-400 font-medium -mt-3 mb-5 block` |
| `.label` | `text-[12px] font-semibold text-gray-600 mb-1.5 block` |
| `.label .required` | Use `<span class="text-primary ml-0.5">*</span>` inline |
| `.input` | `w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-[13px] text-gray-800 bg-white transition-all outline-none focus:border-primary focus:ring-[3px] focus:ring-primary/5` |
| `.input:disabled` | `disabled:bg-gray-50 disabled:text-gray-500 disabled:cursor-not-allowed` (add alongside .input) |
| `.btn-save` | `bg-primary text-white text-[13px] font-bold px-6 py-2.5 rounded-lg transition-all hover:bg-dark hover:-translate-y-px` |
| `.btn-cancel` | `bg-white border border-gray-200 text-gray-500 text-[13px] font-semibold px-5 py-2.5 rounded-lg transition-colors hover:bg-gray-50 hover:text-gray-800` |
| `.btn-delete-top` | `bg-primary text-white text-[12px] font-bold px-4 py-2 rounded-lg transition-colors` |
| `.breadcrumb` | `text-[12px] text-gray-400 mb-3 flex items-center gap-2` |
| `.breadcrumb a` | `text-gray-400 hover:text-primary transition-colors` |
| `.breadcrumb .active` | `text-gray-600 font-medium` |
| `.input-group` | `relative flex items-center` |
| `.input-prefix` | `absolute left-3.5 text-[12px] text-gray-400 font-semibold pointer-events-none` |
| `.input-with-prefix` | `pl-9` |
| `.select-wrapper` | `relative` (remove `::after` pseudo-element) |
| `.select-wrapper::after` | **DELETE** — replace with `<i class="ph ph-caret-down absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>` |
| `.select` | `appearance-none cursor-pointer pr-9` (add alongside `.input` classes) |

### 3.1 `admin/edit-product.php` (~29 lines CSS → 0)
### 3.2 `admin/create-product.php` (~26 lines CSS → 0)
### 3.3 `admin/create-batch.php` (~22 lines CSS → 0)
### 3.4 `admin/edit-customer.php` (~33 lines CSS → 0)
### 3.5 `admin/create-order.php` (~42 lines CSS → 0)
### 3.6 `admin/edit-order.php` (~54 lines CSS → 0)
### 3.7 `admin/view-order.php` (~26 lines CSS → 0)

All use shared form replacements above. Page-specific extras:

**edit-order.php & create-order.php** — order item table:
| Native CSS | Tailwind Replacement |
|------------|---------------------|
| `.table-items` | `w-full text-[12.5px]` + inline `style="border-collapse:separate;border-spacing:0 12px;"` (Tailwind can't do border-spacing) |
| `.table-items th` | `text-[11px] font-bold text-gray-400 uppercase text-left px-3.5 -mt-3` |
| `.table-items td` | `bg-white border-t border-b border-gray-100 py-4 px-3.5` |
| `.table-items td:first-child` | `border-l border-gray-100 rounded-l-lg` |
| `.table-items td:last-child` | `border-r border-gray-100 rounded-r-lg` |
| `.btn-add` | `inline-flex items-center gap-1.5 bg-white border border-gray-200 text-gray-600 text-[12px] font-semibold px-3.5 py-2 rounded-lg cursor-pointer transition-colors hover:bg-gray-50` |
| `.btn-delete-item` | `text-red-400 transition-colors bg-transparent border-0 cursor-pointer hover:text-red-500` |

**edit-order.php** — collapsible catatan:
| Native CSS | Tailwind Replacement |
|------------|---------------------|
| `.collapsible-header` | `cursor-pointer flex items-center justify-between` |
| `.collapsible-header i.caret` | `transition-transform duration-200` |
| `.collapsible-header.open i.caret` | `rotate-180` (toggled via JS) |

**edit-customer.php** — stats sidebar:
| Native CSS | Tailwind Replacement |
|------------|---------------------|
| `.edit-grid` | `grid grid-cols-[1fr_300px] gap-6 items-start` |
| `.stat-item` | `mb-4` |
| `.stat-label` | `text-[11px] font-semibold text-gray-500 mb-1` |
| `.stat-value` | `text-[13px] font-medium text-gray-800` |

**view-order.php** — items table + info display:
| Native CSS | Tailwind Replacement |
|------------|---------------------|
| `.items-table` | `w-full text-[12.5px] border-collapse` |
| `.items-table th` | `text-[11px] font-bold text-gray-400 uppercase text-left px-3.5 py-2.5 border-b border-gray-100` |
| `.items-table td` | `px-3.5 py-3 border-b border-gray-50 text-gray-700 last:border-b-0` |
| `.info-row` | `flex items-start py-2.5 border-b border-gray-50 last:border-b-0` |
| `.info-label` | `w-[140px] text-[12px] font-semibold text-gray-400 flex-shrink-0` |
| `.info-value` | `text-[13px] text-gray-800 font-medium` |
| `.status-badge` | `inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[12px] font-semibold border` |
| `.btn-edit` | `bg-primary text-white text-[12px] font-bold px-4 py-2 rounded-lg transition-colors hover:bg-dark inline-flex items-center gap-1` |
| `.btn-secondary` | `bg-white border border-gray-200 text-gray-500 text-[12px] font-semibold px-4 py-2 rounded-lg transition-colors hover:bg-gray-50 hover:text-gray-800 inline-flex items-center gap-1` |

---

## Phase 4 — Admin Dashboard + Special Pages (3 files)

### 4.1 `admin/dashboard.php` (~28 lines CSS → 0)

| Native CSS | Tailwind Replacement |
|------------|---------------------|
| `.stat-card` | `bg-white border border-gray-100 rounded-xl p-5 relative overflow-hidden` |
| `.badge-processing` | `bg-blue-50 text-blue-600 border border-blue-200` |
| `.badge-pending` | `bg-amber-50 text-amber-600 border border-amber-200` |
| `.badge-ready` | `bg-emerald-50 text-emerald-600 border border-emerald-200` |
| `.badge-pickedup` | `bg-gray-50 text-gray-500 border border-gray-200` |
| `.status-badge` | `inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold` |
| `.dot-status` | `w-1.5 h-1.5 rounded-full bg-current flex-shrink-0` |
| `.section-card` | `bg-white border border-gray-100 rounded-xl overflow-hidden mb-5` |
| `.section-head` | `px-4.5 py-3.5 border-b border-gray-50 text-[13px] font-bold text-navy` |

### 4.2 `admin/pengaturan.php` (~24 lines CSS → 0)

Same list page shared replacements. Rename inconsistent classes:
- `.table-container` → same as `.table-wrap` Tailwind
- `.search-input` → same as `.mini-search` Tailwind
- `.checkbox-custom` → same as `.cb` Tailwind
- `.btn-new` → same as `.btn-primary` Tailwind

Extra classes:

| Native CSS | Tailwind Replacement |
|------------|---------------------|
| `.breadcrumb-item` | `text-[11px] text-gray-400` |
| `.breadcrumb-item.active` | `text-gray-800 font-medium` |
| `.role-badge` | `inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold border border-gray-100` |

### 4.3 `admin/activity-log.php` (~24 lines CSS → 0)

Same list page shared replacements. Extra:

| Native CSS | Tailwind Replacement |
|------------|---------------------|
| `.badge-action` | `inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold` |
| `.badge-target` | `text-[10px] text-gray-400 font-mono` |
| `.pagination-btn` | `w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-[12px] text-gray-400 transition-colors hover:bg-gray-50` |

---

## Phase 5 — Customer Pages (4 files, ~130 lines to remove)

### 5.1 `customer/preorder.php` (~46 lines CSS → ~5 lines)

Most classes convert to Tailwind. Exceptions that need small `<style>` block:

```css
/* KEEP — JS-toggled accordion animation */
#catatan-body { max-height: 0; overflow: hidden; transition: max-height 0.25s ease; }
#catatan-body.open { max-height: 200px; }
#catatan-caret { transition: transform 0.2s ease; }
#catatan-caret.open { transform: rotate(180deg); }
/* KEEP — JS-toggled selected state */
.product-row.selected { outline: 2px solid #E02424; outline-offset: -2px; background-color: #fef2f2; }
```

Tailwind conversions:

| Native CSS | Tailwind Replacement |
|------------|---------------------|
| `.form-select` | Same as admin `.input` + `appearance-none cursor-pointer pr-9` |
| `.form-input` | Same as admin `.input` |
| `.section-card` | `bg-white border border-gray-100 rounded-xl overflow-hidden` |
| `.product-row` | `flex items-center gap-3 px-4 py-3 border border-gray-100 rounded-lg cursor-pointer transition-colors hover:bg-gray-50` |
| `.icon-btn` | `w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-600 transition-colors bg-transparent border-0 cursor-pointer` |
| `.icon-btn.danger:hover` | Add `hover:text-red-500` alongside `.icon-btn` classes |
| `.alert-error` | `bg-red-50 border border-red-200 text-red-600 text-[12px] rounded-lg px-4 py-3` |
| `.badge-blue` | `bg-blue-50 text-blue-600 border border-blue-200 px-2 py-0.5 rounded-full text-[10px] font-semibold` |
| `.badge-amber` | `bg-amber-50 text-amber-600 border border-amber-200 px-2 py-0.5 rounded-full text-[10px] font-semibold` |
| `.badge-green` | `bg-emerald-50 text-emerald-600 border border-emerald-200 px-2 py-0.5 rounded-full text-[10px] font-semibold` |
| `.badge-gray` | `bg-gray-50 text-gray-500 border border-gray-200 px-2 py-0.5 rounded-full text-[10px] font-semibold` |

### 5.2 `customer/orders.php` (~27 lines CSS → 0)

| Native CSS | Tailwind Replacement |
|------------|---------------------|
| `.table-container` | `bg-white border border-gray-100 rounded-xl overflow-visible` |
| `.search-input` | `border border-gray-200 rounded-lg py-1.5 pl-[30px] pr-3 text-[12px] outline-none bg-gray-50 w-[200px] transition-colors focus:border-primary focus:bg-white` |
| `.page-select` | `border border-gray-200 rounded-md px-2 py-1 text-[12px] outline-none bg-white appearance-none cursor-pointer` |
| `.action-link` | `inline-flex items-center gap-1 text-[12px] text-gray-400 hover:text-primary transition-colors` |
| `.alert-error` | Same as above |
| `.badge-*` | Same as preorder |

### 5.3 `customer/edit-order.php` (~24 lines CSS → ~5 lines)

Same exceptions as preorder (accordion + selected state). Tailwind conversions same as preorder.

### 5.4 `customer/profile.php` (~35 lines CSS → 0)

| Native CSS | Tailwind Replacement |
|------------|---------------------|
| `.form-card` | `bg-white border border-gray-100 rounded-xl overflow-hidden` |
| `.input-group` | `relative` |
| `.label-text` | `text-[12px] font-semibold text-gray-600 mb-1.5 block` |
| `.input-box` | `w-full border border-gray-200 rounded-lg px-3.5 py-2.5 text-[13px] text-gray-800 bg-white transition-all outline-none focus:border-primary focus:ring-[3px] focus:ring-primary/5 disabled:bg-gray-50 disabled:text-gray-500 disabled:cursor-not-allowed` |
| `.input-box.no-icon` | Same as `.input-box` without `pl-12` (conditionally add `pl-12` in PHP when icon exists) |
| `.alert-box` | `bg-amber-50 border border-amber-200 text-amber-600 text-[12px] rounded-lg px-4 py-3 flex items-center gap-2` |
| `.btn-submit` | `bg-primary text-white text-[13px] font-bold px-6 py-2.5 rounded-lg transition-all hover:bg-dark` |

---

## Inline Style Fixes (all files)

Replace these inline styles with Tailwind classes:
- `style="color:#E02424"` → `text-primary`
- `style="color:<?php echo $wc;?>"` → Keep only for truly dynamic color values (e.g., Chart.js sparkline colors)
- `style="font-size:16px;"` → `text-[16px]`
- `style="padding-right: 42px;"` → `pr-[42px]`
- `style="font-size:13px; flex-shrink:0;"` → `text-[13px] shrink-0`

Keep these inline styles (acceptable):
- `style="height:32px;"` — Chart.js canvas container heights
- `style="border-collapse:separate;border-spacing:0 12px;"` — order item tables (Tailwind can't do border-spacing)

## Bugs Found During Verification

### `admin/customers.php` — `overflow:hidden` on `.table-wrap`
Line 41 has `overflow:hidden` which breaks dropdown menus. Must use `overflow-visible` (same fix applied to other list pages).

---

## Verification Checklist

After each file:
- [ ] `<style>` block fully removed
- [ ] Page renders identical to before
- [ ] No broken layout or missing styles
- [ ] Responsive still works (sidebar collapse, mobile)
- [ ] Hover/focus states still function
- [ ] Dropdowns still work (overflow:visible on table containers!)

---

## Notes

- `includes/header-admin.php` — KEEP the 3-line `<style>` for accordion animation (`max-height` transition). Tailwind can't do this.
- `views/pdf/order-report.php` — KEEP all CSS. This is a PDF template, DomPDF requires inline CSS.
- Dynamic `style=""` for chart canvas heights (`height:32px`) is acceptable — keep those.
