<?php
include 'db.php'; // Database connection

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to view this page.";
    exit;
}

// Get user id
$user_id = $_SESSION['user_id'];

// Default filter
$filter = 'All Contacts';
if (isset($_GET['filter'])) {
    $filter = $_GET['filter'];
}

// Base query for fetching contacts
$sql = "SELECT c.id, c.title, c.firstname, c.lastname, c.email, c.telephone, c.company, c.type, u.firstname AS assigned_to
        FROM contacts c
        JOIN users u ON c.assigned_to = u.id";

// Add filters based on the selected type
if ($filter === 'Sales Leads') {
    $sql .= " WHERE c.type = 'Sales Lead'";
} elseif ($filter === 'Support') {
    $sql .= " WHERE c.type = 'Support'";
} elseif ($filter === 'Assigned to me') {
    $sql .= " WHERE c.assigned_to = :user_id";
}

// Query 
$stmt = $conn->prepare($sql);
if ($filter === 'Assigned to me') {
    $stmt->bindParam(':user_id', $user_id);
}
$stmt->execute();
$contacts = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="dash_styles.css">
</head>
<body>
    <h1>Dashboard</h1>
    <!-- Add New Contact button -->
    <div class="new-contact">
            <button onclick="window.location.href='new_contact.php'"><i class="fas fa-plus"></i> Add New Contact</button>
        </div>

    <main>
        <!-- Filters for contacts -->
        <div class="filters">
            <h2>Filter By:</h2>
            <a href="dashboard.php?page=home.php?filter=All Contacts" class="filter-link all"><i class="fas fa-filter"></i> All Contacts</a>
            <a href="dashboard.php?page=home.php?filter=Sales Leads" class="filter-link sales"><i class="fas fa-filter"></i> Sales Leads</a>
            <a href="dashboard.php?page=home.php?filter=Support" class="filter-link support"><i class="fas fa-filter"></i> Support</a>
            <a href="dashboard.php?page=home.php?filter=Assigned to me" class="filter-link assigned"><i class="fas fa-filter"></i> Assigned to me</a>
        </div>

        

        <!-- Contacts Table -->
        <table border="1">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Telephone</th>
                    <th>Company</th>
                    <th>Type</th>
                    <th>Assigned To</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($contacts)): ?>
                    <tr>
                        <td colspan="8">No Contacts Available</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($contacts as $contact): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($contact['title']); ?></td>
                            <td><?php echo htmlspecialchars($contact['firstname'] . ' ' . $contact['lastname']); ?></td>
                            <td><?php echo htmlspecialchars($contact['email']); ?></td>
                            <td><?php echo htmlspecialchars($contact['telephone']); ?></td>
                            <td><?php echo htmlspecialchars($contact['company']); ?></td>
                            <td><?php echo htmlspecialchars($contact['type']); ?></td>
                            <td><?php echo htmlspecialchars($contact['assigned_to']); ?></td>
                            <td><a href="view_details.php?id=<?php echo $contact['id']; ?>" class="view-details-link">View Details</a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
