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

    function remove_offdates_conflicts(array $data, array $off_dates) {
        for ($i = 0; $i < count($data); $i++) {
            if (in_array($data[$i]["date"], $off_dates)) {
                array_splice($data, $i, 1);
                $i--;
            }
        }
        return $data;
    }

    function split_attendance(array $data, array &$h, array &$l) {
        $max_len = count($data);
        for ($i = 0; $i < $max_len; $i++) {
            $type = $data[$i]["type"];
            unset($data[$i]["type"]);
            $type == "leave" ? array_push($l, $data[$i]) : array_push($h, $data[$i]);
        }
    }

    function total_days($begin_date, $end_date = null) {
        $end_date == null ? $end_date = strtotime(date(DB_DATE_FORMAT)) : false;
        $end_date += 86400; /* to count current day as well */
        return $end_date < $begin_date ? 0 : floor(($end_date - $begin_date) / 86400);
    }

    function compute_offdates(array $offdays, $begin_date, $end_date = null) {
        $end_date == null ? $end_date = strtotime(date(DB_DATE_FORMAT)) : false;
        $offdates = [];
        while ($begin_date <= $end_date) {
            in_array(strtolower(date("l", $begin_date)), $offdays) ? array_push($offdates, date(DB_DATE_FORMAT, $begin_date)) : false;
            $begin_date += 86400;
        }
        return $offdates;
    }
?>
