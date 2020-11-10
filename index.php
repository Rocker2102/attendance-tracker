<!DOCTYPE html>

<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Track Attendance | Homepage</title>

        <link rel="icon" type="image/webp" href="assets/img/icon.png" />
        <link rel="stylesheet" type="text/css" href="assets/vendor/materialize-1.0.0/css/materialize.min.css" />
        <link rel="stylesheet" type="text/css" href="assets/vendor/bootstrap-4.5.3/css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" href="assets/css/style.css" />
    </head>

    <body>
        <?php include "includes/header.html"; ?>

        <main class="ctm-container mt-3">
            <div class="row">
                <div class="col s12 m6">
                    <div class="card grey darken-3">
                        <div class="card-content white-text">
                            <h2 class="text-white center-align">Attendance Management System</h2>
                            <span class="card-title">Hello there 👋</span>
                            <p>
                                Quickly get started by creating an account by clicking on the link below.
                                <strong>Already done!?</strong> Log in to see how many classes you missed ;-)
                            </p>
                        </div>
                        <div class="card-action">
                            <a href="register.php">Create Account</a>
                            <a class="modal-trigger" href="#login-modal">Login</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8 d-flex align-self-stretch">
                    <div class="card card-body grey darken-3 text-white z-depth-5 ctm-card-bg">
                        <h3><i class="material-icons left-align">label_important</i> Next step</h3>
                        <span class="card-title">Continue by enrolling in a subject, either from a list of predefined subjects or
                            add your own subject.</span>
                        <div class="row mx-1">
                            <a class="btn btn-primary waves-effect mx-1"><i class="material-icons left-align">category</i>Enroll</a>
                            <a class="btn btn-primary waves-effect mx-1"><i class="material-icons left-align">add</i>Add new
                                subject</a>
                        </div>
                        <h3><i class="material-icons left-align">face</i> Your Account</h3>
                        <span class="card-title">Update your information by following the link below</span>
                        <div class="row mx-1">
                            <a class="btn btn-primary waves-effect mx-1"><i class="material-icons left-align">person</i>Go to
                                profile</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-body blue darken-4 text-white z-depth-5 hoverable">
                        <h3><i class="material-icons left-align">calendar_today</i> Calender</h3>
                        <span class="card-title">Open calender to mark attendance</span>
                        <a class="btn btn-primary btn-block waves-effect" target="_blank" href="#"><i
                                class="material-icons left-align">open_in_new</i>Open</a>
                    </div>
                    <div class="card card-body teal darken-4 text-white z-depth-5">
                        <h3><i class="material-icons left-align">analytics</i> Attendance</h3>
                        <span class="card-title">Check your subject wise attendance</span>
                        <a class="btn btn-primary btn-block waves-effect" target="_blank" href="#"><i
                                class="material-icons left-align">open_in_new</i>View</a>
                    </div>
                </div>
            </div>
        </main>

        <?php include "includes/login-modal.html"; ?>

        <script src="assets/vendor/jquery-3.5.1.min.js"></script>
        <script src="assets/vendor/bootstrap-4.5.3/js/bootstrap.bundle.min.js"></script>
        <script src="assets/vendor/materialize-1.0.0/js/materialize.min.js"></script>
        <script src="assets/js/main.js"></script>
        <script src="assets/js/login.js"></script>
    </body>

</html>
