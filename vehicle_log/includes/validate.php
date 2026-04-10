<?php

/**
 * =============================================================================
 * validate.php — Centralized Server-Side Validation Library
 * =============================================================================
 *
 * PURPOSE:
 *   This file provides a single place to define all validation rules for every
 *   form field in the application. Instead of duplicating validation logic
 *   across multiple controller files, every controller calls the same two
 *   functions defined here.
 *
 *   This follows the DRY principle (Don't Repeat Yourself): if a rule needs
 *   to change (e.g., the max VIN length), you change it in one place.
 *
 * HOW IT WORKS — TWO FUNCTIONS:
 *
 *   1. validateField($field, $value, $all)
 *      - Validates a SINGLE field by its name.
 *      - Uses a switch/case to look up the rule for that field name.
 *      - Returns a human-readable error string if invalid, or NULL if valid.
 *      - The optional $all parameter gives access to OTHER fields so cross-
 *        field rules can be checked (e.g. year_purchased <= vehicle_year).
 *
 *   2. validateFields($fields)
 *      - Validates an ARRAY of fields all at once.
 *      - Loops over the array and calls validateField() for each one.
 *      - Returns an array of all error messages (empty array = all valid).
 *
 * HOW TO USE IT IN A CONTROLLER:
 *
 *   Step 1 — Load the library (once per controller file):
 *     require_once __DIR__ . '/../includes/validate.php';
 *
 *   Step 2 — Collect POST data into a named array:
 *     $fields = [
 *         'vehicle_make'  => $_POST['vehicle_make']  ?? '',
 *         'vehicle_model' => $_POST['vehicle_model'] ?? '',
 *         'fuel_gallons'  => $_POST['fuel_gallons']  ?? '',
 *     ];
 *
 *   Step 3 — Run validation:
 *     $errors = validateFields($fields);
 *
 *   Step 4 — Stop if there are errors:
 *     if (!empty($errors)) {
 *         $feedback = [
 *             'type'    => 'error',
 *             'title'   => 'Please fix the following errors',
 *             'message' => implode(' ', $errors),
 *         ];
 *         return; // stop the controller — nothing is written to the database
 *     }
 *
 *   Step 5 — Safe to proceed with database INSERT or UPDATE.
 *
 * FIELD NAMING CONVENTION:
 *   Field names in the $fields array must exactly match the HTML form's
 *   `name` attribute AND the case label in the switch below.
 *   Example: <input name="vehicle_make"> → 'vehicle_make' → case 'vehicle_make'
 *
 * CROSS-FIELD VALIDATION:
 *   Some rules depend on the VALUE of another field. For example:
 *   - vehicle_year_purchased cannot be later than vehicle_year.
 *   - vehicle_current_mileage cannot be less than vehicle_purchase_mileage.
 *   These rules use the $all parameter inside validateField() to read the
 *   other field's value. This is why validateFields() passes the entire
 *   $fields array as the third argument to every validateField() call.
 *
 * OPTIONAL vs. REQUIRED FIELDS:
 *   - Required fields return an error if $value === '' (empty string).
 *   - Optional fields skip format checks when empty, so blank is always valid.
 *     They only run their format/range check if the user typed something.
 *
 * UNKNOWN FIELDS:
 *   If a field name is not listed in the switch, the default case returns null
 *   (no error). This means hidden form flags like 'add_vehicle' or 'update_fuel'
 *   can safely be included in the $fields array without causing errors.
 *
 * =============================================================================
 */


/**
 * Validate a set of POST fields all at once.
 *
 * Loops over every field in the array, calls validateField() for each,
 * and collects any error messages into a single flat array.
 *
 * The entire $fields array is passed as the third argument ($all) to every
 * individual validateField() call so that cross-field rules can see each
 * other's values.
 *
 * @param  array $fields  Associative array of [ field_name => value ]
 * @return array          List of human-readable error strings (empty = all valid)
 */
function validateFields(array $fields): array
{
    $errors = [];

    foreach ($fields as $field => $value) {
        // Pass the full $fields array as $all so cross-field rules can read
        // sibling values (e.g. comparing year_purchased to vehicle_year).
        $error = validateField($field, $value, $fields);
        if ($error !== null) {
            $errors[] = $error;
        }
    }

    return $errors;
}


