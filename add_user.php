<?php 
include 'db.php'; // Database connection

session_start();

// Check if the logged-in user is an admin
if ($_SESSION['role'] !== 'Admin') {
    echo "You do not have permission to add new users.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate form data to prevent XSS
    $first_name = htmlspecialchars(trim($_POST['first_name']));
    $last_name = htmlspecialchars(trim($_POST['last_name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $role = htmlspecialchars(trim($_POST['role']));

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } else {
        // Check if email already exists in the database
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $error_message = "User with this email already exists.";
        } else {
            // Password validation using regular expression
            if (!preg_match('/^(?=.*[A-Za-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
                $error_message = "Password must be at least 8 characters long, contain at least one number, one letter, and one capital letter.";
            } else {
                // Hash the password for storage in the database
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                try {
                    // Insert the new user into the database
                    $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, email, password, role) VALUES (:firstname, :lastname, :email, :password, :role)");
                    $stmt->bindParam(':firstname', $first_name);
                    $stmt->bindParam(':lastname', $last_name);
                    $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':password', $hashed_password);
                    $stmt->bindParam(':role', $role);

                    if ($stmt->execute()) {
                        $success_message = "User successfully added.";
                    } else {
                        $error_message = "Failed to add user.";
                    }
                } catch (PDOException $e) {
                    $error_message = "Error: " . $e->getMessage();
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <link rel="stylesheet" href="form.css">
    <link rel="icon" href="dolphin.png" type="image/png">
</head>
<header>
        <div class="header-content">
            <h1>Dolphin CRM</h1>
        </div>
    </header>
<body>
    <div class="add-user-title">
    <h1>Add a New User</h1>
    </div>

    <!-- Display success or error messages -->
    <?php if (isset($success_message)) { ?>
        <div id="responseMessage" class="success"><?php echo $success_message; ?></div>
        <script>
            setTimeout(function() {
                window.location.href = 'dashboard.php?page=home.php';
            }, 1000);
        </script>
    <?php } elseif (isset($error_message)) { ?>
        <div id="responseMessage" class="error"><?php echo $error_message; ?></div>
    <?php } ?>

    <form id="addUserForm" method="POST">
        <div class="form-group">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" placeholder="First Name" required>
        </div>
        
        <div class="form-group">
            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" placeholder="Last Name" required>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Email" required>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Must be at least 8 characters long, contain at least one number, and one capital letter." required>
        </div>

        <div class="form-group">
            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="Admin">Admin</option>
                <option value="Member">Member</option>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="add-user-btn">Add User</button>
        </div>
    </form>

    <a href="dashboard.php?page=home.php"><button id="goToDashboardBtn">Go to Dashboard</button></a>
</body>
</html>
