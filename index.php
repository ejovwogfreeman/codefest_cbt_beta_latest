<?php

include('./partials/header.php');


// Fetch courses from the database
$sql = "SELECT * FROM programs ORDER BY created_at ASC LIMIT 6";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    // Fetch all courses into an associative array
    $programs = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // var_dump($programs);
} else {
    echo "No courses found.";
}

?>

<!-- Carousel Start -->
<div class="container-fluid p-0 pb-5 mb-5">
    <div id="header-carousel" class="carousel slide carousel-fade" data-ride="carousel">
        <ol class="carousel-indicators">
            <li data-target="#header-carousel" data-slide-to="0" class="active"></li>
            <li data-target="#header-carousel" data-slide-to="1"></li>
            <li data-target="#header-carousel" data-slide-to="2"></li>
        </ol>
        <div class="carousel-inner">
            <div class="carousel-item active" style="min-height: 300px;">
                <img class="position-relative w-100" src="images/cbt1.jpg" style="min-height: 300px; object-fit: cover;">
                <div class="carousel-caption d-flex align-items-center justify-content-center">
                    <div class="p-5" style="width: 100%; max-width: 900px;">
                        <h5 class="text-white text-uppercase mb-md-3">CODEFEST CBT</h5>
                        <h1 class="display-3 text-white mb-md-4">Making Exams Easy for Everyone Everywhere</h1>
                        <a href="https://exams.codefest.africa/login" class="btn btn-primary py-md-2 px-md-4 font-weight-semi-bold mt-2">Enroll Today</a>
                    </div>
                </div>
            </div>
            <div class="carousel-item" style="min-height: 300px;">
                <img class="position-relative w-100" src="images/cbt2.jpg" style="min-height: 300px; object-fit: cover;">
                <div class="carousel-caption d-flex align-items-center justify-content-center">
                    <div class="p-5" style="width: 100%; max-width: 900px;">
                        <h5 class="text-white text-uppercase mb-md-3">CODEFEST CBT</h5>
                        <h1 class="display-3 text-white mb-md-4">Simplifying the Way You Take Exams</h1>
                        <a href="https://exams.codefest.africa/login" class="btn btn-primary py-md-2 px-md-4 font-weight-semi-bold mt-2">Enroll Today</a>
                    </div>
                </div>
            </div>
            <div class="carousel-item" style="min-height: 300px;">
                <img class="position-relative w-100" src="images/cbt3.jpg" style="min-height: 300px; object-fit: cover;">
                <div class="carousel-caption d-flex align-items-center justify-content-center">
                    <div class="p-5" style="width: 100%; max-width: 900px;">
                        <h5 class="text-white text-uppercase mb-md-3">Best Online Courses</h5>
                        <h1 class="display-3 text-white mb-md-4">Experience Stress-Free Testing with Every Attempt</h1>
                        <a href="https://exams.codefest.africa/login" class="btn btn-primary py-md-2 px-md-4 font-weight-semi-bold mt-2">Enroll Today</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Carousel End -->


<!-- About Start -->
<div class="container-fluid py-5" id='about'>
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-5">
                <img class="img-fluid rounded mb-4 mb-lg-0 border" src="images/codefest.jpg" alt="" style='width: 100%; height: 100%'>
            </div>
            <div class="col-lg-7">
                <div class="text-left mb-4">
                    <h5 class="text-primary text-uppercase mb-3" style="letter-spacing: 5px;">About Us</h5>
                    <h1>About COdefest Innovative Of Technology</h1>
                </div>
                <p>
                    Codefest Institute of Technology: Pioneering Tech Education in Abakaliki and Lagos

                    Codefest Institute of Technology has established itself as a beacon of innovation and technical education in Nigeria. As a premier institution, it is committed to equipping students with cutting-edge knowledge and skills to thrive in the ever-evolving tech industry.t</p>
                <a class="btn btn-primary py-2 px-4 ml-auto"
                    href="<?php echo isset($_SESSION['user']['user_id']) ? 'https://exams.codefest.africa/dashboard?id=' . urlencode($_SESSION['user']['user_id']) : 'https://exams.codefest.africa/login'; ?>">
                    <?php echo isset($_SESSION['user']['user_id']) ? 'Dashboard' : 'Register/Login' ?>
                </a>
            </div>
        </div>
    </div>
