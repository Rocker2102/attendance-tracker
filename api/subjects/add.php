<?php
    require "local_config.php";

    $allowed_req_methods = ["POST"];

    if (!check_request_method($allowed_req_methods)) {
        send_response(405, $error->data_error(1));
    }

    $access_token = get_access_token();
    if (!$access_token) {
        send_response(401, $error->api_error(1));
    }

    $data = $_POST;

    if ($data == null || empty($data)) {
        send_response(400, $error->data_error(2));
    }

    $required_keys = ["subject_id", "name"];
    $valid_keys = [];

    $validator = new Validate_Data;
    $data = $validator->verify($data, $required_keys, $valid_keys);

    if (!$data) {
        send_response(400, $error->form_error(1));
    }

    $data["subject_id"] = strtoupper($data["subject_id"]);
    Utility::escape_array($data);

    require "../config/database.php";
    $database = new Database;
    $connect = $database->get_connect_var();

    if (!Authenticate::verify_access_token($connect, $access_token)) {
        send_response(401, $error->api_error(2));
    }

    $query = new Build_Query("subjects", "insert");
    $query->set_columns(array_keys($data));
    $query->set_values(array_values($data));

    $connect->query($query->get_query());

    if (mysqli_affected_rows($connect) == 1) {
        send_response(201, array(
            "error" => false,
            "message" => "Subject added",
            "data" => array(
                "code" => $data["subject_id"],
                "name" => $data["name"]
            )
        ));
    } else {
        send_response(409, $error->db_error(1), array(
            "info" => "Check for duplicate subject code"
        ));
    }
?>
