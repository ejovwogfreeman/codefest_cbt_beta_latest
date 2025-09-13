<?php

ob_start();

include('./config/session.php');
include('./partials/header.php');
require_once('./config/random_id.php');
require_once('./config/pin.php');
// include('./config/db.php');

// Check if user is logged in
if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    $user_id = $user['user_id']; // Assuming user ID comes from the session
    $full_name = $user['full_name']; // Assuming full name is available in the session
    $email = $user['email']; // Assuming email is available in the session
}

// Fetch exams and their corresponding program names from the database
$exams = [];
$query = "SELECT exam_id, exam_name, program_id, program_name FROM exams"; // Add program_name here
$result = mysqli_query($conn, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $exams[] = $row;
    }
}

// Form validation
$errors = [
    'exam_id' => '',
    'exam_name' => '',
    'exam_status' => ''
];

$exam_id = $exam_name = '';
$program_id = '';
$program_name = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $exam_id = trim($_POST['exam_id']); // Hidden field populated via JS
    $exam_name = trim($_POST['exam_name']);

    // Fetch the program ID and program name for the selected exam
    foreach ($exams as $exam) {
        if ($exam['exam_id'] === $exam_id) {
            $program_id = $exam['program_id'];
            $program_name = $exam['program_name']; // Store program name
            break;
        }
    }

    // Validate exam selection
    if (empty($exam_id)) {
        $errors['exam_id'] = 'Exam ID is required.';
    }
    if (empty($exam_name)) {
        $errors['exam_name'] = 'Exam Name is required.';
    }

    if (empty($program_name)) {
        $errors['program_name'] = "Program is required";
    }

    // Check if the student has already registered for the exam using their email
    if (!empty($exam_id) && empty($errors['exam_id'])) {
        // $check_query = "SELECT exam_status FROM exam_registrations WHERE email = ? AND exam_id = ? AND exam_status = 'unwritten'";
        $check_query = 'SELECT exam_status 
                        FROM exam_registrations 
                        WHERE email = ? AND exam_id = ?';
        if ($stmt = mysqli_prepare($conn, $check_query)) {
            mysqli_stmt_bind_param($stmt, 'ss', $email, $exam_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $existing_status);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);

            if ($existing_status === 'unwritten' || $existing_status === 'written') {
                $errors['exam_status'] = 'You have already registered for this exam.';
            }
        }
    }

    // Check exam status if the user is not already registered
    if (!empty($exam_id) && empty($errors['exam_id']) && empty($errors['exam_status'])) {
        $query = "SELECT status FROM exams WHERE exam_id = ?";
        if ($stmt = mysqli_prepare($conn, $query)) {
            mysqli_stmt_bind_param($stmt, 's', $exam_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $exam_status);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);

            // If exam status is closed, prevent registration and show message
            if ($exam_status === 'closed') {
                $errors['exam_status'] = 'Registration for this exam is closed.';
            }
        }
    }

    // Check if the student is already registered for the program the exam belongs to
    $program_pin = '';
    if (!empty($program_id) && empty($errors['exam_status'])) {
        $program_query = "SELECT program_pin FROM program_registrations WHERE email = ? AND program_id = ?";
        if ($stmt = mysqli_prepare($conn, $program_query)) {
            mysqli_stmt_bind_param($stmt, 'ss', $email, $program_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $program_pin);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    // If student is not registered for the program, generate a new exam pin
    if (empty($program_pin)) {
        $program_pin = pin(); // Generate a new pin if no program pin exists
    }

    // If no errors and exam is open, insert registration
    if (!array_filter($errors)) {
        $registration_id = random_id(); // Generate a unique registration ID
        $registration_time = date('Y-m-d H:i:s'); // Set current time as registration time

        // Default status
        $exam_status = 'unwritten';
        $payment_status = 'pending';
        $result_status = 'unavailable';

        $query = "INSERT INTO exam_registrations (registration_id, student_id, exam_id, exam_pin, full_name, email, exam_name, registration_time, program_name, exam_status, payment_status, result_status) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($conn, $query)) {
            mysqli_stmt_bind_param($stmt, 'ssssssssssss', $registration_id, $user_id, $exam_id, $program_pin, $full_name, $email, $exam_name, $registration_time, $program_name, $exam_status, $payment_status, $result_status);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            $_SESSION['msg'] = 'Exam registration successful';
            // Redirect or show success message
            header("Location: https://exams.codefest.africa/dashboard.php");
            exit();
        }
    }
}

