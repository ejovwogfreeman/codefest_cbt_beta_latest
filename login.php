<?php

ob_start();

// include('./config/session.php');
include('./partials/header.php');
// require_once('./config/db.php');

$email = $password = '';
$errors = [];

if (isset($_SESSION['user'])) {
    header("Location: /dashboard?id={$_SESSION['user']['user_id']}");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate email
    if (empty($_POST["email"])) {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format.';
    } else {
        $email = htmlspecialchars($_POST["email"]);
    }

    // Validate password
    if (empty($_POST["password"])) {
        $errors['password'] = 'Password is required.';
    } else {
        $password = $_POST["password"];
    }

    // If no errors, proceed to login the user
    if (empty($errors)) {
        // Check if the email exists
        $emailCheckQuery = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $emailCheckQuery);

        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            // Check if the account is inactive
            if ($user['status'] === 'inactive') {
                $errors['status'] = 'Account is inactive, please check your email to activate your account.';
                $err = $errors['status'];
            } else {
                // Verify the password
                if (password_verify($password, $user['password'])) {
                    $_SESSION['msg'] = 'Login Successful';
                    $_SESSION['login_success_modal'] = ' <br>
<h4>Welcome to Codefest CBT</h4>
<div class="mb-3">
<strong>Official website:</strong> exams.codefestuniversity.online<br>
<strong>Official customer service:</strong> exams@codefest.africa <br>
</div>
 <ul class="list-group">
  <li class="list-group-item">Codefest Exam Portal</li>
  <li class="list-group-item">A True Test Of Knowledge</li>
  <li class="list-group-item">Know Your Strength</li>
  <li class="list-group-item">Get Certified</li>
</ul><br><br>
';
                    $_SESSION['user'] = $user;
                    header("Location: https://exams.codefest.africa/dashboard?id={$user['user_id']}");
                    exit();
                } else {
                    $errors['password'] = 'Invalid password.';
                }
            }
        } else {
            $errors['email'] = 'No user found with this email.';
        }
    }

    // Close the database connection
    mysqli_close($conn);
}

ob_end_flush();

?>

<!-- Registration Start -->
<div class="container-fluid py-5 px-0">
    <div class=" container h-100 d-flex justify-content-center align-items-center p-0">
        <div class="col-lg-5">
            <div class="card border">
                <div class="card-header text-center p-4 bg-primary">
                    <h1 class="m-0 text-light">Sign In Now</h1>
                </div>
                <div class="card-body rounded-bottom py-5 px-sm-5 px-3 bg-light">
                    <form method="POST">
                        <?php if (!empty($err)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $err; ?>
                            </div>
                        <?php endif; ?>
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" name="email" value="<?php echo $email; ?>" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" placeholder="Your Email" />
                            <?php echo isset($errors['email']) ? "<div class='invalid-feedback'>" . $errors['email'] . "</div>" : ""; ?>
                        </div>
                        <div class="form-group">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" value="<?php echo $password; ?>" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" placeholder="Your Password" />
                            <?php echo isset($errors['password']) ? "<div class='invalid-feedback'>" . $errors['password'] . "</div>" : ""; ?>
                        </div>
                        <div>
                            <button class="btn btn-dark btn-block border-0 py-3" type="submit">Sign In Now</button>
                        </div>
                        <small class="d-block text-center mt-3">New Here? <a href="https://exams.codefest.africa/register">REGISTER</a></small>
                        <small class="d-block text-center mt-3"><a href="https://exams.codefest.africa/forgot_password">FORGOT PASSWORD?</a></small>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Registration End -->

<?php include('./partials/footer.php') ?>