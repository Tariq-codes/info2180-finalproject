document.getElementById('login').addEventListener('submit', function(event) {
    event.preventDefault();

    // Get the form data
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    // Prepare the form data to send
    const formData = new FormData();
    formData.append('email', email);
    formData.append('password', password);

    // Send the AJAX request to the PHP script
    fetch('login.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            document.getElementById('error-message').innerText = data.error;
            document.getElementById('error-message').style.display = 'block';
        } else if (data.success) {
            // successs
            window.location.href = 'dashboard.php';
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});

