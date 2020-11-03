<?php
    // error_reporting(0);

    class Database {
        private $host = "localhost";
        private $db_name = "attendance_tracker";
        private $username = "root";
        private $password = "";
        private $db = null;
        private $error = "";

        function __construct() {
            try {
                $this->db = new \mysqli($this->host, $this->username, $this->password, $this->db_name);
            } catch (\Exception $e) {
                $this->error = $e->connect_error;
            }
        }

        public function get_connect_var() {
            return $this->db;
        }

        public function get_error_msg() {
            return $this->error;
        }

        function __destruct() {
            if ($this->db instanceof \mysqli) {
                mysqli_close($this->db);
            }
        }
    }
?>
