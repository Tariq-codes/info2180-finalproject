<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="dash_styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="dolphin.png" type="image/png">
</head>
<body>
    <header>
        <div class="header-content">
            <h1>Dolphin CRM</h1>
        </div>
    </header>

    <div class="sidebar">
        <button id="homeBtn">
            <i class="fas fa-home"></i> Home
        </button>
        <button id="NewContactBtn">
            <i class="fas fa-user-plus"></i> New Contact
        </button>
        <button id="viewUserBtn">
            <i class="fas fa-users"></i> View Users
        </button>
        <hr>
        <button onclick="window.location.href='logout.php'">
            <i class="fas fa-sign-out-alt"></i> Logout
        </button>
    </div>

    <div class="content">
        <div id="dashboardContent">
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const homeBtn = document.getElementById("homeBtn");
            const viewUserBtn = document.getElementById("viewUserBtn");
            const newContactBtn = document.getElementById("NewContactBtn");
            const dashboardContent = document.getElementById("dashboardContent");

            // Function to load content into the dashboardContent div
            function loadPage(page) {
                const xhr = new XMLHttpRequest();
                xhr.open("GET", page, true);

                xhr.onload = function () {
                    if (xhr.status === 200) {
                        console.log("Page loaded successfully:", page);
                        dashboardContent.innerHTML = xhr.responseText;

                        // Update the URL without reloading the page
                        history.pushState(null, "", `?page=${page}`);
                    } else {
                        console.error("Failed to load page:", page, xhr.statusText);
                        dashboardContent.innerHTML = `<p>Error loading page: ${xhr.statusText}</p>`;
                    }
                };

                xhr.onerror = function () {
                    console.error("Network error while loading page:", page);
                    dashboardContent.innerHTML = "<p>Network error. Please try again later.</p>";
                };

                xhr.send();
            }


            // Event listeners for buttons
            homeBtn.addEventListener("click", () => {
                console.log("Home button clicked");
                loadPage("home.php");
            });

            viewUserBtn.addEventListener("click", () => {
                console.log("View Users button clicked");
                loadPage("view_users.php");
            });

            newContactBtn.addEventListener("click", () => {
                console.log("New Contact button clicked");
                loadPage("new_contact.php"); 
            });

            //back and forward buttons
            window.addEventListener("popstate", () => {
                const urlParams = new URLSearchParams(window.location.search);
                const page = urlParams.get('page');
                if (page) {
                    loadPage(page);
                }
            });

            // check current page
            const urlParams = new URLSearchParams(window.location.search);
            const page = urlParams.get('page');
            if (page) {
                loadPage(page);
            }
        });
    </script>

</body>
</html>
