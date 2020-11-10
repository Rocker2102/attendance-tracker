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

    $required_keys = ["subject_id", "date"];
    $valid_keys = ["type", "note"];

    $validator = new Validate_Data;
    $data = $validator->verify($data, $required_keys, $valid_keys);

    if (!$data) {
        send_response(400, $error->form_error(1));
    }

    if (isset($data["type"])) {
        $validate_type = [
            [$data["type"], ["leave", "holiday", "present"]]
        ];
        if (!$validator->basic($validate_type)) {
            send_response(400, $error->form_error(3), array(
                "info" => "Failed to validate leave type"
            ));
        }
    }

    if (!verify_date($data["date"])) {
        send_response(400, $error->form_error(3), array(
            "info" => "Failed to validate entered date"
        ));
    } else {
        $data["date"] = date(DB_DATE_FORMAT, strtotime($data["date"]));
    }

    Utility::escape_array($data);

    require "../config/database.php";
    $database = new Database;
    $connect = $database->get_connect_var();

    if (!Authenticate::verify_access_token($connect, $access_token)) {
        send_response(401, $error->api_error(2));
    }

    $user_id = Authenticate::get_user_id($access_token);
    $data["subject_id"] = strtoupper($data["subject_id"]);

    $query = new Build_Query("attendance", "update");
    $query->set_conditions([
        ["user_id", $user_id],
        ["subject_id", $data["subject_id"]],
        ["date", $data["date"]]
    ]);

    Utility::remove_keys($data, array_merge($required_keys, ["user_id"]));

    if (isset($data["type"]) && $data["type"] == "present") {
        $query->set_type("delete");
    } else {
        $query->set_columns(array_keys($data));
        $query->set_values(array_values($data));
    }

    $connect->query($query->get_query());
    if (mysqli_affected_rows($connect) == 1) {
        send_response(200, array(
            "error" => false,
            "message" => "Records updated",
            "data" => $data
        ));
    } else {
        send_response(503, array(
            "error" => true,
            "message" => "Failed to update records",
            "info" => [
                "Nothing to update",
                "Already updated",
                "Try again after some time"
            ]
        ));
    }
?>
