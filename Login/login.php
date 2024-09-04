<?php
session_start();
include '../config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT id, username, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $username, $hashed_password);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['username'] = $username;

        setcookie('user_id', $id, time() + (86400 * 1), "/"); 
        setcookie('username', $username, time() + (86400 * 1), "/");

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
            $mail->addAddress($email, 'Reciptent');
        
            $mail->isHTML(true);
            $mail->Subject = 'Login successully';
            $mail->Body    = '<b>You are logged in to our gadget recommendation website</b>';
        
            $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
  
        echo "<script>alert('Login Successfully!'); window.location.href='../Home/index.php';</script>";
        exit();
    } else {
        echo "<script>alert('Login failed!'); window.location.href='login.php';</script>";
    }

    $stmt->close();
    $conn->close();
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
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800;900&display=swap"
      rel="stylesheet"
    />
    <title>Login Page</title>
  </head>

  <body>
    <section>
      <div class="login-modal">
        <div class="login-form">
          <div class="login-content">
            <h1 class="title">Welcome back</h1>
            <h3 class="subtitle">Welcome back! Please enter your details.</h3>
            <form method="post" action="login.php">
              <div class="email-input">
                <span class="input-title">Email</span>
                <input
                  class="input"
                  type="email"
                  placeholder="Enter your email"
                  name = "email"
                  required
                />
              </div>
              <div class="password-input">
                <span class="input-title">Password</span>
                <input 
                  class="input" 
                  type="password" 
                  placeholder="********"
                  name = "password"
                  required
                  />
              </div>
              <button class="btn btn-primary" type="submit">Sign in</button>
            </form>
            <div class="login-footer">
              Don’t have an account?<a href="../Signup/sign_up.php">Sign up fo free!</a>
            </div>
          </div>
        </div>
        <div class="login-image">
          <img src="../Assets/login.png" alt="Laptops and Phone Illustrator" />
        </div>
      </div>
    </section>
  </body>
</html>
