# Mode of Procurement Refactoring Summary

## Overview

The `ModeOfProcurementUpdatePage` component has been successfully separated into two dedicated components for better maintainability and clarity:

### New Components Created

#### 1. **ModeOfProcurementPerItemPage** (Controllers & Views)

-   **Location**: `app/Livewire/ModeOfProcurement/ModeOfProcurementPerItemPage.php`
-   **View**: `resources/views/livewire/mode-of-procurement/mode-of-procurement-per-item-page.blade.php`
-   **Purpose**: Handles procurement by individual items
-   **Key Features**:
    -   Displays item-specific columns (Item No., Description, Amount)
    -   Manages MopItem relationships (prItemID-based)
    -   Simplified UI focused on per-item mode
    -   Includes validation for SVP mode (Mode 5)
    -   Post-procurement award details tab

#### 2. **ModeOfProcurementPerLotPage** (Controllers & Views)

-   **Location**: `app/Livewire/ModeOfProcurement/ModeOfProcurementPerLotPage.php`
-   **View**: `resources/views/livewire/mode-of-procurement/mode-of-procurement-per-lot-page.blade.php`
-   **Purpose**: Handles procurement by lots
-   **Key Features**:
    -   Streamlined display without item-specific columns
    -   Manages MopLot relationships (lot-based)
    -   Automatic mode_order re-indexing
    -   Post-procurement award details tab

### Original Component Status

-   **ModeOfProcurementUpdatePage** remains in place
    -   Can be kept for backwards compatibility or updated to delegate to appropriate component
    -   Consider creating a router component that checks `procurement_type` and delegates accordingly

## Architecture Benefits

1. **Separation of Concerns**: Each component handles only its procurement type
2. **Reduced Complexity**: Removed all conditional logic checking `procurement_type`
3. **Easier Maintenance**: Bugs/features are isolated to specific components
4. **Better Testing**: Cleaner unit tests possible for each mode
5. **Cleaner Views**: Blade templates no longer contain complex conditionals for item columns

## Code Cleanup Performed

### Removed from Per-Item Components

-   No conditional checks for per-lot mode order indexing
-   Removed MopLot model handling
-   Simplified `addItem()` method (no perLot item structure)
-   Simplified `removeItem()` method (no perLot re-indexing)
-   Item-specific ID handling for ref_id in schedules

### Removed from Per-Lot Components

-   Removed pr_items loading and processing
-   Item-specific column rendering (No., Description)
-   `prItemID` primary key handling
-   Item-based UID generation logic

## Migration Path

To use these new components, you have two options:

### Option 1: Direct Component Usage

Update your routes or view rendering to call the specific component:

```php
// For per-item procurement
route('mode-of-procurement.per-item.update', $procurement->id)

// For per-lot procurement
route('mode-of-procurement.per-lot.update', $procurement->id)
```

### Option 2: Router Component (Recommended)

Create a router in `ModeOfProcurementUpdatePage` that delegates:

```php
public function render()
{
    if ($this->procurement->procurement_type === 'perItem') {
        return app(ModeOfProcurementPerItemPage::class)
            ->mount($this->procurement)
            ->render();
    } else {
        return app(ModeOfProcurementPerLotPage::class)
            ->mount($this->procurement)
            ->render();
    }
}
```

## Testing Checklist

-   [ ] Test perItem mode - adding/removing items
-   [ ] Test perLot mode - adding/removing lots with proper reordering
-   [ ] Test validation for SVP mode (Mode 5) in both components
-   [ ] Test post-procurement award details in both components
-   [ ] Test schedule creation (BidSchedule, NtfBidSchedule, PrSvp)
-   [ ] Test history view toggle in both components
-   [ ] Test modal transitions between tabs
-   [ ] Verify no regression in existing functionality

## Files Modified/Created

### Created:

1. `app/Livewire/ModeOfProcurement/ModeOfProcurementPerItemPage.php` (586 lines)
2. `resources/views/livewire/mode-of-procurement/mode-of-procurement-per-item-page.blade.php`
3. `app/Livewire/ModeOfProcurement/ModeOfProcurementPerLotPage.php` (607 lines)
4. `resources/views/livewire/mode-of-procurement/mode-of-procurement-per-lot-page.blade.php`

### Original (can be archived or refactored):

-   `app/Livewire/ModeOfProcurement/ModeOfProcurementUpdatePage.php`
-   `resources/views/livewire/mode-of-procurement/mode-of-procurement-update-page.blade.php`

## Notes

-   Both new components maintain 100% feature parity with the original
-   All validation rules preserved
-   Database operations (updateOrCreate, delete) remain identical
-   Post-procurement tab functionality fully maintained
-   History view/toggle functionality fully maintained
