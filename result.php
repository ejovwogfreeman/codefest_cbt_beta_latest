<?php

// session_start();

// include('./config/db.php');
// // include('./partials/header.php');

// // Get the exam ID from the URL
// $exam_id = isset($_GET['id']) ? $_GET['id'] : '';

// if ($exam_id == '') {
//     $_SESSION['msg'] = 'Exam not found!';
//     header("Location: check_result?id=$exam_id");
//     exit();
// }

// if (!isset($_SESSION['exam_pin']) || $_SESSION['exam_pin'] == '') {
//     $_SESSION['msg'] = 'Exam pin is required';
//     header("Location: check_result?id=$exam_id");
//     exit();
// } else {
//     $exam_pin = $_SESSION['exam_pin'];
// }

// // Prepare the query to fetch user info, exam details, and student answers in a single query
// $sql = "
//     SELECT 
//         users.full_name, 
//         users.email, 
//         exam_registrations.program_name, 
//         student_answers.selected_option, 
//         student_answers.correct_option, 
//         student_answers.remark
//     FROM users 
//     JOIN student_answers ON users.user_id = student_answers.student_id 
//     JOIN exam_registrations ON student_answers.registration_id = exam_registrations.registration_id
//     WHERE users.user_id = ? AND student_answers.exam_id = ?
// ";

// // Prepare the statement to ensure it's executed only once
// $stmt = mysqli_prepare($conn, $sql);
// mysqli_stmt_bind_param($stmt, 'ss', $user_id, $exam_id); // Bind user_id and exam_id
// mysqli_stmt_execute($stmt);
// $result = mysqli_stmt_get_result($stmt);

// // Check if any answers are retrieved
// if ($result && mysqli_num_rows($result) > 0) {
//     // Fetch user info and answers
//     $user_info = mysqli_fetch_assoc($result); // First row for user info
//     mysqli_data_seek($result, 0); // Reset result pointer to fetch all rows for answers
//     $student_answers = mysqli_fetch_all($result, MYSQLI_ASSOC); // Fetch all answers
// } else {
//     $_SESSION['msg'] = "No result found for the specified exam";
//     header("Location: check_result.php?id=$exam_id");
//     exit();
// }

// $correct_score = 0;
// $total_questions = count($student_answers); // Count of total questions

// // Loop through the student answers to compute the correct score
// foreach ($student_answers as $answer) {
//     if ($answer['selected_option'] === $answer['correct_option']) {
//         $correct_score++;
//     }
// }

// // Calculate percentage
// $percentage = ($total_questions > 0) ? ($correct_score / $total_questions) * 100 : 0;

// // Determine remark based on the percentage
// if ($percentage >= 70) {
//     $remark = "Excellent";
// } elseif ($percentage >= 60) {
//     $remark = "Very Good";
// } elseif ($percentage >= 50) {
//     $remark = "Good";
// } elseif ($percentage >= 40) {
//     $remark = "Fair";
// } else {
//     $remark = "Needs Improvement";
// }

// // Close the statement and connection
// mysqli_stmt_close($stmt);
// mysqli_close($conn);
?>

<?php

session_start();

include('./config/db.php');

// Get the exam ID from the URL
$exam_id = isset($_GET['id']) ? $_GET['id'] : '';

if ($exam_id == '') {
    $_SESSION['msg'] = 'Exam not found!';
    header("Location: check_result?id=$exam_id");
    exit();
}

// Check if exam pin is set in the session
if (!isset($_SESSION['exam_pin']) || $_SESSION['exam_pin'] == '') {
    $_SESSION['msg'] = 'Exam pin is required';
    header("Location: check_result?id=$exam_id");
    exit();
} else {
    $exam_pin = $_SESSION['exam_pin'];
}

// Step 1: Fetch user_id from exam_registrations table using the exam pin
$examPinQuery = "
    SELECT student_id, registration_id 
    FROM exam_registrations 
    WHERE exam_id = ? AND exam_pin = ?
