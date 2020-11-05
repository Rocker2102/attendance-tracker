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

    require "../config/database.php";
    $database = new Database;
    $connect = $database->get_connect_var();

    if (!Authenticate::verify_access_token($connect, $access_token)) {
        send_response(401, $error->custom("ERR_API_TOKEN", "Token invalid or expired!"));
    }

    $user_id = Authenticate::get_user_id($access_token);
    $query = new Build_Query("users", "select");
    $query->set_conditions([
        ["user_id", $user_id]
    ]);

    $result = $connect->query($query->get_query());

    if (!$result || $result->num_rows == 0) {
        send_response(404, array(
            "error" => true,
            "message" => "User not found"
        ));
    } else {
        $user = $result->fetch_assoc();
        unset($user["password"]);
    }

    send_response(200, array(
        "error" => false,
        "message" => "User details found",
        "data" => $user
    ));
?>
