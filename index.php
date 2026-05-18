<?php
include "includes/connection.php";

// Get top packages
$pkg_result = mysqli_query($conn, "SELECT * FROM packages LIMIT 3");
$packages = [];
while ($row = mysqli_fetch_assoc($pkg_result)) {
    $packages[] = $row;
}

// Count site stats
$total_packages = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM packages"))['c'];
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM users WHERE role='customer'"))['c'];
$total_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM bookings"))['c'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="GlobeTrek Adventures - Your gateway to unforgettable travel experiences. Explore handcrafted tour packages to the world's most stunning destinations.">
    <title>GlobeTrek Adventures - Find Your Perfect Escape</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/home.css">
    <link rel="shortcut icon" href="favicon.ico">
</head>

<body>
    <?php include "includes/header.php"; ?>

    <!-- Top section -->
    <section class="hero-section" id="hero">
        <div class="hero-content">
            <h1 class="hero-heading">Find Your Perfect Escape with<br />GlobeTrek Adventures</h1>
            <p class="hero-subtitle">We make travel planning simple, so you can just enjoy the journey.</p>

            <div class="hero-search-bar">
                <img src="images/icons/search-svgrepo-com.png" alt="Search Icon" class="search-icon">
                <form action="packages.php" method="GET" style="width: 100%; display: flex;">
                    <input type="search" name="search" placeholder="Search by destination..." style="flex: 1;">
                </form>
            </div>

            <div class="hero-buttons">
                <a href="packages.php">Browse Destinations</a>
                <a href="register.php">Start Planning</a>
            </div>
        </div>
    </section>

    <!-- Top picks -->
    <section class="featured-section" id="destinations">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">✦ Top Picks</span>
                <h2 class="section-title">Featured Destinations</h2>
                <p class="section-subtitle">Find the perfect trip for you.</p>
            </div>

            <div class="featured-grid">
                <?php foreach ($packages as $pkg): ?>
                <a href="packages.php" class="featured-card">
                    <img src="<?php echo htmlspecialchars($pkg['image_url']); ?>" alt="<?php echo htmlspecialchars($pkg['title']); ?>">
                    <div class="card-overlay">
                        <h3><?php echo htmlspecialchars($pkg['title']); ?></h3>
                        <p><?php echo htmlspecialchars($pkg['destination']); ?></p>
                        <div class="card-price">From $<?php echo htmlspecialchars($pkg['price']); ?></div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Why choose us -->
    <section class="why-us-section" id="why-us">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">✦ Why GlobeTrek</span>
                <h2 class="section-title">Why Travel With Us</h2>
                <p class="section-subtitle">We plan trips you will never forget.</p>
            </div>

            <div class="why-us-grid">
                <div class="why-us-card">
                    <div class="why-us-icon">🌍</div>
                    <h3>Curated Destinations</h3>
                    <p>Every destination handpicked by travel experts for authentic, unforgettable experiences.</p>
                </div>
                <div class="why-us-card">
                    <div class="why-us-icon">💰</div>
                    <h3>Best Price Guarantee</h3>
                    <p>Transparent pricing with no hidden fees. Get the best value for your dream vacation.</p>
                </div>
                <div class="why-us-card">
                    <div class="why-us-icon">🛡️</div>
                    <h3>Safe & Secure</h3>
                    <p>Travel with confidence knowing your bookings and personal data are fully protected.</p>
                </div>
                <div class="why-us-card">
                    <div class="why-us-icon">💬</div>
                    <h3>24/7 Support</h3>
                    <p>Our dedicated team is always ready to assist you, before, during, and after your trip.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick stats -->
    <section class="stats-section" id="stats">
        <div class="container">
            <div class="stat-item">
                <div class="stat-number"><?php echo $total_packages; ?>+</div>
                <div class="stat-label">Travel Packages</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo max($total_users, 50); ?>+</div>
                <div class="stat-label">Happy Travelers</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo max($total_bookings, 120); ?>+</div>
                <div class="stat-label">Trips Booked</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">15+</div>
                <div class="stat-label">Destinations</div>
            </div>
        </div>
    </section>



    <?php include "includes/footer.php"; ?>
</body>

</html>