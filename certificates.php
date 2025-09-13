<?php

include('./config/session.php');
include('./partials/header.php');
include('./config/random_id.php');

if (isset($_SESSION['user'])) {
    $userId = $_SESSION['user']['user_id'];
    $user = $_SESSION['user'];
    $username = $user['username'];
    $email = $user['email'];

    // Fetch all certificates from the database
$sql = "SELECT * FROM certificates WHERE student_email = '$email' ORDER BY created_at DESC";
    $result = mysqli_query($conn, $sql);
    $certificates = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>

<div class="container">
    <div class="d-flex align-items-center my-4">
        <?php if (isset($_SESSION['user'])) : ?>
            <?php include('./partials/sidebar.php'); ?>
            <button class="btn border" id="menuBtn" style="font-size: 20px">&#9776;</button>
            <h2 class="m-0" style="margin-left: 20px !important">All Certificates</h2>
        <?php endif ?>
    </div>

    <?php if (!empty($certificates)) : ?>
        <?php
        // Group certificates by month
        $groupedCertificates = [];
        foreach ($certificates as $certificate) {
            $month = date('F Y', strtotime($certificate['created_at']));
            $groupedCertificates[$month][] = $certificate;
        }
        ?>

        <?php foreach ($groupedCertificates as $month => $monthCertificates) : ?>
            <h3><?php echo $month; ?></h3>
            <div class="table-responsive mb-5">
                <table class="table table-striped table-bordered">
                    <tr>
                        <th scope="col">S/N</th>
                        <th scope="col">STUDENT NAME</th>
                        <th scope="col">COURSE</th>
                        <th scope="col">LEVEL</th>
                        <th scope="col">DATE ISSUED</th>
                        <th scope="col">VIEW CERTIFICATE</th>
                    </tr>
                    <?php $counter = 1; ?>
                    <?php foreach ($monthCertificates as $certificate) : ?>
                        <tr>
                            <td scope="row" style="font-weight: bold;"><?php echo $counter++ ?></td>
                            <td><?php echo $certificate['student_name']; ?></td>
                            <td><?php echo $certificate['program_name']; ?></td>
                            <td><?php echo $certificate['program_level']; ?></td>
                            <td><?php echo date('M d, Y', strtotime($certificate['created_at'])); ?></td>
                            <td>
                                <a href="http://exams.codefestuniversity.online/certificate?id=<?php echo $certificate['certificate_id']; ?>" class="btn btn-primary" target='_blank'>View Certificate</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <p class="mt-3">No certificates found.</p>
    <?php endif; ?>
</div>

<?php include('./partials/footer.php'); ?>
