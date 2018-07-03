<?php
    class db_connection {
        
        private $db_hostname = "localhost";
        private $db_username = "root";
        private $db_password = "";
        private $db_name = "plans_maker";
        
        private $db_connection;

        function __construct() {
            $this->db_connection = new mysqli(
                $this->db_hostname, 
                $this->db_username, 
                $this->db_password, 
                $this->db_name
            );
            if ($this->db_connection->connect_error) {
                die("connection fialed" . $this->db_connection->connect_error);
            }
        }

        function __destruct() {
            $this->db_connection->close();
        }
        
        public function get_users() {
            $sql = "SELECT * FROM `users;";
            return $this->query($sql);
        }

        public function get_user($user_id) {
            $sql = "SELECT * FROM `users` WHERE `id`= ?";
            return $this->query($sql, $user_id);
        }

        public function get_plans() {
            $sql = "SELECT * FROM `plans`;";
            return $this->query($sql);
        }

        public function get_plan($plan_id) {
            $sql = "SELECT * FROM `plans` WHERE `id` = ?";
            $plan = $this->query($sql, $plan_id);
            if (count($plan) > 0) {
                $plan[0]["lists"] = $this->get_lists($plan_id);
                $plan[0]["schedules"] = $this->get_schedules($plan_id);
                return $plan[0];
            } else {
                return [];
            }
        }

        public function get_lists($plan_id) {
            $sql = "SELECT * FROM `lists` WHERE `plan_id` = ?;";
            $lists = $this->query($sql, $plan_id);
            for ($i = 0; $i < count($lists); $i++) {
                $options = $this->get_options($lists[$i]["id"]);
                for ($j = 0; $j < count($options); $j++) {
                    $assigns = $this->get_assigns($options[$j]["id"]);
                    $options[$j]["assigns"] = $assigns;
                }
                $lists[$i]["options"] = $options;
            }

            return $lists;
        }

        public function get_schedules($plan_id) {
            $sql = "SELECT * FROM `schedules` WHERE `plan_id` = ?;";
            $schedules = $this->query($sql, $plan_id);
            for ($i = 0; $i < count($schedules); $i++) {
                $entries = $this->get_schedule_entries($schedules[$i]["id"]);
                $schedules[$i]["entries"] = $entries;
            }

            return $schedules;
        }

        private function get_options($list_id) {
            $sql = "SELECT * FROM `options` WHERE `list_id` = ?;";
            return $this->query($sql, $list_id);
        }

        private function get_assigns($option_id) {
            $sql = "SELECT * FROM `assigns` WHERE `option_id` = ?;";
            $assigns = $this->query($sql, $option_id);
            for ($i = 0; $i < count($assigns); $i++) {
                $assigns[$i]["user"] = $this->get_user($assigns[$i]["user_id"]);
            }
            return $assigns;
        }

        private function get_schedule_entries($schedule_id) {
            $sql = "SELECT * FROM `schedule_entries` WHERE `schedule_id` = ?;";
            return $this->query($sql, $schedule_id);
        }

        private function query(string $sql, int $binds = -1) {
            $stmt = $this->db_connection->prepare($sql);
            if ($binds >= 0) {
                $stmt->bind_param("i", $binds);
            }
            $stmt->execute();

            $rows = [];
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    array_push($rows, $row);
                }
            }
            $stmt->close();

            return $rows;
        }

        public function add_plan($plan) {
            $sql = "INSERT INTO `plans` (`title`, `description`) VALUES (?, ?);";
            $stmt = $this->db_connection->prepare($sql);
            $stmt->bind_param("ssii", $plan["title"], $plan["description"]);
            $stmt->execute();
            $stmt->close();
        }

        public function add_user($user) {
            $sql = "INSERT INTO `users` (`name`, `surname`, `email`) VALUES (?, ?, ?);";
            $stmt = $this->db_connection->prepare($sql);
            $stmt->bind_param("sss", $user["name"], $user["surname"], $user["email"]);
            $stmt->execute();
            $stmt->close();
        }

        public function add_list($list) {
            $sql = "INSERT INTO `lists` (`title`, `description`, `plan_id`, `type`, `editable`) 
                VALUES (?, ?, ?, ?, ?);";
            $stmt = $this->db_connection->prepare($sql);
            $stmt->bind_param("ssisi", $list["title"], $list["description"],
                $list["plan_id"], $list["type"], $list["editable"]);
            $stmt->execute();
            $stmt->close();
            $this->add_options($list["options"]);
        }

        public function add_schedule($schedule) {
            $sql = "INSERT INTO `schedules` (`title`, `description`, `plan_id`) 
                VALUES (?, ?, ?);";
            $stmt = $this->db_connection->prepare($sql);
            $stmt->bind_param("ssi", $list["title"], $list["description"], $list["plan_id"]);
            $stmt->execute();
            $stmt->close();
            $this->add_schedule_entries($schedule["entries"]);
        }

        private function add_options($options) {
            $sql = "INSERT INTO `options` (`title`, `description`, `list_id`, `max_assigns`) 
                VALUES (?, ?, ?, ?);";
            $stmt = $this->db_connection->prepare($sql);
            $stmt->bind_param("ssii", $title, $description, $list_id, $max_assign);
            for ($i = 0; $i < count($options); $i++) {
                $title = $options[$i]["title"];
                $description = $options[$i]["description"];
                $list_id = $options[$i]["list_id"];
                $max_assign = $options[$i]["max_assigns"];
                $stmt ->execute();
                $this->add_assigns($options[$i]["assigns"]);
            }
            $stmt->close();
        }

        private function add_assigns($assigns) {
            $sql = "INSERT INTO `assigns` (`comment`, `user_id`, `option_id`) 
                VALUES (?, ?, ?);";
            $stmt = $this->db_connection->prepare($sql);
            $stmt->bind_param("sii", $comment, $uesr_id, $option_id);
            for ($i = 0; $i < count($assigns); $i++) {
                $comment = $assigns[$i]["comment"];
                $user_id = $assigns[$i]["user_id"];
                $option_id = $assigns[$i]["option_id"];
                $stmt->execute();
            }
            $stmt->close();
        }

        private function add_schedule_entries($entries) {
            $sql = "INSERT INTO `schedule_entries` (`comment`, `start_time`, `end_time`,
                `user_id`, `schedule_id`) VALUES (?, ?, ?, ?, ?);";
            $stmt = $this->db_connection->prepare($sql);
            $stmt->bind_param("sssii", $comment, $start_time, $end_time, $user_id, $schedule_id);
            for ($i = 0; $i < count($entries); $i++) {
                $comment = $entries[$i]["comment"];
                $start_time = $entries[$i]["start_time"];
                $end_time = $entries[$i]["end_time"];
                $user_id = $entries[$i]["user_id"];
                $schedule_id = $entries[$i]["schedule_id"];
                $stmt->execute();
            }
            $stmt->close();
        }

    }
?>