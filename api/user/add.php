<?php
    require "../config/headers.php";
    require "../config/operations.php";

    $allowed_req_methods = ["POST", "PUT"];

    if (!in_array(strtoupper($_SERVER["REQUEST_METHOD"]), $allowed_req_methods)) {
        send_response(400, array(
            "error" => true,
            "message" => "Request method not allowed!"
        ));
    }

    $data = get_input_data(file_get_contents("php://input"), $_POST);

    if ($data == null) {
        send_response(400, array(
            "error" => true,
            "message" => "Invalid data format!"
        ));
    }

    $required_keys = ["username", "password", "confirm_password", "name"];
    $valid_keys = [];

    $validator = new Validate_Data();
    $data = $validator->verify($data, $required_keys, $valid_keys);

    if (!$data) {
        send_response(400, array(
            "error" => true,
            "message" => "Bad Request"
        ));
    }

    if (!$validator->advanced($data["password"], "equals", $data["confirm_password"])) {
        send_response(400, array(
            "error" => true,
            "message" => "Passwords do not match"
        ));
    }
    unset($data["confirm_password"]);
    $data["password"] = Utility::get_hash($data["password"]);

    require "../config/database.php";
    $database = new Database();
    $db = $database->get_connect_var();

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
        send_response(503, array(
            "error" => true,
            "message" => "Failed to create account!",
            "info" => "Check for duplicate username"
        ));
    }
?>
