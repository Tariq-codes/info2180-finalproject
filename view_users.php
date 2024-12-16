<?php
include 'db.php';

session_start();

// Ensure the user is an Admin
if ($_SESSION['role'] !== 'Admin') {
    echo "You do not have permission to add new users.";
    exit;
}

try {
    // Fetch user data from the database
    $sql = "SELECT * FROM users";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
    <link rel="stylesheet" href="dash_styles.css"> 
</head>
<body>
    <div class="user-list">
        <h1>Users</h1>
        <div class="new-contact">
            <button onclick="window.location.href='add_user.php'" type="button" class="add-user-btn" id="addUserBtn">
                <i class="fas fa-plus"></i> Add User
            </button>
        </div>
    </div>    
    <table border="1">
        <tr>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Created</th>
        </tr>
        <?php if (!empty($users)): ?>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['firstname']) ?></td>
                <td><?= htmlspecialchars($user['lastname']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['role']) ?></td>
                <td><?= htmlspecialchars($user['created_at']) ?></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">No users found.</td>
            </tr>
        <?php endif; ?>
    </table>

</body>
</html>
