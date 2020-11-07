<?php
    require "local_config.php";

    $allowed_req_methods = ["POST", "GET"];

    if (!check_request_method($allowed_req_methods)) {
        send_response(405, $error->data_error(1));
    }

    $data = array_merge($_GET, $_POST);

    $required_keys = [];
    $valid_keys = ["subject_id", "name"];

    $validator = new Validate_Data;
    $data = $validator->verify($data, $required_keys, $valid_keys);

    Utility::escape_array($data);

    require "../config/database.php";
    $database = new Database;
    $connect = $database->get_connect_var();

    $query = "SELECT * FROM subjects WHERE 1 ";
    isset($data["subject_id"]) ? $query .= " AND subject_id LIKE '%" . $data["subject_id"] . "%' " : false;
    isset($data["name"]) ? $query .= " AND name LIKE '%" . $data["name"] . "%' " : false;

    $result = $connect->query($query);

    if (!$result || $result->num_rows == 0) {
        send_response(404, array(
            "error" => true,
            "message" => "Empty result set"
        ));
    } else {
        $subjects = [];
        while ($row = $result->fetch_assoc()) {
            array_push($subjects, array(
                "code" => $row["subject_id"],
                "name" => $row["name"]
            ));
        }
    }

    send_response(200, array(
        "error" => false,
        "message" => "Request completed",
        "rowcount" => $result->num_rows,
        "data" => $subjects
    ));
?>
