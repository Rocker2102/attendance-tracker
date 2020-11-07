<?php
    require "local_config.php";

    $allowed_req_methods = ["POST"];

    if (!check_request_method($allowed_req_methods)) {
        send_response(405, $error->data_error(1));
    }

    $access_token = get_access_token();
    if (!$access_token) {
        send_response(401, $error->custom("ERR_API_AUTH", "Access Token missing"));
    }

    $data = get_input_data(file_get_contents("php://input"), $_POST);

    if ($data == null || empty($data)) {
        send_response(400, $error->data_error(2));
    }

    $required_keys = ["password"];
    $valid_keys = [];

    $validator = new Validate_Data;
    $data = $validator->verify($data, $required_keys, $valid_keys);

    if (!$data) {
        send_response(400, $error->form_error(1));
    }

    Utility::escape_array($data);

    require "../config/database.php";
    $database = new Database;
    $connect = $database->get_connect_var();

    if (!Authenticate::verify_access_token($connect, $access_token)) {
        send_response(401, $error->custom("ERR_API_TOKEN", "Token invalid or expired!"));
    }

    $user_id = Authenticate::get_user_id($access_token);

    $query = new Build_Query("users", "delete");
    $query->set_conditions([
        ["user_id", $user_id],
        ["password", Utility::get_hash($data["password"])]
    ]);

    $connect->query($query->get_query());

    if (mysqli_affected_rows($connect) == 1) {
        send_response(202, array(
            "error" => false,
            "message" => "User Account deleted",
            "redirect" => HOME_URL
        ));
    } else {
        send_response(500, $error->db_error(2), array(
            "info" => [
                "Check if password is correct",
                "Check if the user account exists",
                "Send request with a new token"
            ]
        ));
    }
?>
