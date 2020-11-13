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

    $required_keys = ["subject_id"];

    $validator = new Validate_Data;
    $data = $validator->verify($data, $required_keys);

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

    $user_id = Authenticate::get_user_id($access_token);

    $query = new Build_Query("enrolled", "delete");
    $query->set_conditions([
        ["user_id", $user_id],
        ["subject_id", $data["subject_id"]]
    ]);
    $result = $connect->query($query->get_query());

    if ($result && mysqli_affected_rows($connect) >= 1) {
        $query->set_table("attendance");
        $connect->query($query->get_query());

        send_response(200, array(
            "error" => false,
            "message" => "Disenrolled from subject",
            "data" => array(
                "subject_id" => $data["subject_id"]
            )
        ));
    } else {
        send_response(500, $error->db_error(2), array(
            "info" => [
                "You aren't enrolled in the specified subject",
                "Internal error. Retry after some time!"
            ]
        ));
    }
?>