";
$stmt = mysqli_prepare($conn, $examPinQuery);
mysqli_stmt_bind_param($stmt, 'ss', $exam_id, $exam_pin);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) > 0) {
    $registrationData = mysqli_fetch_assoc($result);
    $user_id = $registrationData['student_id']; // Get the user ID
    $registration_id = $registrationData['registration_id']; // Get the registration ID
} else {
    // If no match is found for exam pin
    $_SESSION['msg'] = "Invalid exam pin or no registration found.";
    header("Location: check_result.php?id=$exam_id");
    exit();
}
mysqli_stmt_close($stmt); // Close the first query statement

// Step 2: Fetch user info, exam details, and student answers using user_id and exam_id
$sql = "SELECT 
    users.full_name, 
    users.email, 
    exam_registrations.exam_name,
    exam_registrations.program_name,
    exam_registrations.result_status, 
    student_answers.selected_option, 
    student_answers.correct_option, 
    student_answers.remark,
    student_answers.exam_id,
    questions.question_text
FROM student_answers 
JOIN exam_registrations ON student_answers.registration_id = exam_registrations.registration_id 
JOIN users ON exam_registrations.student_id = users.user_id 
JOIN questions ON student_answers.question_id = questions.question_id 
WHERE exam_registrations.exam_pin = ? 
ORDER BY questions.created_at ASC
";

// Prepare the statement for the second query
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 's', $exam_pin); // Bind user_id and exam_id
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Check if any answers are retrieved
if ($result && mysqli_num_rows($result) > 0) {
    // Fetch user info and the first row for user and exam details
    $user_info = mysqli_fetch_assoc($result);

    // Check if result_status is available
    if ($user_info['result_status'] == 'available') {
        // Reset the result pointer to fetch all rows for answers
        mysqli_data_seek($result, 0);
        $student_answers = mysqli_fetch_all($result, MYSQLI_ASSOC); // Fetch all answers
    } else {
        $_SESSION['msg'] = "Results are not available yet for this exam.";
        header("Location: check_result.php?id=$exam_id");
        exit();
    }
} else {
    $_SESSION['msg'] = "No result found for the specified exam.";
    header("Location: check_result.php?id=$exam_id");
    exit();
}

$correct_score = 0;
$total_questions = count($student_answers); // Count of total questions

// Loop through the student answers to compute the correct score
foreach ($student_answers as $answer) {
    if ($answer['selected_option'] === $answer['correct_option']) {
        $correct_score++;
    }
}

// Calculate percentage
$percentage = ($total_questions > 0) ? ($correct_score / $total_questions) * 100 : 0;

// Determine remark based on the percentage
if ($percentage >= 70) {
    $remark = "Excellent";
} elseif ($percentage >= 60) {
    $remark = "Very Good";
} elseif ($percentage >= 50) {
    $remark = "Good";
} elseif ($percentage >= 40) {
    $remark = "Fair";
} else {
    $remark = "Needs Improvement";
}

// Close the statement and connection
mysqli_stmt_close($stmt);
mysqli_close($conn);

function showFlyingAlert($message, $className)
{
    echo <<<EOT
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var alertDiv = document.createElement("div");
            alertDiv.className = "{$className}";
            alertDiv.innerHTML = "{$message}";
            document.body.appendChild(alertDiv);

            alertDiv.offsetWidth;
            alertDiv.style.left = "10px";

            setTimeout(function() {
                alertDiv.style.left = "10px";
            }, 2000);

            setTimeout(function() {
                alertDiv.style.left = "-300px";
            }, 4000);

            setTimeout(function() {
                alertDiv.remove();
            }, 6000);
        });
    </script>
EOT;
}

