# Phase 8: UI Widgets & Components - Copilot Prompt for Next PR

## Context

This document provides a comprehensive prompt for implementing Phase 8 (UI Widgets & Components) in a future PR. This phase was intentionally deferred to focus on core backend functionality first.

## Copilot Prompt

```
@copilot 

Please implement Phase 8: UI Widgets & Components Migration for the Laravel Invoice application.

## Objective

Migrate the UI components from the Yii3 invoice application (src/Widget/*) to Laravel 12, following established architecture patterns and maintaining a professional, modern UI.

## Requirements

### 1. Component Architecture

Create reusable UI components using Laravel Blade components:

- Location: `resources/views/components/`
- Follow Laravel Blade component conventions
- Support slots for flexible content
- Include proper escaping and security
- Apply Tailwind CSS for styling (or Bootstrap if preferred)
- Ensure mobile responsiveness

### 2. Components to Create

#### Form Components
- `<x-forms.text-input>` - Text input with label and validation
- `<x-forms.select>` - Dropdown select with options
- `<x-forms.textarea>` - Textarea with label
- `<x-forms.checkbox>` - Checkbox with label
- `<x-forms.radio>` - Radio button group
- `<x-forms.date-picker>` - Date selection input
- `<x-forms.currency-input>` - Currency input with formatting
- `<x-forms.tax-input>` - Tax rate input with percentage

#### Button Components
- `<x-buttons.primary>` - Primary action button
- `<x-buttons.secondary>` - Secondary action button
- `<x-buttons.danger>` - Destructive action button
- `<x-buttons.link>` - Link-styled button
- `<x-buttons.icon>` - Icon-only button

#### Table Components
- `<x-table>` - Responsive data table
- `<x-table.header>` - Table header with sorting
- `<x-table.row>` - Table row
- `<x-table.cell>` - Table cell
- `<x-table.actions>` - Action buttons column
- `<x-table.pagination>` - Pagination controls

#### Card Components
- `<x-card>` - Generic card container
- `<x-card.header>` - Card header section
- `<x-card.body>` - Card body section
- `<x-card.footer>` - Card footer section
- `<x-stats-card>` - Statistics display card
- `<x-info-card>` - Information display card

#### Dashboard Widgets
- `<x-widgets.revenue-chart>` - Revenue visualization
- `<x-widgets.invoice-summary>` - Invoice statistics
- `<x-widgets.recent-activity>` - Activity feed
- `<x-widgets.top-clients>` - Top clients list
- `<x-widgets.payment-status>` - Payment status overview
- `<x-widgets.quick-actions>` - Quick action buttons

#### Navigation Components
- `<x-nav.sidebar>` - Main sidebar navigation
- `<x-nav.breadcrumb>` - Breadcrumb trail
- `<x-nav.tabs>` - Tab navigation
- `<x-nav.dropdown>` - Dropdown menu

#### Alert/Notification Components
- `<x-alert.success>` - Success message
- `<x-alert.error>` - Error message
- `<x-alert.warning>` - Warning message
- `<x-alert.info>` - Info message
- `<x-flash-message>` - Session flash messages

#### Modal Components
- `<x-modal>` - Generic modal dialog
- `<x-modal.confirm>` - Confirmation dialog
- `<x-modal.form>` - Form modal

#### Invoice-Specific Components
- `<x-invoice.header>` - Invoice header display
- `<x-invoice.items-table>` - Invoice items table
- `<x-invoice.totals>` - Invoice totals section
- `<x-invoice.status-badge>` - Status badge
- `<x-invoice.actions>` - Invoice action buttons

### 3. Dashboard Implementation

Create a comprehensive dashboard at `resources/views/dashboard.blade.php`:

**Features:**
- Revenue chart (last 12 months)
- Invoice summary (total, paid, pending, overdue)
- Recent invoices list (last 10)
- Quick action buttons (New Invoice, New Quote, New Client)
- Top 5 clients by revenue
- Payment status pie chart
- Recent activity feed
- Responsive grid layout (3 columns desktop, 1 column mobile)

### 4. Chart Integration

Add Chart.js for data visualization:

```bash
npm install chart.js
```

Create chart components:
- Revenue line chart
- Payment status pie chart
- Client distribution bar chart
- Invoice trend area chart

### 5. JavaScript/Alpine.js Integration

Use Alpine.js for interactive components:

```bash
npm install alpinejs
```

Interactive features:
- Dropdown menus
- Modal dialogs
- Form validation feedback
- Dynamic table filtering
- Sortable tables
- Toast notifications

### 6. CSS Framework

Use Tailwind CSS for styling:

**Ensure:**
- Consistent color scheme
- Professional typography
- Proper spacing and layout
- Mobile-first responsive design
- Dark mode support (optional)

### 7. Icon System

Integrate Heroicons or Font Awesome:

```bash
npm install @heroicons/vue
```

Use icons for:
- Navigation items
- Action buttons
- Status indicators
- Form fields

### 8. Component Testing

Create tests for all components:

**Location:** `tests/Feature/Components/`

**Test examples:**
```php
public function it_renders_text_input_component(): void
public function it_displays_validation_errors_on_text_input(): void
public function it_renders_table_with_data(): void
public function it_sorts_table_by_column(): void
public function it_paginates_table_results(): void
public function it_renders_dashboard_widgets(): void
public function it_displays_revenue_chart_with_data(): void
```

### 9. Accessibility

Ensure all components are accessible:
- Proper ARIA labels
- Keyboard navigation support
- Screen reader compatibility
- Focus management
- Sufficient color contrast

### 10. Documentation

Create component documentation:

**File:** `docs/UI_COMPONENTS.md`

Include:
- Component usage examples
- Available props and slots
- Styling customization options
- Accessibility guidelines
- Screenshots/examples

## Implementation Pattern

For each component, follow this structure:

```php
// resources/views/components/forms/text-input.blade.php
@props([
    'name',
    'label' => null,
    'value' => null,
    'error' => null,
    'required' => false,
    'placeholder' => '',
    'type' => 'text'
])

