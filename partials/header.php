<?php
// Database connection

$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

include('./config/db.php');


// Start the session only if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fetch programs and exams from the database
$programs_sql = "SELECT * FROM programs";
$programs_result = mysqli_query($conn, $programs_sql);

// Prepare an array to hold the programs and their exams
$programs = [];
while ($program = mysqli_fetch_assoc($programs_result)) {
    $program_id = $program['program_id'];

    // Fetch exams for each program
    $exams_sql = "SELECT * FROM exams WHERE program_id = '$program_id' ORDER BY created_at DESC";
    $exams_result = mysqli_query($conn, $exams_sql);

    $exams = [];
    while ($exam = mysqli_fetch_assoc($exams_result)) {
        $exams[] = $exam; // Add exam to the exams array
    }

    // Store the program and its exams in the programs array
    $programs[] = [
        'id' => $program['program_id'],
        'name' => $program['program_name'],
        'exams' => $exams
    ];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>CODEFEST CBT</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Codefest CBT platform" name="keywords">
    <meta content="Codefest Institute Of Technology" name="description">

    <!-- Favicon -->
    <!-- <link href="img/favicon.ico" rel="icon"> -->
    <link rel="shortcut icon" href="images/codefest.png" type="image/x-icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <!-- <link href="css/style.css" rel="stylesheet"> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet" />


</head>

<body>

    <!-- Topbar Start -->
    <div class="container-fluid d-none d-lg-block">
        <div class="row align-items-center py-2 px-xl-5">
            <div class="col-lg-3">
                <a href="https://exams.codefest.africa/" class="text-decoration-none">
                    <img src="images/codefest.png" alt="" width="100px">
                </a>
            </div>
            <div class="col-lg-3 text-right">
                <div class="d-inline-flex align-items-center">
                    <i class="fa fa-2x fa-map-marker-alt text-primary mr-3"></i>
                    <div class="text-left">
                        <h6 class="font-weight-semi-bold mb-1">Our Office</h6>
                        <small>Lagos, Nigeria</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 text-right">
                <div class="d-inline-flex align-items-center">
                    <i class="fa fa-2x fa-envelope text-primary mr-3"></i>
                    <div class="text-left">
                        <h6 class="font-weight-semi-bold mb-1">Email Us</h6>
                        <small>exams@codefest.africa</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 text-right">
                <div class="d-inline-flex align-items-center">
                    <i class="fa fa-2x fa-phone text-primary mr-3"></i>
                    <div class="text-left">
                        <h6 class="font-weight-semi-bold mb-1">Call Us</h6>
                        <small>+234(0) 902 000 7572</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Topbar End -->

    <!-- Navbar Start -->
    <div class="container-fluid shadow-sm">
        <div class="row border-top px-xl-5">
            <div class="col-lg-3 d-none d-lg-block">
                <a class="d-flex align-items-center justify-content-between bg-secondary w-100 text-decoration-none" data-toggle="collapse" href="#navbar-vertical" style="height: 67px; padding: 0 30px;">
                    <h5 class="text-primary m-0 d-flex align-items-center"><i class="fa fa-book-open mr-2"></i><span>Programs</span></h5>
                    <!-- <i class="fa fa-angle-down text-primary"></i> -->
                </a>
            </div>

            <div class="col-lg-9">
                <nav class="navbar navbar-expand-lg bg-light navbar-light py-3 py-lg-0 px-0">
                    <a href="https://exams.codefest.africa/" class="text-decoration-none d-block d-lg-none">
                        <?php
                        if (strpos($url, 'admin') !== false) {
                            echo '<img src="../images/codefest.png" alt="" width="100px">';
                        } else {
                            echo '<img src="images/codefest.png" alt="" width="100px">';
                        }
                        ?>
                    </a>
                    <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
                        <div class="navbar-nav py-0">
                            <a href="https://exams.codefest.africa/" class="nav-item nav-link active">Home</a>
                            <a href="https://exams.codefest.africa/#about" class="nav-item nav-link">About</a>
                            <a href="https://exams.codefest.africa/#exams" class="nav-item nav-link">Exams</a>
                            <!-- <a href="https://exams.codefest.africa/#programs" class="nav-item nav-link">Programs</a> -->
                            <!--<a href="https://exams.codefest.africa/#instructors" class="nav-item nav-link">Instructors</a>-->
                            <!-- <div class="nav-item dropdown">
                                <a href="https://exams.codefest.africa/blogs" class="nav-link dropdown-toggle" data-toggle="dropdown">Blog</a>
                                <div class="dropdown-menu rounded-0 m-0">
                                    <a href="blog.html" class="dropdown-item">Blog List</a>
                                    <a href="single.html" class="dropdown-item">Blog Detail</a>
                                </div>
                            </div> -->
                            <a href="https://exams.codefest.africa/#contact" class="nav-item nav-link">Contact</a>
                        </div>
                        <a class="btn btn-primary py-2 px-4 ml-auto d-block"
                            href="<?php echo isset($_SESSION['user']['user_id']) ? 'https://exams.codefest.africa/dashboard?id=' . urlencode($_SESSION['user']['user_id']) : 'https://exams.codefest.africa/login'; ?>">
                            <?php echo isset($_SESSION['user']['user_id']) ? 'Dashboard' : 'Register/Login' ?>
                        </a>
                    </div>
                </nav>
            </div>
        </div>
    </div>
    <!-- Navbar End -->