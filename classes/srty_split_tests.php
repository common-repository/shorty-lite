<?php

class Srty_split_tests extends Srty_core {

    public function __construct() {
        parent::__construct();
    }

    public function init() {
    }

    public function display() {
        wp_cache_delete(SH_PREFIX . 'cache_the_content');
        $this->view('v_split_tests', $this->view_data);
    }

}
