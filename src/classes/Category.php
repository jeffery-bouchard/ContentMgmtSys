<?php
    class Category {
        protected $db;

        public function __construct(Database $db) {
            $this->db = $db;
        }

        public function get(int $id) {
            $sql = "SELECT id, name FROM category WHERE id = :id;";
            return $this->db->runSQL($sql, [$id])->fetch();
        }

        public function getAll(): array {
            $sql = "SELECT id, name FROM category;";
            return $this->db->runSQL($sql)->fetchAll();
        }

        public function count(): int {
            $sql = "SELECT COUNT(id) FROM category;";
            return $this->db->runSQL($sql)->fetchColumn();
        }
    }
?>