<?php
ob_start();
include('./config/session.php');
// include('./config/db.php');
include('./partials/header.php');

// Fetch user data from the database
$user_id = $_SESSION['user']['user_id']; // Assuming you have user_id stored in session

$query = "SELECT * FROM users WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

$user = mysqli_fetch_assoc($result);

// Initialize variables with user data
$email = $user['email'];
$full_name = $user['full_name'];
$bio = $user['bio'];
$bio = $user['phone_number'];
$max_bio_length = 100;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $bio = mysqli_real_escape_string($conn, $_POST['bio']);
    $errors = [];

    // Validate inputs
    if (empty($email)) {
        $errors['email'] = 'Email is required';
    }
    if (empty($full_name)) {
        $errors['full_name'] = 'First Name is required';
    }
    if (empty($phone_number)) {
        $errors['phone_number'] = 'Phone Number is required';
    }
    if (empty($bio)) {
        $errors['bio'] = 'Bio is required';
    } elseif (strlen($bio) > $max_bio_length) {
        $errors['bio'] = 'Bio must not exceed 100 characters. Current length: ' . strlen($bio) . '/' . $max_bio_length;
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageFileType = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['png', 'jpg', 'jpeg', 'webp'];

        if (in_array($imageFileType, $allowedExtensions)) {
            $image = $_FILES['image']['tmp_name'];
            $imageData = file_get_contents($image);
            $imageData = mysqli_real_escape_string($conn, $imageData);
        } else {
            $errors['profileImage'] = 'Invalid image type. Only PNG, JPG, JPEG, and WEBP are allowed.';
        }
    } else {
        $errors['profileImage'] = 'Profile image is required.';
    }

    // Update user data if no errors
    if (empty($errors)) {
        $query = "UPDATE users SET email = '$email', full_name = '$full_name', phone_number='$phone_number', bio = '$bio', profile_picture = '$imageData' WHERE user_id = '$user_id'";
        $result = mysqli_query($conn, $query);

        if ($result) {
            $_SESSION['msg'] = "Profile updated successfully!";
            header("Location: https://exams.codefest.africa/dashboard?id={$_SESSION['user']['user_id']}");
            exit();
        } else {
            $err = "Failed to update profile. Please try again.";
        }
    }
}
?>

<div class="container mb-5">

    <div class="d-flex align-items-center my-4">
        <?php if (isset($_SESSION['user'])) : ?>
            <?php include('./partials/sidebar.php'); ?>
            <button class="btn border" id="menuBtn" style="font-size: 20px">&#9776;</button>
            <h2 class="m-0" style="margin-left: 20px !important">Update Profile</h2>
        <?php endif ?>
    </div>

    <div class="container d-flex justify-content-center px-0">
        <div class="col-lg-5 p-0">
            <div class="card border">
                <div class="card-header text-center p-4 bg-primary">
                    <h1 class="m-0 text-light">Update Profile</h1>
                </div>
                <div class="card-body rounded-bottom py-5 px-sm-5 px-3 bg-light">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" name="email" readonly value="<?php echo $email; ?>" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" placeholder="Your Email" />
                            <?php echo isset($errors['email']) ? "<div class='invalid-feedback'>" . $errors['email'] . "</div>" : ""; ?>
                        </div>
                        <div class="form-group">
                            <label for="full_name" class="form-label">Fullname</label>
                            <input type="text" name="full_name" value="<?php echo $full_name; ?>" class="form-control <?php echo isset($errors['full_name']) ? 'is-invalid' : ''; ?>" placeholder="Your fullname" />
                            <?php echo isset($errors['full_name']) ? "<div class='invalid-feedback'>" . $errors['full_name'] . "</div>" : ""; ?>
                        </div>
                        <div class="form-group">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input type="text" name="phone_number" value="<?php echo $phone_number; ?>" class="form-control <?php echo isset($errors['phone_number']) ? 'is-invalid' : ''; ?>" placeholder="Your Phone Number" />
                            <?php echo isset($errors['phone_number']) ? "<div class='invalid-feedback'>" . $errors['phone_number'] . "</div>" : ""; ?>
                        </div>
                        <div class="form-group">
                            <label for="bio" class="form-label">Bio</label>
                            <textarea id="bio" name="bio" placeholder="Enter bio" class="form-control  <?php echo isset($errors['bio']) ? 'is-invalid' : ''; ?>"><?php echo htmlspecialchars($bio); ?></textarea>
                            <?php echo isset($errors['bio']) ? "<div class='invalid-feedback'>" . $errors['bio'] . "</div>" : ""; ?>
                        </div>
                        <div class="form-group">
                            <label for="image" class="form-label">Profile Picture</label>
                            <input type="file" name="image" placeholder="Upload profile image" id="image" accept="image/png, image/jpeg, image/webp" class="form-control  <?php echo isset($errors['profileImage']) ? 'is-invalid' : ''; ?>">
                            <?php echo isset($errors['profileImage']) ? "<div class='invalid-feedback'>" . $errors['profileImage'] . "</div>" : ""; ?>
                        </div>
                        <div>
                            <button class="btn btn-dark btn-block border-0 py-3" type="submit">Update Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('./partials/footer.php'); ?>