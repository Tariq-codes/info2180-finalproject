<?php
include 'db.php'; // Database connection

// Start session and check if the user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to view this page.";
    exit;
}

// Get the contact ID from the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Invalid contact ID.";
    exit;
}

$contact_id = $_GET['id'];

// Fetch contact details from the database
$sql = "SELECT c.id, c.title, c.firstname, c.lastname, c.email, c.telephone, c.created_by, c.created_at, c.company, c.type, c.updated_at, u.firstname AS assigned_to
        FROM contacts c
        JOIN users u ON c.assigned_to = u.id
        WHERE c.id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $contact_id, PDO::PARAM_INT);
$stmt->execute();
$contact = $stmt->fetch(PDO::FETCH_ASSOC);

// If contact not found, show error
if (!$contact) {
    echo "Contact not found.";
    exit;
}

// Fetch existing notes for the contact
$notes_sql = "SELECT comment, created_by, created_at FROM notes WHERE contact_id = :id";
$notes_stmt = $conn->prepare($notes_sql);
$notes_stmt->bindParam(':id', $contact_id, PDO::PARAM_INT);
$notes_stmt->execute();
$notes = $notes_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Contact Details</title>
    <link rel="stylesheet" href="view_details.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="dolphin.png" type="image/png" alter="Dolphin">
</head>
<header>
        <div class="header-content">
            <h1>Dolphin CRM</h1>
        </div>
    </header>

<body>
    <!-- Button to go back to dashboard -->
    <a href="dashboard.php?page=home.php"><button id="goToDashboardBtn">Go to Dashboard</button></a>

    <!-- Contact details container -->
    <div class="contact-details-container">
        <?php
            // Fetch the user's name who created the contact
            $created_by_user_id = $contact['created_by'];
            $sql_user = "SELECT firstname, lastname FROM users WHERE id = :user_id";
            $stmt_user = $conn->prepare($sql_user);
            $stmt_user->bindParam(':user_id', $created_by_user_id, PDO::PARAM_INT);
            $stmt_user->execute();
            $user = $stmt_user->fetch(PDO::FETCH_ASSOC);

            // If the user is found, concatenate the name
            $user_name = $user ? $user['firstname'] . ' ' . $user['lastname'] : 'Unknown User';

            // Get the created and updated times
            $created_at = $contact['created_at'];
            $updated_at = $contact['updated_at'];
            ?>

            <h2><?php echo htmlspecialchars($contact['firstname'] . ' ' . $contact['lastname']); ?></h2>
            <small>Created By: <?php echo htmlspecialchars($user_name); ?> on <?php echo htmlspecialchars($created_at); ?></small>
            <br>
            <small>Last Updated: <?php echo htmlspecialchars($updated_at); ?></small>

        <div class="contact-details">
            <p><strong>Title:</strong> <?php echo htmlspecialchars($contact['title']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($contact['email']); ?></p>
            <p><strong>Telephone:</strong> <?php echo htmlspecialchars($contact['telephone']); ?></p>
            <p><strong>Company:</strong> <?php echo htmlspecialchars($contact['company']); ?></p>
            <p><strong>Type:</strong> <?php echo htmlspecialchars($contact['type']); ?></p>
            <p><strong>Assigned To:</strong> <?php echo htmlspecialchars($contact['assigned_to']); ?></p>
        </div>

        <!-- Role Switching and Assign Contact to Admin Buttons -->
        <div class="form-buttons-container">
            <!-- Assign Contact to Admin Button (only for admins) -->
                <form action="assign_to_admin.php" method="POST">
                    <input type="hidden" name="contact_id" value="<?php echo $contact_id; ?>">
                    <button type="submit"> <i class="fas fa-hand-pointer"></i> Assign to Me</button>
                </form>

                <form action="switch_role.php" method="POST">
                <input type="hidden" name="contact_id" value="<?php echo $contact_id; ?>">
                <?php if ($contact['type'] === 'Sales Lead'): ?>
                    <button type="submit" name="new_type" value="Support"> <i class="fas fa-random"></i>Switch to Support</button>
                <?php elseif ($contact['type'] === 'Support'): ?>
                    <button type="submit" name="new_type" value="Sales Lead"> <i class="fas fa-random"></i> Switch to Sales Lead</button>
                <?php endif; ?>
            </form>
        </div>
        <hr>


        <!-- Notes Section -->
        <div class="notes-section">
    <h3><i class="fas fa-sticky-note"></i> Notes:</h3>
    <ul>
        <?php foreach ($notes as $note): ?>
            <?php
                // Fetch the user's name who created the note
                $note_user_id = $note['created_by'];
                $sql_user = "SELECT firstname, lastname FROM users WHERE id = :user_id";
                $stmt_user = $conn->prepare($sql_user);
                $stmt_user->bindParam(':user_id', $note_user_id, PDO::PARAM_INT);
                $stmt_user->execute();
                $user = $stmt_user->fetch(PDO::FETCH_ASSOC);
                
                // If the user is found, concatenate the name
                $user_name = $user ? $user['firstname'] . ' ' . $user['lastname'] : 'Unknown User';
                
                // Get the created date of the note
                $created_at = $note['created_at'];
            ?>
            <li>
                <p><?php echo htmlspecialchars($note['comment']); ?></p>
                <small>By: <?php echo htmlspecialchars($user_name); ?> | Date: <?php echo htmlspecialchars($created_at); ?></small>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

            <!-- Add a new note -->
            <form action="save_notes.php" method="POST">
                <textarea name="comment" placeholder="Add your note here..."></textarea><br>
                <input type="hidden" name="contact_id" value="<?php echo $contact_id; ?>">
                <button type="submit">Save Note</button>
            </form>
        </div>

        
    </div>
</body>
</html>
