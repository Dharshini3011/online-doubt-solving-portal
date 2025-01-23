<?php
// Database connection details
$host = 'localhost'; // Replace with your database host
$db = 'askpeer';     // Your database name
$user = 'root';      // Your database username
$pass = '';          // Your database password

// Initialize variables to store form data and errors
$name = '';
$email = '';
$password = '';
$profilePicturePath = 'uploads/default.jpg'; // Default image if no photo is uploaded
$errorMessage = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check email format for ".tce.edu"
    $emailPattern = '/[a-zA-Z0-9._%+-]+\.tce\.edu$/';
    if (!preg_match($emailPattern, $email)) {
        $errorMessage = "Please enter a valid email address that contains '.tce.edu'.";
    }
    
     else {
        // Hash the password before storing it
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Handle the profile photo upload
        if ($_FILES['profilePhoto']['error'] === UPLOAD_ERR_OK) {
            // Upload photo to a directory (ensure 'uploads/' exists and has write permissions)
            $uploadDir = 'uploads/';
            $fileTmpPath = $_FILES['profilePhoto']['tmp_name'];
            $fileName = $_FILES['profilePhoto']['name'];
            $filePath = $uploadDir . $fileName;

            if (move_uploaded_file($fileTmpPath, $filePath)) {
                $profilePicturePath = $filePath; // Set the path of the uploaded photo
            }
        }

        // Database insertion logic
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Check if name or email already exists
            $stmt = $pdo->prepare("SELECT * FROM users WHERE name = :name OR email = :email");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existingUser) {
                $errorMessage = "Name or email already exists!";
            } else {
                // Insert new user data into the database
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password, profile_picture) 
                                       VALUES (:name, :email, :password, :profile_picture)");
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $hashedPassword);
                $stmt->bindParam(':profile_picture', $profilePicturePath);
                $stmt->execute();

                // Redirect to login page
                header("Location: home.html");
                exit();
            }
        } catch (PDOException $e) {
            $errorMessage = "Database connection failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TCE-IT</title>
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

    .profile-photo {
        margin-bottom: 20px;
        border-radius: 50%;
        width: 100px;
        height: 100px;
        object-fit: cover;
        border: 2px solid #f5D59B;
    }
    </style>
</head>

<body>
    <div class="overlay"></div>
    <div class="login-container">
        <h2>Sign in to TCE-IT</h2>

        <?php if ($errorMessage): ?>
        <div style="color: red; margin-bottom: 10px;"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <img src="https://via.placeholder.com/100" alt="Profile Photo" class="profile-photo" id="profilePreview">
        <input type="file" id="profilePhoto" name="profilePhoto" onchange="previewProfilePhoto()" accept="image/*">

        <form id="loginForm" method="POST" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Name" value="<?php echo htmlspecialchars($name); ?>" required>
            <input type="email" id="email" name="email" placeholder="Email"
                value="<?php echo htmlspecialchars($email); ?>" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Sign</button>
        </form>
        <p>Already have an account? <a href="login.php">Login</a></p> <!-- Link to login page -->
    </div>

    <script>
    function previewProfilePhoto() {
        const file = document.getElementById('profilePhoto').files[0];
        const reader = new FileReader();

        reader.onloadend = function() {
            document.getElementById('profilePreview').src = reader.result;
        }

        if (file) {
            reader.readAsDataURL(file);
        }
    }
    </script>
</body>

</html>