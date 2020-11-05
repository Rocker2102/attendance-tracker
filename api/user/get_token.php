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

    require "../config/database.php";
    $database = new Database;
    $connect = $database->get_connect_var();

    $data = $_POST;

    if ($data == null || empty($data)) {
        send_response(400, $error->data_error(2));
    }

    $required_keys = ["username", "password"];
    $valid_keys = [];

    $validator = new Validate_Data;
    $data = $validator->verify($data, $required_keys, $valid_keys);

    if (!$data) {
        send_response(400, $error->form_error(1));
    }

    Utility::escape_array($data);

    $query = new Build_Query("users", "select");
    $query->set_conditions([
        ["username", $data["username"]],
        ["password", Utility::get_hash($data["password"])]
    ]);

    $result = $connect->query($query->get_query() . " LIMIT 1");

    if (!$result || $result->num_rows == 0) {
        send_response(404, array(
            "error" => true,
            "message" => "Invalid Credentials!"
        ));
    } else {
        $user = $result->fetch_assoc();
        unset($user["password"]);
    }

    $token = [
        $user["user_id"],
        time() . Utility::get_random_str(8),
        Utility::get_random_str(32)
    ];
    $expires = date("Y-m-d H:i:s", time() + TOKEN_VALIDITY);

    $query->set_table("access_tokens");
    $query->set_type("insert");
    $query->set_columns(["user_id", "token_id", "token_payload", "expires"]);
    $query->set_values(array_merge($token, [$expires]));

    Authenticate::delete_all_expired($connect, $user["user_id"]);
    $connect->query($query->get_query());

    if (mysqli_affected_rows($connect) == 1) {
        send_response(200, array(
            "error" => false,
            "message" => "Token granted",
            "data" => array(
                "token" => implode(".", $token),
                "valid_till" => $expires
            )
        ));
    } else {
        send_response(500, array(
            "error" => true,
            "message" => "Failed to grant token"
        ));
    }
?>
