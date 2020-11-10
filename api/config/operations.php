<?php
    define("HASH_SALT", "AtTEnD@nc3-SiTe");
    define("TOKEN_VALIDITY", 3600);
    define("HOME_URL", "http://localhost/development/attendance-tracker/");
    define("MIN_START_DATE", "2020-01-01");
    const ENABLE_REAUTH = array("reauth" => true);
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

        public static function remove_keys(array &$arr, array $remove) {
            $max_len = count($remove);
            for ($i = 0; $i < $max_len; $i++) {
                if (isset($arr[$remove[$i]])) {
                    unset($arr[$remove[$i]]);
                }
            }
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

    class Validate_Data {
        private $last_set = null;

        public function verify(array $data, array $required_keys = [], array $valid_keys = []) {
            $filter_data = [];

            for ($i = 0; $i < count($required_keys); $i++) {
                if (isset($data[$required_keys[$i]]) && !empty($data[$required_keys[$i]])) {
                    $filter_data[$required_keys[$i]] = $data[$required_keys[$i]];
                } else {
                    return false;
                }
            }

            for ($i = 0; $i < count($valid_keys); $i++) {
                if (isset($data[$valid_keys[$i]]) && !empty($data[$valid_keys[$i]])) {
                    $filter_data[$valid_keys[$i]] = $data[$valid_keys[$i]];
                }
            }

            $this->last_set = $filter_data;
            return $filter_data;
        }

        public function basic(array $data) {
            for ($i = 0; $i < count($data); $i++) {
                if (!in_array(strtolower($data[$i][0]), $data[$i][1])) {
                    return false;
                }
            }
            return true;
        }

        public function advanced($data, string $mode, $extra_data = null) {
            if ($mode == "equals") {
                if ($data === $extra_data) {
                    return true;
                } else {
                    return false;
                }
            } else if ($mode == "email") {
                if (filter_var($data, FILTER_VALIDATE_EMAIL)) {
                    return true;
                } else {
                    return false;
                }
            } else if ($mode == "char_only") {
                if (!preg_match("/^[a-zA-Z ]*$/", $data)) {
                    return false;
                } else {
                    return true;
                }
            } else if ($mode == "none") {
                return true;
            } else {
                return false;
            }
        }

        public function get_last_result() {
            return $this->last_set;
        }
    }

    class Build_Query {
        private $type = null;
        private $table = null;
        private $columns = null;
        private $values = null;
        private $conditions = null;
        private $sep = "AND";

        function __construct(string $table = null, string $type = "select") {
            $this->table = $table;
            $this->type = strtoupper($type);
        }

        public function set_table(string $table) {
            $this->table = $table;
        }

        public function set_type(string $type) {
            $this->type = strtoupper($type);
        }

        public function set_columns(array $cols = null) {
            $this->columns = $cols;
        }

        public function set_values(array $val = null) {
            $this->values = $val;
        }

        public function set_conditions($conditions) {
            $this->conditions = $conditions;
        }

        public function set_sep(string $sep) {
            $this->sep = strtoupper($sep);
        }

        public function restore_all() {
            $this->type = null;
            $this->table = null;
            $this->columns = null;
            $this->values = null;
            $this->conditions = null;
            $this->sep = "AND";
        }

        public function get_query() {
            switch ($this->type) {
                case "INSERT": return $this->insert_query(); break;
                case "SELECT": return $this->select_query(); break;
                case "UPDATE": return $this->update_query(); break;
                case "DELETE": return $this->delete_query(); break;
                default: return null; break;
            }
        }

        private function query_part($attr, $val) {
            $tmp = mb_substr($val, 0, 2);
            if ($tmp == "~~") {
                $val = mb_substr($val, 2);
                return " `" . $attr . "` = (" . $val . ") ";
            } else {
                return " `" . $attr . "` = '" . $val . "' ";
            }
        }

        private function merge_conditions(string $query) {
            $query .= " WHERE ";
            $size = count($this->conditions);
            for ($i = 0; $i < $size; $i++) {
                $query .= $this->query_part($this->conditions[$i][0], $this->conditions[$i][1]);
                if ($i < $size - 1) {
                    $query .= " " . $this->sep . " ";
                }
            }
            return $query;
        }

        private function select_query() {
            $query = "SELECT ";
            if (is_array($this->columns)) {
                $query .= join(", ", $this->columns);
                $query .= " FROM `" . $this->table . "` ";
            } else {
                $query .= " * FROM `" . $this->table . "` ";
            }

            if ($this->conditions == null) {
                return $query;
            } else if (is_array($this->conditions)) {
                return $this->merge_conditions($query);
            } else {
                $query .= " WHERE " . $this->conditions;
                return $query;
            }
        }

        private function insert_query() {
            $query = "INSERT INTO `" . $this->table. "` ";
            if (is_array($this->columns)) {
                $query .= "(";
                $query .= join(", ", $this->columns);
                $query .= ")";
            }
            $query .= " VALUES ";

            $size = count($this->values);
            $query .= " ( ";
            for ($i = 0; $i < $size; $i++) {
                $query .= " '" . $this->values[$i] . "' ";
                if ($i < $size - 1) {
                    $query .= ", ";
                }
            }
            $query .= " ) ";
            return $query;
        }

        private function delete_query() {
            $query = "DELETE FROM `" . $this->table. "` ";

            if ($this->conditions == null) {
                return $query;
            } else if (is_array($this->conditions)) {
                return $this->merge_conditions($query);
            } else {
                $query .= " WHERE " . $this->conditions;
                return $query;
            }
        }

        private function update_query() {
            $query = "UPDATE " . $this->table . " SET ";

            if (is_array($this->columns)) {
                $size = count($this->columns);
                for ($i = 0; $i < $size; $i++) {
                    $query .= $this->query_part($this->columns[$i], $this->values[$i]);
                    if ($i < $size - 1) {
                        $query .= ", ";
                    }
                }
            }

            if ($this->conditions == null) {
                return $query;
            } else if (is_array($this->conditions)) {
                return $this->merge_conditions($query);
            } else {
                $query .= " WHERE " . $this->conditions;
                return $query;
            }
        }
    }

    class Authenticate {
        private function is_valid(string $expires) {
            $current = time();
            $expires = strtotime($expires);

            return $expires > $current;
        }

        public static function generate_token($user_id) {
            return [
                $user_id,
                time() . Utility::get_random_str(8),
                Utility::get_random_str(32)
            ];
        }

        public static function split_token(string $token) {
            $token = explode(".", $token);
            return count($token) == 3 ? array(
                "user_id" => $token[0],
                "token_id" => $token[1],
                "token_payload" => $token[2]
            ) : false;
        }

        public static function get_user_id(string $token) {
            $token = self::split_token($token);
            return $token["user_id"];
        }

        public static function delete_access_token($connect, string $token) {
            $token = self::split_token($token);
            if (!$token) {
                return false;
            }

            $query = new Build_Query("access_tokens", "delete");
            $query->set_conditions([
                ["user_id", $token["user_id"]],
                ["token_id", $token["token_id"]]
            ]);

            $connect->query($query->get_query());
            return mysqli_affected_rows($connect) == 1;
        }

        public static function delete_all($connect, $user_id) {
            $query = "DELETE FROM access_tokens WHERE user_id = '$user_id'";

            $connect->query($query);
            return mysqli_affected_rows($connect) >= 1;
        }

        public static function delete_all_expired($connect, $user_id) {
            $current = date("Y-m-d H:i:s");
            $query = "DELETE FROM access_tokens WHERE user_id = '$user_id' AND expires < '$current'";

            $connect->query($query);
            return mysqli_affected_rows($connect) >= 1;
        }

        public static function verify_access_token($connect, string $token) {
            $token = self::split_token($token);
            if (!$token) {
                return false;
            }

            $query = new Build_Query("access_tokens");
            $query->set_columns(["expires"]);
            $query->set_conditions([
                ["user_id", $token["user_id"]],
                ["token_id", $token["token_id"]],
                ["token_payload", $token["token_payload"]],
                ["ip", Utility::get_client_ip()]
            ]);

            try {
                $result = $connect->query($query->get_query());
                if ($result && $result->num_rows == 1) {
                    $row = $result->fetch_assoc();
                    if (self::is_valid($row["expires"])) {
                        return true;
                    } else {
                        @self::delete_access_token($connect, join(".", $token));
                        return false;
                    }
                } else {
                    @self::delete_access_token($connect, join(".", $token));
                    return false;
                }
            } catch (Exception $e) {
                return false;
            }
        }
    }

    function check_request_method(array $allowed) {
        array_push($allowed, "OPTIONS");
        return in_array(strtoupper($_SERVER["REQUEST_METHOD"]), $allowed);
    }

    function get_access_token() {
        if (isset($_SERVER["HTTP_X_ACCESS_TOKEN"]) && !empty($_SERVER["HTTP_X_ACCESS_TOKEN"])) {
            return trim($_SERVER["HTTP_X_ACCESS_TOKEN"]);
        } else {
            return false;
        }
    }

    function is_username_available($connect, $username) {
        $query = "SELECT user_id FROM users WHERE username = '$username'";
        $result = $connect->query($query);
        return $result->num_rows == 0;
    }

    function send_response($httpcode, array $response = null, array $merge = []) {
        http_response_code($httpcode);
        echo $response == null ? "" : json_encode(array_merge($response, $merge));
        exit();
    }
?>
