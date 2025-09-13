<?php
include('./config/session.php');
// include('./config/db.php'); // Ensure you include your database connection
include('./partials/header.php');

// Check if a user is logged in
if (isset($_SESSION['user'])) {
    $user_id = $_SESSION['user']['user_id']; // Get the logged-in user's ID

    // Get the status parameter from the URL (defaults to 'available' if not provided)
    $status = isset($_GET['status']) ? $_GET['status'] : 'available';

    // Base SQL query to fetch exams based on the selected result status
    $sql = "
    SELECT exams.*, exam_registrations.exam_status, exam_registrations.result_status, exam_registrations.exam_pin
    FROM exams
    LEFT JOIN exam_registrations ON exams.exam_id = exam_registrations.exam_id
    WHERE exam_registrations.student_id = ?
    AND exam_registrations.exam_status = 'written'";

    // Modify the query based on the result status filter
    if ($status === 'available') {
        $sql .= " AND exam_registrations.result_status = 'available'";
    } else {
        $sql .= " AND exam_registrations.result_status = 'unavailable'";
    }

    $sql .= " ORDER BY exams.exam_date DESC"; // Order by exam date

    // Prepare and execute the statement
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $user_id); // Bind the user_id
    mysqli_stmt_execute($stmt);
    $examResult = mysqli_stmt_get_result($stmt);
    $examData = mysqli_fetch_all($examResult, MYSQLI_ASSOC);

    // Helper function to format the exam date
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
            <h2 class="m-0" style="margin-left: 20px !important">Your Results</h2>
        <?php endif ?>
    </div>

    <div class="row g-3"> <!-- Added g-2 for gap between buttons -->
        <div class="col-12 col-sm-6">
            <a href="https://exams.codefest.africa/results?status=available" class="btn btn-success w-100">Avaialable Results</a>
        </div>
        <div class="col-12 col-sm-6">
            <a href="https://exams.codefest.africa/results?status=unavailable" class="btn btn-warning w-100">Pending Results</a>
        </div>
    </div>

    <div class="table-responsive mb-5">
        <table border="" class="table table-striped table-bordered">
            <tr>
                <th>S/N</th>
                <th>Exam Name</th>
                <th>Program Name</th>
                <th>Exam Date</th>
                <?php if ($status == 'available') : ?>
                    <th>Exam Pin</th> <!-- Only show this column if status is not 'pending' -->
                <?php endif; ?>
                <th>Result Status</th>
                <?php if ($status == 'available') : ?>
                    <th>Actions</th>
                <?php endif; ?>
            </tr>

            <?php if (!empty($examData)) : ?>
                <?php $sn = 1; ?>
                <?php foreach ($examData as $data) : ?>
                    <tr>
                        <td><?php echo $sn++; ?></td>
                        <td><?php echo htmlspecialchars($data['exam_name']); ?></td>
                        <td><?php echo htmlspecialchars($data['program_name']); ?></td>
                        <td><?php echo htmlspecialchars(formatDate($data['exam_date'])); ?></td>
                        <?php if ($status == 'available') : ?>
                            <td><?php echo htmlspecialchars($data['exam_pin']); ?></td>
                        <?php endif; ?>
                        <td>
                            <span class="badge <?php echo $status == 'available' ? 'text-bg-success' : 'text-bg-danger' ?>">
                                <?php echo htmlspecialchars($data['result_status']); ?>
                            </span>
                        </td>
                        <?php if ($status == 'available') : ?>
                            <td>
                                <a class="btn btn-primary" href="https://exams.codefest.africa/check_result?id=<?php echo urlencode($data['exam_id']); ?>">View Result</a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="7" style="text-align: center;">
                        No records to show
                    </td>
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

    .exam-nav a.active {
        background-color: #4CAF50;
        /* Highlight the active tab */
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