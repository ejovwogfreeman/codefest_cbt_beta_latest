<?php

ob_start();

include('./config/session.php');
include('./partials/header.php');
require_once('./config/random_id.php');
require_once('./config/pin.php');

// Check if user is logged in
if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    $user_id = $user['user_id'];
    $full_name = $user['full_name'];
    $email = $user['email'];
}

// Fetch programs from the database
$programs = [];
$query = "SELECT program_id, program_name FROM programs";
$result = mysqli_query($conn, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $programs[] = $row;
    }
}

// Form validation
$errors = [
    'program_id' => '',
    'program_name' => '',
    'program_status' => '',
    'duration' => ''
];

$program_id = $program_name = $duration = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $program_id = trim($_POST['program_id']);
    $program_name = trim($_POST['program_name']);
    $duration = trim($_POST['duration']);
    $price = trim($_POST['price']);

    if (empty($program_id)) {
        $errors['program_id'] = 'Program ID is required.';
    }
    if (empty($program_name)) {
        $errors['program_name'] = 'Program Name is required.';
    }
    if (empty($duration)) {
        $errors['duration'] = 'Duration is required.';
    }

    if (!empty($program_id) && empty($errors['program_id'])) {
        $check_query = "SELECT COUNT(*) FROM program_registrations WHERE email = ? AND program_id = ?";
        if ($stmt = mysqli_prepare($conn, $check_query)) {
            mysqli_stmt_bind_param($stmt, 'ss', $email, $program_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $existing_registration_count);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);

            if ($existing_registration_count > 0) {
                $errors['program_status'] = 'You have already registered for this program.';
            }
        }
    }

    if (!array_filter($errors)) {
        $registration_id = random_id();
        $registration_time = date('Y-m-d H:i:s');
        $program_pin = pin();
        $payment_status = 'pending';

        $query = "INSERT INTO program_registrations (registration_id, student_id, program_id, program_pin, full_name, email, program_name, duration, price, registration_time, payment_status) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($conn, $query)) {
            mysqli_stmt_bind_param($stmt, 'sssssssssss', $registration_id, $user_id, $program_id, $program_pin, $full_name, $email, $program_name, $duration, $price, $registration_time, $payment_status);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            $_SESSION['msg'] = 'Program registration successful';
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
            <h2 class="m-0" style="margin-left: 20px !important">Enroll Program</h2>
        <?php endif ?>
    </div>

    <div class="container d-flex justify-content-center px-0">
        <div class="col-lg-5 p-0">
            <div class="card border">
                <div class="card-header text-center p-4 bg-primary">
                    <h1 class="m-0 text-light">Enroll Program</h1>
                </div>
                <div class="card-body rounded-bottom py-5 px-sm-5 px-3 bg-light">
                    <form method="POST" class="needs-validation" novalidate>
                        <?php if (!empty($errors['program_status'])): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $errors['program_status']; ?>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="program_name" class="form-label">Program</label>
                            <select name="program_name" id="program_name" class="custom-select" style="height: 47px;">
                                <option value="">Select A Program</option>
                                <?php foreach ($programs as $program): ?>
                                    <option value="<?php echo htmlspecialchars($program['program_name']); ?>" data-id="<?php echo htmlspecialchars($program['program_id']); ?>">
                                        <?php echo htmlspecialchars($program['program_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <input type="hidden" id="program_id" name="program_id">

                        <div class="form-group">
                            <label for="duration" class="form-label">Duration</label>
                            <select name="duration" id="duration" class="custom-select" style="height: 47px;">
                                <option value="">Select Duration</option>
                                <option value="3 months">3 Months (₦150,000)</option>
                                <option value="6 months">6 Months (₦300,000</option>
                                <option value="1 year">1 Year (₦500,000)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="price" class="form-label">Price (₦)</label>
                            <input type="text" id="price" name="price" class="form-control" readonly>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-dark py-3">Enroll Program</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('program_name').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        document.getElementById('program_id').value = selectedOption.getAttribute('data-id');
    });

    document.getElementById('duration').addEventListener('change', function() {
        var priceField = document.getElementById('price');
        switch (this.value) {
            case '3 months':
                priceField.value = '150000';
                break;
            case '6 months':
                priceField.value = '300000';
                break;
            case '1 year':
                priceField.value = '500000';
                break;
            default:
                priceField.value = '';
        }
    });
</script>

<?php include('./partials/footer.php'); ?>