/**
 * Validate a single field by name.
 *
 * This function uses a switch/case structure where each case matches a
 * specific HTML form field name. The validation logic for that field lives
 * entirely inside its case block.
 *
 * Why switch/case instead of if/else?
 *   - Each field is independent — switch makes them easy to find and edit.
 *   - Adding a new field rule is as simple as adding a new case block.
 *   - It reads almost like a lookup table: field name → rule set.
 *
 * Return convention:
 *   - Return a string to report an error (the string is shown to the user).
 *   - Return null to signal that the field is valid.
 *
 * @param  string $field   The field name (must match the HTML `name` attribute)
 * @param  mixed  $value   The submitted value from $_POST
 * @param  array  $all     The full set of submitted fields — used for cross-field rules
 * @return string|null     A human-readable error message, or null if valid
 */
function validateField(string $field, $value, array $all = []): ?string
{
    // Strip leading/trailing whitespace from every string value before checking.
    // This prevents " " (a space) from passing a required-field check.
    if (is_string($value)) {
        $value = trim($value);
    }

    switch ($field) {

        // ── LOGIN ──────────────────────────────────────────────────────────────

        case 'email':
            if ($value === '') {
                return 'Email address is required.';
            }
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                return 'Email address is not valid.';
            }
            return null;

        case 'password':
            if ($value === '') {
                return 'Password is required.';
            }
            return null;


        // ── VEHICLES ───────────────────────────────────────────────────────────

        case 'vehicle_type':
            if ($value === '') {
                return 'Vehicle type is required.';
            }
            if (strlen($value) > 50) {
                return 'Vehicle type must be 50 characters or fewer.';
            }
            return null;

        case 'vehicle_make':
            if ($value === '') {
                return 'Vehicle make is required.';
            }
            if (strlen($value) > 50) {
                return 'Vehicle make must be 50 characters or fewer.';
            }
            return null;

        case 'vehicle_model':
            if ($value === '') {
                return 'Vehicle model is required.';
            }
            if (strlen($value) > 50) {
                return 'Vehicle model must be 50 characters or fewer.';
            }
            return null;

        case 'vehicle_year':
            if ($value === '') {
                return 'Vehicle year is required.';
            }
            $year = (int) $value;
            if ($year < 1980 || $year > (int) date('Y')) {
                return 'Vehicle year must be between 1980 and ' . date('Y') . '.';
            }
            return null;

        case 'vehicle_year_purchased':
            if ($value === '') {
                return 'Year purchased is required.';
            }
            $year = (int) $value;
            if ($year < 1980 || $year > (int) date('Y')) {
                return 'Year purchased must be between 1980 and ' . date('Y') . '.';
            }
            // Cross-field: can't buy a car before it was made
            if (isset($all['vehicle_year']) && $all['vehicle_year'] !== '') {
                if ($year < (int) $all['vehicle_year']) {
                    return 'Year purchased cannot be earlier than the vehicle year (' . $all['vehicle_year'] . ').';
                }
            }
            return null;

        case 'vehicle_color':
            $allowed = ['Black', 'White', 'Purple', 'Green', 'Silver', 'Red', 'Blue', 'Other'];
            if ($value === '') {
                return 'Vehicle color is required.';
            }
            if (!in_array($value, $allowed, true)) {
                return 'Vehicle color must be one of: ' . implode(', ', $allowed) . '.';
            }
            return null;

        case 'vehicle_VIN':
            if ($value === '') {
                return 'VIN is required.';
            }
            // Standard VINs are exactly 17 alphanumeric characters (no I, O, Q)
            if (!preg_match('/^[A-HJ-NPR-Z0-9]{17}$/i', $value)) {
                return 'VIN must be exactly 17 characters (letters and numbers only, no I/O/Q).';
            }
            return null;

        case 'vehicle_license_tag':
            if ($value === '') {
                return 'License plate is required.';
            }
            if (strlen($value) > 10) {
                return 'License plate must be 10 characters or fewer.';
            }
            return null;

        case 'vehicle_license_state':
            $states = [
                'AL','AK', 'AZ','AR','CA','CO','CT','DE','DC','FL','GA','HI','ID','IL','IN','IA','KS','KY','LA','ME','MD','MA','MI','MN','MS','MO','MT','NE','NV','NH','NJ','NM','NY','NC','ND','OH','OK','OR','PA','RI','SC','SD','TN','TX','UT','VT','VA','WA','WV','WI','WY'
            ];
            if ($value === '') {
                return 'License state is required.';
            }
            if (!in_array(strtoupper($value), $states, true)) {
                return 'License state must be a valid two-letter US state abbreviation.';
            }
            return null;

        case 'vehicle_purchase_price':
            if ($value === '') {
                return 'Purchase price is required.';
            }
            if (!is_numeric($value) || (float) $value < 0) {
                return 'Purchase price must be a positive number.';
            }
            return null;

        case 'vehicle_purchase_mileage':
            if ($value === '') {
                return 'Purchase mileage is required.';
            }
            if (!preg_match('/^\d+$/', (string) $value)) {
                return 'Purchase mileage must be a whole number with no decimals.';
            }
            return null;

        case 'vehicle_current_mileage':
            if ($value === '') {
                return 'Current mileage is required.';
            }
            if (!preg_match('/^\d+$/', (string) $value)) {
                return 'Current mileage must be a whole number with no decimals.';
            }
            // Cross-field: current >= purchase mileage
            if (isset($all['vehicle_purchase_mileage']) && preg_match('/^\d+$/', (string) $all['vehicle_purchase_mileage'])) {
                if ((int) $value < (int) $all['vehicle_purchase_mileage']) {
                    return 'Current mileage cannot be less than purchase mileage.';
                }
            }
            return null;


        // ── FUEL ───────────────────────────────────────────────────────────────

        case 'fuel_date':
            if ($value === '') {
                return 'Fuel date is required.';
            }
            $d = DateTime::createFromFormat('Y-m-d', $value);
            if (!$d || $d->format('Y-m-d') !== $value) {
                return 'Fuel date must be a valid date (YYYY-MM-DD).';
            }
            if ($d > new DateTime('today')) {
                return 'Fuel date cannot be in the future.';
            }
            return null;

        case 'fuel_mileage':
            // Optional field — only validate if something was entered
            if ($value !== '' && !preg_match('/^\d+$/', (string) $value)) {
                return 'Odometer mileage must be a whole number with no decimals.';
            }
            return null;

        case 'fuel_payment_method':
            $allowed = ['', 'Cash', 'Credit', 'Debit', 'Fleet'];
            if (!in_array($value, $allowed, true)) {
                return 'Payment method must be Cash, Credit, Debit, or Fleet Card.';
            }
            return null;

        case 'fuel_gallons':
            if ($value === '') {
                return 'Gallons is required.';
            }
            if (!is_numeric($value) || (float) $value <= 0) {
                return 'Gallons must be a positive number.';
            }
            return null;

        case 'fuel_cost_per_gallon':
            if ($value === '') {
                return 'Cost per gallon is required.';
            }
            if (!is_numeric($value) || (float) $value <= 0) {
                return 'Cost per gallon must be a positive number.';
            }
            return null;

        case 'fuel_source':
            // Optional — just cap length
            if (strlen($value) > 100) {
                return 'Fuel source must be 100 characters or fewer.';
            }
            return null;

        case 'fuel_receipt_url':
            // Optional — validate format only when provided
            if ($value !== '' && !filter_var($value, FILTER_VALIDATE_URL)) {
                return 'Receipt URL must be a valid web address (e.g. https://...).';
            }
            return null;

        case 'fuel_notes':
            // Optional — just cap length
            if (strlen($value) > 1000) {
                return 'Fuel notes must be 1,000 characters or fewer.';
            }
            return null;


        // ── MAINTENANCE ────────────────────────────────────────────────────────

        case 'maintenance_type_id':
            if ($value === '' || (int) $value <= 0) {
                return 'Maintenance type is required.';
            }
            return null;

        case 'maintenance_date':
            if ($value === '') {
                return 'Maintenance date is required.';
            }
            $d = DateTime::createFromFormat('Y-m-d', $value);
            if (!$d || $d->format('Y-m-d') !== $value) {
                return 'Maintenance date must be a valid date (YYYY-MM-DD).';
            }
            return null;

        case 'maintenance_mileage':
            // Optional — validate only if provided
            if ($value !== '' && !preg_match('/^\d+$/', (string) $value)) {
                return 'Maintenance odometer mileage must be a whole number with no decimals.';
            }
            return null;

        case 'maintenance_cost':
            // Optional / auto-filled — validate only if provided
            if ($value !== '' && (!is_numeric($value) || (float) $value < 0)) {
                return 'Maintenance cost must be 0 or more.';
            }
            return null;

        case 'maintenance_status':
            $allowed = ['', 'Scheduled', 'In Progress', 'Completed'];
            if (!in_array($value, $allowed, true)) {
                return 'Maintenance status must be Scheduled, In Progress, or Completed.';
            }
            return null;

        case 'maintenance_description':
            // Optional — just cap length
            if (strlen($value) > 2000) {
                return 'Maintenance description must be 2,000 characters or fewer.';
            }
            return null;


        // ── MAINTENANCE TYPE ───────────────────────────────────────────────────

        case 'maintenance_code':
            // Optional
            if (strlen($value) > 20) {
                return 'Maintenance code must be 20 characters or fewer.';
            }
            return null;

        case 'maintenance_type':
            if ($value === '') {
                return 'Maintenance type name is required.';
            }
            if (strlen($value) > 100) {
                return 'Maintenance type name must be 100 characters or fewer.';
            }
            return null;

        case 'recommended_interval_miles':
            // Optional
            if ($value !== '' && !preg_match('/^\d+$/', (string) $value)) {
                return 'Recommended interval miles must be a whole number with no decimals.';
            }
            return null;

        case 'recommended_interval_days':
            // Optional
            if ($value !== '' && (!is_numeric($value) || (int) $value < 0)) {
                return 'Recommended interval days must be 0 or more.';
            }
            return null;

        case 'recommended_cost':
            // Optional
            if ($value !== '' && (!is_numeric($value) || (float) $value < 0)) {
                return 'Recommended cost must be 0 or more.';
            }
            return null;


        // ── VENDORS ────────────────────────────────────────────────────────────

        case 'vendor_name':
            if ($value === '') {
                return 'Vendor name is required.';
            }
            if (strlen($value) > 100) {
                return 'Vendor name must be 100 characters or fewer.';
            }
            return null;

        case 'vendor_address':
            // Optional
            if (strlen($value) > 200) {
                return 'Vendor address must be 200 characters or fewer.';
            }
            return null;

        case 'vendor_city':
            // Optional
            if (strlen($value) > 100) {
                return 'Vendor city must be 100 characters or fewer.';
            }
            return null;

        case 'vendor_state':
            // Optional, but if provided must be 2-letter abbreviation
            if ($value !== '' && !preg_match('/^[A-Za-z]{2}$/', $value)) {
                return 'Vendor state must be a two-letter abbreviation (e.g. OH).';
            }
            return null;

        case 'vendor_zip':
            // Optional — US ZIP: 5 digits, or ZIP+4
            if ($value !== '' && !preg_match('/^\d{5}(-\d{4})?$/', $value)) {
                return 'Zip code must be in 5-digit format (e.g. 44101) or ZIP+4 (e.g. 44101-0000).';
            }
            return null;

        case 'vendor_phone':
            // Optional — loose format check: 7–15 digits/symbols
            if ($value !== '' && !preg_match('/^[\d\s\-\(\)\+\.]{7,20}$/', $value)) {
                return 'Phone number format is not valid.';
            }
            return null;

        case 'vendor_email':
            // Optional
            if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                return 'Vendor email address is not valid.';
            }
            return null;


        // ── USERS ──────────────────────────────────────────────────────────────

        case 'first_name':
            if ($value === '') {
                return 'First name is required.';
            }
            if (strlen($value) > 50) {
                return 'First name must be 50 characters or fewer.';
            }
            return null;

        case 'last_name':
            if ($value === '') {
                return 'Last name is required.';
            }
            if (strlen($value) > 50) {
                return 'Last name must be 50 characters or fewer.';
            }
            return null;

        case 'user_password':
            // Required on add — optional on edit (blank = keep existing)
            // The controller must decide which rule applies; pass context via $all.
            // If $all['_require_password'] is set, treat as required.
            if (isset($all['_require_password']) && $all['_require_password']) {
                if ($value === '') {
                    return 'Password is required.';
                }
            }
            if ($value !== '' && strlen($value) < 8) {
                return 'Password must be at least 8 characters.';
            }
            return null;

        case 'user_role':
            $allowed = ['user', 'admin'];
            if ($value === '' || !in_array($value, $allowed, true)) {
                return 'User role must be "user" or "admin".';
            }
            return null;

        case 'is_active':
            // Accepts '0' or '1' (select) or checkbox presence
            if (!in_array((string) $value, ['0', '1'], true)) {
                return 'Active status must be 0 or 1.';
            }
            return null;


        // ── HIDDEN / ID FIELDS ─────────────────────────────────────────────────

        case 'vehicle_id':
        case 'fuel_id':
        case 'maintenance_id':
        case 'vendor_id':
        case 'user_id':
            // IDs are set by the system — just ensure they are positive integers
            if ($value !== '' && ((int) $value <= 0 || !ctype_digit((string) $value))) {
                return ucwords(str_replace('_', ' ', $field)) . ' is not valid.';
            }
            return null;


        // ── UNKNOWN FIELD ──────────────────────────────────────────────────────

        default:
            // Silently skip fields that are not in the switch
            // (e.g. hidden action flags like 'add_vehicle', 'update_fuel')
            return null;
    }
}

