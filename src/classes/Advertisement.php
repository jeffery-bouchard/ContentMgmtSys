<?php
    class Advertisement {

        public function __construct() {
            $this->path = 'inc/ads';
        }

        public function get() {
            $files = glob($this->path . '/*.*');
            $file = array_rand($files);
            return $files[$file];
        }

    }
?>