<?php
include 'db.php'; // Database connection

session_start(); 
if (!isset($_SESSION['user_id'])) {
    echo "You are not logged in.";
    exit;
}

if (isset($_POST['contact_id']) && isset($_POST['new_type'])) {
    $contact_id = $_POST['contact_id'];
    $new_type = $_POST['new_type'];

    // Update the contact type in the database
    $sql = "UPDATE contacts SET type = :new_type WHERE id = :contact_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':contact_id', $contact_id, PDO::PARAM_INT);
    $stmt->bindParam(':new_type', $new_type, PDO::PARAM_STR);

    if ($stmt->execute()) {
        // Update the update field after success
        $update_time_sql = "UPDATE contacts SET updated_at = NOW() WHERE id = :contact_id";
        $update_time_stmt = $conn->prepare($update_time_sql);
        $update_time_stmt->bindParam(':contact_id', $contact_id, PDO::PARAM_INT);
        $update_time_stmt->execute();

        echo "Role updated successfully!";
    } else {
        echo "Failed to update role.";
    }
} else {
    echo "Invalid request.";
}
?>

<script>
    setTimeout(function() {
        window.history.back();
    }, 1000);
</script>