ob_end_flush();

?>

<div class="container mb-5">
    <div class="d-flex align-items-center my-4">
        <?php if (isset($_SESSION['user'])) : ?>
            <?php include('./partials/sidebar.php'); ?>
            <button class="btn border" id="menuBtn" style="font-size: 20px">&#9776;</button>
            <h2 class="m-0" style="margin-left: 20px !important">Enroll Exam</h2>
        <?php endif ?>
    </div>

    <div class="container d-flex justify-content-center px-0">
        <div class="col-lg-5 p-0">
            <div class="card border">
                <!-- Card Header -->
                <div class="card-header text-center p-4 bg-primary">
                    <h1 class="m-0 text-light">Enroll Exam</h1>
                </div>

                <!-- Card Body -->
                <div class="card-body rounded-bottom py-5 px-sm-5 px-3 bg-light">
                    <form method="POST" class="needs-validation" novalidate>
                        <!-- Display Error Message for Exam Status -->
                        <?php if (!empty($errors['exam_status'])): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $errors['exam_status']; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Exam Name Field -->

                        <div class="form-group">
                            <label for="exam_name" class="form-label">Exam</label>
                            <select name="exam_name" id="exam_name" class="custom-select <?php echo !empty($errors['exam_name']) ? 'is-invalid' : ''; ?>" style="height: 47px;">
                                <option value="">Select an Exam</option>
                                <?php foreach ($exams as $exam): ?>
                                    <option value="<?php echo htmlspecialchars($exam['exam_name']); ?>" data-id="<?php echo htmlspecialchars($exam['exam_id']); ?>" data-program="<?php echo htmlspecialchars($exam['program_name']); ?>">
                                        <?php echo htmlspecialchars($exam['exam_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['exam_name'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo $errors['exam_name']; ?>
                                </div>
                            <?php endif; ?>
                        </div>


                        <!-- Hidden Input for Exam ID -->
                        <input type="hidden" id="exam_id" name="exam_id" value="<?php echo htmlspecialchars($exam_id); ?>">

                        <!-- Program Name Field (Read Only) -->
                        <div class="form-group">
                            <label for="program_name" class="form-label">Program</label>
                            <input type="text" id="program_name" name="program_name" placeholder="Program" value="<?php echo htmlspecialchars($program_name); ?>" readonly class="form-control <?php echo isset($errors['program_name']) ? 'is-invalid' : ''; ?>" required>
                            <?php if (isset($errors['program_name'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo $errors['program_name']; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-dark py-3">Enroll Exam</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    document.getElementById('exam_name').addEventListener('change', function() {
        // Get the selected option
        var selectedOption = this.options[this.selectedIndex];

        // Get the exam_id and program name from the selected option's attributes
        var examId = selectedOption.getAttribute('data-id');
        var programName = selectedOption.getAttribute('data-program');

        // Set the hidden exam_id field and program name field
        document.getElementById('exam_id').value = examId;
        document.getElementById('program_name').value = programName; // Set the program name
    });

    // Ensure the program field remains populated after form submission
    window.addEventListener('DOMContentLoaded', function() {
        const selectedExamId = "<?php echo isset($exam_id) ? $exam_id : ''; ?>"; // Retain the selected exam ID
        const programName = "<?php echo isset($program_name) ? htmlspecialchars($program_name) : ''; ?>"; // Retain the program name

        if (selectedExamId) {
            const examSelect = document.getElementById('exam_name');
            // Find and set the selected option based on exam_id
            for (let i = 0; i < examSelect.options.length; i++) {
                if (examSelect.options[i].getAttribute('data-id') === selectedExamId) {
                    examSelect.selectedIndex = i;
                    break;
                }
            }
            document.getElementById('program_name').value = programName; // Retain the program name
        }
    });
</script>

<?php include('./partials/footer.php'); ?>