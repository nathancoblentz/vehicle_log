<?php

require 'config.php';
$title = 'Lab 01-02 | Database Documentation';
include_once '../includes/head.php';
include_once '../includes/nav.php';
include_once '../includes/hero.php';
include_once 'includes/functions.php';

?>
<div class="container mt-4">

  <!-- HEADER CARD -->
  <div class="card mb-4 border-0 shadow-sm">
    <div class="card-header bg-primary text-white py-3">
      <span class="badge bg-light text-primary me-2">Lab 01-02</span>
      <h5 class="d-inline mb-0">Database Documentation</h5>
    </div>
    <div class="card-body">
      <p class="lead mb-2">Vehicle Log Database &mdash; Schema &amp; Design</p>
      <p class="text-muted mb-0">Complete documentation of the <code>vehicle_log</code> database schema, including table
        structures, relationships, key design considerations, and sample queries for reporting.</p>
    </div>
  </div>
  <ul>
    <li>Vehicle tracking including purchase and current mileage.</li>
    <li>Fuel tracking with cost, gallons, and notes.</li>
    <li>Maintenance tracking with recommended intervals.</li>
    <li>User assignments and roles.</li>
    <li>Dynamic reporting using calculated next maintenance due.</li>
  </ul>

  <h3>Key Design Considerations</h3>
  <ul>
    <li>Normalization: Separate tables for users, vehicles, maintenance, and fuel to reduce redundancy.</li>
    <li>Data Integrity: Foreign keys enforce relationships between tables.</li>
    <li>Flexibility: Recommended intervals stored separately to allow easy updates.</li>
    <li>Performance: Indexes on foreign keys for faster joins.</li>
    <li>Scalability: Designed to handle multiple users and vehicles efficiently.</li>
  </ul>

  <h3>Additional features</h3>
  <ul>
    <li>Foreign keys & InnoDB engine: Ensures data integrity across users, vehicles, maintenance, and fuel.</li>
    <li>Recommended intervals in maintenance_type: Avoids redundant storage of next due dates/mileage.</li>
    <li>Derived next due values: Calculated dynamically in queries for flexibility.</li>
    <li>Decimal data types: For gallons, mileage, and costs to allow precise calculations.</li>
    <li>Timestamps: _modified columns auto-update for auditing.</li>

  </ul>

  <div class="card mb-4 mt-4">
    <div class="card-header">Schema Diagram</div>
    <div class="card-body">
      <figure>

        <img src="img/schema.png" alt="Vehicle Log Schema Diagram" class="img-fluid mt-3">
      </figure>
      <figcaption>Vehicle Log Schema Diagram</figcaption>

    </div>
  </div>

  <!-- Users Table -->
  <div class="mb-4">
    <div class="card">
      <div class="card-header">Users Table</div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <thead class="table-dark">
              <tr>
                <th>Column</th>
                <th>Description</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>user_id</td>
                <td>Primary key</td>
              </tr>
              <tr>
                <td>first_name / last_name</td>
                <td>User name</td>
              </tr>
              <tr>
                <td>user_password</td>
                <td>Hashed password</td>
              </tr>
              <tr>
                <td>email</td>
                <td>Unique login email</td>
              </tr>
              <tr>
                <td>user_role</td>
                <td>Role: admin, user, etc.</td>
              </tr>
              <tr>
                <td>is_active</td>
                <td>1=active, 0=inactive</td>
              </tr>
              <tr>
                <td>date_created</td>
                <td>Timestamp when created</td>
              </tr>
              <tr>
                <td>date_lastlogin</td>
                <td>Last login timestamp</td>
              </tr>
              <tr>
                <td>date_modified</td>
                <td>Last modification timestamp</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Vehicles Table -->
  <div class="mb-4">
    <div class="card">
      <div class="card-header">Vehicles Table</div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <thead class="table-dark">
              <tr>
                <th>Column</th>
                <th>Description</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>vehicle_id</td>
                <td>Primary key</td>
              </tr>
              <tr>
                <td>vehicle_type</td>
                <td>Car, Truck, Motorcycle, etc.</td>
              </tr>
              <tr>
                <td>vehicle_make / model</td>
                <td>Manufacturer and model</td>
              </tr>
              <tr>
                <td>vehicle_year / vehicle_year_purchased</td>
                <td>Model year and purchase year</td>
              </tr>
              <tr>
                <td>vehicle_color</td>
                <td>Color</td>
              </tr>
              <tr>
                <td>vehicle_VIN</td>
                <td>Vehicle Identification Number</td>
              </tr>
              <tr>
                <td>vehicle_license_tag / state</td>
                <td>License info</td>
              </tr>
              <tr>
                <td>vehicle_purchase_price</td>
                <td>Purchase price</td>
              </tr>
              <tr>
                <td>vehicle_purchase_mileage / current_mileage</td>
                <td>Odometer readings</td>
              </tr>
              <tr>
                <td>assigned_user_id</td>
                <td>Foreign key to users</td>
              </tr>
              <tr>
                <td>is_active</td>
                <td>1=active, 0=inactive</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Maintenance Type Table -->
  <div class="mb-4">
    <div class="card">
      <div class="card-header">Maintenance Type Table</div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <thead class="table-dark">
              <tr>
                <th>Column</th>
                <th>Description</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>maintenance_id</td>
                <td>Primary key</td>
              </tr>
              <tr>
                <td>maintenance_code</td>
                <td>Short code like OIL01</td>
              </tr>
              <tr>
                <td>maintenance_type</td>
                <td>Name of maintenance</td>
              </tr>
              <tr>
                <td>maintenance_description</td>
                <td>Optional description</td>
              </tr>
              <tr>
                <td>recommended_interval_miles</td>
                <td>Miles between services</td>
              </tr>
              <tr>
                <td>recommended_interval_days</td>
                <td>Days between services</td>
              </tr>
              <tr>
                <td>is_active</td>
                <td>1=active, 0=inactive</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Maintenance Table -->
  <div class="mb-4">
    <div class="card">
      <div class="card-header">Maintenance Table</div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <thead class="table-dark">
              <tr>
                <th>Column</th>
                <th>Description</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>maintenance_id</td>
                <td>Primary key</td>
              </tr>
              <tr>
                <td>maintenance_type_id</td>
                <td>Foreign key to maintenance_type</td>
              </tr>
              <tr>
                <td>vehicle_id</td>
                <td>Foreign key to vehicles</td>
              </tr>
              <tr>
                <td>maintenance_vendor / address</td>
                <td>Vendor info</td>
              </tr>
              <tr>
                <td>maintenance_description</td>
                <td>Notes</td>
              </tr>
              <tr>
                <td>maintenance_cost</td>
                <td>Service cost</td>
              </tr>
              <tr>
                <td>maintenance_mileage</td>
                <td>Odometer reading</td>
              </tr>
              <tr>
                <td>maintenance_date</td>
                <td>Date of service</td>
              </tr>
              <tr>
                <td>maintenance_status</td>
                <td>completed / pending / overdue</td>
              </tr>
              <tr>
                <td>is_active</td>
                <td>1=active, 0=inactive</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Fuel Table -->
  <div class="mb-4">
    <div class="card">
      <div class="card-header">Fuel Table</div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <thead class="table-dark">
              <tr>
                <th>Column</th>
                <th>Description</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>fuel_id</td>
                <td>Primary key</td>
              </tr>
              <tr>
                <td>vehicle_id</td>
                <td>Foreign key to vehicles</td>
              </tr>
              <tr>
                <td>fuel_date</td>
                <td>Date of fuel purchase</td>
              </tr>
              <tr>
                <td>fuel_gallons</td>
                <td>Gallons filled</td>
              </tr>
              <tr>
                <td>fuel_cost_per_gallon</td>
                <td>Cost per gallon</td>
              </tr>
              <tr>
                <td>fuel_mileage</td>
                <td>Odometer reading</td>
              </tr>
              <tr>
                <td>fuel_payment_method</td>
                <td>Optional payment type</td>
              </tr>
              <tr>
                <td>fuel_notes</td>
                <td>Optional notes</td>
              </tr>
              <tr>
                <td>fuel_receipt_url</td>
                <td>Optional receipt image</td>
              </tr>
              <tr>
                <td>is_active</td>
                <td>1=active, 0=inactive</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>


  <!-- Schema Diagram -->


  <!-- Sample Queries -->
  <div class="card mb-4">
    <div class="card-header">Sample Queries</div>
    <div class="card-body">

      <h5>Next Maintenance Due</h5>
      <pre><code>
