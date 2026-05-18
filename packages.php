<?php
require_once "includes/connection.php";
session_start();

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$query = "SELECT * FROM packages";
if($search) {
    $query .= " WHERE title LIKE '%$search%' OR destination LIKE '%$search%'";
}
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Packages - GlobeTrek Adventures</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="shortcut icon" href="favicon.ico">
</head>
<body class="page-body">
    <?php include "includes/header.php"; ?>
    <div class="page-container">
        <h2 class="page-title">Available Travel Packages</h2>
        <form method="GET" class="search-bar-wrapper">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search packages or destinations...">
            <button type="submit" class="btn btn-register">Search</button>
        </form>

        <div class="package-grid">
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <div class="package-card">
                    <div class="package-img" style="background-image: url('<?php echo htmlspecialchars($row['image_url']); ?>');"></div>
                    <div class="package-content">
                        <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                        <p style="color: #888; margin-bottom: 8px; font-size: 13px;">📍 <?php echo htmlspecialchars($row['destination']); ?></p>
                        <div class="price">$<?php echo htmlspecialchars($row['price']); ?> <span>/ person</span></div>
                        <p class="description"><?php echo htmlspecialchars($row['description']); ?></p>
                        <?php if(isset($_SESSION['user_id']) && $_SESSION['role'] == 'customer'): ?>
                            <a href="book.php?id=<?php echo $row['id']; ?>" class="btn btn-register btn-block">Book Now</a>
                        <?php elseif(!isset($_SESSION['user_id'])): ?>
                            <a href="login.php" class="btn btn-outline btn-block">Login to Book</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
            <?php if(mysqli_num_rows($result) == 0) echo "<p style='color:#888;'>No packages found matching your search.</p>"; ?>
        </div>
    </div>
    <?php include "includes/footer.php"; ?>
</body>
</html>
