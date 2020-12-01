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
                            <h2 class="text-white">Mark your attendance</h2>
                            <span class="card-title">Select a subject & choose a date to mark attendance</span>
                            <p id="info-area"></p>

                            <div class="row mt-3">
                                <div class="input-field col s6 m6">
                                    <select id="subject-id">
                                        <option disabled selected>Loading ...</option>
                                    </select>
                                    <label>Enrolled subjects</label>
                                </div>
                                <div class="input-field col s6 m6">
                                    <input type="date" id="start-date" placeholder="Date">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col s12">
                                    <h3 id="subject-details">-</h3>
                                    <h5>Date: <span class="ctm-in-text" id="mark-date">-</span>,
                                        <span class="ctm-in-text p-1 rounded" id="actual-type">-</span></h5>
                                    <div>
                                        <p>
                                            <label>
                                                <input name="type" type="radio" class="with-gap" value="present" />
                                                <span>Present</span>
                                            </label>
                                        </p>
                                        <p>
                                            <label>
                                                <input name="type" type="radio" class="with-gap" value="absent" />
                                                <span>Absent</span>
                                            </label>
                                        </p>
                                        <p>
                                            <label>
                                                <input name="type" type="radio" class="with-gap" value="leave" />
                                                <span>Holiday</span>
                                            </label>
                                        </p>
                                        <div class="input-field col s4">
                                            <i class="material-icons prefix">bookmark</i>
                                            <input id="note" type="text" name="note">
                                            <label for="note">Change Note</label>
                                        </div>
                                        <br>
                                        <button type="button" class="btn btn-success waves-effect" id="save-btn">Save</button>
                                    </dib>
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
        <script src="assets/js/calender.js"></script>
    </body>

</html>
