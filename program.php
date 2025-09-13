<?php
include('./config/session.php');
// include('./config/db.php');
include('./partials/header.php');

if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    $userId = $user['user_id'];
}

// Get the program ID from the query parameter
$programId = isset($_GET['id']) ? $_GET['id'] : '';

// Fetch program details and related program from the database
$sql_program = "
    SELECT programs.*, programs.program_id, programs.program_name 
    FROM programs 
    WHERE programs.program_id = ?";
$stmt = mysqli_prepare($conn, $sql_program);
mysqli_stmt_bind_param($stmt, 's', $programId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) > 0) {
    $program = mysqli_fetch_assoc($result);
    $programId = $program['program_id']; // Get the program ID
} else {
    echo "Program not found.";
    exit();
}

// Fetch enrolled students for the program
$sql_enrolled_students = "
    SELECT users.user_id, users.full_name
    FROM program_registrations 
    JOIN users ON program_registrations.student_id = users.user_id 
    WHERE program_registrations.program_id = ?";
$stmt_students = mysqli_prepare($conn, $sql_enrolled_students);
mysqli_stmt_bind_param($stmt_students, 's', $programId);
mysqli_stmt_execute($stmt_students);
$result_students = mysqli_stmt_get_result($stmt_students);

// Fetch courses under the program
$sql_courses = "SELECT * FROM exams WHERE program_id = ?";
$stmt_courses = mysqli_prepare($conn, $sql_courses);
mysqli_stmt_bind_param($stmt_courses, 's', $programId);
mysqli_stmt_execute($stmt_courses);
$result_courses = mysqli_stmt_get_result($stmt_courses);


// Fetch the exam registration details for the user
$sql_registration = "SELECT program_pin, payment_status FROM program_registrations WHERE program_id = ? AND student_id = ?";
$stmt_registration = mysqli_prepare($conn, $sql_registration);
mysqli_stmt_bind_param($stmt_registration, 'ss', $programId, $userId);
mysqli_stmt_execute($stmt_registration);
$result_registration = mysqli_stmt_get_result($stmt_registration);

// Initialize variables
$program_pin = null;
$payment_status = '';

// Check if the user has a registration for this exam
if ($result_registration && mysqli_num_rows($result_registration) > 0) {
    $registration = mysqli_fetch_assoc($result_registration);
    $program_pin = $registration['program_pin'];
    $payment_status = $registration['payment_status'];
}

// Utility functions
function convertMinutesToHours($minutes)
{
    $hours = intdiv($minutes, 60);
    $minutes = $minutes % 60;
    if ($hours > 0) {
        return "{$hours} HRS " . ($minutes > 0 ? "{$minutes} MINS" : "");
    } else {
        return "{$minutes} MINS";
    }
}

function formatTime($time)
{
    return date('h:i A', strtotime($time));
}

function formatDate($dateString)
{
    return date('M d, Y', strtotime($dateString));
}

function showFlyingAlert($message, $className)
{
    echo <<<EOT
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var alertDiv = document.createElement("div");
            alertDiv.className = "{$className}";
            alertDiv.innerHTML = "{$message}";
            document.body.appendChild(alertDiv);

            // Triggering reflow to enable animation
            alertDiv.offsetWidth;

            // Add a class to trigger the fly-in animation
            alertDiv.style.left = "10px";

            // Remove the fly-in style after 3 seconds
            setTimeout(function() {
                alertDiv.style.left = "10px";
            }, 2000);

            // Add a class to trigger the fly-out animation after 3 seconds
            setTimeout(function() {
                alertDiv.style.left = "-300px";
            }, 4000);

            // Remove the element after the total duration of the animation (6 seconds)
            setTimeout(function() {
                alertDiv.remove();
            }, 6000);
        });
    </script>
EOT;
}

if (isset($_SESSION['msg'])) {
    $message = $_SESSION['msg'];
    if (stristr($message, "successfully") || stristr($message, "Successfully") || stristr($message, "SUCCESSFUL")) {
        showFlyingAlert($message, "flying-success-alert");
        unset($_SESSION['msg']);
    } else {
        showFlyingAlert($message, "flying-danger-alert");
        unset($_SESSION['msg']);
    }
}
?>

<style>
    .flying-success-alert {
        position: fixed;
        z-index: 111111;
        top: 15px;
        left: -300px;
        background-color: var(--primary);
        color: #fff;
        padding: 10px;
        border-radius: 5px;
        transition: left 1.5s ease-in-out;
    }

    .flying-danger-alert {
        position: fixed;
        z-index: 111111;
        top: 15px;
        left: -300px;
        background-color: #FF5252;
        color: #fff;
        padding: 10px;
        border-radius: 5px;
        transition: left 1.5s ease-in-out;
    }
</style>

