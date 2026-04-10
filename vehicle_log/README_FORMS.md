# Form Validation and Modal Handling System

This document explains the architecture for form validation, "Sticky Forms" (data preservation), and modal error recovery in the Vehicle Log application.

## 1. Overview of the Flow

1.  **Submission**: User submits a form (e.g., Add Fuel).
2.  **Controller Validation**: The corresponding controller (e.g., `fuel_controller.php`) collects fields and runs validation.
3.  **Error Handling**:
    *   If validation fails, the controller sets a `$feedback` array with `type => 'error'`.
    *   The page reloads, and `renderFeedbackModal()` is called.
4.  **Recovery**:
    *   The error modal displays a "Continue Editing" button.
    *   Clicking this button re-opens the original modal and sets a `window.isErrorRecovery` flag.
    *   The form fields use `getStickyVal()` to repopulate from the failed `$_POST` data.

---

## 2. Centralized Validation Library (`validate.php`)

Validation logic is centralized in `includes/validate.php`. This follows the **DRY (Don't Repeat Yourself)** principle, ensuring that rules for fields like VINs, emails, or mileage are defined once and reused everywhere.

### Core Functions

1.  **`validateFields($fields)`**: Validates an associative array of data. It loops through each field, calls `validateField()`, and collects all errors into a single list.
2.  **`validateField($field, $value, $all)`**: Validates a single field by name using a `switch/case` structure.
    *   Returns a **string** if invalid.
    *   Returns **null** if valid.

### Cross-Field Validation
The `$all` parameter allows the library to perform "context-aware" validation. For example:
*   **Mileage Consistency**: Ensuring `vehicle_current_mileage` is not less than `vehicle_purchase_mileage`.
*   **Date Logic**: Ensuring `vehicle_year_purchased` is not earlier than the vehicle's manufacture year.

---

## 3. Controller Integration

Controllers (found in `/controller/`) act as the orchestrators. They collect data, run validation, and decide whether to proceed to the database.

### The Controller Pattern
Every "Save" action in a controller follows this sequence:
```php
function saveFuel(PDO $db, string $mode) {
    $fields = collectFuelFields();
    $errors = validateFields($fields); // Checks for empty fields, formats, etc.

    if (!empty($errors)) {
        global $feedback;
        $feedback = [
            'type'    => 'error',
            'title'   => 'Validation Error',
            'message' => implode('<br>', $errors),
        ];
        return; // Stops execution; page re-renders with $feedback
    }
    // Proceed to Model if valid...
}
```

---

## 3. Sticky Forms (Data Preservation)

To prevent users from losing work after an error, the `getStickyVal()` helper (in `functions.php`) retrieves values from the `$_POST` superglobal.

### Usage in Partials
Form fields in `view/partials/` are defined like this:
```php
<input type="text" name="vehicle_make" 
       value="<?= htmlspecialchars(getStickyVal(['add_vehicle', 'update_vehicle'], 'vehicle_make')) ?>">
```

*   **First Argument**: A string or array of "action" keys (the name of the submit button) to check in `$_POST`.
*   **Second Argument**: The field name to retrieve.

---

## 4. Modal Error Recovery

The system can automatically re-open the correct modal after a failed submission.

### Feedback Modal (`functions.php`)
The `renderFeedbackModal()` function checks the `$feedback` type. If it's an `'error'`, it adds a "Continue Editing" button.
It uses an `idMap` to determine which modal ID corresponds to which form action:
```php
$idMap = [
    'add_fuel'    => 'addFuelModal',
    'update_fuel' => 'editFuelModal',
    // ...
];
```

### JavaScript Recovery Flag
When "Continue Editing" is clicked:
1.  `window.isErrorRecovery` is set to `true`.
2.  The target modal is shown using Bootstrap's `.show()`.

### Edit Modal Protection
In "Edit" modals, the JavaScript normally populates fields from the table row's data attributes. To prevent this from overwriting the "Sticky" data after an error, the `show.bs.modal` listener checks the recovery flag:

```javascript
modal.addEventListener('show.bs.modal', function (e) {
    // If we are recovering from an error, do NOT overwrite fields with DB values.
    if (window.isErrorRecovery) {
        window.isErrorRecovery = false; // Reset flag
        return; 
    }
    // Normal population logic continues...
});
```

---

## 5. Summary of Key Files

| File | Purpose |
| :--- | :--- |
| `includes/functions.php` | Contains `getStickyVal()` and `renderFeedbackModal()`. |
| `includes/validate.php` | Contains logic for checking required fields and data formats. |
| `controller/*_controller.php` | Processes form submissions and triggers feedback. |
| `view/partials/_*_form.php` | Shared form fields using sticky value logic. |
| `view/edit_*_modal.php` | Logic to handle recovery flags in Edit modes. |
