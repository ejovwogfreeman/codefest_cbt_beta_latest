<?php include('./partials/header.php') ?>
<style>
    .exam-page {
        background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('./images/background.jpg');
        height: 100vh;
        background-size: cover;
    }

    .content {
        padding-top: 100px;
        height: 500px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .content form {
        background-color: rgba(255, 255, 255, 0.7);
        width: 500px;
        padding: 30px;
        text-align: center;
    }

    .content form h2 {
        text-align: center;
        margin-bottom: 20px;
    }

    .content form a {
        color: var(--secondary);
    }
</style>

<div class="container my-5">
    <div class="container d-flex justify-content-center px-0">
        <div class="col-lg-5 p-0">
            <div class="card border">
                <!-- Card Header -->
                <div class="card-header text-center p-4 bg-primary">
                    <h1 class="m-0 text-light">Exam Submitted</h1>
                </div>

                <!-- Card Body -->
                <div class="card-body rounded-bottom py-5 px-sm-5 px-3 bg-light">
                    <form method="POST" class="needs-validation" novalidate>
                        <div class='alert alert-success'>Exam Submitted Successfully!</div>
                        <a href=" https://exams.codefest.africa/" class="btn btn-dark btn-block border-0 py-3">Go Home</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('./partials/footer.php') ?>