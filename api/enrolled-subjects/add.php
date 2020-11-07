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

    $access_token = get_access_token();
    if (!$access_token) {
        send_response(401, $error->custom("ERR_API_AUTH", "Access Token missing"));
    }

    $data = $_POST;

    if ($data == null || empty($data)) {
        send_response(400, $error->data_error(2));
    }

    $required_keys = ["subject_id", "start_date"];
    $valid_keys = ["weekly_off"];

    $validator = new Validate_Data;
    $data = $validator->verify($data, $required_keys, $valid_keys);

    if (!$data) {
        send_response(400, $error->form_error(1));
    }

    $data["subject_id"] = strtoupper($data["subject_id"]);
    if (isset($data["weekly_off"])) {
        $data["weekly_off"] = verify_weekly_off($data["weekly_off"]);
        if (!$data["weekly_off"]) {
            send_response(400, $error->form_error(3), array(
                "info" => "An invalid weekday was detected!"
            ));
        }
    }

    if (verify_start_date($data["start_date"])) {
        $data["start_date"] = date("Y-m-d", strtotime($data["start_date"]));
    } else {
        send_response(400, $error->form_error(3), array(
            "info" => [
                "Failed to validate start date!",
                "Start date should be after " . date("d M Y", strtotime(MIN_START_DATE))
            ]
        ));
    }
    Utility::escape_array($data, ["weekly_off"]);

    require "../config/database.php";
    $database = new Database;
    $connect = $database->get_connect_var();

    if (!Authenticate::verify_access_token($connect, $access_token)) {
        send_response(401, $error->custom("ERR_API_TOKEN", "Token invalid or expired!"));
    }

    $user_id = Authenticate::get_user_id($access_token);

    $query = new Build_Query("subjects");
    $query->set_columns(["subject_id"]);
    $query->set_conditions([
        ["subject_id", $data["subject_id"]]
    ]);
    $result = $connect->query($query->get_query());
    if ($result->num_rows == 0) {
        send_response(409, array(
            "error" => true,
            "message" => "Invalid subject! Add subject before enrolling!"
        ));
    }
    mysqli_free_result($result);

    $query->set_table("enrolled");
    $query->set_conditions([
        ["user_id", $user_id],
        ["subject_id", $data["subject_id"]]
    ]);
    $result = $connect->query($query->get_query());
    if ($result->num_rows > 0) {
        send_response(409, array(
            "error" => true,
            "message" => "Duplicate entry for same subject!"
        ));
    }
    mysqli_free_result($result);

    $data["user_id"] = $user_id;
    $query->set_type("insert");
    $query->set_columns(array_keys($data));
    $query->set_values(array_values($data));
    unset($data["user_id"]);

    $result = $connect->query($query->get_query());

    if (mysqli_affected_rows($connect) == 1) {
        send_response(201, array(
            "error" => false,
            "message" => "Successfully enrolled in subject",
            "data" => $data
        ));
    } else {
        send_response(500, $error->db_error(1));
    }

    function verify_weekly_off(string $str_arr) {
        $valid = ["monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday"];
        $tmp = json_decode($str_arr, true);
        if (is_null($tmp) || !is_array($tmp)) {
            return false;
        }
        for ($i = 0; $i < count($tmp); $i++) {
            if (!in_array(strtolower($tmp[$i]), $valid)) {
                return false;
            }
        }
        return json_encode(array_map("strtolower", $tmp));
    }

    function verify_start_date(string $str_date) {
        try {
            $date = strtotime($str_date);
            if (!$date || !checkdate(date("m", $date), date("d", $date), date("Y", $date))) {
                return false;
            }
            if ($date < strtotime(MIN_START_DATE)) {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
        return true;
    }
?>
