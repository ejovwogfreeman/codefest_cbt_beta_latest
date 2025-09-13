<?php
include('./config/session.php');
include('./partials/header.php')
?>

<div class="container">
    <div class="d-flex align-items-center my-4">
        <?php if (isset($_SESSION['user'])) : ?>
            <?php include('./partials/sidebar.php'); ?>
            <button class="btn border" id="menuBtn" style="font-size: 20px">&#9776;</button>
            <h2 class="m-0" style="margin-left: 20px !important">Your Courses</h2>
        <?php endif ?>
    </div>

    <div class="table-responsive border">
        <table border="" class="table" style="width: 1000px;">
            <tr>
                <th>S/N</th>
                <th>USERNAME</th>
                <th>EMAIL</th>
                <th>AMOUNT (USD)</th>
                <th>MODE</th>
                <th>TYPE</th>
                <th>DATE</th>
                <th>STATUS</th>
            </tr>
            <!-- <?php $sn = 1; ?>
        <?php foreach ($courses as $course) : ?>
        <?php endforeach; ?> -->
        </table>
    </div>
</div>