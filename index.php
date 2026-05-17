<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ceylora - Find Your Perfect Escape</title>

    <link rel="stylesheet" href="styles/home.css">
    <link rel="shortcut icon" href="favicon.ico" />
</head>

<body>
    <?php
    include "includes/connection.php";
    ?>

    <section class="hero-section">
        <?php
        include "includes/header.php";
        ?>

        <div class="hero-content">
            <h1 class="hero-heading">Find Your Perfect Escape with<br />Ceylora</h1>
            <p class="hero-tagline">We make travel planning simple, so you can just enjoy the journey.</p>

            <div class="hero-search-bar">
                <input type="search" placeholder="Search by destination">
            </div>

            <div class="hero-buttons">
                <a href="#">Browse Destinations</a>
                <a href="#">Start Planning</a>
            </div>
        </div>
    </section>

    <main>
        <h1>Welcome to Our Website</h1>
    </main>

    <?php
    include "includes/footer.php";
    ?>
</body>

</html>