<?php

ob_start();

session_start(); // Start the session

// include('./config/db.php');
include('./config/random_id.php');
include('./partials/header.php');

// Initialize variables for error messages and form values
$exam_pinError = "";
$exam_pin = "";
$exam = null; // To store exam details

// Get the exam ID from the URL
$examId = isset($_GET['id']) ? $_GET['id'] : '';

// Fetch the exam details based on the exam_id
if (!empty($examId)) {
    $examQuery = "SELECT * FROM exams WHERE exam_id = ?";
    $stmt = mysqli_prepare($conn, $examQuery);
    mysqli_stmt_bind_param($stmt, "s", $examId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $exam = mysqli_fetch_assoc($result); // Fetch exam details
    } else {
        echo "Exam not found.";
        exit();
    }
    mysqli_stmt_close($stmt);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $isValid = true;

    // Validate Exam Pin
    if (empty($_POST["exam_pin"])) {
        $exam_pinError = "Exam Pin is required.";
        $isValid = false;
    } else {
        $exam_pin = sanitizeInput($_POST["exam_pin"]);

        // Check if exam_pin exists in student_answers table (i.e., if they have already submitted answers)
        $examPinCheckQuery = "SELECT * FROM student_answers WHERE exam_pin = ?";
        $stmt = mysqli_prepare($conn, $examPinCheckQuery);
        mysqli_stmt_bind_param($stmt, "s", $exam_pin);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            // Exam pin is already used for submitting answers
            $exam_pinError = "You have already taken this exam.";
            $isValid = false;
        } else {
            // Check if the exam_pin exists in the exam_registrations table and if the exam has already been written
            $examPinQuery = "SELECT * FROM exam_registrations WHERE exam_id = ? AND exam_pin = ?";
            $stmt = mysqli_prepare($conn, $examPinQuery);
            mysqli_stmt_bind_param($stmt, "ss", $examId, $exam_pin);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result && mysqli_num_rows($result) > 0) {
                $registration = mysqli_fetch_assoc($result);

                // Check if the exam_status is set to 'written'
                if ($registration['exam_status'] == 'written') {
                    $exam_pinError = "You have already written this exam.";
                    $isValid = false;
                }
            } else {
                // Exam pin is invalid
                $exam_pinError = "Invalid Exam Pin.";
                $isValid = false;
            }
            mysqli_stmt_close($stmt);
        }
    }

    // If the form is valid, process the data (e.g., save to session and redirect to test page)
    if ($isValid) {
        $_SESSION['exam'] = $exam; // Store exam information in session
        $_SESSION['exam_pin'] = $exam_pin; // Store the exam pin
        $_SESSION['msg'] = "Exam started successfully.";

        // Redirect to the test page
        header("Location: https://exams.codefest.africa/ongoing_exam?id={$examId}");
        exit();
    }
}

// Function to sanitize user input
function sanitizeInput($data)
{
    global $conn; // Assuming $conn is your database connection
    return htmlspecialchars(mysqli_real_escape_string($conn, trim($data)));
}

ob_end_flush();

?>

<div class="container my-5">
    <div class="container d-flex justify-content-center px-0">
        <div class="col-lg-5 p-0">
            <div class="card border">
                <!-- Card Header -->
                <div class="card-header text-center p-4 bg-primary">
                    <h1 class="m-0 text-light">Start Exam</h1>
                </div>

                <!-- Card Body -->
                <div class="card-body rounded-bottom py-5 px-sm-5 px-3 bg-light">
                    <form method="POST" class="needs-validation" novalidate>
                        <!-- Display Error Message for Exam Status -->
                        <?php if (isset($_SESSION['msg'])): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $_SESSION['msg'];
                                unset($_SESSION['msg']); ?>
                            </div>
                        <?php endif; ?>
                        <!-- Display exam details if available -->
                        <?php if ($exam): ?>
                            <div class="form-group">
                                <label for="exam_name">Exam Name</label>
                                <input type="text" id="exam_name" name="exam_name" value="<?php echo htmlspecialchars($exam['exam_name']); ?>" class="form-control" readonly>
                            </div>
                            <div class="form-group">
                                <label for="program">Program</label>
                                <input type="text" id="program" name="program" value="<?php echo htmlspecialchars($exam['program_name']); ?>" class="form-control" readonly>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="exam_pin">Exam Pin</label>
                            <input type="password" id="exam_pin" name="exam_pin" placeholder="Enter Exam Pin" value="<?php echo htmlspecialchars($exam_pin); ?>" class="form-control <?php echo !empty($exam_pinError) ? 'is-invalid' : ''; ?>">
                            <div class="invalid-feedback"><?php echo $exam_pinError; ?></div>
                        </div>

                        <div>
                            <button class="btn btn-dark btn-block border-0 py-3" type="submit" formtarget="_blank">Start Exam</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('./partials/footer.php'); ?>