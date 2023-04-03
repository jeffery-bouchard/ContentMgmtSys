<?php
    class Article {
        protected $db;

        public function __construct(Database $db) {
            $this->db = $db;
        }

        // get one article
        public function get(int $id, bool $active = true) {
            $sql  = "SELECT article.id, article.title, article.date, article.category_id, article.price, article.prod_name, article.prod_description, article.summary, 
                            article.image, article.image_alt, article.content, article.advertisement, category.name, article.active
                       FROM article
                       JOIN category ON article.category_id = category.id
                      WHERE article.id = :id ";
            if ($active) {
                $sql .= "AND article.active = 1;";
            }
            return $this->db->runSQL($sql, [$id])->fetch();
        }

        // get all articles
        public function getAll($category = null, $limit = 1000, $active = true): array {

            $arguments['category'] = $category;
            $arguments['category1'] = $category;
            $arguments['limit'] = $limit;
            
            $sql  = "SELECT article.id, article.title, article.date, article.category_id, article.price, article.prod_name, article.prod_description, article.summary, 
                            article.image, article.image_alt, article.video_name, article.video_description, article.video_mp4, article.video_mov, article.video_ogg, 
                            article.video_webm, article.video_poster, article.video_thumbnail, category.name, article.active
                       FROM article
                       JOIN category ON article.category_id = category.id
                      WHERE (article.category_id = :category OR :category1 IS null) ";
            if ($active) {
                $sql .= "AND article.active = 1 ";
            }
            $sql .= "ORDER BY article.date DESC LIMIT :limit;";
            return $this->db->runSQL($sql, $arguments)->fetchAll();
        }

        // get number of search matches
        public function searchCount(string $term, $category=null): int {
            $arguments['term1'] = $arguments['term2'] = $arguments['term3'] = '%' . $term . '%';
            if ($category) {
                $arguments['category'] = $category;
            } 
            $sql   = "SELECT COUNT(article.id)
                        FROM article
                       WHERE (article.title  LIKE :term1 
                          OR article.summary LIKE :term2 
                          OR article.content LIKE :term3) ";
            if ($category) {
                $sql .= "AND article.category_id = :category ";
            }
            $sql .= "AND article.active = 1;";
            return $this->db->runSQL($sql, $arguments)->fetchColumn();
        }

        // get articles of search matches
        public function search(string $term, $category=null, int $show = 10, int $from = 0): array {
            $arguments['term1'] = $arguments['term2'] = $arguments['term3'] = '%' . $term . '%';
            if ($category) {
                $arguments['category'] = $category;
            }
            $arguments['show']  = $show;
            $arguments['from']  = $from;
            $sql  = "SELECT article.id, article.title, article.date, article.category_id, article.summary, 
                            article.image_search, article.image_alt, article.active,
                            category.name as category
                       FROM article
                       JOIN category ON article.category_id = category.id
                      WHERE (article.title  LIKE :term1 
                         OR article.summary LIKE :term2
                         OR article.content LIKE :term3) ";
            if ($category) {
                $sql .= "AND article.category_id = :category ";
            }
            $sql .=    "AND article.active = 1
                   ORDER BY article.date DESC
                      LIMIT :show 
                     OFFSET :from;";
            return $this->db->runSQL($sql, $arguments)->fetchAll();
        }
    }
?>