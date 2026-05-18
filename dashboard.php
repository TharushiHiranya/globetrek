<?php
require_once "includes/connection.php";
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role    = $_SESSION['role'];
$name    = $_SESSION['name'];
$msg     = '';

// Handle booking status updates
if (isset($_POST['update_booking'])) {
    $b_id       = intval($_POST['booking_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);
    mysqli_query($conn, "UPDATE bookings SET status='$new_status' WHERE id=$b_id");
}

// Handle package removal (Admin only)
if (isset($_POST['remove_package']) && $role === 'admin') {
    $p_id = intval($_POST['package_id']);
    mysqli_query($conn, "DELETE FROM packages WHERE id=$p_id");
    $msg = "<p class='success'>Package removed successfully.</p>";
}

// Handle employee addition (Admin only)
if (isset($_POST['add_employee']) && $role === 'admin') {
    $emp_name = mysqli_real_escape_string($conn, $_POST['emp_name']);
    $emp_email = mysqli_real_escape_string($conn, $_POST['emp_email']);
    $emp_pass = $_POST['emp_password'];
    $emp_role = mysqli_real_escape_string($conn, $_POST['emp_role']);
    
    $check = mysqli_query($conn, "SELECT id FROM users WHERE email = '$emp_email'");
    if (mysqli_num_rows($check) > 0) {
        $msg = "<p class='error'>Email already exists.</p>";
    } else {
        mysqli_query($conn, "INSERT INTO users (name, email, password, role) VALUES ('$emp_name', '$emp_email', '$emp_pass', '$emp_role')");
        $msg = "<p class='success'>Employee added successfully.</p>";
    }
}

// Handle employee removal (Admin only)
if (isset($_POST['remove_employee']) && $role === 'admin') {
    $emp_id = intval($_POST['employee_id']);
    if ($emp_id !== $user_id) { // Prevent self-deletion
        mysqli_query($conn, "DELETE FROM users WHERE id=$emp_id");
        $msg = "<p class='success'>Employee removed successfully.</p>";
    } else {
        $msg = "<p class='error'>You cannot remove yourself.</p>";
    }
}

// Handle new package creation
if (isset($_POST['add_package']) && ($role === 'admin' || $role === 'staff')) {
    $title       = mysqli_real_escape_string($conn, $_POST['title']);
    $destination = mysqli_real_escape_string($conn, $_POST['destination']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price       = floatval($_POST['price']);
    $image_url   = '';

    // Check if they uploaded a file
    if (!empty($_FILES['image']['name'])) {
        $upload_dir = 'images/destinations/';
        // Clean up the file name
        $safe_name  = preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($_FILES['image']['name']));
        $target     = $upload_dir . $safe_name;

        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (in_array($_FILES['image']['type'], $allowed)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $image_url = $target;  // e.g. images/destinations/paris.jpg
            } else {
                $msg = "<p class='error'>Image upload failed. Check folder permissions.</p>";
            }
        } else {
            $msg = "<p class='error'>Only JPG, PNG, WebP, or GIF images are allowed.</p>";
        }
    }

    if (empty($msg)) {
        $sql = "INSERT INTO packages (title, destination, description, price, image_url)
                VALUES ('$title', '$destination', '$description', $price, '$image_url')";
        if (mysqli_query($conn, $sql)) {
            $msg = "<p class='success'>Package added successfully!</p>";
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
        <?php if ($role === 'customer'): ?>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="margin: 0; color: #2d2d2d;">Your Bookings</h3>
                <a href="packages.php" class="btn btn-register">New Booking</a>
            </div>
            <?php
            $sql    = "SELECT b.id, p.title, b.status, b.booking_date
                       FROM bookings b
                       JOIN packages p ON b.package_id = p.id
                       WHERE b.user_id = $user_id
                       ORDER BY b.id DESC";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) > 0) {
                echo "<table><tr><th>Booking ID</th><th>Package</th><th>Status</th><th>Date</th><th>Action</th></tr>";
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['title']}</td>
                            <td>{$row['status']}</td>
                            <td>{$row['booking_date']}</td>
                            <td>
                                <form method='POST' style='margin:0;'>
                                    <input type='hidden' name='booking_id' value='{$row['id']}'>
                                    <input type='hidden' name='status' value='cancelled'>
                                    <button type='submit' name='update_booking' class='btn btn-outline' style='padding:6px 12px; font-size:13px;' " . ($row['status'] === 'cancelled' ? 'disabled' : '') . ">Cancel</button>
                                </form>
                            </td>
                          </tr>";
                }
                echo "</table>";
            } else {
                echo "<p style='color:#888;'>You have no bookings yet. <a href='packages.php' class='text-link'>Browse packages</a></p>";
            }
            ?>

        <!-- Admin and staff area -->
        <?php elseif ($role === 'staff' || $role === 'admin'): ?>

            <!-- New package form -->
            <h3 style="margin-bottom: 16px; color: #2d2d2d;">Add New Package</h3>
            <div class="form-card" style="max-width: 700px; margin: 0 0 48px 0;">
                <form method="POST" enctype="multipart/form-data">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label for="pkg-title">Package Title</label>
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
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label for="pkg-price">Price (USD)</label>
                            <input type="number" id="pkg-price" name="price" placeholder="e.g. 1200" step="0.01" min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="pkg-image">Destination Image</label>
                            <input type="file" id="pkg-image" name="image" accept="image/*"
                                   style="padding: 10px 14px; background:#fafcfa;">
                            <small style="color:#888; font-size:12px;">JPG/PNG/WebP — saved to images/destinations/</small>
                        </div>
                    </div>
                    <button type="submit" name="add_package" class="btn btn-register" style="margin-top: 8px;">Add Package</button>
                </form>
            </div>

            <!-- Manage bookings -->
            <h3 style="margin-bottom: 16px; color: #2d2d2d;">Manage Bookings</h3>
            <?php
            $sql    = "SELECT b.id, u.name as customer, p.title, b.status, b.booking_date
                       FROM bookings b
                       JOIN users u ON b.user_id = u.id
                       JOIN packages p ON b.package_id = p.id
                       ORDER BY b.id DESC";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) > 0) {
                echo "<table><tr><th>ID</th><th>Customer</th><th>Package</th><th>Status</th><th>Date</th><th>Action</th></tr>";
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['customer']}</td>
                            <td>{$row['title']}</td>
                            <td>{$row['status']}</td>
                            <td>{$row['booking_date']}</td>
                            <td>
                                <form method='POST' style='display:flex; gap:10px;'>
                                    <input type='hidden' name='booking_id' value='{$row['id']}'>
                                    <select name='status' style='padding:8px 12px; border:2px solid #e8ede7; border-radius:10px; font-family:Poppins,sans-serif; font-size:13px; outline:none;'>
                                        <option value='pending'   ".($row['status']==='pending'   ?'selected':'').">Pending</option>
                                        <option value='confirmed' ".($row['status']==='confirmed' ?'selected':'').">Confirmed</option>
                                        <option value='cancelled' ".($row['status']==='cancelled' ?'selected':'').">Cancelled</option>
                                    </select>
                                    <button type='submit' name='update_booking' class='btn btn-register' style='padding:8px 16px; font-size:13px;'>Update</button>
                                </form>
                            </td>
                          </tr>";
                }
                echo "</table>";
            } else {
                echo "<p style='color:#888;'>No bookings found.</p>";
            }
            ?>

            <!-- Messages from customers -->
            <h3 style="margin-top: 48px; margin-bottom: 16px; color: #2d2d2d;">Customer Queries</h3>
            <?php
            $sql    = "SELECT * FROM queries ORDER BY id DESC";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) > 0) {
                echo "<table><tr><th>ID</th><th>Name</th><th>Email</th><th>Message</th><th>Date</th></tr>";
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['name']}</td>
                            <td>{$row['email']}</td>
                            <td>{$row['message']}</td>
                            <td>{$row['created_at']}</td>
                          </tr>";
                }
                echo "</table>";
            } else {
                echo "<p style='color:#888;'>No queries found.</p>";
            }
            ?>

            <!-- Admin Only: Manage Packages (Remove) -->
            <?php if ($role === 'admin'): ?>
            <h3 style="margin-top: 48px; margin-bottom: 16px; color: #2d2d2d;">Existing Packages</h3>
            <?php
            $sql_pkgs = "SELECT * FROM packages ORDER BY id DESC";
            $res_pkgs = mysqli_query($conn, $sql_pkgs);
            if (mysqli_num_rows($res_pkgs) > 0) {
                echo "<table><tr><th>ID</th><th>Title</th><th>Destination</th><th>Price</th><th>Action</th></tr>";
                while ($row = mysqli_fetch_assoc($res_pkgs)) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['title']}</td>
                            <td>{$row['destination']}</td>
                            <td>\${$row['price']}</td>
                            <td>
                                <form method='POST' style='margin:0;'>
                                    <input type='hidden' name='package_id' value='{$row['id']}'>
                                    <button type='submit' name='remove_package' class='btn btn-outline' style='padding:6px 12px; font-size:13px; color:#c0392b; border-color:#c0392b;' onclick=\"return confirm('Are you sure you want to remove this package?');\">Remove</button>
                                </form>
                            </td>
                          </tr>";
                }
                echo "</table>";
            } else {
                echo "<p style='color:#888;'>No packages found.</p>";
            }
            ?>

            <!-- Admin Only: Manage Employees -->
            <h3 style="margin-top: 48px; margin-bottom: 16px; color: #2d2d2d;">Manage Employees</h3>
            
            <div class="form-card" style="max-width: 700px; margin: 0 0 24px 0;">
                <form method="POST">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label for="emp-name">Employee Name</label>
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
                            <select id="emp-role" name="emp_role" style="width:100%; padding:14px; border:2px solid #e8ede7; border-radius:14px; font-family:Poppins,sans-serif; font-size:14px; outline:none; background:#f5f8f5;">
                                <option value="staff">Staff</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" name="add_employee" class="btn btn-register" style="margin-top: 8px;">Add Employee</button>
                </form>
            </div>

            <?php
            $sql_emps = "SELECT * FROM users WHERE role IN ('staff', 'admin') ORDER BY id DESC";
            $res_emps = mysqli_query($conn, $sql_emps);
            if (mysqli_num_rows($res_emps) > 0) {
                echo "<table><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Action</th></tr>";
                while ($row = mysqli_fetch_assoc($res_emps)) {
                    $disabled = ($row['id'] == $user_id) ? "disabled" : "";
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['name']}</td>
                            <td>{$row['email']}</td>
                            <td>{$row['role']}</td>
                            <td>
                                <form method='POST' style='margin:0;'>
                                    <input type='hidden' name='employee_id' value='{$row['id']}'>
                                    <button type='submit' name='remove_employee' class='btn btn-outline' style='padding:6px 12px; font-size:13px; color:#c0392b; border-color:#c0392b;' $disabled onclick=\"return confirm('Remove this employee?');\">Remove</button>
                                </form>
                            </td>
                          </tr>";
                }
                echo "</table>";
            }
            ?>
            <?php endif; ?>

        <?php endif; ?>
    </div>
    <?php include "includes/footer.php"; ?>
</body>
</html>