</div>
<!-- About End -->


<!-- Category Start -->
<div class="container-fluid py-5" id='exams'>
    <div class="container pt-5 pb-3">
        <div class="text-center mb-5">
            <h5 class="text-primary text-uppercase mb-3" style="letter-spacing: 5px;">EXAMS</h5>
            <h1>Types Of Exams On Codefest CBT</h1>
        </div>
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="cat-item position-relative overflow-hidden rounded mb-2">
                    <img class="img-fluid" src="images/cat-1.jpg" alt="">
                    <span class="cat-overlay text-white text-decoration-none">
                        <h5 class="text-white font-weight-medium">CERTIFICATION EXAMS</h5>
                    </span>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="cat-item position-relative overflow-hidden rounded mb-2">
                    <img class="img-fluid" src="images/cat-2.jpg" alt="">
                    <span class="cat-overlay text-white text-decoration-none">
                        <h5 class="text-white font-weight-medium">ACADEMIC EXAMS</h5>
                    </span>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="cat-item position-relative overflow-hidden rounded mb-2">
                    <img class="img-fluid" src="images/cat-3.jpg" alt="">
                    <span class="cat-overlay text-white text-decoration-none">
                        <h5 class="text-white font-weight-medium">COMPETITIVE EXAMS</h5>
                    </span>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="cat-item position-relative overflow-hidden rounded mb-2">
                    <img class="img-fluid" src="images/cat-4.jpg" alt="">
                    <span class="cat-overlay text-white text-decoration-none">
                        <h5 class="text-white font-weight-medium">RECRUITMENT EXAMS</h5>
                    </span>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="cat-item position-relative overflow-hidden rounded mb-2">
                    <img class="img-fluid" src="images/cat-5.jpg" alt="">
                    <span class="cat-overlay text-white text-decoration-none">
                        <h5 class="text-white font-weight-medium">PROFESSIONAL EXAMS</h5>
                    </span>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="cat-item position-relative overflow-hidden rounded mb-2">
                    <img class="img-fluid" src="images/cat-6.jpg" alt="">
                    <span class="cat-overlay text-white text-decoration-none">
                        <h5 class="text-white font-weight-medium">ADMISSION EXAMS</h5>
                        </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="cat-item position-relative overflow-hidden rounded mb-2">
                    <img class="img-fluid" src="images/cat-7.jpg" alt="">
                    <span class="cat-overlay text-white text-decoration-none">
                        <h5 class="text-white font-weight-medium">PROMOTIONAL EXAMS</h5>
                    </span>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="cat-item position-relative overflow-hidden rounded mb-2">
                    <img class="img-fluid" src="images/cat-8.jpg" alt="">
                    <span class="cat-overlay text-white text-decoration-none">
                        <h5 class="text-white font-weight-medium">APTITUDE EXAMS</h5>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Category Start -->