// Show session messages if available
if (isset($_SESSION['msg'])) {
    $message = $_SESSION['msg'];
    if (stristr($message, "successfully") || stristr($message, "Successfully") || stristr($message, "SUCCESSFUL")) {
        showFlyingAlert($message, "flying-success-alert");
    } else {
        showFlyingAlert($message, "flying-danger-alert");
    }
    unset($_SESSION['msg']);
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars(strtoupper($user_info['full_name'])) ?> STATEMENT OF RESULT</title>
</head>

<body>

    <div class="result-container">

        <div class="container">

            <div class="top-section">
                <img src="./images/codefest.png" alt="" width="100px">
                <div>
                    <h1>CODEFEST INSTITUTE OF TECHNOLOGY</h1>
                    <h2>SILICON VALLEY, CENTENARY CITY</h2>
                    <h3>STATEMENT OF RESULT</h3>
                </div>
            </div>

            <!-- Display User Information -->
            <div class="name-section">
                <div>
                    <div><strong>NAME:</strong> <span><?php echo htmlspecialchars(strtoupper($user_info['full_name'])); ?></span></div>
                    <div><strong>EMAIL:</strong> <span><a href="mailto:<?php echo htmlspecialchars($user_info['email']); ?>"><?php echo htmlspecialchars(strtoupper($user_info['email'])); ?></a></span></div>
                    <div><strong>PROGRAM:</strong> <span><?php echo htmlspecialchars(strtoupper($user_info['program_name'])); ?></span></div>
                    <div><strong>COURSE:</strong> <span><?php echo htmlspecialchars(strtoupper($user_info['exam_name'])); ?></span></div>
                </div>
                <div class="print-section">
                    <button id="print-btn" class="print-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em" viewBox="0 0 24 24">
                            <path fill="currentColor" d="M5 20h14v-2H5zM19 9h-4V3H9v6H5l7 7z" />
                        </svg><span>DOWNLOAD</span></button>
                </div>
            </div>

            <!-- Display Student Answers -->
            <table border="1">
                <tr>
                    <th>S/N</th>
                    <th>SELECTED OPTION</th>
                    <th>CORRECT OPTION</th>
                    <th>REMARK</th>
                </tr>
                <?php
                $sn = 1;
                foreach ($student_answers as $answer) : ?>
                    <tr>
                        <td><?php echo $sn++; ?></td>
                        <td><?php echo htmlspecialchars($answer['selected_option']); ?></td>
                        <td><?php echo htmlspecialchars($answer['correct_option']); ?></td>
                        <td><?php echo htmlspecialchars($answer['remark'] === 'correct') ? '<svg xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em" viewBox="0 0 24 24" style="color: #4CAF50">
                                <path fill="currentColor" d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2m-8.29 13.29a.996.996 0 0 1-1.41 0L5.71 12.7a.996.996 0 1 1 1.41-1.41L10 14.17l6.88-6.88a.996.996 0 1 1 1.41 1.41z" />
                            </svg>' : '<svg xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em" viewBox="0 0 1024 1024" style="color: #FF5252">
                                <path fill="currentColor" fill-rule="evenodd" d="M880 112c17.7 0 32 14.3 32 32v736c0 17.7-14.3 32-32 32H144c-17.7 0-32-14.3-32-32V144c0-17.7 14.3-32 32-32ZM639.978 338.82l-.034.006c-.023.007-.042.018-.083.059L512 466.745l-127.86-127.86c-.042-.041-.06-.052-.084-.059a.12.12 0 0 0-.07 0c-.022.007-.041.018-.082.059l-45.02 45.019c-.04.04-.05.06-.058.083a.12.12 0 0 0 0 .07l.01.022a.3.3 0 0 0 .049.06L466.745 512l-127.86 127.862c-.041.04-.052.06-.059.083a.12.12 0 0 0 0 .07c.007.022.018.041.059.082l45.019 45.02c.04.04.06.05.083.058a.12.12 0 0 0 .07 0c.022-.007.041-.018.082-.059L512 557.254l127.862 127.861c.04.041.06.052.083.059a.12.12 0 0 0 .07 0c.022-.007.041-.018.082-.059l45.02-45.019c.04-.04.05-.06.058-.083a.12.12 0 0 0 0-.07l-.01-.022a.3.3 0 0 0-.049-.06L557.254 512l127.861-127.86c.041-.042.052-.06.059-.084a.12.12 0 0 0 0-.07c-.007-.022-.018-.041-.059-.082l-45.019-45.02a.2.2 0 0 0-.083-.058a.12.12 0 0 0-.07 0Z" />
                            </svg>'; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <div class="bottom-section">
                <div class="correct-score"><strong>CORRECT SCORE:</strong> <strong><?php echo htmlspecialchars($correct_score); ?></strong></div>
                <div class="total-question"><strong>TOTAL QUESTION:</strong> <strong><?php echo htmlspecialchars($total_questions); ?></strong></div>
                <div class="percentage"><strong>PERCENTAGE:</strong> <strong><?php echo htmlspecialchars(number_format($percentage, 2)); ?>%</strong></div>
                <div class="remark"><strong>REMARK:</strong> <strong><?php echo htmlspecialchars(strtoupper($remark)); ?></strong></div>
            </div>

        </div>

    </div>


    <script>
        // Add event listener for the print button
        document.getElementById('print-btn').addEventListener('click', function() {
            window.print(); // Trigger the browser's print dialog
        });
    </script>

    <style>
        @import url("https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=SUSE:wght@100..800&display=swap");

        * {
            margin: 0px;
            padding: 0px;
            box-sizing: border-box;
            font-family: "SUSE";
        }

        :root {
            --primary: #fe5d37;
            /* Light Blue */
            --secondary: #064778;
            /* Green */
            --text: #333;
            /* Dark Gray */
            --background: #f5f5f5;
            /* Light Gray */
            --hover: #2980b9;
            /* Dark Blue for hover */
        }
        
        .result-container {
            min-height: 100vh;
            width: 100%;
            background: linear-gradient(rgba(256, 256, 256, 0.9), rgba(256, 256, 256, 0.9)), url('./images/codefest.png');
            /*background-repeat: no-repeat;*/
            background-size: contain;
            /*background-position: center;*/
            /*overflow-y: auto;*/
            padding-bottom: 20px; /* Prevent content from touching the bottom */
        }

        .container {
            width: 650px;
            margin: auto;
        }

        .flying-success-alert {
            position: fixed;
            z-index: 11111111111111;
            top: 15px;
            left: -300px;
            background-color: #4caf50;
            color: #fff;
            padding: 10px;
            border-radius: 5px;
            transition: left 1.5s ease-in-out;
        }

        .flying-danger-alert {
            position: fixed;
            z-index: 11111111111111;
            top: 15px;
            left: -300px;
            background-color: #ff5252;
            color: #fff;
            padding: 10px;
            border-radius: 5px;
            transition: left 1.5s ease-in-out;
        }

        .top-section {
            text-align: left;
            margin-top: 30px;
            display: flex;
            align-items: center;
        }

        .top-section img {
            margin-left: -15px;
        }

        h1 {
            font-size: 25px;
        }

        h2 {
            font-size: 20px;
        }

        h3 {
            font-size: 15px;
        }

        .name-section {
            margin-top: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .print-btn {
            background-color: #1976D2;
            color: #fff;
            border: none;
            outline: none;
            padding: 3px;
            border-radius: 3px;
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        table {
            width: 100%;
            text-align: center;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid var(--secondary);
            padding: 5px;
            text-align: center;
        }

        .bottom-section {
            margin-top: 30px;
        }

        .correct-score,
        .percentage {
            border-top: 2px dashed black;
            padding-top: 20px;
            margin-bottom: 10px;
        }

        .total-question,
        .remark {
            margin-bottom: 20px;
        }

        .correct-score,
        .total-question,
        .percentage,
        .remark {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        @media screen and (max-width:500px) {
            .container {
                padding: 10px;
                width: 100%;
                margin: auto;
                font-family: "SUSE";
            }

            h1,
            h2,
            h3 {
                font-size: 14px;
            }

        }

        @media print {

            /* Hide elements that shouldn't appear in the PDF */
            .print-section,
            .download-section {
                display: none;
            }

            /* Customize print layout */
            body {
                margin: 0;
                padding: 0;
                font-size: 14px;
            }

            .result-container {
                width: 100%;
                padding: 20px;
            }
        }
    </style>

</body>

</html>