<div class="container">
    <div class="d-flex align-items-center my-4">
        <?php if (isset($_SESSION['user'])) : ?>
            <?php include('./partials/sidebar.php'); ?>
            <button class="btn border" id="menuBtn" style="font-size: 20px">&#9776;</button>
            <h2 class="m-0" style="margin-left: 20px !important">Program Details</h2>
        <?php endif ?>
    </div>

    <div class="card custom-card py-4 px-sm-4 px-3">
        <ul class="list-group list-group-flush">
            <li class="list-group-item px-0">
                <strong>Program Title:</strong>
                <p class="mb-0"><?php echo htmlspecialchars($program['program_name']); ?></p>
            </li>

            <li class="list-group-item px-0">
                <strong>Description:</strong>
                <p class="mb-0"><?php echo htmlspecialchars($program['program_description']); ?></p>
            </li>

            <li class="list-group-item px-0">
                <strong>Program Fee:</strong>
                <p class="mb-0">NGN&nbsp;<?php echo htmlspecialchars(number_format($program['price'])); ?></p>
            </li>

            <li class="list-group-item px-0">
                <strong>Date Uploaded:</strong>
                <p class="mb-0"><?php echo htmlspecialchars(formatDate($program['created_at'])); ?></p>
            </li>

            <li class="list-group-item px-0">
                <strong>Enrolled Students:</strong>
                <p class="mb-0">
                    <?php if (mysqli_num_rows($result_students) > 0) : ?>
                        <?php echo mysqli_num_rows($result_students); ?>
                        (<a href="https://exams.codefest.africa/admin/enrolled_students_program?id=<?php echo urlencode($program['program_id']); ?>">View students</a>)
                    <?php else : ?>
                        No students enrolled.
                    <?php endif; ?>
                </p>
            </li>

            <li class="list-group-item px-0">
                <strong>Exams Under Program:</strong>
                <p class="mb-0">
                    <?php if (mysqli_num_rows($result_courses) > 0) : ?>
                        <?php echo mysqli_num_rows($result_courses); ?>
                        (<a href="https://exams.codefest.africa/admin/program_exams?id=<?php echo urlencode($program['program_id']); ?>">View Exams</a>)
                    <?php else : ?>
                        No courses found for this program.
                    <?php endif; ?>
                </p>
            </li>
        </ul>

        <?php if ($payment_status === 'approved' && $program_pin) : ?>
        <?php else: ?>
            <div class="card-body d-flex justify-content-between pb-0 px-0">
                <button id="openModal" class="btn btn-primary btn-block">MAKE PAYMENT</button>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Simple Modal -->
<div id="loginSuccessModal" class="modal" style="display: none;">
    <div class="modal-content" style="height: 60%;">
        <div class="modal-header">
            <h2>Codefest CBT</h2>
            <span class="btn btn-primary" id="closeModalIcon">&times;</span>
        </div>
        <div class="modal-body">
            <p>You are required to make payment to the account details below and send a proof of payment via the form below.</p>
            <ul class="list-group">
                <li class="list-group-item d-flex justify-content-between"><strong style="flex: 2">Bank Name</strong><strong style="flex: 1">:</strong><strong style="flex: 3">Zenith Bank</strong></li>
                <li class="list-group-item d-flex justify-content-between"><strong style="flex: 2">Account Name</strong><strong style="flex: 1">:</strong><strong style="flex: 3">CODEFEST International Ltd</strong></li>
                <li class="list-group-item d-flex justify-content-between"><strong style="flex: 2">Bank Number</strong><strong style="flex: 1">:</strong><strong style="flex: 3">1016084310</strong></li>
            </ul>

            <a href="https://wa.me/2349061817858" class="mt-3 btn btn-primary btn-block">
                Send Proof
            </a>

            <!-- <div class="card border mt-4">
                <div class="card-header text-center p-4 bg-primary">
                    <h1 class="m-0 text-light">Send Proof</h1>
                </div>
                <div class="card-body rounded-bottom py-5 px-sm-5 px-3 bg-light">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="text" name="amount" value="<?php echo $amount; ?>" class="form-control <?php echo isset($errors['amount']) ? 'is-invalid' : ''; ?>" placeholder="Your fullname" />
                            <?php echo isset($errors['amount']) ? "<div class='invalid-feedback'>" . $errors['amount'] . "</div>" : ""; ?>
                        </div>
                        <div class="form-group">
                            <label for="image" class="form-label">Proof/Reciept Of Payment</label>
                            <input type="file" name="image" placeholder="Upload proof image" id="image" accept="image/png, image/jpeg, image/webp" class="form-control  <?php echo isset($errors['proofImage']) ? 'is-invalid' : ''; ?>">
                            <?php echo isset($errors['proofImage']) ? "<div class='invalid-feedback'>" . $errors['proofImage'] . "</div>" : ""; ?>
                        </div>
                        <div>
                            <button class="btn btn-dark btn-block border-0 py-3" type="submit">Send Now</button>
                        </div>
                    </form>
                </div>
            </div> -->
        </div>
        <div class="modal-footer">
            <button id="closeModalButton" class="btn btn-primary">Close</button>
        </div>
    </div>
</div>

<?php include('./partials/footer.php'); ?>

<script>
    document.getElementById('openModal').addEventListener('click', Modal); // Correctly passing the function

    function Modal() {
        var modal = document.getElementById('loginSuccessModal');
        var closeButton = document.getElementById('closeModalButton');
        var closeIcon = document.getElementById('closeModalIcon');

        // Display the modal
        modal.style.display = 'flex';

        // Close modal when the close button is clicked
        closeButton.addEventListener('click', function() {
            modal.style.display = 'none';
        });
        closeIcon.addEventListener('click', function() {
            modal.style.display = 'none';
        });

        // Close modal when anywhere outside the modal is clicked
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        };
    }
</script>