<!-- Courses Start -->
<div class="container-fluid py-5" id='courses'>
    <div class="container py-5">
        <div class="text-center mb-5">
            <h5 class="text-primary text-uppercase mb-3" style="letter-spacing: 5px;">Programs</h5>
            <h1>Our Popular Programs</h1>
        </div>
        <div class="row">
            <!--<div class="col-lg-4 col-md-6 mb-4">-->
            <!--    <div class="rounded overflow-hidden mb-2">-->
            <!--        <img class="img-fluid" src="images/course-1.jpg" alt="">-->
            <!--        <div class="bg-secondary p-4">-->
            <!--            <div class="d-flex justify-content-between mb-3">-->
            <!--                <small class="m-0"><i class="fa fa-users text-primary mr-2"></i>25 Students</small>-->
            <!--                <small class="m-0"><i class="far fa-clock text-primary mr-2"></i>01h 30m</small>-->
            <!--            </div>-->
            <!--            <a class="h5" href="">Web design & development courses for beginner</a>-->
            <!--            <div class="border-top mt-4 pt-4">-->
            <!--                <div class="d-flex justify-content-between">-->
            <!--                    <h6 class="m-0"><i class="fa fa-star text-primary mr-2"></i>4.5 <small>(250)</small></h6>-->
            <!--                    <h5 class="m-0">$99</h5>-->
            <!--                </div>-->
            <!--            </div>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--</div>-->
            <!--<div class="col-lg-4 col-md-6 mb-4">-->
            <!--    <div class="rounded overflow-hidden mb-2">-->
            <!--        <img class="img-fluid" src="images/course-2.jpg" alt="">-->
            <!--        <div class="bg-secondary p-4">-->
            <!--            <div class="d-flex justify-content-between mb-3">-->
            <!--                <small class="m-0"><i class="fa fa-users text-primary mr-2"></i>25 Students</small>-->
            <!--                <small class="m-0"><i class="far fa-clock text-primary mr-2"></i>01h 30m</small>-->
            <!--            </div>-->
            <!--            <a class="h5" href="">Web design & development courses for beginner</a>-->
            <!--            <div class="border-top mt-4 pt-4">-->
            <!--                <div class="d-flex justify-content-between">-->
            <!--                    <h6 class="m-0"><i class="fa fa-star text-primary mr-2"></i>4.5 <small>(250)</small></h6>-->
            <!--                    <h5 class="m-0">$99</h5>-->
            <!--                </div>-->
            <!--            </div>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--</div>-->
            <!--<div class="col-lg-4 col-md-6 mb-4">-->
            <!--    <div class="rounded overflow-hidden mb-2">-->
            <!--        <img class="img-fluid" src="images/course-3.jpg" alt="">-->
            <!--        <div class="bg-secondary p-4">-->
            <!--            <div class="d-flex justify-content-between mb-3">-->
            <!--                <small class="m-0"><i class="fa fa-users text-primary mr-2"></i>25 Students</small>-->
            <!--                <small class="m-0"><i class="far fa-clock text-primary mr-2"></i>01h 30m</small>-->
            <!--            </div>-->
            <!--            <a class="h5" href="">Web design & development courses for beginner</a>-->
            <!--            <div class="border-top mt-4 pt-4">-->
            <!--                <div class="d-flex justify-content-between">-->
            <!--                    <h6 class="m-0"><i class="fa fa-star text-primary mr-2"></i>4.5 <small>(250)</small></h6>-->
            <!--                    <h5 class="m-0">$99</h5>-->
            <!--                </div>-->
            <!--            </div>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--</div>-->
            <!--<div class="col-lg-4 col-md-6 mb-4">-->
            <!--    <div class="rounded overflow-hidden mb-2">-->
            <!--        <img class="img-fluid" src="images/course-4.jpg" alt="">-->
            <!--        <div class="bg-secondary p-4">-->
            <!--            <div class="d-flex justify-content-between mb-3">-->
            <!--                <small class="m-0"><i class="fa fa-users text-primary mr-2"></i>25 Students</small>-->
            <!--                <small class="m-0"><i class="far fa-clock text-primary mr-2"></i>01h 30m</small>-->
            <!--            </div>-->
            <!--            <a class="h5" href="">Web design & development courses for beginner</a>-->
            <!--            <div class="border-top mt-4 pt-4">-->
            <!--                <div class="d-flex justify-content-between">-->
            <!--                    <h6 class="m-0"><i class="fa fa-star text-primary mr-2"></i>4.5 <small>(250)</small></h6>-->
            <!--                    <h5 class="m-0">$99</h5>-->
            <!--                </div>-->
            <!--            </div>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--</div>-->
            <!--<div class="col-lg-4 col-md-6 mb-4">-->
            <!--    <div class="rounded overflow-hidden mb-2">-->
            <!--        <img class="img-fluid" src="images/course-5.jpg" alt="">-->
            <!--        <div class="bg-secondary p-4">-->
            <!--            <div class="d-flex justify-content-between mb-3">-->
            <!--                <small class="m-0"><i class="fa fa-users text-primary mr-2"></i>25 Students</small>-->
            <!--                <small class="m-0"><i class="far fa-clock text-primary mr-2"></i>01h 30m</small>-->
            <!--            </div>-->
            <!--            <a class="h5" href="">Web design & development courses for beginner</a>-->
            <!--            <div class="border-top mt-4 pt-4">-->
            <!--                <div class="d-flex justify-content-between">-->
            <!--                    <h6 class="m-0"><i class="fa fa-star text-primary mr-2"></i>4.5 <small>(250)</small></h6>-->
            <!--                    <h5 class="m-0">$99</h5>-->
            <!--                </div>-->
            <!--            </div>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--</div>-->
            <!--<div class="col-lg-4 col-md-6 mb-4">-->
            <!--    <div class="rounded overflow-hidden mb-2">-->
            <!--        <img class="img-fluid" src="images/course-6.jpg" alt="">-->
            <!--        <div class="bg-secondary p-4">-->
            <!--            <div class="d-flex justify-content-between mb-3">-->
            <!--                <small class="m-0"><i class="fa fa-users text-primary mr-2"></i>25 Students</small>-->
            <!--                <small class="m-0"><i class="far fa-clock text-primary mr-2"></i>01h 30m</small>-->
            <!--            </div>-->
            <!--            <a class="h5" href="">Web design & development courses for beginner</a>-->
            <!--            <div class="border-top mt-4 pt-4">-->
            <!--                <div class="d-flex justify-content-between">-->
            <!--                    <h6 class="m-0"><i class="fa fa-star text-primary mr-2"></i>4.5 <small>(250)</small></h6>-->
            <!--                    <h5 class="m-0">$99</h5>-->
            <!--                </div>-->
            <!--            </div>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--</div>-->
        </div>
    </div>
