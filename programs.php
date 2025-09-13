<?php
include('./config/session.php');
include('./partials/header.php');
// include('./config/db.php'); // Include the database connection

// Get the logged-in user's ID from the session
$user_id = $_SESSION['user']['user_id'];

// Get the status parameter from the URL (defaults to 'all' if not provided)
$status = isset($_GET['status']) ? $_GET['status'] : 'all';

// Base SQL query to fetch programs
$sql = "SELECT 
            program_registrations.program_id, 
            program_registrations.program_name, 
            program_registrations.duration, 
            program_registrations.price, 
            program_registrations.program_pin, 
            program_registrations.registration_time, 
            program_registrations.payment_status 
        FROM program_registrations 
        WHERE program_registrations.student_id = ?";

// Modify the query based on the status filter
if ($status === 'pending') {
    $sql .= " AND program_registrations.payment_status = 'pending'";
} else {
    // Default to showing only 'approved' programs for the 'all' status
    $sql .= " AND program_registrations.payment_status = 'approved'";
}

// Prepare the statement
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $user_id); // Bind user_id as a string
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Check if any results were found
$programs = [];
if ($result && mysqli_num_rows($result) > 0) {
    $programs = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function formatDate($dateString)
{
    return date('M d, Y', strtotime($dateString));
}

?>
<div class="container">
    <div class="d-flex align-items-center my-4">
        <?php if (isset($_SESSION['user'])) : ?>
            <?php include('./partials/sidebar.php'); ?>
            <button class="btn border" id="menuBtn" style="font-size: 20px">&#9776;</button>
            <h2 class="m-0" style="margin-left: 20px !important">Your Programs</h2>
        <?php endif ?>
    </div>

    <!-- <div class="exam-nav">
        <a href="https://exams.codefest.africa/programs?status=approved">Approved Programs</a>
        <a href="https://exams.codefest.africa/programs?status=pending">Pending Programs</a>
    </div> -->

    <div class="row g-3"> <!-- Added g-2 for gap between buttons -->
        <div class="col-12 col-sm-6">
            <a href="https://exams.codefest.africa/programs?status=approved" class="btn btn-success w-100">Approved Programs</a>
        </div>
        <div class="col-12 col-sm-6">
            <a href="https://exams.codefest.africa/programs?status=pending" class="btn btn-warning w-100">Pending Programs</a>
        </div>
    </div>

    <div class="table-responsive mb-5">
        <table border="" class="table table-striped table-bordered">
            <tr>
                <th>S/N</th>
                <th>Program Name</th>
                <th>Duration</th>
                <th>Price</th>
                <?php if ($status !== 'pending') : ?>
                    <th>Program Pin</th> <!-- Only show this column if status is not 'pending' -->
                <?php endif; ?>
                <th>Registration Time</th>
                <th>Payment Status</th>
                <th>Action</th>
            </tr>

            <?php if (count($programs) > 0) : ?>
                <?php $sn = 1; ?>
                <?php foreach ($programs as $program) : ?>
                    <tr>
                        <td><?php echo $sn++; ?></td>
                        <td><?php echo $program['program_name']; ?></td>
                        <td><?php echo $program['duration']; ?></td>
                        <td><?php echo number_format($program['price']); ?></td>
                        <?php if ($status !== 'pending') : ?>
                            <td><?php echo $program['program_pin']; ?></td> <!-- Show Program Pin only if status is not 'pending' -->
                        <?php endif; ?>
                        <td><?php echo formatDate($program['registration_time']); ?></td>
                        <td>
                            <span class="badge <?php echo $program['payment_status'] === 'pending' ? 'text-bg-warning' : 'text-bg-success'; ?>">
                                <?php echo htmlspecialchars($program['payment_status']); ?>
                            </span>
                        </td>
                        <td>
                            <a class="btn btn-primary" href="https://exams.codefest.africa/program?id=<?php echo urlencode($program['program_id']); ?>">View Program</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="<?php echo $status === 'pending' ? 5 : 6; ?>" style="text-align: center;">
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