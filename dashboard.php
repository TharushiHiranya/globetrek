<?php
require_once "includes/connection.php";
session_start();

// Make sure the user is actually logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Grab the user details to use throughout the page
$user_id = $_SESSION['user_id'];
$role    = $_SESSION['role'];
$name    = $_SESSION['name'];
$msg     = '';

// Check if a staff member or admin is updating a booking status
if (isset($_POST['update_booking'])) {
    $b_id       = intval($_POST['booking_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);
    mysqli_query($conn, "UPDATE bookings SET status='$new_status' WHERE id=$b_id");
}

// Check if the admin is deleting a customer query
if (isset($_POST['remove_query']) && $role === 'admin') {
    $q_id = intval($_POST['query_id']);
    mysqli_query($conn, "DELETE FROM queries WHERE id=$q_id");
    $msg = "<p class='success'>Query removed successfully.</p>";
}

// Check if the admin is deleting a travel package entirely
if (isset($_POST['remove_package']) && $role === 'admin') {
    $p_id = intval($_POST['package_id']);
    
    // Find the image file for this package so we can delete it too
    $res = mysqli_query($conn, "SELECT image_url FROM packages WHERE id=$p_id");
    if ($row = mysqli_fetch_assoc($res)) {
        if (!empty($row['image_url']) && file_exists($row['image_url'])) {
            // Delete the image file from the server
            unlink($row['image_url']);
        }
    }
    
    // Remove the actual package record from the database
    mysqli_query($conn, "DELETE FROM packages WHERE id=$p_id");
    $msg = "<p class='success'>Package removed successfully.</p>";
}

// Check if the admin is adding a new staff or admin account
if (isset($_POST['add_employee']) && $role === 'admin') {
    
    // Clean up the input data
    $emp_name = mysqli_real_escape_string($conn, $_POST['emp_name']);
    $emp_email = mysqli_real_escape_string($conn, $_POST['emp_email']);
    $emp_pass = $_POST['emp_password'];
    $emp_role = mysqli_real_escape_string($conn, $_POST['emp_role']);
    
    // Ensure the email is not already taken by another user
    $check = mysqli_query($conn, "SELECT id FROM users WHERE email = '$emp_email'");
    if (mysqli_num_rows($check) > 0) {
        $msg = "<p class='error'>Email already exists.</p>";
    } else {
        // Create the new employee record
        mysqli_query($conn, "INSERT INTO users (name, email, password, role) VALUES ('$emp_name', '$emp_email', '$emp_pass', '$emp_role')");
        $msg = "<p class='success'>Employee added successfully.</p>";
    }
}

// Check if the admin is removing an existing employee
if (isset($_POST['remove_employee']) && $role === 'admin') {
    $emp_id = intval($_POST['employee_id']);
    
    // Stop the admin from accidentally deleting their own account
    if ($emp_id !== $user_id) {
        mysqli_query($conn, "DELETE FROM users WHERE id=$emp_id");
        $msg = "<p class='success'>Employee removed successfully.</p>";
    } else {
        $msg = "<p class='error'>You cannot remove yourself.</p>";
    }
}

// Check if a staff member or admin is adding a new travel package
if (isset($_POST['add_package']) && ($role === 'admin' || $role === 'staff')) {
    
    // Clean up the text inputs
    $title       = mysqli_real_escape_string($conn, $_POST['title']);
    $destination = mysqli_real_escape_string($conn, $_POST['destination']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price       = floatval($_POST['price']);
    $image_url   = '';

    // Check if the user uploaded an image file for the package
    if (!empty($_FILES['image']['name'])) {
        $upload_dir = 'images/destinations/';
        
        // Strip out any weird characters from the file name to keep it safe
        $safe_name  = preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($_FILES['image']['name']));
        $target     = $upload_dir . $safe_name;

        // Make sure it is actually an image file
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (in_array($_FILES['image']['type'], $allowed)) {
            
            // Try to move the uploaded file into our destinations folder
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $image_url = $target;
            } else {
                $msg = "<p class='error'>Image upload failed. Check folder permissions.</p>";
            }
        } else {
            $msg = "<p class='error'>Only JPG, PNG, WebP, or GIF images are allowed.</p>";
        }
    }

    // If there were no errors so far, save the package to the database
    if (empty($msg)) {
        $sql = "INSERT INTO packages (title, destination, description, price, image_url)
                VALUES ('$title', '$destination', '$description', $price, '$image_url')";
        if (mysqli_query($conn, $sql)) {
            $msg = "<p class='success'>Package added successfully.</p>";
        } else {
            $msg = "<p class='error'>Error: " . mysqli_error($conn) . "</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - GlobeTrek Adventures</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="shortcut icon" href="favicon.ico">
</head>
<body class="page-body">
    <?php include "includes/header.php"; ?>
    <div class="page-container">
        <h2 class="page-title">Welcome, <?php echo htmlspecialchars($name); ?> (<?php echo ucfirst(htmlspecialchars($role)); ?>)</h2>

        <?php echo $msg; ?>

        <!-- Customer area -->
        <?php if ($role === 'customer') { ?>
            <div class="dashboard-header-flex">
                <h3 class="section-title-sm">Your bookings</h3>
                <a href="packages.php" class="btn btn-register">New booking</a>
            </div>
            
            <?php
            // Look up all the bookings for the currently logged-in customer
            $sql    = "SELECT b.id, p.title, b.status, b.booking_date
                       FROM bookings b
                       JOIN packages p ON b.package_id = p.id
                       WHERE b.user_id = $user_id
                       ORDER BY b.id DESC";
            
            // Run the query to fetch the bookings
            $result = mysqli_query($conn, $sql);
            
            // If they have any bookings, display them in a table
            if (mysqli_num_rows($result) > 0) {
                echo "<table><tr><th>Booking ID</th><th>Package</th><th>Status</th><th>Date</th><th>Action</th></tr>";
                
                // Loop through each booking row
                while ($row = mysqli_fetch_assoc($result)) {
                    
                    // Stop them from clicking cancel again if the booking is already cancelled
                    $disabled = ($row['status'] === 'cancelled') ? "disabled" : "";

                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['title']}</td>
                            <td>{$row['status']}</td>
                            <td>{$row['booking_date']}</td>
                            <td>
                                <form method='POST' class='m-0'>
                                    <input type='hidden' name='booking_id' value='{$row['id']}'>
                                    <input type='hidden' name='status' value='cancelled'>
                                    <button type='submit' name='update_booking' class='btn btn-outline btn-sm' $disabled>Cancel</button>
                                </form>
                            </td>
                          </tr>";
                }
                echo "</table>";
            } else {
                echo "<p class='text-muted'>You have no bookings yet. <a href='packages.php' class='text-link'>Browse packages</a></p>";
            }
            ?>

        <!-- Admin and staff area -->
        <?php } elseif ($role === 'staff' || $role === 'admin') { ?>

            <!-- New package form -->
            <h3 class="section-heading">Add new package</h3>
            
            <div class="form-card-lg">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="pkg-title">Package title</label>
                            <input type="text" id="pkg-title" name="title" placeholder="e.g. Santorini Escape" required>
                        </div>
                        <div class="form-group">
                            <label for="pkg-destination">Destination</label>
                            <input type="text" id="pkg-destination" name="destination" placeholder="e.g. Greece" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="pkg-description">Description</label>
                        <textarea id="pkg-description" name="description" rows="3" placeholder="Describe the trip..." required></textarea>
                    </div>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="pkg-price">Price (USD)</label>
                            <input type="number" id="pkg-price" name="price" placeholder="e.g. 1200" step="0.01" min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="pkg-image">Destination image</label>
                            <input type="file" id="pkg-image" name="image" accept="image/*" class="file-input">
                            <small class="text-muted-sm">Upload a JPG, PNG, or WebP image.</small>
                        </div>
                    </div>
                    <button type="submit" name="add_package" class="btn btn-register mt-8">Add package</button>
                </form>
            </div>

            <!-- Manage bookings -->
            <h3 class="section-heading">Manage bookings</h3>
            
            <?php
            // Look up every booking in the entire system for the admin or staff to manage
            $sql    = "SELECT b.id, u.name as customer, p.title, b.status, b.booking_date
                       FROM bookings b
                       JOIN users u ON b.user_id = u.id
                       JOIN packages p ON b.package_id = p.id
                       ORDER BY b.id DESC";
            
            // Run the query to pull all bookings
            $result = mysqli_query($conn, $sql);
            
            // If any bookings exist, display them in a table
            if (mysqli_num_rows($result) > 0) {
                echo "<table><tr><th>ID</th><th>Customer</th><th>Package</th><th>Status</th><th>Date</th><th>Action</th></tr>";
                
                // Loop through each booking row
                while ($row = mysqli_fetch_assoc($result)) {
                    
                    // Check the current status so we can pre-select it in the dropdown menu
                    $selected_pending = ($row['status'] === 'pending') ? 'selected' : '';
                    $selected_confirmed = ($row['status'] === 'confirmed') ? 'selected' : '';
                    $selected_cancelled = ($row['status'] === 'cancelled') ? 'selected' : '';
                    
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['customer']}</td>
                            <td>{$row['title']}</td>
                            <td>{$row['status']}</td>
                            <td>{$row['booking_date']}</td>
                            <td>
                                <form method='POST' class='flex-gap-10'>
                                    <input type='hidden' name='booking_id' value='{$row['id']}'>
                                    <select name='status' class='form-select'>
                                        <option value='pending' $selected_pending>Pending</option>
                                        <option value='confirmed' $selected_confirmed>Confirmed</option>
                                        <option value='cancelled' $selected_cancelled>Cancelled</option>
                                    </select>
                                    <button type='submit' name='update_booking' class='btn btn-register btn-sm'>Update</button>
                                </form>
                            </td>
                          </tr>";
                }
                echo "</table>";
            } else {
                echo "<p class='text-muted'>No bookings found.</p>";
            }
            ?>

            <!-- Messages from customers -->
            <h3 class="section-heading-mt">Customer queries</h3>
            
            <?php
            // Get all contact queries
            $sql    = "SELECT * FROM queries ORDER BY id DESC";
            $result = mysqli_query($conn, $sql);
            
            if (mysqli_num_rows($result) > 0) {
                // Display queries table
                echo "<table><tr><th>ID</th><th>Name</th><th>Email</th><th>Message</th><th>Date</th>";
                if ($role === 'admin') {
                    echo "<th>Action</th>";
                }
                echo "</tr>";
                
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['name']}</td>
                            <td>{$row['email']}</td>
                            <td>{$row['message']}</td>
                            <td>{$row['created_at']}</td>";
                            
                    // Allow admin to delete queries
                    if ($role === 'admin') {
                        echo "<td>
                                <form method='POST' class='m-0'>
                                    <input type='hidden' name='query_id' value='{$row['id']}'>
                                    <button type='submit' name='remove_query' class='btn btn-outline btn-sm btn-danger-outline' onclick=\"return confirm('Remove this query?');\">Remove</button>
                                </form>
                              </td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p class='text-muted'>No queries found.</p>";
            }
            ?>

            <!-- Admin Only: Manage Packages (Remove) -->
            <?php if ($role === 'admin') { ?>
            <h3 class="section-heading-mt">Existing packages</h3>
            <?php
            // Get all packages
            $sql_pkgs = "SELECT * FROM packages ORDER BY id DESC";
            $res_pkgs = mysqli_query($conn, $sql_pkgs);
            
            if (mysqli_num_rows($res_pkgs) > 0) {
                // Show packages
                echo "<table><tr><th>ID</th><th>Title</th><th>Destination</th><th>Price</th><th>Featured</th><th>Action</th></tr>";
                while ($row = mysqli_fetch_assoc($res_pkgs)) {
                    $checked = ($row['is_featured'] == 1) ? 'checked' : '';
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['title']}</td>
                            <td>{$row['destination']}</td>
                            <td>\${$row['price']}</td>
                            <td>
                                <input type='checkbox' class='feature-toggle' data-id='{$row['id']}' $checked style='cursor: pointer; width: 18px; height: 18px;'>
                            </td>
                            <td>
                                <form method='POST' class='m-0'>
                                    <input type='hidden' name='package_id' value='{$row['id']}'>
                                    <button type='submit' name='remove_package' class='btn btn-outline btn-sm btn-danger-outline' onclick=\"return confirm('Are you sure you want to remove this package?');\">Remove</button>
                                </form>
                            </td>
                          </tr>";
                }
                echo "</table>";
            } else {
                echo "<p class='text-muted'>No packages found.</p>";
            }
            ?>

            <!-- Admin Only: Manage Employees -->
            <h3 class="section-heading-mt">Manage employees</h3>
            
            <div class="form-card-mb">
                <form method="POST">
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="emp-name">Employee name</label>
                            <input type="text" id="emp-name" name="emp_name" placeholder="Name" required>
                        </div>
                        <div class="form-group">
                            <label for="emp-email">Email</label>
                            <input type="email" id="emp-email" name="emp_email" placeholder="Email" required>
                        </div>
                        <div class="form-group">
                            <label for="emp-password">Password</label>
                            <input type="text" id="emp-password" name="emp_password" placeholder="Password" required>
                        </div>
                        <div class="form-group">
                            <label for="emp-role">Role</label>
                            <select id="emp-role" name="emp_role" class="form-select-lg">
                                <option value="staff">Staff</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" name="add_employee" class="btn btn-register mt-8">Add employee</button>
                </form>
            </div>

            <?php
            // Get all staff and admin accounts
            $sql_emps = "SELECT * FROM users WHERE role IN ('staff', 'admin') ORDER BY id DESC";
            $res_emps = mysqli_query($conn, $sql_emps);
            
            if (mysqli_num_rows($res_emps) > 0) {
                // Show employees table
                echo "<table><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Action</th></tr>";
                
                while ($row = mysqli_fetch_assoc($res_emps)) {
                    // Prevent user from removing themselves
                    $disabled = ($row['id'] == $user_id) ? "disabled" : "";
                    
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['name']}</td>
                            <td>{$row['email']}</td>
                            <td>{$row['role']}</td>
                            <td>
                                <form method='POST' class='m-0'>
                                    <input type='hidden' name='employee_id' value='{$row['id']}'>
                                    <button type='submit' name='remove_employee' class='btn btn-outline btn-sm btn-danger-outline' $disabled onclick=\"return confirm('Remove this employee?');\">Remove</button>
                                </form>
                            </td>
                          </tr>";
                }
                echo "</table>";
            }
            ?>
            <?php } ?>

        <?php } ?>
    </div>
    <?php include "includes/footer.php"; ?>
    
    <script src="scripts/dashboard.js"></script>
</body>
</html>