</div>
<!-- Courses End -->


<!-- Registration Start -->
<div class="container-fluid bg-registration py-5" style="margin: 90px 0;">
    <div class="container py-5">
        <div class="row align-items-center">
            <!--<div class="col-lg-7 mb-5 mb-lg-0">-->
            <div class="mb-4">
                <h5 class="text-primary text-uppercase mb-3" style="letter-spacing: 5px;"> Enroll in Any of Our Courses</h5>
                <h1 class="text-white">30% Off For New Students</h1>
            </div>
            <p class="text-white">Boost your career with our industry-relevant programs designed to equip you with in-demand skills. Enjoy flexible exam options that fit your schedule and earn a certification upon completion to showcase your expertise.</p>
            <ul class="list-inline text-white m-0">
                <li class="py-2"><i class="fa fa-check text-primary mr-3"></i>Industry relevant programs</li>
                <li class="py-2"><i class="fa fa-check text-primary mr-3"></i>Flexible exam options</li>
                <li class="py-2"><i class="fa fa-check text-primary mr-3"></i>Recognized certification</li>
            </ul>
            <!--</div>-->
            <!--<div class="col-lg-5">-->
            <!--    <div class="card border-0">-->
            <!--        <div class="card-header bg-light text-center p-4">-->
            <!--            <h1 class="m-0">Sign Up Now</h1>-->
            <!--        </div>-->
            <!--        <div class="card-body rounded-bottom bg-primary p-5">-->
            <!--            <form>-->
            <!--                <div class="form-group">-->
            <!--                    <input type="text" class="form-control border-0 p-4" placeholder="Your name" required="required" />-->
            <!--                </div>-->
            <!--                <div class="form-group">-->
            <!--                    <input type="email" class="form-control border-0 p-4" placeholder="Your email" required="required" />-->
            <!--                </div>-->
            <!--                <div class="form-group">-->
            <!--                    <select class="custom-select border-0 px-4" style="height: 47px;">-->
            <!--                        <option selected>Select a course</option>-->
            <!--                        <option value="1">Course 1</option>-->
            <!--                        <option value="2">Course 1</option>-->
            <!--                        <option value="3">Course 1</option>-->
            <!--                    </select>-->
            <!--                </div>-->
            <!--                <div>-->
            <!--                    <button class="btn btn-dark btn-block border-0 py-3" type="submit">Sign Up Now</button>-->
            <!--                </div>-->
            <!--            </form>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--</div>-->
            <a href='https://exams.codefest.africa/register' class='btn btn-primary mt-3'>Sign Up Now</a>
        </div>
    </div>
