<?php
    require "../config/headers.php";
    require "../config/operations.php";
    require "../config/error_def.php";

    $allowed_req_methods = ["POST", "GET"];
    $error = new Error_Definitions;

    if (!check_request_method($allowed_req_methods)) {
        send_response(405, $error->data_error(1));
    }

    require "../config/preflight.php";

    $access_token = get_access_token();
    if (!$access_token) {
        send_response(401, $error->custom("ERR_API_AUTH", "Access Token missing"));
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

    Utility::escape_array($data);

    require "../config/database.php";
    $database = new Database;
    $connect = $database->get_connect_var();

    if (!Authenticate::verify_access_token($connect, $access_token)) {
        send_response(401, $error->custom("ERR_API_TOKEN", "Token invalid or expired!"));
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
            "info" => "Looks like you are not yet enrolled in the subject"
        ));
    }
    $subject_details = $result->fetch_assoc();
    mysqli_free_result($result);

    $query->set_table("attendance");
    $query->set_columns(["type", "date", "note"]);

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
    split_attendance($absent_details, $holidays, $leaves);
    $total_days = total_days(strtotime($subject_details["start_date"]));
    $off_days = json_decode($subject_details["weekly_off"]);
    $off_dates = compute_offdates($off_days, strtotime($subject_details["start_date"]));

    send_response(200, array(
        "error" => false,
        "message" => "Attendance calculated",
        "data" => array(
            "subject_id" => $subject_id,
            "start_date" => $subject_details["start_date"],
            "end_date" => date("Y-m-d"),
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

    function split_attendance(array $data, array &$h, array &$l) {
        $max_len = count($data);
        for ($i = 0; $i < $max_len; $i++) {
            $type = $data[$i]["type"];
            unset($data[$i]["type"]);
            $type == "leave" ? array_push($l, $data[$i]) : array_push($h, $data[$i]);
        }
    }

    function total_days($begin_date, $end_date = null) {
        $end_date == null ? $end_date = strtotime(date("Y-m-d")) : false;
        $end_date += 86400; /* to count current day as well */
        return $end_date < $begin_date ? 0 : floor(($end_date - $begin_date) / 86400);
    }

    function compute_offdates(array $offdays, $begin_date, $end_date = null) {
        $end_date == null ? $end_date = strtotime(date("Y-m-d")) : false;
        $offdates = [];
        while ($begin_date <= $end_date) {
            in_array(strtolower(date("l", $begin_date)), $offdays) ? array_push($offdates, date("Y-m-d", $begin_date)) : false;
            $begin_date += 86400;
        }
        return $offdates;
    }
?>
