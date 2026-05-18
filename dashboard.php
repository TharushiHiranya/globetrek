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
            <h3 style="margin-bottom: 16px; color: #2d2d2d;">Your Bookings</h3>
            <?php
            $sql    = "SELECT b.id, p.title, b.status, b.booking_date
                       FROM bookings b
                       JOIN packages p ON b.package_id = p.id
                       WHERE b.user_id = $user_id
                       ORDER BY b.id DESC";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) > 0) {
                echo "<table><tr><th>Booking ID</th><th>Package</th><th>Status</th><th>Date</th></tr>";
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['title']}</td>
                            <td>{$row['status']}</td>
                            <td>{$row['booking_date']}</td>
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

        <?php endif; ?>
    </div>
    <?php include "includes/footer.php"; ?>
</body>
</html>
