<?php
    class Logger {
        protected $db;

        public function __construct(Database $db) {
            $this->db = $db;
        }

        // create new entry
        public function insert($date, $time, $ip_address, $user_agent, $referer, $page, $country, $state, $city) {
            $arguments['date'] = $date;
            $arguments['time'] = $time;
            $arguments['ip_address'] = $ip_address;
            $arguments['user_agent'] = $user_agent;
            $arguments['referer'] = $referer;
            $arguments['page'] = $page;
            $arguments['country'] = $country;
            $arguments['state'] = $state;
            $arguments['city'] = $city;
            $this->db->beginTransaction();  
            $sql = "INSERT INTO users (date, time, ip_address, user_agent, referer, page, country, state, city)
                         VALUES (:date, :time, :ip_address, :user_agent, :referer, :page, :country, :state, :city);";
            $this->db->runSQL($sql, $arguments);
            $this->db->commit();
        }
    }
?>