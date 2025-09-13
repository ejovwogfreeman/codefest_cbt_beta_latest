<?php

ob_start();

session_start();

include('./config/db.php');

// Get the exam ID from the URL
$exam_id = isset($_GET['id']) ? $_GET['id'] : '';

if ($exam_id == '') {
    $_SESSION['msg'] = 'Exam not found!';
    header("Location: start_exam?id=$exam_id");
    exit();
}

if (!isset($_SESSION['exam_pin']) || $_SESSION['exam_pin'] == '') {
    $_SESSION['msg'] = 'Exam pin is required';
    header("Location: start_exam?id=$exam_id");
    exit();
} else {
    $exam_pin = $_SESSION['exam_pin'];
}

// Check if exam_pin exists in student_answers table
$examPinCheckQuery = "SELECT * FROM student_answers WHERE exam_pin = ?";
$stmt = mysqli_prepare($conn, $examPinCheckQuery);
mysqli_stmt_bind_param($stmt, "s", $exam_pin);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) > 0) {
    // Exam pin is already used
    $_SESSION['msg'] = "You have already taken this exam.";
    header("Location: start_exam.php?id=$exam_id");
    exit();
}

// Check if the exam_status is set to 'written' and fetch user_id
$examPinQuery = "SELECT * FROM exam_registrations WHERE exam_id = ? AND exam_pin = ?";
$stmt = mysqli_prepare($conn, $examPinQuery);
mysqli_stmt_bind_param($stmt, "ss", $exam_id, $exam_pin);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$registration = mysqli_fetch_assoc($result);

if (!$registration) {
    $_SESSION['msg'] = "Invalid exam registration.";
    header("Location: start_exam.php?id=$exam_id");
    exit();
}

if ($registration['exam_status'] == 'written') {
    $_SESSION['msg'] = "You have already taken this exam.";
    header("Location: start_exam.php?id=$exam_id");
    exit();
}

$user_id = $registration['student_id']; // Get user_id from registration

// Fetch user information from users table using user_id
$userQuery = "SELECT * FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $userQuery);
mysqli_stmt_bind_param($stmt, "s", $user_id);  // Assuming user_id is a string/UUID
mysqli_stmt_execute($stmt);
$userResult = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($userResult);

