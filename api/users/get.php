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
    $query = new Build_Query("users", "select");
    $query->set_conditions([
        ["user_id", $user_id]
    ]);

    $result = $connect->query($query->get_query());

    if (!$result || $result->num_rows == 0) {
        send_response(404, array(
            "error" => true,
            "message" => "User not found"
        ));
    } else {
        $user = $result->fetch_assoc();
        unset($user["password"]);
    }

    send_response(200, array(
        "error" => false,
        "message" => "User details found",
        "data" => $user
    ));
?>
