<?php
    class Error_Definitions {
        private function format(array $err, array $merge = []) {
            return array_merge(array(
                "error" => true,
                "code" => $err[0],
                "message" => $err[1]
            ), $merge);
        }

        public static function custom($code, $msg) {
            return self::format([$code, $msg]);
        }

        public static function api_error($e = 1, $custom = "") {
            switch ($e) {
                case 0: return self::format(["ERR_API_CTM", $custom], ENABLE_REAUTH); break;
                case 1: return self::format(["ERR_API_AUTH", "Access Token missing!"], ENABLE_REAUTH); break;
                case 2: return self::format(["ERR_API_TOKEN", "Token invalid or expired!"], ENABLE_REAUTH); break;
                default: return self::format(["ERR_API_DEF", "API Error!"], ENABLE_REAUTH);
            }
        }

        public static function data_error($e = 1, $custom = "") {
            switch ($e) {
                case 0: return self::format(["ERR_DATA_CTM", $custom]); break;
                case 1: return self::format(["ERR_DATA_1", "Request method not allowed!"]); break;
                case 2: return self::format(["ERR_DATA_2", "Invalid data Format!"]); break;
                default: return self::format(["ERR_DATA_DEF", "Invalid Request!"]);
            }
        }

        public static function form_error($e = 1, $custom = "") {
            switch ($e) {
                case 0: return self::format(["ERR_FORM_CTM", $custom]); break;
                case 1: return self::format(["ERR_FORM_1", "Bad Request! Check for empty form fields"]); break;
                case 2: return self::format(["ERR_FORM_2", "Invalid Request! Form contains invalid fields!"]); break;
                case 3: return self::format(["ERR_FORM_3", "Invalid Request! Validation Failed!"]); break;
                default: return self::format(["ERR_FORM_DEF", "Invalid Request!"]);
            }
        }

        public static function db_error($e = 1, $custom = "") {
            switch ($e) {
                case 0: return self::format(["ERR_DB_CTM", $custom]); break;
                case 1: return self::format(["ERR_DB_1", "Unable to update database!"]); break;
                case 2: return self::format(["ERR_DB_2", "Unable to delete! Try refreshing the page!"]); break;
                case 3: return self::format(["ERR_DB_3", "Failed to query database!"]); break;
                default: return self::format(["ERR_DB_DEF", "Internal DB Error!"]);
            }
        }
    }
?>
