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
                            <h2 class="text-white">Check your attendance</h2>
                            <span class="card-title">Select a subject from the list below to see your attendance</span>
                            <p class="text-info">Just logged in? Try refreshing the page to see your enrolled subjects</p>
                            <p id="info-area"></p>

                            <div class="row mt-3">
                                <div class="input-field col s12">
                                    <select id="select-subject">
                                        <option disabled selected>Loading ...</option>
                                    </select>
                                    <label>Enrolled subjects</label>
                                </div>
                            </div>

                            <div class="row grey darken-3 rounded p-3 hoverable">
                                <div class="col s12">
                                    <h3 id="subject-details">-</h3>
                                    <h5>Start Date: <span class="ctm-in-text" id="start-date">-</span>, Till: 
                                        <span class="ctm-in-text" id="end-date">-</span></h5>
                                    <hr>
                                    <p>Total Days: <span class="ctm-in-text" id="total">0</span>, Working Days: 
                                        <span class="ctm-in-text" id="working">0</span></p>
                                    <p>Present: <span class="ctm-in-text" id="present">0</span>, Absent: 
                                        <span class="ctm-in-text" id="absent">0</span>, Holidays: 
                                        <span class="ctm-in-text" id="holidays">0</span></p>
                                    <p>Attendance (in percentage): <span class="ctm-in-text" id="percent-present">0</span></p>
                                    <hr>
                                    <div class="progress tooltipped" id="attendance-bar"
                                        data-tooltip="This is evaluated using total days (not working days)">
                                        <div class="progress-bar bg-success progress-bar-striped" style="width: 100%"></div>
                                    </div>
                                    <p>
                                        <span class="bg-success p-1 rounded">Present</span>
                                        <span class="bg-danger p-1 rounded">Absent</span>
                                        <span class="bg-warning p-1 rounded text-dark">Holidays (includes weekly offs)</span>
                                    </p>
                                </div>
                            </div>
                        </div>
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
        <script src="assets/js/subjects.js"></script>
        <script src="assets/js/attendance.js"></script>
    </body>

</html>
