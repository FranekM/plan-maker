<?php
    class user_model {
        public $name;
        public $surname;
        public $email;
    }

    class plan_model {
        public $title;
        public $decsription;
        public $lists;
        public $schedules;
    }

    class list_model {
        public $title;
        public $description;
        public $type;
        public $editable;
        public $options;
    }

    class option_model {
        public $title;
        public $description;
        public $assigns;
    }

    class assign_model {
        public $comment;
        public $user;
    }

    class schedule_model {
        public $title;
        public $description;
        public $entries;
    }

    class schedule_entry_model {
        public $comment;
        public $start_time;
        public $end_time;
        public $user;
    }
?>