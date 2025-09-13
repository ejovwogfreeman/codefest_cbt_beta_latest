<?php

ob_start();
// Start session
// Check if a session is already started before calling session_start()
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('./partials/header.php');
require_once('./config/db.php');
require_once('./config/random_id.php');
include 'mail.php';

if (isset($_SESSION['user'])) {
    header("Location: /dashboard?id={$_SESSION['user']['user_id']}");
}

$emailSubject = 'WELCOME ON BOARD';
$htmlFilePath = './html_mails/register.html';

$fullName = $email = $phone_number = $password = $confirmPassword = '';
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate first name
    if (empty($_POST["fullName"])) {
        $errors['fullName'] = 'Full Name is required.';
    } else {
        $fullName = htmlspecialchars($_POST["fullName"]);
    }


    // Validate email
    if (empty($_POST["email"])) {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format.';
    } else {
        $email = htmlspecialchars($_POST["email"]);
    }

    // Validate first name
    if (empty($_POST["phone_number"])) {
        $errors['phone_number'] = 'Phone Number is required.';
    } else {
        $phone_number = htmlspecialchars($_POST["phone_number"]);
    }

    // Validate password
    if (empty($_POST["password"])) {
        $errors['password'] = 'Password is required.';
    } elseif (strlen($_POST["password"]) < 8) {
        $errors['password'] = 'Password must be at least 8 characters.';
    } else {
        $password = $_POST["password"];
    }

    // Validate confirm password
    if (empty($_POST["confirmPassword"])) {
        $errors['confirmPassword'] = 'Confirm password is required.';
    } elseif ($_POST["confirmPassword"] !== $_POST["password"]) {
        $errors['confirmPassword'] = 'Passwords do not match.';
    } else {
        $confirmPassword = $_POST["confirmPassword"];
    }

    // If no errors, proceed to register the user
    if (empty($errors)) {
        // Check if the email already exists
        $emailCheckQuery = "SELECT email FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $emailCheckQuery);

        if (mysqli_num_rows($result) > 0) {
            $errors['email'] = 'User already exists with this email.';
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Prepare the SQL statement
            $username = explode('@', $email)[0]; // Extract the part before '@' in the email
            $userId = random_id(); // Assuming you have this function defined somewhere
            $dateJoined = date('Y-m-d H:i:s'); // Get the current date and time
            $status = 'inactive';
            $isadmin = 'false';

            // Prepare the SQL statement
            $sql = "INSERT INTO users (user_id, username, full_name, email, phone_number, password, status, is_admin, date_joined) VALUES ('$userId', '$username', '$fullName', '$email', $phone_number,  '$hashedPassword', '$status', '$isadmin', '$dateJoined')";

            $sql_query = mysqli_query($conn, $sql);

            if ($sql_query) {

                // Send the email
                sendEmail($email, $emailSubject, $htmlFilePath, $email, $password);

                $_SESSION['msg'] = "Account Created for $username successfully, please check your email/spam folder for an activation mail";
                header('Location: login');
                exit();
            } else {
                $msg = 'Database error: ' . mysqli_error($conn);
            }
        }
    } else {
        // echo 'error occured';
    }

    // Close the database connection
    mysqli_close($conn);
}

ob_end_flush();

?>

<style>
    .phone-container {
        display: flex;
        align-items: center;
        justify-content: space-bwtween;
        padding: 0px;
        width: 100%;
    }

    .country-code-dropdown {
        width: 100px;
        padding: 5px;
        border: none;
        outline: none;
    }

    .phone-number-input {
        width: 100%;
        padding: 10px;
        margin-right: 0px;
    }
</style>

