<?php
include('./config/session.php');
// include('./config/db.php'); // Ensure you include your database connection
include('./partials/header.php');

// Check if a user is logged in
if (isset($_SESSION['user'])) {
    $user_id = $_SESSION['user']['user_id']; // Get the logged-in user's ID

    $status = isset($_GET['status']) ? $_GET['status'] : 'all';

    // Fetch exams that the user is registered for, along with registration details
    $sql = "
    SELECT exams.*, exam_registrations.exam_status, exam_registrations.payment_status, exam_registrations.exam_pin
    FROM exams
    LEFT JOIN exam_registrations ON exams.exam_id = exam_registrations.exam_id
    WHERE exam_registrations.student_id = ? 
";

    // Modify the query based on the requested status
    switch ($status) {
        case 'upcoming':
            // Upcoming exams: exam status is open and user has not written the exam yet
            $sql .= "AND exams.status = 'open' AND exam_registrations.exam_status = 'unwritten' AND exam_registrations.payment_status = 'approved' ";
            break;
        case 'written':
            // Written exams: exam status is either open or closed, and user has written the exam
            $sql .= "AND exam_registrations.exam_status = 'written' AND exam_registrations.payment_status = 'approved' ";
            break;
        case 'missed':
            // Missed exams: exam status is closed and user has not written the exam
            $sql .= "AND exams.status = 'closed' AND exam_registrations.exam_status = 'unwritten' AND exam_registrations.payment_status = 'approved' ";
            break;
        case 'pending':
            // Pending payment status: the registration is pending
            $sql .= "AND exam_registrations.payment_status = 'pending' ";
            break;
        case 'all':
        default:
            // Fetch all exams with approved payment status
            $sql .= "AND exam_registrations.payment_status = 'approved' ";
            break;
    }

    $sql .= "ORDER BY exams.exam_date DESC"; // Order by exam date

    // Prepare and execute the statement
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $user_id); // Bind the user_id
    mysqli_stmt_execute($stmt);
    $examResult = mysqli_stmt_get_result($stmt);
    $examData = mysqli_fetch_all($examResult, MYSQLI_ASSOC);


    function formatDate($dateString)
    {
        return date('M d, Y', strtotime($dateString));
    }
}
// Example to display results
?>
<div class="container">
    <div class="d-flex align-items-center my-4">
        <?php if (isset($_SESSION['user'])) : ?>
            <?php include('./partials/sidebar.php'); ?>
            <button class="btn border" id="menuBtn" style="font-size: 20px">&#9776;</button>
            <h2 class="m-0" style="margin-left: 20px !important">Your Exams</h2>
        <?php endif ?>
    </div>
    <!-- <div class="exam-nav">
        <a href="https://exams.codefest.africa/exams?status=all">All Exams</a>
        <a href="https://exams.codefest.africa/exams?status=upcoming">Upcoming Exams</a>
        <a href="https://exams.codefest.africa/exams?status=written">Written Exams</a>
        <a href="https://exams.codefest.africa/exams?status=missed">Missed Exams</a>
        <a href="https://exams.codefest.africa/exams?status=pending">Pending Exams</a>
    </div> -->

    <div class="row g-3"> <!-- Added g-2 for gap between buttons -->
        <div class="col-12 col-sm-6 col-md-4">
            <a href="https://exams.codefest.africa/exams?status=all" class="btn btn-primary w-100">All Exams</a>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <a href="https://exams.codefest.africa/exams?status=upcoming" class="btn btn-info w-100">Upcoming Exams</a>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <a href="https://exams.codefest.africa/exams?status=written" class="btn btn-success w-100">Written Exams</a>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <a href="https://exams.codefest.africa/exams?status=missed" class="btn btn-danger w-100">Missed Exams</a>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <a href="https://exams.codefest.africa/exams?status=pending" class="btn btn-warning w-100">Pending Exams</a>
        </div>
    </div>

    <div class="table-responsive mb-5">
        <table border="" class="table table-striped table-bordered">
            <tr>
                <th>S/N</th>
                <th>Exam Name</th>
                <th>Program Name</th>
                <th>Exam Date</th>
                <?php if ($status !== 'pending'): ?>
                    <th>Exam PIN</th>
                    <th>Status</th>
                <?php else: ?>
                    <th>Registration Status</th>
                <?php endif; ?>
                <th>Actions</th>
            </tr>

            <?php if (!empty($examData)) : ?>
                <?php $sn = 1; ?>
                <?php foreach ($examData as $data) : ?>
                    <tr>
                        <td><?php echo $sn++; ?></td>
                        <td><?php echo htmlspecialchars($data['exam_name']); ?></td>
                        <td><?php echo htmlspecialchars($data['program_name']); ?></td>
                        <td><?php echo htmlspecialchars(formatDate($data['exam_date'])); ?></td>
                        <?php if ($status !== 'pending'): ?>
                            <td><?php echo htmlspecialchars($data['exam_pin']); ?></td>
                            <td>
                                <!-- Exam Status Display -->
                                <span class="badge <?php echo $data['status'] === 'closed' ? 'text-bg-danger' : 'text-bg-success'; ?>">
                                    <?php echo htmlspecialchars($data['status']); ?>
                                </span>
                                &nbsp;
                                <!-- Exam Registration Status Display -->
                                <span class="badge <?php echo $data['exam_status'] === 'unwritten' ? 'text-bg-danger' : 'text-bg-success'; ?>">
                                    <?php echo htmlspecialchars($data['exam_status']); ?>
                                </span>
                            </td>
                            <!-- <td>
                                <a class="btn btn-primary" href="https://exams.codefest.africa/exam?id=<?php echo urlencode($data['exam_id']); ?>">View Exam</a>
                            </td> -->
                        <?php else: ?>
                            <td><span class="badge text-bg-warning">pending</span></td>
                        <?php endif; ?>
                        <td>
                            <a class="btn btn-primary" href="https://exams.codefest.africa/exam?id=<?php echo urlencode($data['exam_id']); ?>">View Exam</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="7" class="text-center">No records to show</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</div>

<?php include('./partials/footer.php') ?>

<!-- Styles -->
<style>
    .exam-nav {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .exam-nav a {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(0, 0, 0, 0.9);
        color: white;
        text-decoration: none;
        padding: 5px;
        border-radius: 3px;
    }

    .exam-nav a:hover {
        background-color: rgba(0, 0, 0, 0.7);
    }

    /* Styles for the exam status */
    .exam-status {
        padding: 5px 10px;
        color: white;
        border-radius: 3px;
    }

    .open-status {
        background-color: green;
    }

    .closed-status {
        background-color: red;
    }

    .written-status {
        background-color: green;
    }

    .unwritten-status {
        background-color: red;
    }
</style>