<?php
    class CMS {
        protected $db = null;
        protected $article = null;
        protected $category = null;
        protected $logger = null;
        
        public function __construct($dsn, $username, $password) {
            $this->db = new Database($dsn, $username, $password);
        }

        public function getArticle() {
            if ($this->article === null) {
                $this->article = new Article($this->db);
            }
            return $this->article;
        }

        public function getCategory() {
            if ($this->category === null) {
                $this->category = new Category($this->db);
            }
            return $this->category;
        }

        public function getLogger() {
            if ($this->logger === null) {
                $this->logger = new Logger($this->db);
            }
            return $this->logger;
        }

    }
?>