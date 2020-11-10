<?php
    require "local_config.php";

    $allowed_req_methods = ["GET", "POST"];

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

    if (Authenticate::delete_access_token($connect, $access_token)) {
        send_response(200, array(
            "error" => false,
            "message" => "Token destroyed"
        ));
    } else {
        send_response(204, array(
            "error" => true,
            "message" => "Token not found"
        ));
    }
?>
