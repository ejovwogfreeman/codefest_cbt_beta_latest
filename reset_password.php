<?php

// Start output buffering
ob_start();

// include('./config/session.php');
// Ensure session is started
session_start();
include('./partials/header.php');
// include('./config/db.php');

// Redirect to dashboard if user is already logged in
if (isset($_SESSION['user'])) {
    header("Location: /dashboard/{$_SESSION['user']['user_id']}");
    exit();
}

$email = ''; // Initialize email variable

// Get the current URL
$currentUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

// Parse the URL and extract the query string
$parsedUrl = parse_url($currentUrl);
if (isset($parsedUrl['query'])) {
    // Parse the query string to get individual parameters
    parse_str($parsedUrl['query'], $queryParams);

    // Check if 'email' parameter exists in the URL
    if (isset($queryParams['email'])) {
        $email = htmlspecialchars($queryParams['email']); // Sanitize the email
    }
}

// Initialize database connection (uncomment and adjust as necessary)
// $conn = mysqli_connect('localhost', 'username', 'password', 'database');

if ($email) {
    // Fetch user data from the database
    $email = mysqli_real_escape_string($conn, $email);
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }

    $user = mysqli_fetch_assoc($result);
} else {
    echo "Email parameter is not found in the URL.";
    exit(); // Stop execution if no email is provided
}

$errors = [];
$password = '';
$confirmPassword = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST["password"])) {
        $errors['password'] = 'Password is required.';
    } elseif (strlen($_POST["password"]) < 8) {
        $errors['password'] = 'Password must be at least 8 characters.';
    } else {
        $password = $_POST["password"];
    }

    // Validate confirm password
    if (empty($_POST["confirmPassword"])) {
        $errors['confirmPassword'] = 'Confirm password is required.';
    } elseif ($_POST["confirmPassword"] !== $_POST["password"]) {
        $errors['confirmPassword'] = 'Passwords do not match.';
    } else {
        $confirmPassword = $_POST["confirmPassword"];
    }

    // Update user password if no errors
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $query = "UPDATE users SET password = '$hashedPassword' WHERE user_id = '{$user['user_id']}'";
        $result = mysqli_query($conn, $query);

        if ($result) {
            $_SESSION['msg'] = "Password reset successfully!";
            header("Location: https://exams.codefest.africa/login");
            exit();
        } else {
            $err = "Failed to reset password. Please try again.";
        }
    }
}
?>

<div class="container my-5">
    <div class="container d-flex justify-content-center px-0">
        <div class="col-lg-5 p-0">
            <div class="card border">
                <!-- Card Header -->
                <div class="card-header text-center p-4 bg-primary">
                    <h1 class="m-0 text-light">Reset Password</h1>
                </div>

                <!-- Card Body -->
                <div class="card-body rounded-bottom py-5 px-sm-5 px-3 bg-light">
                    <form method="POST" action="">
                        <?php if (isset($_SESSION['msg'])): ?>
                            <div class="alert alert-success" role="alert">
                                <?php
                                echo $_SESSION['msg'];
                                unset($_SESSION['msg']);
                                ?>
                            </div>
                        <?php endif; ?>
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" name="email" readonly value="<?php echo $email; ?>" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" placeholder="Your Email" />
                            <?php echo isset($errors['email']) ? "<div class='invalid-feedback'>" . $errors['email'] . "</div>" : ""; ?>
                        </div>
                        <div class="form-group">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" id="password" name="password" placeholder="Enter password" value="<?php echo htmlspecialchars($password); ?>" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>">
                            <?php echo isset($errors['password']) ? "<div class='invalid-feedback'>" . $errors['password'] . "</div>" : ""; ?>
                        </div>
                        <div class="form-group">
                            <label for="confirmPassword" class="form-label">Confirm Password</label>
                            <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Enter password again" value="<?php echo htmlspecialchars($confirmPassword); ?>" class="form-control <?php echo isset($errors['confirmPassword']) ? 'is-invalid' : ''; ?>">
                            <?php echo isset($errors['confirmPassword']) ? "<div class='invalid-feedback'>" . $errors['confirmPassword'] . "</div>" : ""; ?>
                        </div>
                        <div>
                            <button class="btn btn-dark btn-block border-0 py-3" type="submit">Submit</button>
                        </div>
                        <small class="d-block text-center mt-3">Back to <a href="https://exams.codefest.africa/login">LOGIN</a></small>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('./partials/footer.php'); ?>