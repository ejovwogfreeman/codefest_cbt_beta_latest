<?php


include('./config/session.php');
include('./partials/header.php');
// include('./config/db.php');

if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    $userId = $user['user_id'];
}

// Function to get user ID from the URL using regular expression
$userid = '';

// Check if the 'id' parameter is set in the GET request
if (isset($_GET['id'])) {
    // Sanitize and retrieve the 'id' parameter
    $userid = htmlspecialchars($_GET['id']); // Sanitize output to prevent XSS
}

// Debug: Print the result of the regex match
if ($userId === null) {
    echo "No user ID found in the URL.<br>";
} else {
    // echo "User ID: $userId<br>";
}

if ($userId !== null) {
    // Fetch the user details from the database
    $sql = "SELECT * FROM users WHERE user_id = '$userId'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $user = mysqli_fetch_assoc($result);
        $firstName = isset($user['first_name']) ? $user['first_name'] : null;
        $lastName = isset($user['last_name']) ? $user['last_name'] : null;

        // Use first name and last name if available, otherwise use username
        $nameToShow = $firstName && $lastName ? "$firstName $lastName" : $user['username'];

        $imageData = $user['profile_picture'];
        if ($imageData) {
            $imageInfo = getimagesizefromstring($imageData);
            if ($imageInfo !== false) {
                $imageFormat = $imageInfo['mime'];
                $img_src = "data:$imageFormat;base64," . base64_encode($imageData);
            } else {
                $img_src = ""; // Handle case where image data is invalid or not found
            }
        } else {
            $img_src = "";
        }
    } else {
        echo "Error fetching user details: " . mysqli_error($conn);
    }
} else {
    echo "Invalid user ID.";
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

            // Remove the element after the total duration of the animation (9 seconds)
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

$url = $_SERVER['REQUEST_URI'];


?>

<style>
    .flying-success-alert {
        position: fixed;
        z-index: 11111111111111;
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
        z-index: 11111111111111;
        top: 15px;
        left: -300px;
        background-color: #FF5252;
        color: #fff;
        padding: 10px;
        border-radius: 5px;
        transition: left 1.5s ease-in-out;
    }
</style>

<!-- <div class="header">
    <h1 style="color:white">Codefest CBT / Dashboard</h1>
</div> -->
<div class="container">
    <div class="d-flex align-items-center my-4">
        <?php if (isset($_SESSION['user'])) : ?>
            <?php include('./partials/sidebar.php'); ?>
            <button class="btn border" id="menuBtn" style="font-size: 20px">&#9776;</button>
            <h2 class="m-0" style="margin-left: 20px !important">Your Dashboard</h2>
        <?php endif ?>
    </div>
    <!-- Grid system for 3 card below the dashboard panel -->
    <div class="row">
        <!-- Enroll A Program Card -->
        <div class="col-lg-4 col-12 mb-4">
            <div class="card h-100 text-center">
                <div class="card-body py-4">
                    <h3 class="card-title text-primary">Enroll A Program</h3>
                    <svg style="font-size: 30px" xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em" viewBox="0 0 2048 2048" class="mb-3">
                        <path fill="currentColor" d="M1848 896q42 0 78 15t64 42t42 63t16 78q0 39-15 76t-43 65l-717 719l-377 94l94-377l717-718q28-28 65-42t76-15m51 249q21-21 21-51q0-31-20-50t-52-20q-14 0-27 4t-23 15l-692 694l-34 135l135-34zM640 896H512V768h128zm896 0H768V768h768zM512 1152h128v128H512zm128-640H512V384h128zm896 0H768V384h768zM384 1664h443l-32 128H256V0h1536v743q-67 10-128 44V128H384zm384-512h514l-128 128H768z" />
                    </svg>
                    <p class="card-text">Enroll for a program.</p>
                    <a href="https://exams.codefest.africa/enroll_program" class="btn btn-primary">Enroll Now</a>
                </div>
            </div>
        </div>

        <!-- Enroll An Exam Card -->
        <div class="col-lg-4 col-12 mb-4">
            <div class="card h-100 text-center">
                <div class="card-body py-4">
                    <h3 class="card-title text-primary">Enroll An Exam</h3>
                    <svg style="font-size: 30px" xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em" viewBox="0 0 2048 2048" class="mb-3">
                        <path fill="currentColor" d="M1848 896q42 0 78 15t64 42t42 63t16 78q0 39-15 76t-43 65l-717 719l-377 94l94-377l717-718q28-28 65-42t76-15m51 249q21-21 21-51q0-31-20-50t-52-20q-14 0-27 4t-23 15l-692 694l-34 135l135-34zM640 896H512V768h128zm896 0H768V768h768zM512 1152h128v128H512zm128-640H512V384h128zm896 0H768V384h768zM384 1664h443l-32 128H256V0h1536v743q-67 10-128 44V128H384zm384-512h514l-128 128H768z" />
                    </svg>
                    <p class="card-text">Enroll for an exam.</p>
                    <a href="https://exams.codefest.africa/enroll_exam" class="btn btn-primary">Enroll Now</a>
                </div>
            </div>
        </div>

        <!-- Check Result Card -->
        <div class="col-lg-4 col-12 mb-4">
            <div class="card h-100 text-center">
                <div class="card-body py-4">
                    <h3 class="card-title text-primary">Check Result</h3>
                    <svg style="font-size: 30px" xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em" viewBox="0 0 32 32" class="mb-3">
                        <circle cx="26" cy="26" r="4" fill="currentColor" />
                        <path fill="currentColor" d="M10 13h2v2h-2zm0 5h2v2h-2zm0 5h2v2h-2zm4-10h8v2h-8zm0 5h8v2h-8zm0 5h4v2h-4z" />
                        <path fill="currentColor" d="M7 28V7h3v3h12V7h3v11h2V7a2 2 0 0 0-2-2h-3V4a2 2 0 0 0-2-2h-8a2 2 0 0 0-2 2v1H7a2 2 0 0 0-2 2v21a2 2 0 0 0 2 2h11v-2Zm5-24h8v4h-8Z" />
                    </svg>
                    <p class="card-text">Check results of written exams.</p>
                    <a href="https://exams.codefest.africa/results" class="btn btn-primary">Check Now</a>
                </div>
            </div>
        </div>

        <!--Get Certificate Card -->
        <div class="col-lg-4 col-12 mb-4">
            <div class="card h-100 text-center">
                <div class="card-body py-4">
                    <h3 class="card-title text-primary">Get Certificate</h3>
                    <svg style="font-size: 30px" xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em" viewBox="0 0 256 256" class="mb-3">
                        <path fill="currentColor" d="M128 136a8 8 0 0 1-8 8H72a8 8 0 0 1 0-16h48a8 8 0 0 1 8 8m-8-40H72a8 8 0 0 0 0 16h48a8 8 0 0 0 0-16m112 65.47V224a8 8 0 0 1-12 7l-24-13.74L172 231a8 8 0 0 1-12-7v-24H40a16 16 0 0 1-16-16V56a16 16 0 0 1 16-16h176a16 16 0 0 1 16 16v30.53a51.88 51.88 0 0 1 0 74.94M160 184v-22.53A52 52 0 0 1 216 76V56H40v128Zm56-12a51.88 51.88 0 0 1-40 0v38.22l16-9.16a8 8 0 0 1 7.94 0l16 9.16Zm16-48a36 36 0 1 0-36 36a36 36 0 0 0 36-36" />
                    </svg>
                    <p class="card-text">Learn more about our company and services.</p>
                    <a href="https://exams.codefest.africa/certificates" class="btn btn-primary">Get Certified</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Simple Modal -->
<div id="loginSuccessModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Codefest CBT</h2>
            <span class="btn btn-primary" id="closeModalIcon">&times;</span>
        </div>
        <div class="modal-body">
            <?php
            // if (isset($_SESSION['login_success_modal'])) {
            //     echo $_SESSION['login_success_modal'];
            // }
            ?>
        </div>
        <div class="modal-footer">
            <button id="closeModalButton" class="btn btn-primary">Close</button>
        </div>
    </div>
</div>

<?php include('./partials/footer.php'); ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if there's a session message for login success
        <?php if (isset($_SESSION['login_success_modal'])): ?>
            var modal = document.getElementById('loginSuccessModal');
            var closeButton = document.getElementById('closeModalButton');
            var closeIcon = document.getElementById('closeModalIcon');
            var modalBody = document.querySelector('.modal-body')

            modalBody.innerHTML = `<?php echo $_SESSION['login_success_modal']; ?>`

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

            // Unset the session message after showing the modal
            <?php unset($_SESSION['login_success_modal']); ?>
        <?php endif; ?>
    });
</script>

<script>
    // JavaScript function to copy the referral link to clipboard
    function copyToClipboard() {
        // Get the referral link text
        var referralLink = document.getElementById("referralLink").textContent;

        // Create a temporary input element to copy text
        var tempInput = document.createElement("input");
        tempInput.type = "text";
        tempInput.value = referralLink;

        // Append the input element to the body and select the text
        document.body.appendChild(tempInput);
        tempInput.select();
        tempInput.setSelectionRange(0, 99999); // For mobile devices

        // Copy the text to the clipboard
        document.execCommand("copy");

        // Remove the temporary input element
        document.body.removeChild(tempInput);

        // Optionally, provide user feedback (alert or any other method)
        alert("Referral link copied to clipboard!");
    }
</script>