if (!$user) {
    $_SESSION['msg'] = "User information not found.";
    header("Location: start_exam.php?id=$exam_id");
    exit();
}

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
        }

        .quiz label:hover {
            background-color: rgba(0, 0, 0, 0.1);
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
        <div class="intro">
            <h1>CODEFEST INSTITUTE OF TECHNOLOGY</h1>
            <h2>SILICON VALLEY, CENTENARY CITY</h2>
            <div style="display: flex; align-items: center; justify-content: center">
                <h3 style="margin-right: 10px">PROGRAM: <?php echo htmlspecialchars(strtoupper($exam['program_name'])); ?>
            </div>
        </div>
        <div class="name-section">
            <div>
                <div><strong>NAME:</strong> <span><?php echo htmlspecialchars(strtoupper($user['full_name'])); ?></span></div>
                <div><strong>EMAIL:</strong> <span><a href="mailto:<?php echo htmlspecialchars($user['email']); ?>"><?php echo htmlspecialchars(strtoupper($user['email'])); ?></a></span></div>
                <div><strong>PROGRAM:</strong> <span><?php echo htmlspecialchars(strtoupper($exam['program_name'])); ?></span></div>
                <div><strong>COURSE:</strong> <span><?php echo htmlspecialchars(strtoupper($exam['exam_name'])); ?></span></div>
            </div>
            <div class="timer">
                <strong>DURATION: <span class="countdown"><?php echo htmlspecialchars(convertMinutesToHours($exam['duration_in_minutes'])); ?></span></strong> <br>
                <strong>TIME LEFT: <span id="countdown" class="countdown"><?php echo htmlspecialchars(convertMinutesToHours($exam['duration_in_minutes'])); ?></span></span></h3>
            </div>
        </div>

        <form class="quiz" id="quiz-form" method="POST" action="process_form.php">
            <?php if (!empty($questions)): ?>
                <?php foreach ($questions as $index => $question): ?>
                    <div style="margin-bottom: 20px;">
                        <p id="q" style="margin-bottom: 15px;">
                            <strong><?php echo ($index + 1) . '. ' . htmlspecialchars($question['question_text']); ?></strong>
                        </p>

                        <!-- Check if the question has an image (stored as longblob) -->
                        <?php if (!empty($question['image'])): ?>
                            <?php
                            // Convert the binary image data to base64
                            $imageData = base64_encode($question['image']);
                            // Determine the image's MIME type (adjust if needed)
                            $mimeType = 'image/jpeg'; // You can store MIME type in the database, or infer it dynamically if needed.
                            ?>
                            <img src="data:<?php echo $mimeType; ?>;base64,<?php echo $imageData; ?>" alt="Question Image" style="max-width: 100%; height: auto; margin-bottom: 15px;">
                        <?php endif; ?>

                        <?php if (isset($optionsMap[$question['question_id']])): ?>
                            <?php foreach ($optionsMap[$question['question_id']] as $option): ?>
                                <label style='font-weight: normal'>
                                    <input type="radio"
                                        name="selected_option[<?php echo htmlspecialchars($question['question_id']); ?>]"
                                        value="<?php echo htmlspecialchars($option['option_label']); ?>">
                                    &nbsp;&nbsp;&nbsp;<?php echo htmlspecialchars($option['option_text']); ?>
                                </label><br>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No questions available for this exam.</p>
            <?php endif; ?>

            <button type="button" onclick="submitQuiz(false)">SUBMIT EXAM</button>
        </form>


        <div id="result"></div>
    </div>

    <script>
        function submitQuiz(autoSubmit = false) {
            const form = document.getElementById('quiz-form');
            const inputs = form.querySelectorAll('input[type="radio"]');
            const answeredQuestions = {};
            const exam_pin = '<?php echo $exam_pin; ?>'; // Fetch the exam_pin from PHP variable
            const exam_id = '<?php echo $exam_id; ?>'; // Fetch the exam_id from PHP variable

            // Get all question names (to ensure each question is accounted for)
            const questionNames = new Set([...inputs].map(input => input.name));

            // Iterate over all unique question names
            questionNames.forEach(name => {
                const selectedInput = form.querySelector(`input[name="${name}"]:checked`);
                if (selectedInput) {
                    answeredQuestions[name] = selectedInput.value;
                } else {
                    answeredQuestions[name] = 'NONE'; // Mark unanswered questions
                }
            });

            const answeredCount = Object.values(answeredQuestions).filter(value => value !== 'NONE').length;

            // Confirm before submission if not all questions are answered
            if (!autoSubmit && answeredCount < questionNames.size) {
                if (!confirm("You haven't answered all questions. Do you want to submit anyway?")) {
                    return; // Stop submission if the user chooses not to submit
                }
            } else if (!autoSubmit && answeredCount === questionNames.size) {
                // Confirm submission if all questions are answered
                if (!confirm("You have answered all questions. Are you sure you want to submit?")) {
                    return; // Stop submission if the user chooses not to submit
                }
            }

            // Prepare data to send to the server
            const formDataToSend = {
                answeredQuestions,
                exam_pin,
                exam_id
            };

            console.log(formDataToSend);

            // Use the Fetch API to submit the data
            fetch('https://exams.codefest.africa/process_form.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formDataToSend),
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error status: ${response.status}`);
                    }
                    return response.text(); // Assuming the server returns a JSON response
                })
                .then(data => {
                    console.log("Success:", data); // Log the server's response
                    clearStorage(); // Clear localStorage after submission
                    window.location.href = 'exam_submitted'; // Redirect upon success
                })
                .catch(error => {
                    console.error("Error:", error);
                });
        }

        // Timer function
        function startTimer() {
            let duration = <?php echo $duration ?>;
            let timeLimit = duration; // Time in seconds
            const timerElement = document.getElementById('countdown');

            // Load remaining time from localStorage
            const savedTime = localStorage.getItem('examTime');
            if (savedTime) {
                timeLimit = parseInt(savedTime, 10);
            }

            const updateTimer = () => {
                if (timeLimit <= 0) {
                    alert("Time's up! Submitting your quiz.");
                    submitQuiz(true); // Auto-submit when time is up
                    return;
                }

                // Save remaining time to localStorage
                localStorage.setItem('examTime', timeLimit);

                const hours = Math.floor(timeLimit / 3600);
                const minutes = Math.floor((timeLimit % 3600) / 60);
                const seconds = timeLimit % 60;

                timerElement.textContent = `${hours > 0 ? `${hours}hr` : ''} ${minutes < 10 ? '0' + minutes : minutes}min ${seconds < 10 ? '0' + seconds : seconds}sec`;

                timeLimit--;
                setTimeout(updateTimer, 1000); // Update the timer every second
            };

            updateTimer(); // Start the timer
        }

        function loadAnsweredQuestions() {
            const answeredQuestions = JSON.parse(localStorage.getItem('answeredQuestions')) || {};
            for (const [name, answer] of Object.entries(answeredQuestions)) {
                const input = document.querySelector(`input[name="${name}"][value="${answer}"]`);
                if (input) {
                    input.checked = true; // Set the checked property for the saved answers
                }
            }
        }

        window.onload = function() {
            loadAnsweredQuestions(); // Load answered questions when the page loads
            startTimer(); // Start the timer
        };

        // Save answers to localStorage on change
        const form = document.getElementById('quiz-form');
        form.addEventListener('change', () => {
            const inputs = form.querySelectorAll('input[type="radio"]');
            const answeredQuestions = {};

            inputs.forEach(input => {
                if (input.checked) {
                    answeredQuestions[input.name] = input.value;
                }
            });

            localStorage.setItem('answeredQuestions', JSON.stringify(answeredQuestions)); // Save answered questions
        });

        // Clear localStorage when the exam is submitted
        function clearStorage() {
            localStorage.removeItem('answeredQuestions');
            localStorage.removeItem('examTime');
        }
    </script>
</body>

</html>