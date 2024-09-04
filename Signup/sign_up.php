<?php
session_start();                           
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
include '../config.php';

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<script>alert('Invalid email format'); window.location.href='signup.php';</script>";
            exit();
        }

        if ($password === $confirm_password) {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            $sql = "INSERT INTO users (username, password, email) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $username, $hashed_password, $email);

            if ($stmt->execute()) {
                $user_id = $stmt->insert_id;

                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username;

                setcookie('user_id', $user_id, time() + (86400 * 1), "/"); 
                setcookie('username', $username, time() + (86400 * 1), "/");

                echo "<script>console.log('User ID Cookie:', document.cookie);</script>";
                echo "<script>console.log('Username Cookie:', document.cookie);</script>";

                require '../PHPMailer/src/Exception.php';
                require '../PHPMailer/src/PHPMailer.php';
                require '../PHPMailer/src/SMTP.php';
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'sakthipriyen@gmail.com';
                    $mail->Password   = 'prat wzrp wjgp qogr'; 
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;
                
                    $mail->setFrom('sakthipriyen@gmail.com', 'Mailer');
                    $mail->addAddress($email, 'reciptent');
                
                    $mail->isHTML(true);
                    $mail->Subject = 'Registration successfully';
                    $mail->Body    = 'Successfully registered with Gadgets recs';
                    // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
                
                    $mail->SMTPDebug = 2; 
                
                    $mail->send();
                    echo 'Message has been sent';
                } catch (Exception $e) {
                    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }

                echo "<script>alert('Registration Success !!'); window.location.href='../Home/index.php';</script>";
                exit();
            } else {
                echo "<script>alert('Registration failed !!'); window.location.href='signup.php';</script>";
            }

            $stmt->close();
            $conn->close();
        } else {
            echo "<script>alert('Error: Passwords do not match!');</script>";
        }
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    echo "<script>alert('An error occurred. Please try again later.');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
    <title>Sign Up</title>
    <style>
        .valid {
            color: green;
        }
        .invalid {
            color: red;
        }
        .feedback {
            display: none;
        }
    </style>
    <script>
        function validateEmail(email) {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailPattern.test(email);
        }

        function validatePassword(password) {
            const uppercasePattern = /[A-Z]/;
            const lowercasePattern = /[a-z]/;
            const numberPattern = /[0-9]/;
            const symbolPattern = /[^A-Za-z0-9]/;
            return {
                uppercase: uppercasePattern.test(password),
                lowercase: lowercasePattern.test(password),
                number: numberPattern.test(password),
                symbol: symbolPattern.test(password)
            };
        }

        function showEmailFeedback() {
            const emailInput = document.getElementById('email');
            const emailFeedback = document.getElementById('emailFeedback');
            emailFeedback.style.display = 'block';

            if (validateEmail(emailInput.value)) {
                emailFeedback.textContent = '✓ Valid email';
                emailFeedback.classList.remove('invalid');
                emailFeedback.classList.add('valid');
            } else {
                emailFeedback.textContent = '✗ Invalid email';
                emailFeedback.classList.remove('valid');
                emailFeedback.classList.add('invalid');
            }
        }

        function showPasswordFeedback() {
            const passwordInput = document.getElementById('password');
            const passwordFeedback = document.getElementById('passwordFeedback');
            passwordFeedback.style.display = 'block';

            const passwordValidity = validatePassword(passwordInput.value);
            passwordFeedback.innerHTML = `
                <div class="${passwordValidity.uppercase ? 'valid' : 'invalid'}">${passwordValidity.uppercase ? '✓' : '✗'} Uppercase letter</div>
                <div class="${passwordValidity.lowercase ? 'valid' : 'invalid'}">${passwordValidity.lowercase ? '✓' : '✗'} Lowercase letter</div>
                <div class="${passwordValidity.number ? 'valid' : 'invalid'}">${passwordValidity.number ? '✓' : '✗'} Number</div>
                <div class="${passwordValidity.symbol ? 'valid' : 'invalid'}">${passwordValidity.symbol ? '✓' : '✗'} Symbol</div>
            `;
        }

        function hideFeedback() {
            const emailFeedback = document.getElementById('emailFeedback');
            const passwordFeedback = document.getElementById('passwordFeedback');
            emailFeedback.style.display = 'none';
            passwordFeedback.style.display = 'none';
        }

        window.addEventListener('load', () => {
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            emailInput.addEventListener('input', showEmailFeedback);
            passwordInput.addEventListener('input', showPasswordFeedback);
            emailInput.addEventListener('blur', hideFeedback);
            passwordInput.addEventListener('blur', hideFeedback);
        });
    </script>
</head>
<body>
    <section>
        <div class="signup-modal">
            <div class="signup-form">
                <div class="signup-content">
                    <h1 class="title">Create Account</h1>
                    <h3 class="subtitle">Please fill in the details to create your account.</h3>
                    <form method="post" action="http://localhost/Gadgets%20recommendation/Signup/sign_up.php">
                        <div class="name-input">
                            <span class="input-title">Name</span>
                            <input class="input" type="text" name="username" placeholder="Enter your name" required />
                        </div>
                        <div class="email-input">
                            <span class="input-title">Email</span>
                            <input class="input" type="email" id="email" name="email" placeholder="Enter your email" required />
                            <div id="emailFeedback" class="feedback">✗ Invalid email</div>
                        </div>
                        <div class="password-input">
                            <span class="input-title">Password</span>
                            <input class="input" type="password" id="password" name="password" placeholder="" required />
                            <div id="passwordFeedback" class="feedback">
                                <div class="invalid">✗ Uppercase letter</div>
                                <div class="invalid">✗ Lowercase letter</div>
                                <div class="invalid">✗ Number</div>
                                <div class="invalid">✗ Symbol</div>
                            </div>
                        </div>
                        <div class="password-input">
                            <span class="input-title">Confirm Password</span>
                            <input class="input" type="password" name="confirm_password" placeholder="" required />
                        </div>
                        <button class="btn btn-primary" type="submit">Sign up</button>
                    </form>
                    <div class="signup-footer">
                        Already have an account?<a href="../Login/login.php">Sign in here!</a>
                    </div>
                </div>
            </div>
            <div class="signup-image">
                <img src="../Assets/signup.png" alt="Laptops and Phone Illustrator" />
            </div>
        </div>
    </section>
</body>
</html>
