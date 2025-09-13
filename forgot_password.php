<?php


include('./partials/header.php');
// require_once('./config/db.php');
include('mail.php');

$email = '';
$errors = [];
$password = '';

if (isset($_SESSION['user'])) {
    header("Location: https://capitalstreamexchange.com/dashboard?id={$_SESSION['user']['user_id']}");
}

$emailSubject = 'FORGOT PASSWORD';
$htmlFilePath = './html_mails/reset_password.html';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate email
    if (empty($_POST["email"])) {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format.';
    } else {
        $email = htmlspecialchars($_POST["email"]);
    }

    // If no errors, proceed to send the reset password email
    if (empty($errors)) {
        $email = mysqli_real_escape_string($conn, $email);

        $sql_email = "SELECT * FROM users WHERE email = '$email'";

        $sql_email_query = mysqli_query($conn, $sql_email);

        if ($sql_email_query) {

            if (mysqli_num_rows($sql_email_query) > 0) {

                $user = mysqli_fetch_assoc($sql_email_query);

                // Send the email
                sendEmail($email, $emailSubject, $htmlFilePath, $email, $password);

                $message = "An email has been sent to your email \"$email\" with a link to reset your password";

                $_SESSION['msg'] = $message;
            } else {
                $errors['email']  = "A user with this email does not exist";
            }

            mysqli_free_result($sql_email_query);
        }
    }
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
                    <h1 class="m-0 text-light">Forgot Password</h1>
                </div>

                <!-- Card Body -->
                <div class="card-body rounded-bottom py-5 px-sm-5 px-3 bg-light">
                    <form method="POST" class="needs-validation" novalidate>
                        <?php if (isset($_SESSION['msg'])): ?>
                            <div class="alert alert-success" role="alert">
                                <?php echo $_SESSION['msg'];
                                unset($_SESSION['msg']); ?>
                            </div>
                        <?php endif; ?>
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" placeholder="Enter your email" value="<?php echo $email; ?>" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>">
                            <?php echo isset($errors['email']) ? "<div class='invalid-feedback'>" . $errors['email'] . "</div>" : ""; ?>
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
<?php
include('./partials/footer.php');
?>