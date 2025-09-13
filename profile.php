<?php

include('./config/session.php');
include('./partials/header.php');
// include('./config/db.php');

if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    $userId = $user['user_id'];
}

// Function to extract user ID from URL using regex
if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    $userId = $user['user_id'];
}

// Function to get user ID from the URL using regular expression
$userid = '';

// Check if the 'id' parameter is set in the GET request
if (isset($_GET['id'])) {
    // Sanitize and retrieve the 'id' parameter
    $userid = htmlspecialchars($_GET['id']); // Sanitize output to prevent XSS
}

// Debug: Print the result of the regex match
if ($userId === null) {
    echo "No user ID found in the URL.<br>";
} else {
    // echo "User ID: $userId<br>";
}

// Check if user ID is set in the URL
if ($userid) {
    // Fetch the user with the given ID
    $sqlUser = "SELECT * FROM users WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sqlUser);
    mysqli_stmt_bind_param($stmt, 's', $userid); // Assuming user_id is a string (UUID or similar)
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $profileUser = mysqli_fetch_assoc($result);

    if ($profileUser) {
        // Extract user details
        $fullName = $profileUser['full_name'] ?? '';
        $userName = $profileUser['username'] ?? '';
        $email = $profileUser['email'] ?? '';
        $phoneNumber = $profileUser['phone_number'] ?? '';
        $address = $profileUser['address'] ?? '';
        $birthday = $profileUser['date_of_birth'] ?? '';
        $imageData = $profileUser['profile_picture'] ?? '';

        // Generate image source for profile picture if it exists
        if (!empty($imageData)) {
            $imageInfo = getimagesizefromstring($imageData);
            if ($imageInfo !== false) {
                $imageFormat = $imageInfo['mime'];
                $img_src = "data:$imageFormat;base64," . base64_encode($imageData);
            } else {
                $img_src = ""; // Handle case where image data is invalid or not found
            }
        } else {
            $img_src = ""; // Handle case where image data is empty
        }

        // Set header text
        if (isset($userId)) {
            $headerText = ($userId == $userid) ? "Your Profile" : "$userName's Profile";
        } else {
            $headerText = "$userName's Profile";
        }
    } else {
        // If no user found, redirect to the dashboard or show an error message
        $_SESSION['msg'] = "User with this ID does not exist";
        header("Location: https://exams.codefest.africa/dashboard?id={$_SESSION['user']['user_id']}");
        exit();
    }
} else {
    // If no user ID is found in the URL, redirect to the dashboard or show an error message
    $_SESSION['msg'] = "Invalid profile URL";
    header("Location: https://exams.codefest.africa/dashboard?id={$_SESSION['user']['user_id']}");
    exit();
}
?>


<div class="container">

    <div class="d-flex align-items-center my-4">
        <?php if (isset($_SESSION['user'])) : ?>
            <?php include('./partials/sidebar.php'); ?>
            <button class="btn border" id="menuBtn" style="font-size: 20px">&#9776;</button>
            <h2 class="m-0" style="margin-left: 20px !important"><?php echo ucwords($headerText); ?></h2>
        <?php endif ?>
    </div>

    <div class="card custom-card py-4 px-sm-4 px-3">
        <?php
        if (!empty($imageData)) {
            $imageInfo = getimagesizefromstring($imageData);

            if ($imageInfo !== false) {
                $imageFormat = $imageInfo['mime'];
                $img_src = "data:$imageFormat;base64," . base64_encode($imageData);
            } else {
                echo "Unable to determine image type.";
            }
        } else {
            // If no image is available, use the default image
            $img_src = "images/default.jpg";
            $url = $_SERVER['REQUEST_URI'];

            if (strpos($url, 'admin') !== false) {
                $img_src = "../images/default.jpg";
            } else {
                $img_src = "images/default.jpg";
            }
        }
        ?>
        <ul class="list-group list-group-flush">
            <img class="border rounded-circle d-block m-auto" width="100px" height="100px" style="object-fit: cover;" src="<?php echo $img_src; ?>" alt="<?php echo $user['username']; ?>">
            <li class="list-group-item px-0">
                <strong>Full Name:</strong>
                <p class="mb-0"><?php echo $fullName ?></p>
            </li>
            <li class="list-group-item px-0">
                <strong>Username:</strong>
                <p class="mb-0"><?php echo $userName ?></p>
            </li>
            <li class="list-group-item px-0">
                <strong>Email:</strong>
                <p class="mb-0"><?php echo $email ?></p>
            </li>
            <li class="list-group-item px-0">
                <strong>Phone Number:</strong>
                <p class="mb-0"><?php echo $phoneNumber ?></p>
            </li>
            <li class="list-group-item px-0">
                <strong>Address:</strong>
                <p class="mb-0"><?php echo $address ?></p>
            </li>
            <li class="list-group-item px-0">
                <strong>Birthday:</strong>
                <p class="mb-0"><?php echo $birthday ?></p>
            </li>
        </ul>
    </div>

</div>

<?php include('./partials/footer.php'); ?>