<?php

ob_start();

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

include('./config/session.php');
// include('../config/db.php'); // Ensure you include your database connection
include('./partials/header.php');

// // Get the exam ID from the URL
$exam_id = isset($_GET['id']) ? $_GET['id'] : '';

// Fetch exam details (including duration)
$examQuery = "SELECT * FROM exams WHERE exam_id = ?";
$stmt = mysqli_prepare($conn, $examQuery);
mysqli_stmt_bind_param($stmt, "s", $exam_id); // Bind as string if exam_id is a UUID or string
mysqli_stmt_execute($stmt);
$examResult = mysqli_stmt_get_result($stmt);
$exam = mysqli_fetch_assoc($examResult);
mysqli_stmt_close($stmt);

// Check if the exam exists
if (!$exam) {
    echo "Exam not found.";
    exit();
}

// Fetch questions based on exam_id
$questionsQuery = "SELECT * FROM questions WHERE exam_id = ? ORDER BY created_at ASC";
$stmt = mysqli_prepare($conn, $questionsQuery);
mysqli_stmt_bind_param($stmt, "s", $exam_id);  // Bind as string if exam_id is a UUID or string
mysqli_stmt_execute($stmt);
$questionsResult = mysqli_stmt_get_result($stmt);
$questions = mysqli_fetch_all($questionsResult, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

// Fetch options for each question based on question_id, ordered by option_label (A, B, C, D)
$optionsQuery = "SELECT * FROM options WHERE question_id IN (SELECT question_id FROM questions WHERE exam_id = ?) ORDER BY option_label ASC";
$stmt = mysqli_prepare($conn, $optionsQuery);
mysqli_stmt_bind_param($stmt, "s", $exam_id); // Bind as string if exam_id is a UUID or string
mysqli_stmt_execute($stmt);
$optionsResult = mysqli_stmt_get_result($stmt);
$options = mysqli_fetch_all($optionsResult, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

// Create an associative array to map options to questions based on question_id
$optionsMap = [];
foreach ($options as $option) {
    $optionsMap[$option['question_id']][] = $option;
}


// Query to fetch student answers
$sel_query = "SELECT * FROM student_answers WHERE student_id = '$userId' AND exam_id = '$exam_id'";

// Execute the query
$result_sel = mysqli_query($conn, $sel_query);

// Check if the query executed successfully
if (!$result_sel) {
    die("Query failed: " . mysqli_error($conn));
}

// Initialize an empty array to hold student answers
$studentAnswers = [];

// Loop through the result set and populate the array
while ($row = mysqli_fetch_assoc($result_sel)) {
    // Add the question_id and selected_option to the studentAnswers array
    $studentAnswers[$row['question_id']] = $row['selected_option'];
}

// Pass exam duration to the front-end for countdown (convert to seconds)
$duration = $exam['duration_in_minutes'] * 60; // Assuming duration is in minutes

// Function to show flying alert
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

function convertMinutesToHours($minutes)
{
    $hours = intdiv($minutes, 60);
    $minutes = $minutes % 60;
    if ($hours > 0) {
        return "{$hours}hrs " . ($minutes > 0 ? "{$minutes}min" : "");
    } else {
        return "{$minutes}min";
    }
}

ob_end_flush();

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">



    <style>
        @import url("https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=SUSE:wght@100..800&display=swap");

        * {
            margin: 0px;
            padding: 0px;
            box-sizing: border-box;
            font-family: "SUSE";
        }


        /* Flying Alert Styling */
        .flying-success-alert {
            /* styling for success alert */
        }

        .flying-danger-alert {
            /* styling for danger alert */
        }

        .countdown {
            font-weight: bold;
            color: red;
        }

        .intro {
            text-align: center;
            margin: 30px 0px 0px;
        }

        .warn {
            color: white;
            background-color: #ff5252;
            padding: 10px;
        }

        .name-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            position: sticky;
            margin-bottom: 50px;
            padding: 20px 0px;
            top: 0px;
            background-color: white;
        }

        .timer {
            color: red;
            font-size: 20px;
            color: red;
            text-align: right;
        }

        .name-section div {
            flex: 1;
        }

        .quiz {
            font-size: 18px;
            margin-top: 30px;
        }

        #quiz-form {
            padding: 0px;
        }

        .quiz label {
            background-color: rgba(0, 0, 0, 0.05);
            width: 100%;
            display: block;
            margin-bottom: -15px;
            padding: 15px;
            cursor: pointer;
            border-radius: 3px;
            position: relative;
        }

        .quiz label:hover {
            background-color: rgba(0, 0, 0, 0.1);
        }
        
        .correct-answer{
            #de4dda;
        }
        
        .wrong-answer{
            #f8d7da;
        }

        .quiz button {
            width: 100%;
            margin-top: 10px;
            padding: 15px;
            border: 1px solid #4caf50;
            outline: none;
            cursor: pointer;
            color: white;
            background-color: #4caf50;
            font-size: 18px;
            font-weight: 700;
            border-radius: 3px;
            margin-bottom: 50px;
        }

        .quiz button:hover {
            background-color: #1d7d20;
        }

        .container {
            width: 900px;
            margin: auto;
        }

        @media screen and (max-width: 1000px) {
            .container {
                width: 95%;
            }
        }

        @media screen and (max-width: 500px) {

            .container .quiz {
                padding: 0px 20px 50px;
            }

            .exam-btn {
                padding: 5px 10px;
            }

            .intro h1 {
                font-size: 25px;
            }

            .intro h2 {
                font-size: 20px;
            }

        }
    </style>

    <title><?php echo htmlspecialchars($exam['exam_name']); ?> - Start Exam</title>

