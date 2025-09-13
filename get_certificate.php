<?php
ob_start();
include('./config/session.php');
// include('./config/db.php');
include('./partials/header.php');

// Fetch user data from the database
$user_id = $_SESSION['user']['user_id']; // Assuming you have user_id stored in session
?>

<div class="container mb-5">

    <div class="d-flex align-items-center my-4">
        <?php if (isset($_SESSION['user'])) : ?>
            <?php include('./partials/sidebar.php'); ?>
            <button class="btn border" id="menuBtn" style="font-size: 20px">&#9776;</button>
            <h2 class="m-0" style="margin-left: 20px !important">My Certifications</h2>
        <?php endif ?>
    </div>

    <div class="container d-flex justify-content-center px-0">
        <h1>Coming Soon</h1>
    </div>
</div>

<?php include('./partials/footer.php'); ?>