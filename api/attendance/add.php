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

    $required_keys = ["type", "subject_id", "date"];
    $valid_keys = ["note"];

    $validator = new Validate_Data;
    $data = $validator->verify($data, $required_keys, $valid_keys);

    if (!$data) {
        send_response(400, $error->api_error(2));
    }

    $validate_type = [
        [$data["type"], ["leave", "holiday"]]
    ];
    if (!$validator->basic($validate_type)) {
        send_response(400, $error->form_error(3), array(
            "info" => "Failed to validate leave type"
        ));
    }
    if (!verify_date($data["date"])) {
        send_response(400, $error->form_error(3), array(
            "info" => "Failed to validate entered date"
        ));
    } else {
        $data["date"] = date(DB_DATE_FORMAT, strtotime($data["date"]));
    }

    Utility::escape_array($data, $valid_keys);

    require "../config/database.php";
    $database = new Database;
    $connect = $database->get_connect_var();

    if (!Authenticate::verify_access_token($connect, $access_token)) {
        send_response(401, $error->custom("ERR_API_TOKEN", "Token invalid or expired!"));
    }

    $user_id = Authenticate::get_user_id($access_token);
    $data["subject_id"] = strtoupper($data["subject_id"]);

    $query = new Build_Query("enrolled");
    $query->set_columns(["start_date"]);
    $query->set_conditions([
        ["user_id", $user_id],
        ["subject_id", $data["subject_id"]]
    ]);

    $result = $connect->query($query->get_query());
    if (!$result || $result->num_rows == 0) {
        send_response(400, $error->db_error(3), array(
            "info" => "You are not yet enrolled in the subject"
        ));
    }
    $sub_start_date = $result->fetch_assoc()["start_date"];
    mysqli_free_result($result);

    if (strtotime($data["date"]) < strtotime($sub_start_date)) {
        send_response(400, $error->form_error(3), array(
            "info" => "Cannot mark a holiday/leave before start date of subject",
            "start_date" => $sub_start_date
        ));
    }

    $data["user_id"] = $user_id;
    $query->set_table("attendance");
    $query->set_type("insert");
    $query->set_columns(array_keys($data));
    $query->set_values(array_values($data));
    unset($data["user_id"]);

    $connect->query($query->get_query());
    if (mysqli_affected_rows($connect) == 1) {
        send_response(200, array(
            "error" => false,
            "message" => "Attendance updated",
            "data" => $data
        ));
    } else {
        send_response(409, $error->db_error(3), array(
            "info" => [
                "Leave/Holiday already marked on the specified date"
            ]
        ));
    }
?>
