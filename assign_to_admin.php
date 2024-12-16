<?php
include 'db.php'; // Database connection

// Start the session to check logged-in user
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "You are not logged in.";
    exit;
}

// Get the contact ID from the form
if (isset($_POST['contact_id']) && !empty($_POST['contact_id'])) {
    $contact_id = $_POST['contact_id'];
    $user_id = $_SESSION['user_id']; // Get the logged-in user's ID

    // Update the contact
    $update_sql = "UPDATE contacts SET assigned_to = :user_id, updated_at = NOW() WHERE id = :contact_id";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $update_stmt->bindParam(':contact_id', $contact_id, PDO::PARAM_INT);

    try {
        if ($update_stmt->execute()) {
            echo "Contact assigned successfully!";
        } else {
            echo "Failed to assign contact.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid contact ID.";
}
?>

<script>
    setTimeout(function() {
        window.history.back();
    }, 1000);
</script>
