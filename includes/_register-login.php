<?php


$user_connected = isset($_SESSION['client_id']) && !empty($_SESSION['client_id']);

if (!$user_connected) {
?>
<style>
    .error-message {
        color: red;
        margin-bottom: 10px;
    }
    .success-message {
        color: green;
        margin-bottom: 10px;
    }
</style>

<!-- Popup for Login and Register -->
<div id="popup" class="popup" style="display: none;">
    <div class="popup-content">
        <span class="close" onclick="closePopup()">&times;</span>
        <div id="form-container">
            <div id="login-form" class="form-section">
                <h2>Login</h2>
                <form id="login-form-submit">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                    <button type="submit" >Login</button>
                    <a href="./forgot_password.php">Forgot Password?</a>
                </form>
                <div id="login-alert" class="alert alert-danger" style="display: none; margin-top: 15px"></div>
                <p>Don't have an account? <a href="#" onclick="showRegister()">Register</a></p>
            </div>
            <div id="register-form" class="form-section" style="display: none;">
                <h2>Register</h2>
                <div class="error-message" style="display: none;"></div>
                
                <form id="register-form-submit">
                    <div class="form-row">
                        <div class="form-column">
                            <label for="first_name">Name:</label>
                            <input type="text" id="first_name" name="first_name" required>

                            <label for="last_name">Last Name:</label>
                            <input type="text" id="last_name" name="last_name" required>

                            <label for="password">Password:</label>
                            <input type="password" id="password" name="password" required>

                            <label for="repeat_password">Repeat Password:</label>
                            <input type="password" id="repeat_password" name="repeat_password" required>

                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-column">
                            <label for="address">Address:</label>
                            <input type="text" id="address" name="address" required>

                            <label for="city">City:</label>
                            <input type="text" id="city" name="city" required>

                            <label for="postal_code">Postal Code:</label>
                            <input type="text" id="postal_code" name="postal_code" required>

                            <label for="country">Country:</label>
                            <select id="country" name="country" required>
                                <option value="" disabled selected>Select your country</option>
                                <option value="fr" data-flag="fr.svg">France</option>
                                <option value="us" data-flag="us.svg">United States</option>
                                <option value="de" data-flag="de.svg">Germany</option>
                                <option value="es" data-flag="es.svg">Spain</option>
                                <option value="it" data-flag="it.svg">Italy</option>
                            </select>

                            <label for="phone_number">Phone Number:</label>
                            <input type="tel" id="phone_number" name="phone_number" required>
                        </div>
                    </div>
                    <button type="submit">Register</button>
                </form>
                <p>Already have an account? <a href="#" onclick="showLogin()">Login</a></p>
            </div>
        </div>
    </div>
</div>

<script>
    function openPopup() {
        document.getElementById("popup").style.display = "block";
    }

    function closePopup() {
        document.getElementById("popup").style.display = "none";
    }

    function showLogin() {
        document.getElementById("login-form").style.display = "block";
        document.getElementById("register-form").style.display = "none";
    }

    function showRegister() {
        document.getElementById("login-form").style.display = "none";
        document.getElementById("register-form").style.display = "block";
    }

    document.getElementById('country').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const flag = selectedOption.getAttribute('data-flag');
        this.style.backgroundImage = url('node_modules/flag-icons/flags/1x1/${flag}');
    });

    // Auto-dismiss the welcome message after 5 seconds
    setTimeout(function() {
        var message = document.getElementById('welcome-message');
        if (message) {
            message.style.display = 'none';
        }
    }, 3000); // 3000 milliseconds = 3 seconds
</script>
<script>
document.getElementById('login-form-submit').addEventListener('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);

    fetch('functions/login.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Reload the page on successful login
        } else {
            var loginAlert = document.getElementById('login-alert');
            loginAlert.textContent = data.message;
            loginAlert.style.display = 'block';
            loginAlert.style.backgroundColor = 'rgba(222, 9, 9, 0.58)';
            loginAlert.style.color = 'black';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        var loginAlert = document.getElementById('login-alert');
        loginAlert.textContent = 'An error occurred. Please try again.';
        loginAlert.style.display = 'block';
        loginAlert.style.backgroundColor = 'rgba(222, 9, 9, 0.58)';
        loginAlert.style.color = 'black';
    });
});

document.getElementById('register-form-submit').addEventListener('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);

    fetch('functions/register.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.querySelector('.error-message').style.display = 'none';
            
            // Clear the registration form
            this.reset();
            
            // Switch to login form after a short delay
            setTimeout(() => {
                showLogin();
                // Display alert in login form
                var loginAlert = document.getElementById('login-alert');
                loginAlert.innerHTML = 'Registration successful! You can now log in.';
                loginAlert.style.display = 'block';
                loginAlert.style.backgroundColor = 'rgba(3, 229, 50, 0.56)';
                loginAlert.style.color = 'black';
            }, 3000); // 3 seconds delay
        } else {
            var errorMessage = document.querySelector('.error-message');
            errorMessage.innerHTML = '<p>' + data.messages.join('</p><p>') + '</p>';
            errorMessage.style.display = 'block';
            errorMessage.style.backgroundColor = 'rgba(222, 9, 9, 0.58)';
            errorMessage.style.color = 'black';
            document.querySelector('.success-message').style.display = 'none';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        var errorMessage = document.querySelector('.error-message');
        errorMessage.innerHTML = '<p style="padding: 10px;">User has been registered with this mail.</p>';
        errorMessage.style.display = 'block';
        errorMessage.style.backgroundColor = 'rgba(222, 9, 9, 0.58)';
        errorMessage.style.color = 'black';
    });
});

</script>

<?php
} else {
    // If the user is connected, don't display anything
    echo '<!-- User is already connected -->';
}
?>
