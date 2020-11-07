<?php
    require "../config/headers.php";
    require "../config/preflight.php";
    require "../config/operations.php";
    require "../config/error_def.php";

    $error = new Error_Definitions;

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