</div>
<!-- Registration End -->


<!-- Team Start -->
<!--<div class="container-fluid py-5" id='instructors'>-->
<!--    <div class="container pt-5 pb-3">-->
<!--        <div class="text-center mb-5">-->
<!--            <h5 class="text-primary text-uppercase mb-3" style="letter-spacing: 5px;">Instructors</h5>-->
<!--            <h1>Meet Our Instructors</h1>-->
<!--        </div>-->
<!--        <div class="row">-->
<!--            <div class="col-md-6 col-lg-3 text-center team mb-4">-->
<!--                <div class="team-item rounded overflow-hidden mb-2">-->
<!--                    <div class="team-img position-relative">-->
<!--                        <img class="img-fluid" src="images/team-1.jpg" alt="">-->
<!--                        <div class="team-social">-->
<!--                            <a class="btn btn-outline-light btn-square mx-1" href="#"><i class="fab fa-twitter"></i></a>-->
<!--                            <a class="btn btn-outline-light btn-square mx-1" href="#"><i class="fab fa-facebook-f"></i></a>-->
<!--                            <a class="btn btn-outline-light btn-square mx-1" href="#"><i class="fab fa-linkedin-in"></i></a>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="bg-secondary p-4">-->
<!--                        <h5>Jhon Doe</h5>-->
<!--                        <p class="m-0">Web Designer</p>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="col-md-6 col-lg-3 text-center team mb-4">-->
<!--                <div class="team-item rounded overflow-hidden mb-2">-->
<!--                    <div class="team-img position-relative">-->
<!--                        <img class="img-fluid" src="images/team-2.jpg" alt="">-->
<!--                        <div class="team-social">-->
<!--                            <a class="btn btn-outline-light btn-square mx-1" href="#"><i class="fab fa-twitter"></i></a>-->
<!--                            <a class="btn btn-outline-light btn-square mx-1" href="#"><i class="fab fa-facebook-f"></i></a>-->
<!--                            <a class="btn btn-outline-light btn-square mx-1" href="#"><i class="fab fa-linkedin-in"></i></a>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="bg-secondary p-4">-->
<!--                        <h5>Jhon Doe</h5>-->
<!--                        <p class="m-0">Web Designer</p>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="col-md-6 col-lg-3 text-center team mb-4">-->
<!--                <div class="team-item rounded overflow-hidden mb-2">-->
<!--                    <div class="team-img position-relative">-->
<!--                        <img class="img-fluid" src="images/team-3.jpg" alt="">-->
<!--                        <div class="team-social">-->
<!--                            <a class="btn btn-outline-light btn-square mx-1" href="#"><i class="fab fa-twitter"></i></a>-->
<!--                            <a class="btn btn-outline-light btn-square mx-1" href="#"><i class="fab fa-facebook-f"></i></a>-->
<!--                            <a class="btn btn-outline-light btn-square mx-1" href="#"><i class="fab fa-linkedin-in"></i></a>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="bg-secondary p-4">-->
<!--                        <h5>Jhon Doe</h5>-->
<!--                        <p class="m-0">Web Designer</p>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="col-md-6 col-lg-3 text-center team mb-4">-->
<!--                <div class="team-item rounded overflow-hidden mb-2">-->
<!--                    <div class="team-img position-relative">-->
<!--                        <img class="img-fluid" src="images/team-4.jpg" alt="">-->
<!--                        <div class="team-social">-->
<!--                            <a class="btn btn-outline-light btn-square mx-1" href="#"><i class="fab fa-twitter"></i></a>-->
<!--                            <a class="btn btn-outline-light btn-square mx-1" href="#"><i class="fab fa-facebook-f"></i></a>-->
<!--                            <a class="btn btn-outline-light btn-square mx-1" href="#"><i class="fab fa-linkedin-in"></i></a>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="bg-secondary p-4">-->
<!--                        <h5>Jhon Doe</h5>-->
<!--                        <p class="m-0">Web Designer</p>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
<!-- Team End -->


