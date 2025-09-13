<?php

session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('./config/db.php');
include('./config/random_id.php');

// Set header for JSON response
header('Content-Type: application/json');

// Check the request method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve JSON input from the request
    $data = json_decode(file_get_contents('php://input'), true);

    // Initialize the response array
    $response = [];

    if (isset($data['exam_pin'])) {
        $exam_pin = $data['exam_pin'];

        // Prepare the SQL query to get user_id and registration_id from exam_registrations
        $sqlIdQuery = "SELECT student_id, registration_id FROM exam_registrations WHERE exam_pin = ?";

        // Prepare and execute the statement for the first query
        if ($stmt = mysqli_prepare($conn, $sqlIdQuery)) {
            mysqli_stmt_bind_param($stmt, "s", $exam_pin);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $user_id, $registration_id); // Bind both user_id and registration_id

            // Fetch the user_id and registration_id
            if (mysqli_stmt_fetch($stmt)) {
                // Close the first statement properly
                mysqli_stmt_close($stmt);

                // If user_id is found, proceed to fetch user details
                $sqlUserQuery = "SELECT full_name, email FROM users WHERE user_id = ?";

                // Prepare and execute the statement for the second query
                if ($stmt = mysqli_prepare($conn, $sqlUserQuery)) {
                    mysqli_stmt_bind_param($stmt, "s", $user_id);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt); // Get the result set

                    if ($user = mysqli_fetch_assoc($result)) {
                        // Free the result and close the second statement
                        mysqli_free_result($result);
                        mysqli_stmt_close($stmt);

                        // If user is found, add it to the response
                        $response['status'] = 'success';
                        $response['user'] = $user;
                        $response['registration_id'] = $registration_id; // Include registration_id in the response

                        // Now extract answered questions
                        if (isset($data['answeredQuestions'])) {
                            $answeredQuestions = $data['answeredQuestions'];
                            $response['questions'] = []; // Initialize array to hold question details

                            // Prepare SQL for inserting student answers
                            $insertAnswerQuery = "
                                INSERT INTO student_answers (
                                    attempt_id, 
                                    student_id, 
                                    exam_id,
                                    registration_id,
                                    exam_pin, 
                                    question_id, 
                                    student_name, 
                                    student_email, 
                                    selected_option, 
                                    correct_option, 
                                    remark, 
                                    submission_time
                                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                            ";

                            // Prepare the insert statement
                            if ($insertStmt = mysqli_prepare($conn, $insertAnswerQuery)) {
                                foreach ($answeredQuestions as $key => $selected_option) {
                                    // The key contains the question_id
                                    $question_id = str_replace("selected_option[", "", $key);
                                    $question_id = str_replace("]", "", $question_id);

                                    // Prepare SQL query to get the correct_option from questions table
                                    $sqlQuestionQuery = "SELECT correct_option FROM questions WHERE question_id = ?";

                                    // Prepare and execute the statement for the question query
                                    if ($stmtQuestion = mysqli_prepare($conn, $sqlQuestionQuery)) {
                                        mysqli_stmt_bind_param($stmtQuestion, "s", $question_id);
                                        mysqli_stmt_execute($stmtQuestion);
                                        mysqli_stmt_bind_result($stmtQuestion, $correct_option);

                                        if (mysqli_stmt_fetch($stmtQuestion)) {
                                            // Close the question query statement
                                            mysqli_stmt_close($stmtQuestion);

                                            // Compare correct_option with selected_option
                                            $remark = ($correct_option === $selected_option) ? 'correct' : 'wrong';

                                            // Generate a random attempt_id
                                            $attempt_id = random_id(); // Generate a random 32-character hex string

                                            // Bind the parameters for the insert statement
                                            mysqli_stmt_bind_param(
                                                $insertStmt,
                                                "sssssssssss",
                                                $attempt_id,
                                                $user_id,
                                                $data['exam_id'],
                                                $registration_id, // Include registration_id
                                                $exam_pin,
                                                $question_id,
                                                $user['full_name'],
                                                $user['email'],
                                                $selected_option,
                                                $correct_option,
                                                $remark
                                            );

                                            // Execute the insert statement
                                            if (!mysqli_stmt_execute($insertStmt)) {
                                                // Handle SQL execution error
                                                $response['status'] = 'error';
                                                $response['message'] = 'Database insert error: ' . mysqli_stmt_error($insertStmt);
                                                break; // Stop the loop on error
                                            }

                                            // Add the question result to the response
                                            $response['questions'][] = [
                                                'question_id' => $question_id,
                                                'selected_option' => $selected_option,
                                                'correct_option' => $correct_option,
                                                'remark' => $remark
                                            ];

                                            // Now update the exam_registration table if the registration_id or exam_pin exists in the student_answers table
                                            $checkRegistrationQuery = "
                                                SELECT * FROM student_answers 
                                                WHERE registration_id = ? OR exam_pin = ?
                                            ";

                                            if ($checkStmt = mysqli_prepare($conn, $checkRegistrationQuery)) {
                                                mysqli_stmt_bind_param($checkStmt, "ss", $registration_id, $exam_pin);
                                                mysqli_stmt_execute($checkStmt);
                                                $checkResult = mysqli_stmt_get_result($checkStmt);

                                                if ($checkResult && mysqli_num_rows($checkResult) > 0) {
                                                    // There are entries in student_answers for this registration_id or exam_pin
                                                    $updateRegistrationQuery = "
                                                        UPDATE exam_registrations 
                                                        SET exam_status = 'written' 
                                                        WHERE registration_id = ? OR exam_pin = ?
                                                    ";
                                                    if ($updateStmt = mysqli_prepare($conn, $updateRegistrationQuery)) {
                                                        mysqli_stmt_bind_param($updateStmt, "ss", $registration_id, $exam_pin);
                                                        if (mysqli_stmt_execute($updateStmt)) {
                                                            // Successful update
                                                            $response['status'] = 'success'; // Optional: Update the response status

                                                            // Check if the processing was successful
                                                            $submissionSuccessful = true; // Replace with your actual success condition

                                                            if ($submissionSuccessful) {
                                                                // Remove the exam pin from the session
                                                                unset($_SESSION['exam_pin']);
                                                                echo json_encode(['status' => 'success', 'message' => 'Quiz submitted and exam pin removed.']);
                                                            } else {
                                                                echo json_encode(['status' => 'error', 'message' => 'Failed to submit the quiz.']);
                                                            }
                                                        } else {
                                                            // Handle SQL execution error
                                                            $response['status'] = 'error';
                                                            $response['message'] = 'Update error: ' . mysqli_stmt_error($updateStmt);
                                                        }
                                                        mysqli_stmt_close($updateStmt);
                                                    }
                                                }
                                                mysqli_stmt_close($checkStmt);
                                            }
                                        } else {
                                            // Close the question query statement
                                            mysqli_stmt_close($stmtQuestion);
                                        }
                                    }
                                }
                                // Close the insert statement
                                mysqli_stmt_close($insertStmt);
                            }
                        }
                    } else {
                        // Free the result and close the user query statement in case of failure
                        mysqli_free_result($result);
                        mysqli_stmt_close($stmt);
                    }
                }
            } else {
                // Close the first statement in case of failure to fetch user_id
                mysqli_stmt_close($stmt);
            }
        }
    }
    echo json_encode($response);
}
