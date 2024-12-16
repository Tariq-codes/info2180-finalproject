<?php
include 'db.php'; // Database connection
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to create a contact.";
    exit;
}

$error_message = $success_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input fields
    $title = htmlspecialchars(trim($_POST['title']));
    $firstname = htmlspecialchars(trim($_POST['firstname']));
    $lastname = htmlspecialchars(trim($_POST['lastname']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $telephone = htmlspecialchars(trim($_POST['telephone']));
    $company = htmlspecialchars(trim($_POST['company']));
    $type = htmlspecialchars(trim($_POST['type']));
    $assigned_to = intval($_POST['assigned_to']);

    // Validate required fields
    if (empty($title) || empty($firstname) || empty($lastname) || empty($email) || empty($type) || empty($assigned_to)) {
        $error_message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } elseif (!empty($telephone) && !preg_match("/^\(?\d{3}\)?[-.\s]?\d{3}[-.\s]?\d{4}$/", $telephone)) {
        // Validate telephone format
        $error_message = "Invalid telephone number format. Use (123) 456-7890, 123-456-7890.";
    } else {
        try {
            // Store contact data in the database
            $stmt = $conn->prepare("INSERT INTO contacts 
                (title, firstname, lastname, email, telephone, company, type, assigned_to, created_by, created_at, updated_at)
                VALUES (:title, :firstname, :lastname, :email, :telephone, :company, :type, :assigned_to, :created_by, NOW(), NOW())");
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':firstname', $firstname);
            $stmt->bindParam(':lastname', $lastname);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':telephone', $telephone);
            $stmt->bindParam(':company', $company);
            $stmt->bindParam(':type', $type);
            $stmt->bindParam(':assigned_to', $assigned_to);
            $stmt->bindParam(':created_by', $_SESSION['user_id']);

            if ($stmt->execute()) {
                $success_message = "Contact successfully created.";
                // Redirect to dashboard after success
                header('Location: dashboard.php?page=home.php');
                exit;
            } else {
                $error_message = "Failed to create contact.";
            }
        } catch (PDOException $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    }
}

// Fetch user list for the assigned to
$users = [];
try {
    $result = $conn->query("SELECT id, firstname, lastname FROM users");
    $users = $result->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Failed to load users: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Contact</title>
    <link rel="stylesheet" href="form.css">
    <link rel="icon" href="dolphin.png" type="image/png" Alter="Dolphin">
</head>
<header>
        <div class="header-content">
            <h1>Dolphin CRM</h1>
        </div>
    </header>
<body>
    <div class="add-user-title">
    <h1>New Contact</h1>
    </div>
<body>

    <!-- Display success or error messages -->
    <?php if ($success_message): ?>
        <div id="responseMessage" class="success"><?= $success_message; ?></div>
    <?php elseif ($error_message): ?>
        <div id="responseMessage" class="error"><?= $error_message; ?></div>
    <?php endif; ?>

    <form id="createContactForm" method="POST">
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" placeholder="Mr., Mrs., etc." required>
        </div>

        <div class="form-group">
            <label for="firstname">First Name:</label>
            <input type="text" id="firstname" name="firstname" placeholder="First Name" required>
        </div>

        <div class="form-group">
            <label for="lastname">Last Name:</label>
            <input type="text" id="lastname" name="lastname" placeholder="Last Name" required>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Email" required>
        </div>

        <div class="form-group">
            <label for="telephone">Telephone:</label>
            <input type="text" id="telephone" name="telephone" placeholder="(123) 456-7890 or 123-456-7890" required>
        </div>

        <div class="form-group">
            <label for="company">Company:</label>
            <input type="text" id="company" name="company" placeholder="Company Name" required>
        </div>

        <div class="form-group">
            <label for="type">Type:</label>
            <select id="type" name="type" required>
                <option value="Sales Lead">Sales Lead</option>
                <option value="Support">Support</option>
            </select>
        </div>

        <div class="form-group">
            <label for="assigned_to">Assigned To:</label>
            <select id="assigned_to" name="assigned_to" required>
                <option value="">Select User</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= $user['id']; ?>"><?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="add-user-btn">Create Contact</button>
        </div>
    </form>
</body>
</html>
