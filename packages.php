<?php
require_once "includes/connection.php";
session_start();

// Check if the user typed something into the search bar
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Start building the query to fetch all packages
$query = "SELECT * FROM packages";

// If there is a search term, narrow down the results to match it
if ($search) {
    $query .= " WHERE title LIKE '%$search%' OR destination LIKE '%$search%'";
}

// Run the query to pull the matching packages from the database
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
        <h2 class="page-title">Available travel packages</h2>
        <form method="GET" class="search-bar-wrapper">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search packages or destinations">
            <button type="submit" class="btn btn-register">Search</button>
        </form>

        <div class="package-grid">
            <?php while($row = mysqli_fetch_assoc($result)) { ?>
                <div class="package-card">
                    <div class="package-img" style="background-image: url('<?php echo htmlspecialchars($row['image_url']); ?>');"></div>
                    <div class="package-content">
                        <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                        <p class="text-muted-sm mb-8">📍 <?php echo htmlspecialchars($row['destination']); ?></p>
                        <div class="price">$<?php echo htmlspecialchars($row['price']); ?> <span>/ person</span></div>
                        <p class="description"><?php echo htmlspecialchars($row['description']); ?></p>
                        
                        <?php if(isset($_SESSION['user_id']) && $_SESSION['role'] == 'customer') { ?>
                            <a href="book.php?id=<?php echo $row['id']; ?>" class="btn btn-register btn-block">Book now</a>
                        <?php } elseif(!isset($_SESSION['user_id'])) { ?>
                            <a href="login.php" class="btn btn-outline btn-block">Login to book</a>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
            
            <?php if(mysqli_num_rows($result) == 0) { 
                echo "<p class='text-muted'>No packages found matching your search.</p>"; 
            } ?>
        </div>
    </div>
    <?php include "includes/footer.php"; ?>
</body>
</html>