<div class="form-group">
    @if($label)
        <label for="{{ $name }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <input 
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->class([
            'form-input',
            'border-red-300' => $error
        ]) }}
        @if($required) required @endif
    />
    
    @if($error)
        <p class="text-red-600 text-sm mt-1">{{ $error }}</p>
    @endif
</div>
```

## File Structure

```
resources/
├── views/
│   ├── components/
│   │   ├── forms/
│   │   │   ├── text-input.blade.php
│   │   │   ├── select.blade.php
│   │   │   └── ...
│   │   ├── buttons/
│   │   │   ├── primary.blade.php
│   │   │   └── ...
│   │   ├── table/
│   │   │   ├── index.blade.php
│   │   │   └── ...
│   │   ├── widgets/
│   │   │   ├── revenue-chart.blade.php
│   │   │   └── ...
│   │   └── ...
│   ├── dashboard.blade.php
│   ├── layouts/
│   │   ├── app.blade.php
│   │   ├── navigation.blade.php
│   │   └── footer.blade.php
│   └── ...
├── js/
│   ├── app.js
│   ├── charts.js
│   └── components/
│       ├── modal.js
│       ├── dropdown.js
│       └── ...
└── css/
    ├── app.css
    └── components/
        ├── forms.css
        ├── tables.css
        └── ...
```

## Testing Requirements

- Minimum 20 component tests
- Test rendering with props
- Test validation display
- Test interactive behavior
- Test responsiveness
- Test accessibility features

## Success Criteria

✅ All components render correctly
✅ Components are reusable across views
✅ Mobile responsive on all screen sizes
✅ Consistent styling and behavior
✅ Accessibility standards met
✅ All tests passing
✅ Documentation complete
✅ Dashboard fully functional with live data

## Timeline

Estimated effort: 15-20 hours

**Part 1 (5-7h):** Form and button components + tests
**Part 2 (5-7h):** Table, card, and navigation components + tests
**Part 3 (5-6h):** Dashboard widgets, charts, and documentation

## Notes

- Follow established architecture patterns (SOLID, DRY)
- Use early return pattern in component logic
- Apply consistent naming conventions
- Maintain test coverage above 80%
- Use Blade components, not plain PHP includes
- Ensure all components work without JavaScript (progressive enhancement)
- Consider performance (lazy loading, pagination)
```

## Related Files to Reference

- `resources/views/` - Existing view structure
- `src/Widget/` (original Yii3) - Component reference
- `.junie/guidelines.md` - Architecture standards
- `.github/copilot-instructions.md` - Coding standards

## Dependencies to Add

```bash
# Frontend
npm install alpinejs chart.js @heroicons/vue

# If using Tailwind CSS (recommended)
npm install -D tailwindcss@latest postcss@latest autoprefixer@latest
npx tailwindcss init -p
```

## Preparation Steps Before Starting Phase 8

1. Complete Phase 5 (Payment Gateways)
2. Complete Phase 9 (Middleware & Utilities) 
3. Ensure all backend APIs are stable
4. Review existing Yii3 widgets for feature parity
5. Create design mockups (optional)
6. Set up frontend build pipeline

## Post-Implementation Checklist

- [ ] All components created and tested
- [ ] Dashboard fully functional
- [ ] Charts displaying real data
- [ ] Mobile responsive verified
- [ ] Accessibility audit passed
- [ ] Documentation complete
- [ ] Screenshot examples added
- [ ] Performance optimized
- [ ] Browser compatibility tested
- [ ] Code review completed

---

**Use this prompt when ready to implement Phase 8 in the next PR.**
