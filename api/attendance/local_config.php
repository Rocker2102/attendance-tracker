<?php
    require "../config/headers.php";
    require "../config/preflight.php";
    require "../config/operations.php";
    require "../config/error_def.php";

    define("DB_DATE_FORMAT", "Y-m-d");

    $error = new Error_Definitions;

    function verify_date(string $str_date) {
        try {
            $date = strtotime($str_date);
            if (!$date || !checkdate(date("m", $date), date("d", $date), date("Y", $date))) {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
        return true;
    }
?>