</head>

<body>

    <div class="container">
        <!--<div class="intro">-->
        <!--    <h1>CODEFEST INSTITUTE OF TECHNOLOGY</h1>-->
        <!--    <h2>SILICON VALLEY, CENTENARY CITY</h2>-->
        <!--    <div style="display: flex; align-items: center; justify-content: center">-->
        <!--        <h3 style="margin-right: 10px">PROGRAM: <?php echo htmlspecialchars(strtoupper($exam['program_name'])); ?>-->
        <!--    </div>-->
        <!--</div>-->
        <!--<div class="name-section">-->
        <!--    <div>-->
        <!--        <div><strong>NAME:</strong> <span><?php echo htmlspecialchars(strtoupper($user['full_name'])); ?></span></div>-->
        <!--        <div><strong>EMAIL:</strong> <span><a href="mailto:<?php echo htmlspecialchars($user['email']); ?>"><?php echo htmlspecialchars(strtoupper($user['email'])); ?></a></span></div>-->
        <!--        <div><strong>PROGRAM:</strong> <span><?php echo htmlspecialchars(strtoupper($exam['program_name'])); ?></span></div>-->
        <!--        <div><strong>COURSE:</strong> <span><?php echo htmlspecialchars(strtoupper($exam['exam_name'])); ?></span></div>-->
        <!--    </div>-->
        <!--    <div class="timer">-->
        <!--        <strong>DURATION: <span class="countdown"><?php echo htmlspecialchars(convertMinutesToHours($exam['duration_in_minutes'])); ?></span></strong> <br>-->
        <!--        <strong>TIME LEFT: <span id="countdown" class="countdown"><?php echo htmlspecialchars(convertMinutesToHours($exam['duration_in_minutes'])); ?></span></span></h3>-->
        <!--    </div>-->
        <!--</div>-->
    
    <div class="quiz" id="quiz-form">
<h3>Result Analysis (<?php echo $exam['program_name']; ?> - <?php echo $exam['exam_name']; ?>)</h3>
    <?php if (!empty($questions)): ?>
        <?php foreach ($questions as $index => $question): ?>
            <div style="margin-bottom: 20px;">
                <p id="q" style="margin-bottom: 15px;">
                    <strong><?php echo ($index + 1) . '. ' . htmlspecialchars($question['question_text']); ?></strong>
                </p>

                 <!--Check if the question has an image (stored as longblob) -->
                <?php if (!empty($question['image'])): ?>
                    <?php
                    // Convert the binary image data to base64
                    $imageData = base64_encode($question['image']);
                    // Determine the image's MIME type (adjust if needed)
                    $mimeType = 'image/jpeg'; // You can store MIME type in the database or infer it dynamically if needed.
                    ?>
                    <img src="data:<?php echo $mimeType; ?>;base64,<?php echo $imageData; ?>" alt="Question Image" style="max-width: 100%; height: auto; margin-bottom: 15px;">
                <?php endif; ?>

<?php if (isset($optionsMap[$question['question_id']])): ?>
    <?php 
    // Retrieve the student's selected answer for this question (if it exists)
    $studentAnswer = isset($studentAnswers[$question['question_id']]) ? $studentAnswers[$question['question_id']] : '';
    ?>

    <?php foreach ($optionsMap[$question['question_id']] as $option): ?>
        <?php 
        // Check if the current option matches the correct answer
        $isCorrect = ($option['option_label'] === $question['correct_option']);
        
        // Check if the current option matches the student's selected answer
        $isSelected = ($studentAnswer === $option['option_label']);
        
        $noneSelected =  ($studentAnswer === 'NONE');
        
        // Determine the background color:
        // Green if selected and correct or just correct
        // Red if selected but not correct
        if ($isSelected && $isCorrect) {
            $backgroundColor = '#d4edda';  // Green for selected and correct
        } elseif ($isSelected) {
            $backgroundColor = '#f8d7da';  // Red for selected but wrong
        } elseif ($isCorrect) {
            $backgroundColor = '#d4edda';  // Green for correct but not selected
        } else {
            $backgroundColor = '';  // No background color for other options
        }
        ?>
        
        <!-- Apply the background color -->
        <label style="background-color: <?php echo $backgroundColor; ?>;">
            <span><?php echo htmlspecialchars($option['option_label']); ?>
            &nbsp;&nbsp;&nbsp;<?php echo htmlspecialchars($option['option_text']); ?></span>
                <span style='position: absolute; top:35%; right: 10px; font-size: 12px'><span><?php echo $isSelected ? 'selected option' : ''; ?></span>&nbsp;&nbsp;&nbsp;<span><?php echo $isCorrect ? 'correct option' : ''; ?></span></span>
        </label><br>
    <?php endforeach; ?>
<?php 
echo $noneSelected ? 
    '<span style="display: block; width: 100%; background-color: #f8d7da; border-radius: 3px; padding: 15px;">No option was selected</span>' 
    : null; 
?>
<?php endif; ?>

            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No questions available for this exam.</p>
    <?php endif; ?>
</div>
    
    </div>

<?php
include('./partials/footer.php');;
?>