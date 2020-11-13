<?php
    require "local_config.php";

    $allowed_req_methods = ["POST", "GET"];

    if (!check_request_method($allowed_req_methods)) {
        send_response(405, $error->data_error(1));
    }

    $access_token = get_access_token();
    if (!$access_token) {
        send_response(401, $error->api_error(1));
    }

    require "../config/database.php";
    $database = new Database;
    $connect = $database->get_connect_var();

    if (!Authenticate::verify_access_token($connect, $access_token)) {
        send_response(401, $error->api_error(2));
    }

    $user_id = Authenticate::get_user_id($access_token);

    $query = "SELECT s.subject_id as code, start_date, weekly_off, name
        FROM `enrolled` e, `subjects` s WHERE
        user_id = '$user_id' AND s.subject_id = e.subject_id";

    $result = $connect->query($query);
    $enrolled = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            array_push($enrolled, $row);
        }
    }

    send_response(200, array(
        "error" => false,
        "message" => $result->num_rows . " subject(s) found",
        "data" => $enrolled
    ));
?>