SELECT 
    v.vehicle_id,
    v.vehicle_make,
    v.vehicle_model,
    m.maintenance_id,
    mt.maintenance_type,
    m.maintenance_mileage + mt.recommended_interval_miles AS next_due_mileage,
    DATE_ADD(m.maintenance_date, INTERVAL mt.recommended_interval_days DAY) AS next_due_date
FROM maintenance m
JOIN maintenance_type mt ON m.maintenance_type_id = mt.maintenance_id
JOIN vehicles v ON m.vehicle_id = v.vehicle_id
ORDER BY next_due_date ASC;
      </code></pre>

      <h5>MPG per Fuel Entry</h5>
      <pre><code>
SELECT
    v.vehicle_id,
    v.vehicle_make,
    v.vehicle_model,
    f.fuel_date,
    f.fuel_mileage,
    f.fuel_gallons,
    LAG(f.fuel_mileage) OVER (PARTITION BY v.vehicle_id ORDER BY f.fuel_date) AS previous_mileage,
    (f.fuel_mileage - LAG(f.fuel_mileage) OVER (PARTITION BY v.vehicle_id ORDER BY f.fuel_date)) / f.fuel_gallons AS mpg
FROM fuel f
JOIN vehicles v ON f.vehicle_id = v.vehicle_id
ORDER BY v.vehicle_id, f.fuel_date;
      </code></pre>

      <h5>Total Fuel Cost per Month</h5>
      <pre><code>
SELECT 
    v.vehicle_id,
    v.vehicle_make,
    v.vehicle_model,
    DATE_FORMAT(f.fuel_date, '%Y-%m') AS month,
    SUM(f.fuel_cost_per_gallon * f.fuel_gallons) AS total_fuel_cost
FROM fuel f
JOIN vehicles v ON f.vehicle_id = v.vehicle_id
GROUP BY v.vehicle_id, month
ORDER BY month, v.vehicle_id;
      </code></pre>

    </div>
  </div>

</div>

<?php include_once('../includes/footer.php'); ?>