<!-- Registration Start -->
<div class="container-fluid py-5 px-0">
    <div class="container h-100 d-flex justify-content-center align-items-center p-0">
        <div class="col-lg-5">
            <div class="card border">
                <div class="card-header text-center p-4 bg-primary">
                    <h1 class="m-0 text-light">Sign Up Now</h1>
                </div>
                <div class="card-body rounded-bottom py-5 px-sm-5 px-3 bg-light">
                    <form method="POST">
                        <div class="form-group">
                            <label for="fullname" class="form-label">Fullname</label>
                            <input type="text" name="fullName" value="<?php echo $fullName; ?>" class="form-control <?php echo isset($errors['fullName']) ? 'is-invalid' : ''; ?>" placeholder="Your Full Name" />
                            <?php echo isset($errors['fullName']) ? "<div class='invalid-feedback'>" . $errors['fullName'] . "</div>" : ""; ?>
                        </div>
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" name="email" value="<?php echo $email; ?>" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" placeholder="Your Email" />
                            <?php echo isset($errors['email']) ? "<div class='invalid-feedback'>" . $errors['email'] . "</div>" : ""; ?>
                        </div>
                        <div class="form-group">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input type="text" name="phone_number" value="<?php echo $phone_number; ?>" class="form-control <?php echo isset($errors['phone_number']) ? 'is-invalid' : ''; ?>" placeholder="Your Phone Number" />
                            <?php echo isset($errors['phone_number']) ? "<div class='invalid-feedback'>" . $errors['phone_number'] . "</div>" : ""; ?>
                        </div>
                        <!--<div class="form-group">-->
                        <!--    <label for="phone_number" class="form-label">Phone Number</label>-->
                        <!--    <div class="phone-container form-control">-->
                        <!-- Country Code Dropdown -->
                        <!--        <select id="country_code" name="country_code" class="country-code-dropdown">-->
                        <!-- The options will be added dynamically via JavaScript -->
                        <!--        </select>-->

                        <!-- Phone Number Input -->
                        <!--        <input type="tel" id="phone_number" name="phone_number" -->
                        <!--               value="<?php echo $phone_number; ?>" -->
                        <!--               class="form-control <?php echo isset($errors['phone_number']) ? 'is-invalid' : ''; ?>" -->
                        <!--               placeholder="Your Phone Number" />-->
                        <!--    </div>-->
                        <!--    <?php echo isset($errors['phone_number']) ? "<div class='invalid-feedback'>" . $errors['phone_number'] . "</div>" : ""; ?>-->
                        <!--</div>-->
                        <div class="form-group">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" value="<?php echo $password; ?>" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" placeholder="Your Password" />
                            <?php echo isset($errors['password']) ? "<div class='invalid-feedback'>" . $errors['password'] . "</div>" : ""; ?>
                        </div>
                        <div class="form-group">
                            <label for="password" class="form-label">Confirm Password</label>
                            <input type="password" name="confirmPassword" value="<?php echo $confirmPassword; ?>" class="form-control <?php echo isset($errors['confirmPassword']) ? 'is-invalid' : ''; ?>" placeholder="Confirm Password" />
                            <?php echo isset($errors['confirmPassword']) ? "<div class='invalid-feedback'>" . $errors['confirmPassword'] . "</div>" : ""; ?>
                        </div>
                        <div>
                            <button class="btn btn-dark btn-block border-0 py-3" type="submit">Sign Up Now</button>
                        </div>
                        <small class="d-block text-center mt-3">Already have an account? <a href="https://exams.codefest.africa/login">LOGIN</a></small>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Registration End -->

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Array of country codes and their names, sorted alphabetically
        const countryCodes = [{
                code: "+1",
                country: "USA"
            },
            {
                code: "+93",
                country: "Afghanistan"
            },
            {
                code: "+355",
                country: "Albania"
            },
            {
                code: "+213",
                country: "Algeria"
            },
            {
                code: "+1684",
                country: "American Samoa"
            },
            {
                code: "+376",
                country: "Andorra"
            },
            {
                code: "+244",
                country: "Angola"
            },
            {
                code: "+1264",
                country: "Anguilla"
            },
            {
                code: "+672",
                country: "Antarctica"
            },
            {
                code: "+1268",
                country: "Antigua and Barbuda"
            },
            {
                code: "+54",
                country: "Argentina"
            },
            {
                code: "+374",
                country: "Armenia"
            },
            {
                code: "+61",
                country: "Australia"
            },
            {
                code: "+43",
                country: "Austria"
            },
            {
                code: "+994",
                country: "Azerbaijan"
            },
            {
                code: "+1242",
                country: "Bahamas"
            },
            {
                code: "+973",
                country: "Bahrain"
            },
            {
                code: "+880",
                country: "Bangladesh"
            },
            {
                code: "+1246",
                country: "Barbados"
            },
            {
                code: "+375",
                country: "Belarus"
            },
            {
                code: "+32",
                country: "Belgium"
            },
            {
                code: "+501",
                country: "Belize"
            },
            {
                code: "+229",
                country: "Benin"
            },
            {
                code: "+975",
                country: "Bhutan"
            },
            {
                code: "+591",
                country: "Bolivia"
            },
            {
                code: "+387",
                country: "Bosnia and Herzegovina"
            },
            {
                code: "+267",
                country: "Botswana"
            },
            {
                code: "+55",
                country: "Brazil"
            },
            {
                code: "+1284",
                country: "British Virgin Islands"
            },
            {
                code: "+673",
                country: "Brunei"
            },
            {
                code: "+359",
                country: "Bulgaria"
            },
            {
                code: "+226",
                country: "Burkina Faso"
            },
            {
                code: "+257",
                country: "Burundi"
            },
            {
                code: "+855",
                country: "Cambodia"
            },
            {
                code: "+237",
                country: "Cameroon"
            },
            {
                code: "+1",
                country: "Canada"
            },
            {
                code: "+238",
                country: "Cape Verde"
            },
            {
                code: "+1345",
                country: "Cayman Islands"
            },
            {
                code: "+236",
                country: "Central African Republic"
            },
            {
                code: "+56",
                country: "Chile"
            },
            {
                code: "+86",
                country: "China"
            },
            {
                code: "+61",
                country: "Christmas Island"
            },
            {
                code: "+61",
                country: "Cocos Islands"
            },
            {
                code: "+57",
                country: "Colombia"
            },
            {
                code: "+269",
                country: "Comoros"
            },
            {
                code: "+242",
                country: "Congo (Congo-Brazzaville)"
            },
            {
                code: "+243",
                country: "Congo (Democratic Republic of the Congo)"
            },
            {
                code: "+682",
                country: "Cook Islands"
            },
            {
                code: "+506",
                country: "Costa Rica"
            },
            {
                code: "+225",
                country: "Côte d'Ivoire"
            },
            {
                code: "+385",
                country: "Croatia"
            },
            {
                code: "+53",
                country: "Cuba"
            },
            {
                code: "+599",
                country: "Curaçao"
            },
            {
                code: "+356",
                country: "Malta"
            },
            {
                code: "+257",
                country: "Burundi"
            },
            {
                code: "+682",
                country: "Cook Islands"
            },
            {
                code: "+45",
                country: "Denmark"
            },
            {
                code: "+253",
                country: "Djibouti"
            },
            {
                code: "+1",
                country: "Dominica"
            },
            {
                code: "+1809",
                country: "Dominican Republic"
            },
            {
                code: "+593",
                country: "Ecuador"
            },
            {
                code: "+20",
                country: "Egypt"
            },
            {
                code: "+503",
                country: "El Salvador"
            },
            {
                code: "+240",
                country: "Equatorial Guinea"
            },
            {
                code: "+291",
                country: "Eritrea"
            },
            {
                code: "+372",
                country: "Estonia"
            },
            {
                code: "+251",
                country: "Ethiopia"
            },
            {
                code: "+500",
                country: "Falkland Islands"
            },
            {
                code: "+298",
                country: "Faroe Islands"
            },
            {
                code: "+679",
                country: "Fiji"
            },
            {
                code: "+358",
                country: "Finland"
            },
            {
                code: "+33",
                country: "France"
            },
            {
                code: "+241",
                country: "Gabon"
            },
            {
                code: "+220",
                country: "Gambia"
            },
            {
                code: "+995",
                country: "Georgia"
            },
            {
                code: "+49",
                country: "Germany"
            },
            {
                code: "+233",
                country: "Ghana"
            },
            {
                code: "+350",
                country: "Gibraltar"
            },
            {
                code: "+30",
                country: "Greece"
            },
            {
                code: "+299",
                country: "Greenland"
            },
            {
                code: "+1473",
                country: "Grenada"
            },
            {
                code: "+590",
                country: "Guadeloupe"
            },
            {
                code: "+502",
                country: "Guatemala"
            },
            {
                code: "+224",
                country: "Guinea"
            },
            {
                code: "+245",
                country: "Guinea-Bissau"
            },
            {
                code: "+592",
                country: "Guyana"
            },
            {
                code: "+509",
                country: "Haiti"
            },
            {
                code: "+504",
                country: "Honduras"
            },
            {
                code: "+852",
                country: "Hong Kong"
            },
            {
                code: "+36",
                country: "Hungary"
            },
            {
                code: "+354",
                country: "Iceland"
            },
            {
                code: "+91",
                country: "India"
            },
            {
                code: "+62",
                country: "Indonesia"
            },
            {
                code: "+98",
                country: "Iran"
            },
            {
                code: "+964",
                country: "Iraq"
            },
            {
                code: "+353",
                country: "Ireland"
            },
            {
                code: "+972",
                country: "Israel"
            },
            {
                code: "+39",
                country: "Italy"
            },
            {
                code: "+1",
                country: "Jamaica"
            },
            {
                code: "+81",
                country: "Japan"
            },
            {
                code: "+962",
                country: "Jordan"
            },
            {
                code: "+7",
                country: "Kazakhstan"
            },
            {
                code: "+254",
                country: "Kenya"
            },
            {
                code: "+686",
                country: "Kiribati"
            },
            {
                code: "+383",
                country: "Kosovo"
            },
            {
                code: "+965",
                country: "Kuwait"
            },
            {
                code: "+996",
                country: "Kyrgyzstan"
            },
            {
                code: "+856",
                country: "Laos"
            },
            {
                code: "+371",
                country: "Latvia"
            },
            {
                code: "+961",
                country: "Lebanon"
            },
            {
                code: "+266",
                country: "Lesotho"
            },
            {
                code: "+231",
                country: "Liberia"
            },
            {
                code: "+218",
                country: "Libya"
            },
            {
                code: "+423",
                country: "Liechtenstein"
            },
            {
                code: "+370",
                country: "Lithuania"
            },
            {
                code: "+352",
                country: "Luxembourg"
            },
            {
                code: "+853",
                country: "Macau"
            },
            {
                code: "+389",
                country: "North Macedonia"
            },
            {
                code: "+261",
                country: "Madagascar"
            },
            {
                code: "+265",
                country: "Malawi"
            },
            {
                code: "+60",
                country: "Malaysia"
            },
            {
                code: "+960",
                country: "Maldives"
            },
            {
                code: "+223",
                country: "Mali"
            },
            {
                code: "+356",
                country: "Malta"
            },
            {
                code: "+692",
                country: "Marshall Islands"
            },
            {
                code: "+596",
                country: "Martinique"
            },
            {
                code: "+222",
                country: "Mauritania"
            },
            {
                code: "+230",
                country: "Mauritius"
            },
            {
                code: "+262",
                country: "Mayotte"
            },
            {
                code: "+52",
                country: "Mexico"
            },
            {
                code: "+691",
                country: "Micronesia"
            },
            {
                code: "+373",
                country: "Moldova"
            },
            {
                code: "+377",
                country: "Monaco"
            },
            {
                code: "+976",
                country: "Mongolia"
            },
            {
                code: "+382",
                country: "Montenegro"
            },
            {
                code: "+1",
                country: "Montserrat"
            },
            {
                code: "+212",
                country: "Morocco"
            },
            {
                code: "+258",
                country: "Mozambique"
            },
            {
                code: "+95",
                country: "Myanmar"
            },
            {
                code: "+264",
                country: "Namibia"
            },
            {
                code: "+674",
                country: "Nauru"
            },
            {
                code: "+977",
                country: "Nepal"
            },
            {
                code: "+31",
                country: "Netherlands"
            },
            {
                code: "+599",
                country: "Netherlands Antilles"
            },
            {
                code: "+687",
                country: "New Caledonia"
            },
            {
                code: "+64",
                country: "New Zealand"
            },
            {
                code: "+505",
                country: "Nicaragua"
            },
            {
                code: "+227",
                country: "Niger"
            },
            {
                code: "+234",
                country: "Nigeria"
            },
            {
                code: "+683",
                country: "Niue"
            },
            {
                code: "+850",
                country: "North Korea"
            },
            {
                code: "+47",
                country: "Norway"
            },
            {
                code: "+968",
                country: "Oman"
            },
            {
                code: "+92",
                country: "Pakistan"
            },
            {
                code: "+680",
                country: "Palau"
            },
            {
                code: "+970",
                country: "Palestinian Territories"
            },
            {
                code: "+507",
                country: "Panama"
            },
            {
                code: "+675",
                country: "Papua New Guinea"
            },
            {
                code: "+595",
                country: "Paraguay"
            },
            {
                code: "+51",
                country: "Peru"
            },
            {
                code: "+63",
                country: "Philippines"
            },
            {
                code: "+48",
                country: "Poland"
            },
            {
                code: "+351",
                country: "Portugal"
            },
            {
                code: "+1",
                country: "Puerto Rico"
            },
            {
                code: "+974",
                country: "Qatar"
            },
            {
                code: "+40",
                country: "Romania"
            },
            {
                code: "+7",
                country: "Russia"
            },
            {
                code: "+250",
                country: "Rwanda"
            },
            {
                code: "+290",
                country: "Saint Helena"
            },
            {
                code: "+1",
                country: "Saint Kitts and Nevis"
            },
            {
                code: "+1758",
                country: "Saint Lucia"
            },
            {
                code: "+1",
                country: "Saint Vincent and the Grenadines"
            },
            {
                code: "+684",
                country: "Samoa"
            },
            {
                code: "+378",
                country: "San Marino"
            },
            {
                code: "+239",
                country: "São Tomé and Príncipe"
            },
            {
                code: "+966",
                country: "Saudi Arabia"
            },
            {
                code: "+221",
                country: "Senegal"
            },
            {
                code: "+381",
                country: "Serbia"
            },
            {
                code: "+248",
                country: "Seychelles"
            },
            {
                code: "+232",
                country: "Sierra Leone"
            },
            {
                code: "+65",
                country: "Singapore"
            },
            {
                code: "+421",
                country: "Slovakia"
            },
            {
                code: "+386",
                country: "Slovenia"
            },
            {
                code: "+677",
                country: "Solomon Islands"
            },
            {
                code: "+252",
                country: "Somalia"
            },
            {
                code: "+27",
                country: "South Africa"
            },
            {
                code: "+82",
                country: "South Korea"
            },
            {
                code: "+34",
                country: "Spain"
            },
            {
                code: "+94",
                country: "Sri Lanka"
            },
            {
                code: "+249",
                country: "Sudan"
            },
            {
                code: "+597",
                country: "Suriname"
            },
            {
                code: "+47",
                country: "Svalbard"
            },
            {
                code: "+268",
                country: "Swaziland"
            },
            {
                code: "+46",
                country: "Sweden"
            },
            {
                code: "+41",
                country: "Switzerland"
            },
            {
                code: "+963",
                country: "Syria"
            },
            {
                code: "+886",
                country: "Taiwan"
            },
            {
                code: "+992",
                country: "Tajikistan"
            },
            {
                code: "+255",
                country: "Tanzania"
            },
            {
                code: "+66",
                country: "Thailand"
            },
            {
                code: "+228",
                country: "Togo"
            },
            {
                code: "+690",
                country: "Tokelau"
            },
            {
                code: "+676",
                country: "Tonga"
            },
            {
                code: "+1",
                country: "Trinidad and Tobago"
            },
            {
                code: "+216",
                country: "Tunisia"
            },
            {
                code: "+90",
                country: "Turkey"
            },
            {
                code: "+993",
                country: "Turkmenistan"
            },
            {
                code: "+1",
                country: "Turks and Caicos Islands"
            },
            {
                code: "+688",
                country: "Tuvalu"
            },
            {
                code: "+256",
                country: "Uganda"
            },
            {
                code: "+380",
                country: "Ukraine"
            },
            {
                code: "+971",
                country: "United Arab Emirates"
            },
            {
                code: "+44",
                country: "United Kingdom"
            },
            {
                code: "+1",
                country: "United States"
            },
            {
                code: "+598",
                country: "Uruguay"
            },
            {
                code: "+998",
                country: "Uzbekistan"
            },
            {
                code: "+678",
                country: "Vanuatu"
            },
            {
                code: "+379",
                country: "Vatican City"
            },
            {
                code: "+58",
                country: "Venezuela"
            },
            {
                code: "+84",
                country: "Vietnam"
            },
            {
                code: "+1284",
                country: "Virgin Islands"
            },
            {
                code: "+967",
                country: "Yemen"
            },
            {
                code: "+260",
                country: "Zambia"
            },
            {
                code: "+263",
                country: "Zimbabwe"
            }
        ];

        // Populate country code dropdown dynamically
        const dropdown = document.getElementById("country_code");
        countryCodes.forEach(country => {
            const option = document.createElement("option");
            option.value = country.code;
            option.textContent = `${country.country} (${country.code})`;
            dropdown.appendChild(option);
        });
    });
</script>

<?php include('./partials/footer.php') ?>