<!-- Testimonial Start -->
<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h5 class="text-primary text-uppercase mb-3" style="letter-spacing: 5px;">Testimonial</h5>
            <h1>What Our Students Say</h1>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="owl-carousel testimonial-carousel">
                    <div class="text-center">
                        <i class="fa fa-3x fa-quote-left text-primary mb-4"></i>
                        <h4 class="font-weight-normal mb-4">Codefest CBT made my exams effortless. The interface was user-friendly, the experience seamless, and everything worked perfectly. I highly recommend it for anyone seeking an efficient testing platform!</h4>
                        <img class="img-fluid mx-auto mb-3" src="images/testimonial-1.jpg" alt="">
                        <!--<h5 class="m-0">Emmanuel Ofoke Chinonso</h5>-->
                        <h5 class="m-0">Mark Powells</h5>
                        <span>Softare Engineer</span>
                    </div>
                    <div class="text-center">
                        <i class="fa fa-3x fa-quote-left text-primary mb-4"></i>
                        <h4 class="font-weight-normal mb-4">I loved how smooth and straightforward Codefest CBT was. It’s super easy to use, with no technical issues. A great platform for stress-free online exams!</h4>
                        <img class="img-fluid mx-auto mb-3" src="images/testimonial-2.jpg" alt="">
                        <!--<h5 class="m-0">Agwu Theresa uchechi</h5>-->
                        <h5 class="m-0">Anthonia Kent</h5>
                        <span>Web Developer</span>
                    </div>
                    <div class="text-center">
                        <i class="fa fa-3x fa-quote-left text-primary mb-4"></i>
                        <h4 class="font-weight-normal mb-4">Using Codefest CBT was a breeze. The platform is simple, cool, and reliable, making the whole process enjoyable. It’s a game-changer for online examinations!</h4>
                        <img class="img-fluid mx-auto mb-3" src="images/testimonial-3.jpg" alt="">
                        <!--<h5 class="m-0">Ogbonna Benjamin</h5>-->
                        <h5 class="m-0">Robert Stark</h5>
                        <span>Computer Engineer</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Testimonial End -->


<!-- Blog Start -->
<!--<div class="container-fluid py-5">-->
<!--    <div class="container pt-5 pb-3">-->
<!--        <div class="text-center mb-5">-->
<!--            <h5 class="text-primary text-uppercase mb-3" style="letter-spacing: 5px;">Our Blog</h5>-->
<!--            <h1>Latest From Our Blog</h1>-->
<!--        </div>-->
<!--        <div class="row pb-3">-->
<!--            <div class="col-lg-4 mb-4">-->
<!--                <div class="blog-item position-relative overflow-hidden rounded mb-2">-->
<!--                    <img class="img-fluid" src="images/blog-1.jpg" alt="">-->
<!--                    <a class="blog-overlay text-decoration-none" href="">-->
<!--                        <h5 class="text-white mb-3">Lorem elitr magna stet eirmod labore amet labore clita at ut clita</h5>-->
<!--                        <p class="text-primary m-0">Jan 01, 2050</p>-->
<!--                    </a>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="col-lg-4 mb-4">-->
<!--                <div class="blog-item position-relative overflow-hidden rounded mb-2">-->
<!--                    <img class="img-fluid" src="images/blog-2.jpg" alt="">-->
<!--                    <a class="blog-overlay text-decoration-none" href="">-->
<!--                        <h5 class="text-white mb-3">Lorem elitr magna stet eirmod labore amet labore clita at ut clita</h5>-->
<!--                        <p class="text-primary m-0">Jan 01, 2050</p>-->
<!--                    </a>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="col-lg-4 mb-4">-->
<!--                <div class="blog-item position-relative overflow-hidden rounded mb-2">-->
<!--                    <img class="img-fluid" src="images/blog-3.jpg" alt="">-->
<!--                    <a class="blog-overlay text-decoration-none" href="">-->
<!--                        <h5 class="text-white mb-3">Lorem elitr magna stet eirmod labore amet labore clita at ut clita</h5>-->
<!--                        <p class="text-primary m-0">Jan 01, 2050</p>-->
<!--                    </a>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
<!-- Blog End -->

<?php include('./partials/footer.php') ?>