<?php
    define("HASH_SALT", "AtTEnD@nc3-SiTe");
    date_default_timezone_set("Asia/Kolkata");

    class Utility {
        public static function get_hash($data) {
            return hash("sha256", $data.HASH_SALT);
        }

        public static function get_client_ip() {
            $client  = @$_SERVER["HTTP_CLIENT_IP"];
            $forward = @$_SERVER["HTTP_X_FORWARDED_FOR"];
            $remote  = $_SERVER["REMOTE_ADDR"];

            if (filter_var($client, FILTER_VALIDATE_IP)) {
                $ip = $client;
            } else if (filter_var($forward, FILTER_VALIDATE_IP)) {
                $ip = $forward;
            } else {
                $ip = $remote;
            }

            $ip == "::1" ? $ip = "localhost" : false;
            return $ip;
        }

        public static function escape_array(array &$data, array $skip = []) {
            foreach ($data as $key => $value) {
                if (in_array($key, $skip)) {
                    continue;
                }
                if (is_array($value)) {
                    foreach ($value as $value_key => $value_val) {
                        $value[$value_key] = addslashes($value_val);
                    }
                } else {
                    $data[$key] = addslashes($value);
                }
            }
        }

        public static function get_random_str(int $len = 8, int $mix_mode = 6) {
            $num_arr = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
            $char_arr = ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"];
            $special_arr = ["!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "-", "_", "+", "=", "{", "}", "[", "]", ":", ";", "<", ">", ".", "?", "/", "|", "~"];
            $tmp_arr = [];
            $random_str = "";

            switch ($mix_mode) {
                case 6: $tmp_arr = array_merge($num_arr, $char_arr); break;
                case 7: $tmp_arr = array_merge($num_arr, $char_arr, $special_arr); break;
                case 4: $tmp_arr = $num_arr; break;
                case 2: $tmp_arr = $char_arr; break;
                case 5: $tmp_arr = array_merge($num_arr, $special_arr); break;
                case 3: $tmp_arr = array_merge($char_arr, $special_arr); break;
                case 1: $tmp_arr = $special_arr;
                default: $tmp_arr = array_merge($num_arr, $char_arr, $special_arr); break;
            }

            shuffle($tmp_arr);
            $arr_max_len = count($tmp_arr) - 1;

            for ($i = 0; $i < $len; $i++) {
                $random_str .= $tmp_arr[mt_rand(0, $arr_max_len)];
            }

            return $random_str;
        }

        public static function get_current_url() {
            return (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on" ? "https" : "http")
                . "://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
        }
    }

    function exit_script(stdClass &$json_obj, $error_val = 1, $info) {
        $json_obj->error = $error_val;
        $json_obj->info = $info;

        echo json_encode($json_obj);
        exit();
    }
?>
