<!DOCTYPE html>

<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Track Attendance | Enrolled Subjects</title>

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
                            <h2 class="text-white center-align">Enroll</h2>
                            <span class="card-title">
                                Enroll into subjects for which you want to track attendance.
                                The list on the left shows subjects you are currently enrolled in & the list on the right
                                shows available subjects.
                            </span>
                            <p class="text-info">Just logged in? Try refreshing the page to see your enrolled subjects</p>
                            <p id="info-area"></p>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <ul class="collection with-header ctm-list-grey rounded border-0 hoverable z-depth-5">
                                        <li class="collection-header grey darken-2"><h4>Currently Enrolled</h4></li>
                                        <span id="enrolled">
                                            
                                        </span>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="collection with-header ctm-list-grey rounded border-0 hoverable z-depth-5">
                                        <li class="collection-header grey darken-2"><h4>Available Subjects</h4></li>
                                        <span id="all-subjects">

                                        </span>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include "includes/enroll-modal.html"; ?>
        </main>

        <?php include "includes/login-modal.html"; ?>

        <script src="assets/vendor/jquery-3.5.1.min.js"></script>
        <script src="assets/vendor/bootstrap-4.5.3/js/bootstrap.bundle.min.js"></script>
        <script src="assets/vendor/materialize-1.0.0/js/materialize.min.js"></script>
        <script src="assets/js/main.js"></script>
        <script src="assets/js/login.js"></script>
        <script src="assets/js/subjects.js"></script>
        <script src="assets/js/enrolled.js"></script>
    </body>

</html>
