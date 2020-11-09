<!DOCTYPE html>

<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Track Attendance | Register</title>

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
                            <h2 class="text-white">Create Account</h2>
                            <span class="card-title">Fill a really short form below to get your own account. Click <a
                                    href="index.php?modal=login-modal">here</a> if you already have an account.</span>
                            <p id="info-area"></p>

                            <form id="register-form" class="my-3" method="POST">
                                <div class="form-row mb-3">
                                    <div class="input-field col-md-6">
                                        <i class="material-icons prefix">person</i>
                                        <input id="name" type="text" name="name" class="white-text" required>
                                        <label for="name">Name</label>
                                    </div>
                                    <div class="input-field col-md-6">
                                        <i class="material-icons prefix">account_circle</i>
                                        <input id="username" type="text" name="username" class="white-text" required>
                                        <label for="username">Username</label>
                                    </div>
                                </div>
                                <div class="form-row mb-3">
                                    <div class="input-field col-md-6">
                                        <i class="material-icons prefix">security</i>
                                        <input id="password" type="password" name="password" class="white-text" required>
                                        <label for="password">Password</label>
                                    </div>
                                    <div class="input-field col-md-6">
                                        <i class="material-icons prefix">https</i>
                                        <input id="confirm_password" type="password" name="confirm_password" class="white-text" required>
                                        <label for="confirm_password">Confirm Password</label>
                                    </div>
                                </div>
                                <div class="form-row mb-3">
                                    <div class="col-12 right-align">
                                        <a class="btn btn-info waves-effect" href="index.php"><i
                                                class="material-icons left-align">keyboard_backspace</i>Back to Home</a>
                                        <button type="reset" class="btn btn-danger waves-effect"><i
                                                class="material-icons left-align">undo</i>Reset</button>
                                        <button type="submit" class="btn btn-success waves-effect">Confirm<i
                                                class="material-icons right-align">keyboard_arrow_right</i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <script src="assets/vendor/jquery-3.5.1.min.js"></script>
        <script src="assets/vendor/bootstrap-4.5.3/js/bootstrap.bundle.min.js"></script>
        <script src="assets/vendor/materialize-1.0.0/js/materialize.min.js"></script>
        <script src="assets/js/main.js"></script>
        <script src="assets/js/signup.js"></script>
    </body>

</html>
