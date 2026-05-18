<?php
include "includes/connection.php";

// Fetch all the packages that the admin has marked as featured
$pkg_result = mysqli_query($conn, "SELECT * FROM packages WHERE is_featured = 1");
$packages = [];

// Loop through the results and save them into an array for the carousel
while ($row = mysqli_fetch_assoc($pkg_result)) {
    $packages[] = $row;
}

// Quickly count up the totals to display in the stats section
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
                <form action="packages.php" method="GET" class="hero-search-form">
                    <input type="search" name="search" placeholder="Search by destination" class="flex-1">
                </form>
            </div>

            <div class="hero-buttons">
                <a href="packages.php">Browse destinations</a>
                <a href="register.php">Start planning</a>
            </div>
        </div>
    </section>

    <!-- Top picks -->
    <section class="featured-section" id="destinations">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">✦ Top Picks</span>
                <h2 class="section-title">Featured destinations</h2>
                <p class="section-subtitle">Find the perfect trip for you.</p>
            </div>

            <div class="carousel-container">
                <button class="carousel-arrow carousel-prev">&larr;</button>
                <div class="featured-carousel-wrapper" id="carousel-wrapper">
                    <div class="featured-grid" id="featured-carousel">
                        <?php foreach ($packages as $pkg) { ?>
                        <a href="packages.php" class="featured-card">
                            <img src="<?php echo htmlspecialchars($pkg['image_url']); ?>" alt="<?php echo htmlspecialchars($pkg['title']); ?>">
                            <div class="card-overlay">
                                <h3><?php echo htmlspecialchars($pkg['title']); ?></h3>
                                <p><?php echo htmlspecialchars($pkg['destination']); ?></p>
                                <div class="card-price">From $<?php echo htmlspecialchars($pkg['price']); ?></div>
                            </div>
                        </a>
                        <?php } ?>
                    </div>
                </div>
                <button class="carousel-arrow carousel-next">&rarr;</button>
            </div>
        </div>
    </section>

    <!-- Why choose us -->
    <section class="why-us-section" id="why-us">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">✦ Why GlobeTrek</span>
                <h2 class="section-title">Why travel with us</h2>
                <p class="section-subtitle">We plan trips you will never forget.</p>
            </div>

            <div class="why-us-grid">
                <div class="why-us-card">
                    <div class="why-us-icon">
                        <img src="images/icons/globe-alt-1-svgrepo-com.png" alt="Globe">
                    </div>
                    <h3>Handpicked spots</h3>
                    <p>We only pick places we actually want to go. No tourist traps, just good trips.</p>
                </div>
                <div class="why-us-card">
                    <div class="why-us-icon">
                        <img src="images/icons/dollar-minimalistic-svgrepo-com.png" alt="Dollar">
                    </div>
                    <h3>Clear pricing</h3>
                    <p>You pay what you see. We do not add surprise fees at checkout.</p>
                </div>
                <div class="why-us-card">
                    <div class="why-us-icon">
                        <img src="images/icons/secure-svgrepo-com.png" alt="Shield">
                    </div>
                    <h3>Safe bookings</h3>
                    <p>Your data stays safe. We protect your personal info and trip details.</p>
                </div>
                <div class="why-us-card">
                    <div class="why-us-icon">
                        <img src="images/icons/support-svgrepo-com.png" alt="Support">
                    </div>
                    <h3>Always around</h3>
                    <p>Call us anytime. We are here to help before and during your trip.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick stats -->
    <section class="stats-section" id="stats">
        <div class="container">
            <div class="stat-item">
                <div class="stat-number"><?php echo $total_packages; ?>+</div>
                <div class="stat-label">Travel packages</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo $total_users; ?>+</div>
                <div class="stat-label">Happy travelers</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo $total_bookings; ?>+</div>
                <div class="stat-label">Trips booked</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo $total_packages; ?>+</div>
                <div class="stat-label">Destinations</div>
            </div>
        </div>
    </section>



    <?php include "includes/footer.php"; ?>

    <script src="scripts/carousel.js"></script>
</body>

</html>