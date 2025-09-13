<?php
include('./config/session.php');
// include('./config/db.php');
include('./partials/header.php');

if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    $userId = $user['user_id'];
}

// Get the exam ID from the query parameter
$examId = isset($_GET['id']) ? $_GET['id'] : '';

// Fetch exam details from the database
$sql = "SELECT * FROM exams WHERE exam_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 's', $examId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) > 0) {
    $exam = mysqli_fetch_assoc($result);
} else {
    echo "Exam not found.";
    exit();
}

// Fetch the exam registration details for the user
$sql_registration = "SELECT exam_pin, payment_status, exam_status, result_status FROM exam_registrations WHERE exam_id = ? AND student_id = ?";
$stmt_registration = mysqli_prepare($conn, $sql_registration);
mysqli_stmt_bind_param($stmt_registration, 'ss', $examId, $userId);
mysqli_stmt_execute($stmt_registration);
$result_registration = mysqli_stmt_get_result($stmt_registration);

// Initialize variables
$exam_pin = null;
$payment_status = '';

// Check if the user has a registration for this exam
if ($result_registration && mysqli_num_rows($result_registration) > 0) {
    $registration = mysqli_fetch_assoc($result_registration);
    $exam_pin = $registration['exam_pin'];
    $payment_status = $registration['payment_status'];
    $exam_status = $registration['exam_status'];
    $result_status = $registration['result_status'];
}

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

?>

<div class="container">
    <div class="d-flex align-items-center my-4">
        <?php if (isset($_SESSION['user'])) : ?>
            <?php include('./partials/sidebar.php'); ?>
            <button class="btn border" id="menuBtn" style="font-size: 20px">&#9776;</button>
            <h2 class="m-0" style="margin-left: 20px !important">Exam Details</h2>
        <?php endif ?>
    </div>

    <div class="card custom-card py-4 px-sm-4 px-3">
        <ul class="list-group list-group-flush">
            <li class="list-group-item px-0">
                <strong>Exam Title:</strong>
                <p class="mb-0"><?php echo htmlspecialchars($exam['exam_name']); ?></p>
            </li>
            <li class="list-group-item px-0">
                <strong>Program:</strong>
                <p class="mb-0"><?php echo htmlspecialchars($exam['program_name']); ?></p>
            </li>
            <li class="list-group-item px-0">
                <strong>Description:</strong>
                <p class="mb-0"><?php echo htmlspecialchars($exam['exam_description']); ?></p>
            </li>
            <li class="list-group-item px-0">
                <strong>Total Questions:</strong>
                <p class="mb-0"><?php echo htmlspecialchars($exam['total_questions']); ?></p>
            </li>
            <li class="list-group-item px-0">
                <strong>Duration:</strong>
                <p class="mb-0"><?php echo htmlspecialchars(convertMinutesToHours($exam['duration_in_minutes'])); ?></p>
            </li>
            <li class="list-group-item px-0">
                <strong>Exam Date:</strong>
                <p class="mb-0"><?php echo htmlspecialchars(formatDate($exam['exam_date'])); ?></p>
            </li>
            <li class="list-group-item px-0">
                <strong>Start Time:</strong>
                <p class="mb-0"><?php echo date('h:i A', strtotime(formatTime($exam['start_time']))); ?></p>
            </li>
            <li class="list-group-item px-0">
                <strong>End Time:</strong>
                <p class="mb-0"><?php echo date('h:i A', strtotime(formatTime($exam['end_time']))); ?></p>
            </li>
            <li class="list-group-item px-0">
                <strong>Exam Fee:</strong>
                <p class="mb-0">NGN&nbsp;<?php echo htmlspecialchars(number_format($exam['price'])); ?></p>
            </li>
            <li class="list-group-item px-0">
                <strong>Date Uploaded:</strong>
                <p class="mb-0"><?php echo htmlspecialchars(formatDate($exam['created_at'])); ?></p>
            </li>
            <?php if ($payment_status === 'approved' && $exam_pin) : ?>
                <li class="list-group-item px-0">
                    <strong>Your Exam PIN:</strong>
                    <p class="mb-0"><?php echo htmlspecialchars($exam_pin); ?></p>
                </li>
            <?php endif; ?>
        </ul>

        <?php if ($payment_status === 'approved' && $exam_pin) : ?>
            <div class="card-body d-flex justify-content-between pb-0 px-0">
                     <?php if ($exam_status === 'written' && $result_status=== 'available') : ?>
                <a href="result_analysis?id=<?php echo htmlspecialchars($exam['exam_id']); ?>" class="btn btn-success btn-block">VIEW RESULT ANALYSIS</a>
                <?php elseif ($exam_status === 'written' && $result_status=== 'unavailable') : ?>
                    <span class='btn btn-success btn-block'>written (awaiting result)</span>
                <?php elseif($exam_status==='unwritten'): ?>
                <a href="start_exam?id=<?php echo htmlspecialchars($exam['exam_id']); ?>" class="btn btn-primary btn-block">START NOW</a>
                <?php endif; ?>
            </div>
        <?php elseif ($payment_status === 'pending' && $exam_pin) : ?>
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