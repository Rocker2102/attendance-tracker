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

    $data = $_POST;

    if ($data == null || empty($data)) {
        send_response(400, $error->data_error(2));
    }

    $required_keys = ["current_password"];
    $valid_keys = ["name", "username", "password"];

    $validator = new Validate_Data;
    $data = $validator->verify($data, $required_keys, $valid_keys);

    if (!$data) {
        send_response(400, $error->form_error(1));
    }

    require "../config/database.php";
    $database = new Database;
    $connect = $database->get_connect_var();

    if (!Authenticate::verify_access_token($connect, $access_token)) {
        send_response(401, $error->custom("ERR_API_TOKEN", "Token invalid or expired!"));
    }

    $user_id = Authenticate::get_user_id($access_token);
    $current_pwd = $data["current_password"];
    Utility::remove_keys($data, ["current_password"]);
    Utility::escape_array($data);

    $query = new Build_Query("users", "select");
    $query->set_columns(["user_id"]);
    $query->set_conditions([
        ["user_id", $user_id],
        ["password", Utility::get_hash($current_pwd)]
    ]);

    $result = $connect->query($query->get_query());

    if (!$result || $result->num_rows == 0) {
        send_response(401, array(
            "error" => true,
            "message" => "Failed to authorize!"
        ));
    }
    mysqli_free_result($result);

    isset($data["password"]) ? $data["password"] = Utility::get_hash($data["password"]): false;
    if (isset($data["username"]) && !is_username_available($connect, $data["username"])) {
        send_response(400, array(
            "error" => true,
            "message" => "Username not available!"
        ));
    }

    $query->set_type("update");
    $query->set_columns(array_keys($data));
    $query->set_values(array_values($data));

    $connect->query($query->get_query());

    if (mysqli_affected_rows($connect) == 1) {
        unset($data["password"]);
        send_response(200, array(
            "error" => false,
            "message" => "Account details updated"
        ));
    } else {
        send_response(503, array(
            "error" => true,
            "message" => "Failed to update account details",
            "info" => [
                "Retry after some time",
                "No new data to update"
            ]
        ));
    }
?>
