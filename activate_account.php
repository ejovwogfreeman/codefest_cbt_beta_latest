<?php
include('./partials/header.php');
require_once('./config/db.php');

$email = '';
$msg = '';
$errors = [];

// Check if email is passed in the URL
if (isset($_GET['email'])) {
    $email = $_GET['email'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format.';
    } else {
        // Check if the email exists in the database
        $emailCheckQuery = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $emailCheckQuery);

        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            // Check the user's status
            if ($user['status'] === 'inactive') {
                // Update status to active
                $updateStatusQuery = "UPDATE users SET status = 'active' WHERE email = '$email'";
                if (mysqli_query($conn, $updateStatusQuery)) {
                    $msg = 'Your account has been activated successfully!';
                } else {
                    $errors['database'] = 'Error updating user status. Please try again later.';
                }
            } else {
                // User is already active
                $msg = 'User is already active.';
            }
        } else {
            // Email does not exist in the database
            $msg = 'No user found with this email.';
        }
    }
} else {
    $msg = 'No email provided in the URL.';
}

// Close the database connection
mysqli_close($conn);
?>

<div class="container my-5">
    <div class="container d-flex justify-content-center px-0">
        <div class="col-lg-5 p-0">
            <div class="card border">
                <!-- Card Header -->
                <div class="card-header text-center p-4 bg-primary">
                    <h1 class="m-0 text-light">Activate Account</h1>
                </div>

                <!-- Card Body -->
                <div class="card-body rounded-bottom py-5 px-sm-5 px-3 bg-light">
                    <form method="POST" class="needs-validation" novalidate>
                        <?php
                        if (!empty($msg)) {
                            echo "<div class='alert alert-success'>{$msg}</div>";
                        } elseif (!empty($errors)) {
                            foreach ($errors as $error) {
                                echo "<div class='alert alert-danger'>{$error}</div>";
                            }
                        } else {
                            echo "<div class='text-primary text-center mb-3' style='font-size: 20px'>Loading...</div>";
                        }
                        ?>

                        <?php if (!empty($msg) && strpos($msg, 'activ') !== false) { ?>
                            <div class="bottom-box">
                                <a href=" https://exams.codefest.africa/login" class="btn btn-dark btn-block border-0 py-3">Login Now</a>
                            </div>
                        <?php } ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('./partials/footer.php') ?>