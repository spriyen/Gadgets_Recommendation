<?php
session_start();
$user_logged_in = false;
$username = '';

if (isset($_SESSION['username'])) {
    $user_logged_in = true;
    $username = $_SESSION['username'];
} elseif (isset($_COOKIE['username'])) {
    $user_logged_in = true;
    $username = $_COOKIE['username'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gadget Recommendation</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <a href="#">GadgetRecs</a>
            </div>
            <nav>
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="../Gadgets/gadgets.php">Gadgets</a></li>
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
            <h1>Welcome to Gadget Recommendation</h1>
            <p>Discover the best gadgets recommended by our community</p>
        </div>
    </section>
    
    <div class="container-TS">
        <h2>Top 10 Selling Phones</h2>
        <div id="phone-card" class="card-container">
            <div class="arrow_left" onclick="prevPhone()">&#9664;</div>
            <div class="card">
                <img id="phone-image" src="" alt="Phone">
                <p id="phone-name"></p>
            </div>
            <div class="arrow_right" onclick="nextPhone()">&#9654;</div>
        </div>
        
        <h2>Top 10 Selling Laptops</h2>
        <div id="laptop-card" class="card-container">
            <div class="arrow_left" onclick="prevLaptop()">&#9664;</div>
            <div class="card">
                <img id="laptop-image" src="" alt="Laptop">
                <p id="laptop-name"></p>
            </div>
            <div class="arrow_right" onclick="nextLaptop()">&#9654;</div>
        </div>
    </div>

    <script src="script.js"></script>
    
    <section class="feedback-section">
        <h1>Real-Time Feedback</h1>
        <ul id="feedback-list"></ul>

        <h2>Submit Feedback</h2>
        <form class="feedback-form" onsubmit="event.preventDefault(); submitFeedback();">
            <label for="product_name">Product Name:</label>
            <input type="text" id="product_name" name="product_name" required>
            <br>
            <label for="feedback">Feedback:</label>
            <textarea id="feedback" name="feedback" required></textarea>
            <br>
            <label for="file_input">Upload File:</label>
            <input type="file" id="file_input" name="file_input">
            <br>
            <button type="submit">Submit</button>
        </form>
    </section>


    <footer>
        <div class="container">
            <p>&copy; 2024 GadgetRecs. All rights reserved.</p>
        </div>
    </footer>

    <script>
        const ws = new WebSocket('ws://localhost:8080');

        ws.onopen = () => {
            console.log('Connected to the WebSocket server');
        };

        ws.onmessage = (event) => {
            console.log('Message from server: ', event.data);
            const feedbackList = document.getElementById('feedback-list');
            const newFeedback = JSON.parse(event.data);
            const feedbackItem = document.createElement('li');
            feedbackItem.textContent = `Product: ${newFeedback.product_name}, Feedback: ${newFeedback.feedback}`;
            feedbackList.appendChild(feedbackItem);
        };

        ws.onclose = () => {
            console.log('Disconnected from the WebSocket server');
        };

        ws.onerror = (error) => {
            console.error('WebSocket error: ', error);
        };

        document.getElementById('file_input').addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    document.getElementById('feedback').value = e.target.result;
                };
                reader.readAsText(file);
            }
        });

        function submitFeedback() {
            const productName = document.getElementById('product_name').value;
            const feedback = document.getElementById('feedback').value;
            const feedbackData = { product_name: productName, feedback: feedback };
            ws.send(JSON.stringify(feedbackData));
            console.log('Feedback submitted:', feedbackData);
            document.getElementById('product_name').value = '';
            document.getElementById('feedback').value = '';
            document.getElementById('file_input').value = '';
            alert('Feedback submitted successfully!');
        }
    </script>
</body>
</html>
