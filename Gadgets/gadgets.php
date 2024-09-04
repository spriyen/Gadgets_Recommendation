<?php
session_start();
$user_logged_in = false;
$username = '';

if (isset($_SESSION['username']) && !empty($_SESSION['username'])) {
    $user_logged_in = true;
    $username = $_SESSION['username'];
} elseif (isset($_COOKIE['username']) && !empty($_COOKIE['username'])) {
    $user_logged_in = true;
    $username = $_COOKIE['username'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gadgets</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterForm');
    const phoneFilter = document.getElementById('phoneFilter');
    const laptopFilter = document.getElementById('laptopFilter');

    function setFilter(deviceType) {
        if (deviceType === 'phone') {
            phoneFilter.style.display = 'block';
            laptopFilter.style.display = 'none';
            form.querySelector('input[value="phone"]').checked = true;
        } else if (deviceType === 'laptop') {
            phoneFilter.style.display = 'none';
            laptopFilter.style.display = 'block';
            form.querySelector('input[value="laptop"]').checked = true;
        }
    }

    const urlParams = new URLSearchParams(window.location.search);
    const deviceType = urlParams.get('deviceType') || 'phone'; 
    setFilter(deviceType);

    form.addEventListener('change', function() {
        const selectedValue = form.querySelector('input[name="deviceType"]:checked').value;
        setFilter(selectedValue);
    });
});
    </script>
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <a href="../Home/index.php">GadgetRecs</a>
            </div>
            <nav>
                <ul>
                    <li><a href="../Home/index.php">Home</a></li>
                    <li><a href="#">Gadgets</a></li>
                    <li><a href="../About us/about.html">About Us</a></li>
                </ul>
            </nav>
            <div class="auth-buttons">
                <?php if ($user_logged_in): ?>
                    <h4>Welcome, <?php echo htmlspecialchars($username); ?>
                    <a href="../Login/logout.php" class="signup">Logout</a></h4>
                <?php else: ?>
                    <a href="../Login/login.php" class="login">Login</a>
                    <a href="../Signup/sign_up.php" class="signup">Sign up</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <section class="hero">
    <div class="hero-content">
        <h1>Discover the Latest Gadgets</h1>
        <p>Explore a wide range of electronic devices and find the perfect gadget for you.</p>
    </div>
</section>
<section class="category">
        <h2>Gadgets Categories</h2>
        <div class="categories-grid">
            <div class="category-item" data-device-type="phone" id="phoneImage">
                <img src="../Assets/Phones.png" alt="Phones" >
            </div>
            <div class="category-item" data-device-type="laptop" id="laptopImage">
                <img src="../Assets/laptop.png" alt="Laptops">
            </div>
        </div>
    </section>

    <script>
        document.getElementById('phoneImage').onclick = function() {
            window.location.href = '../index3.html'; 
        };

        document.getElementById('laptopImage').onclick = function() {
            window.location.href = '../lapmain.html'; 
        };
    </script>
   
</body>
</html>
