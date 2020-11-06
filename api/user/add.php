<?php
    require "../config/headers.php";
    require "../config/operations.php";
    require "../config/error_def.php";

    $allowed_req_methods = ["POST"];
    $error = new Error_Definitions;

    if (!check_request_method($allowed_req_methods)) {
        send_response(405, $error->data_error(1));
    }

    require "../config/preflight.php";

    $data = $_POST;

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

    require "../config/database.php";
    $database = new Database;
    $connect = $database->get_connect_var();

    if (!$validator->advanced($data["password"], "equals", $data["confirm_password"])) {
        send_response(400, $error->form_error(0, "Passwords do not match!"));
    }

    Utility::remove_keys($data, ["confirm_password"]);
    $data["password"] = Utility::get_hash($data["password"]);
    Utility::escape_array($data);

    $query = new Build_Query("users", "insert");
    $query->set_columns(array_keys($data));
    $query->set_values(array_values($data));

    $connect->query($query->get_query());

    if (mysqli_affected_rows($connect) == 1) {
        send_response(201, array(
            "error" => false,
            "message" => "Account created",
            "data" => array(
                "username" => $data["username"],
                "name" => $data["name"],
                "identifier" => $connect->insert_id
            )
        ));
    } else {
        send_response(409, $error->db_error(1), array(
            "info" => "Check for duplicate username"
        ));
    }
?>
