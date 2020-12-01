<?php
    require "local_config.php";

    $allowed_req_methods = ["POST", "GET"];

    if (!check_request_method($allowed_req_methods)) {
        send_response(405, $error->data_error(1));
    }

    $access_token = get_access_token();
    if (!$access_token) {
        send_response(401, $error->api_error(1));
    }

    $data = array_merge($_GET, $_POST);

    if ($data == null || empty($data)) {
        send_response(400, $error->data_error(2));
    }

    $required_keys = ["subject_id"];
    $valid_keys = ["start_date", "end_date"];

    $validator = new Validate_Data;
    $data = $validator->verify($data, $required_keys, $valid_keys);

    if (!$data) {
        send_response(400, $error->form_error(1));
    }

    Utility::escape_array($data, $valid_keys);

    require "../config/database.php";
    $database = new Database;
    $connect = $database->get_connect_var();

    if (!Authenticate::verify_access_token($connect, $access_token)) {
        send_response(401, $error->api_error(2));
    }

    $user_id = Authenticate::get_user_id($access_token);
    $subject_id = strtoupper($data["subject_id"]);

    $query = new Build_Query("enrolled");
    $query->set_columns(["start_date", "weekly_off"]);
    $query->set_conditions([
        ["user_id", $user_id],
        ["subject_id", $subject_id]
    ]);

    $result = $connect->query($query->get_query());
    if (!$result || $result->num_rows == 0) {
        send_response(400, $error->db_error(3), array(
            "info" => "You are not yet enrolled in the subject"
        ));
    }
    $subject_details = $result->fetch_assoc();
    mysqli_free_result($result);

    $result = $connect->query("SELECT name FROM subjects WHERE subject_id = '$subject_id'");
    $subject_name = $result->fetch_assoc()["name"];
    mysqli_free_result($result);

    $start_date = date(DB_DATE_FORMAT, strtotime($subject_details["start_date"]));
    $end_date = date(DB_DATE_FORMAT);
    if (isset($data["start_date"]) && verify_date($data["start_date"])) {
        $db_start = strtotime($subject_details["start_date"]);
        $custom_start = strtotime($data["start_date"]);
        $start_date = $custom_start < $db_start ? date(DB_DATE_FORMAT, $db_start) : date(DB_DATE_FORMAT, $custom_start);
    }
    if (isset($data["end_date"]) && verify_date($data["end_date"])) {
        $curr_end = time();
        $custom_end = strtotime($data["end_date"]);
        $end_date = $custom_end > $curr_end ? date(DB_DATE_FORMAT, $curr_end) : date(DB_DATE_FORMAT, $custom_end);
    }

    if (strtotime($end_date) < strtotime($start_date)) {
        $end_date = $start_date;
    }

    $query->set_table("attendance");
    $query->set_columns(["type", "date", "note"]);
    $query->set_conditions("subject_id = '$subject_id' AND date BETWEEN '{$start_date}' AND '{$end_date}'");

    $result = $connect->query($query->get_query());
    $absent_details = [];
    if ($result && $result->num_rows != 0) {
        while ($row = $result->fetch_assoc()) {
            array_push($absent_details, $row);
        }
    }
    mysqli_free_result($result);

    $holidays = [];
    $leaves = [];
    $off_days = json_decode($subject_details["weekly_off"]);
    $off_dates = compute_offdates($off_days, strtotime($start_date), strtotime($end_date));
    $absent_details = remove_offdates_conflicts($absent_details, $off_dates);
    split_attendance($absent_details, $holidays, $leaves);
    $total_days = total_days(strtotime($start_date), strtotime($end_date));

    send_response(200, array(
        "error" => false,
        "message" => "Attendance calculated",
        "data" => array(
            "subject_id" => $subject_id,
            "subject_name" => $subject_name,
            "start_date" => $start_date,
            "end_date" => $end_date,
            "holidays" => $holidays,
            "leaves" => $leaves,
            "offdates" => $off_dates,
            "attendance" => array(
                "total" => $total_days,
                "working" => $total_days - count($holidays) - count($off_dates),
                "absent" => count($leaves),
                "present" => $total_days - count($leaves) - count($holidays) - count($off_dates)
            )
        )
    ));
?>
