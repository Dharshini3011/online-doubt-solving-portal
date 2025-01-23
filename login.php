<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   
    $name = $_POST['name'];  
    $password = $_POST['password'];

  
    $host = 'localhost';  
    $db = 'askpeer';      
    $user = 'root';      
    $pass = '';           

    try {
        // Create PDO connection
        $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Query to check if name exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE name = :name");  // changed username to name
        $stmt->bindParam(':name', $name);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if user exists and password matches
        if ($user && password_verify($password, $user['password'])) {
            // Redirect to home page if valid
            header("Location: home.html");
            exit();
        } else {
            $errorMessage = 'Invalid name or password';  // changed username to name
        }
    } catch (PDOException $e) {
        $errorMessage = 'Database connection failed: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TCE-IT Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 0;
        color: #333;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        height: 100vh;
        background: url('https://img.jagranjosh.com/images/2022/December/29122022/Thiagarajar-College-of-Engineering-TCE-Madurai-Campus-View-1.jpeg') no-repeat center center fixed;
        background-size: cover;
    }

    .overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(245, 213, 155, 0.8);
        z-index: 1;
        filter: brightness(1.4);
    }

    .login-container {
        position: relative;
        z-index: 2;
        background-color: #5D4037;
        border-radius: 10px;
        padding: 40px;
        width: 350px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        text-align: center;
    }

    .login-container h2 {
        color: #f5D59B;
        margin-bottom: 20px;
    }

    .login-container input {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #f5D59B;
        border-radius: 5px;
        outline: none;
        font-size: 1rem;
    }

    .login-container button {
        width: 100%;
        padding: 10px;
        background-color: #f5D59B;
        border: none;
        border-radius: 5px;
        font-size: 1rem;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .login-container button:hover {
        background-color: #e4c48a;
    }

    .login-container p {
        text-align: center;
        margin-top: 10px;
        font-size: 0.9rem;
        color: #f5D59B;
    }

    .login-container p a {
        color: #f5D59B;
        text-decoration: none;
        font-weight: bold;
    }

    .login-container p a:hover {
        text-decoration: underline;
    }

    .error-message {
        color: red;
        font-size: 0.9rem;
        margin-bottom: 15px;
    }
    </style>
</head>

<body>
    <div class="overlay"></div>
    <div class="login-container">
        <h2>Login to TCE-IT</h2>
        <!-- Display error message if exists -->
        <?php if (isset($errorMessage)) { ?>
        <div class="error-message"><?php echo $errorMessage; ?></div>
        <?php } ?>

        <form action="login.php" method="POST">
            <input type="text" name="name" placeholder="Name" required> <!-- changed username to name -->
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="signin.php">Sign Up</a></p>
    </div>
</body>

</html>