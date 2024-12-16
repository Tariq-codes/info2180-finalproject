<?php
include 'db.php'; // Database connection


session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You are not logged in.";
    exit;
}

// Get the contact ID and note content from the form
if (isset($_POST['contact_id'], $_POST['comment']) && !empty($_POST['contact_id']) && !empty($_POST['comment'])) {
    $contact_id = $_POST['contact_id'];
    $comment = $_POST['comment'];
    $created_by = $_SESSION['user_id'];

    // Insert the note into the notes table
    $sql_insert = "INSERT INTO notes (contact_id, comment, created_by) VALUES (:contact_id, :comment, :created_by)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bindParam(':contact_id', $contact_id, PDO::PARAM_INT);
    $stmt_insert->bindParam(':comment', $comment, PDO::PARAM_STR);
    $stmt_insert->bindParam(':created_by', $created_by, PDO::PARAM_INT);

    try {
        if ($stmt_insert->execute()) {
            echo "Note added successfully!";
            
            // Update the update date after success
            $sql_update = "UPDATE contacts SET updated_at = NOW() WHERE id = :contact_id";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bindParam(':contact_id', $contact_id, PDO::PARAM_INT);
            $stmt_update->execute();
            
        } else {
            echo "Failed to add the note.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid contact ID or comment.";
}
// redirect
?>
<script>
    setTimeout(function() {
        window.history.back();
    }, 1000);
</script>
