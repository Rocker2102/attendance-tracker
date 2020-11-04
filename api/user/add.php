<?php
    require "../config/headers.php";
    require "../config/operations.php";
    require "../config/error_def.php";

    $allowed_req_methods = ["POST", "PUT"];
    $error = new Error_Definitions;

    if (!check_request_method($allowed_req_methods)) {
        send_response(400, $error->data_error(1));
    }

    $access_token = get_access_token();
    if (!$access_token) {
        send_response(401, $error->custom("ERR_API_AUTH", "Access Token missing"));
    }

    require "../config/database.php";
    $database = new Database;
    $db = $database->get_connect_var();

    $data = get_input_data(file_get_contents("php://input"), $_POST);

    if ($data == null || empty($data)) {
        send_response(400, $error->data_error(2));
    }

    $required_keys = ["username", "password", "confirm_password", "name"];
    $valid_keys = [];

    $validator = new Validate_Data;
    $data = $validator->verify($data, $required_keys, $valid_keys);

    if (!$data) {
        send_response(400, $error->form_error(1));
    }

    if (!$validator->advanced($data["password"], "equals", $data["confirm_password"])) {
        send_response(400, $error->form_error(0, "Passwords do not match!"));
    }

    Utility::remove_keys($data, ["confirm_password"]);
    $data["password"] = Utility::get_hash($data["password"]);
    Utility::escape_array($data);

    $query = new Build_Query("users", "insert");
    $query->set_columns(array_keys($data));
    $query->set_values(array_values($data));

    $db->query($query->get_query());

    if (mysqli_affected_rows($db) == 1) {
        send_response(201, array(
            "error" => false,
            "message" => "Account created"
        ));
    } else {
        send_response(503, $error->db_error(1), array(
            "info" => "Check for duplicate username"
        ));
    }
?>
