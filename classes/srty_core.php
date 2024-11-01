<?php

class Srty_core {

    public $view_data = array();
    public $wpdb;
    public $table_name;
    public $gump;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->gump = new Srty_gump();
    }

    /**
     * Load view file
     * @param type $filename 
     * @param type $view_data array
     * 
     */
    public function view($filename = 'v_404', $view_data = array(), $with_header_footer = TRUE) {
        extract($view_data);
        $file_path = SH_VIEW_PATH . '/' . $filename . '.php';

        if ($with_header_footer) {
            include(SH_VIEW_PATH . '/v_header.php');
        }
        if (file_exists($file_path)) {
            include($file_path);
        }
        if ($with_header_footer) {
            include(SH_VIEW_PATH . '/v_footer.php');
        }
    }

    public function top_message() {
        if (get_option(SH_PREFIX . 'top_message') !== FALSE) {
            $this->view_data['msg'] = get_option(SH_PREFIX . 'top_message');
            delete_option(SH_PREFIX . 'top_message');
        }

        if (isset($this->view_data['msg']['status'])) {
            echo '<div class="alert ' . $this->view_data['msg']['status'] . '" role="alert">' . $this->view_data['msg']['text'] . '</div>';
        }
    }

    public function set_top_message($msg = array()) {
        update_option(SH_PREFIX . 'top_message', $msg);
    }

    /**
     * default route
     */
    public function routes() {
        if (isset($_GET['action']) && $_GET['action'] == 'add') {
            
                $this->add();
           
        } else if (isset($_GET['action']) && $_GET['action'] == 'edit') {
            $this->edit();
        } else if (isset($_GET['action']) && $_GET['action'] == 'clone') {
            $this->clone_page();
        } else if (isset($_GET['action']) && $_GET['action'] == 'delete') {
           
                $this->delete();
           
        } else {
            $this->display();
        }
    }

    /**
     * add page
     */
    public function add() {
        $this->view();
    }

    /**
     * clone page
     */
    public function clone_page() {
        
    }

    /**
     * edit page
     */
    public function edit() {
        $this->view();
    }

    public function delete() {
        
            $class_name = strtolower(get_class($this));
            $this->_delete($_GET['id']);
            $this->view_data['msg'] = array(
                'status' => 'alert-success',
                'text' => sprintf(SRTY_MSG_GENERAL_DELETED, str_replace(SH_PREFIX, '', $class_name))
            );
            $this->set_top_message($this->view_data['msg']);
            wp_redirect('?page=' . $_GET['page']);
        
    }

    /**
     * display page
     */
    public function display() {
        $this->view();
    }

    public function current_domain($show_protocol = TRUE, $show_tldonly = FALSE, $check_custom_domain = FALSE) {
        $tracking_domain = get_option(SH_PREFIX . 'settings_tracking_domain'); // . '/';
        $tracking_domain = trim($tracking_domain) != '' ? $tracking_domain . '/' : '';

        if (isset($_SERVER['HTTPS'])) {
            $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
        } else {
            $protocol = 'http';
        }

        if ($check_custom_domain && get_option(SH_PREFIX . 'settings_enable_custom_domain')) {
            $site_url = get_option(SH_PREFIX . 'settings_custom_domain');
            $tracking_domain = '';
        } else {
            $site_url = site_url();
            $site_url = str_replace('http://', '', $site_url);
            $site_url = str_replace('https://', '', $site_url);
        }

        return (($show_protocol) ? $protocol . "://" : '') . $site_url . '/' . ((!$show_tldonly) ? $tracking_domain : '');
    }

    public function is_valid_integer($int) {
        if (!filter_var($int, FILTER_VALIDATE_INT) === false) {
            return TRUE;
        }
        return FALSE;
    }

    public function is_valid_float($float) {
        if (!filter_var($float, FILTER_VALIDATE_FLOAT) === false) {
            return TRUE;
        }
        return FALSE;
    }

    public function is_valid_url($url) {
        if (!filter_var($url, FILTER_VALIDATE_URL) === false) {
            return TRUE;
        }
        return FALSE;
    }

    public function explode_trim($str = '', $delimiter = ',') {
        return array_map('trim', explode($delimiter, $str));
    }

    protected function _delete($id) {
        $this->wpdb->delete($this->table_name, array('id' => $id), array('%d'));
    }

    protected function _delete_batch($ids = array()) {
        foreach ($ids as $id) {
            $this->_delete($id);
        }
    }

    protected function _order(&$params) {
        $order = '';
        if (isset($_POST['order'])) {
            $_order = $_POST['order'];
            $order = "ORDER BY %d {$_order[0]['dir']} ";
            $params = array_merge($params, array(intval($_order[0]['column'] + 1)));
        }

        return $order;
    }

    protected function _limit(&$params) {

        $limit = '';
        if (isset($_POST['start']) && $_POST['length'] != -1) {
            $limit = "LIMIT %d, %d";
            $params = array_merge($params, array(intval($_POST['start']), intval($_POST['length'])));
        }
        return $limit;
    }

    public function generate_random_letters() {
//http://stackoverflow.com/questions/307486/short-unique-id-in-php
        $length = 6;
        $random = '';
        for ($i = 0; $i < $length; $i++) {
//$random .= chr(rand(ord('a'), ord('z')));
            $random .= rand(0, 1) ? rand(0, 9) : chr(rand(ord('a'), ord('z')));
        }
        return $random;
    }

    protected function _post($field_name = '', $default = '') {
        return isset($_POST[$field_name]) ? $_POST[$field_name] : $default;
    }

    protected function _get($field_name = '', $default = '') {
        return isset($_GET[$field_name]) ? $_GET[$field_name] : $default;
    }

    function ip_info($ip = NULL) {
        $output = NULL;
        if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
            if (isset($_SERVER["REMOTE_ADDR"])) {
                $ip = $_SERVER["REMOTE_ADDR"];
            } elseif (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            }
        }

        $args = array(
            'timeout' => 5,
            'redirection' => 5,
            'httpversion' => '1.0',
            'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url'),
            'blocking' => true,
            'headers' => array(),
            'cookies' => array(),
            'body' => null,
            'compress' => false,
            'decompress' => true,
            'sslverify' => false,
            'stream' => false,
            'filename' => null
        );
        $ipdat = @json_decode(wp_remote_retrieve_body(wp_remote_get(SH_FREEGEOIP_URL . $ip, $args)));

        if (@strlen(trim($ipdat->country_code)) == 2) {
            $output = array(
                'country_code' => $ipdat->country_code,
                'country' => $ipdat->country_name,
                'region_code' => $ipdat->region_code,
                'region_name' => $ipdat->region_name,
                'city' => $ipdat->city,
                'latitude' => $ipdat->latitude,
                'longitude' => $ipdat->longitude,
            );
        }
//        }
        return $output;
    }

    public function date_format($date, $format = 'm/d/Y') {
        return date($format, strtotime($date));
    }

    public function date_selection() {
        $page = $this->_get('page');
        $action = $this->_get('action');
        $source = $this->_get('source');
        $medium = $this->_get('medium');
        $campaign = $this->_get('campaign');
        $content = $this->_get('content');
        $term = $this->_get('term');

        $date_from = date('Y-m-d 00:00:00', strtotime(current_time("Y-m-d") . " -29 day"));
        $date_to = date('Y-m-d 23:59:59', strtotime(current_time("Y-m-d") . " -0 day"));

        $date_selection = json_decode(get_option(SH_PREFIX . 'date_selection', json_encode(
                                array(
                                    'dtselect' => 'last30days',
                                    'date_from' => $date_from,
                                    'date_to' => $date_to,
                                )
        )));

        $dtselect = $this->_get('dtselect', $date_selection->dtselect);
        $date_from = date('Y-m-d 00:00:00', strtotime($this->_get('date_from', $date_selection->date_from)));
        $date_to = date('Y-m-d 23:59:59', strtotime($this->_get('date_to', $date_selection->date_to)));



        switch ($dtselect) {
            case 'last24hours':
                $display_date = 'Last 24 Hours';
                $display_title = ' for ' . $display_date;

                $date_from = date('Y-m-d H:i:s', strtotime(current_time("Y-m-d H:i:s") . " -23 hours"));
                $date_to = current_time('Y-m-d H:i:s');
                break;
            case 'today':
                $display_date = 'Today';
                $display_title = ' for ' . $display_date;

                $date_from = current_time('Y-m-d 00:00:00');
                $date_to = current_time('Y-m-d 23:59:59');
                break;
            case 'yesterday':
                $display_date = 'Yesterday';
                $display_title = ' for ' . $display_date;

                $date_from = date('Y-m-d 00:00:00', strtotime(current_time("Y-m-d") . " -1 day"));
                $date_to = date('Y-m-d 23:59:59', strtotime(current_time("Y-m-d") . " -1 day"));
                break;
            case 'last7days':
                $display_date = 'Last 7 Days';
                $display_title = ' for ' . $display_date;

                $date_from = date('Y-m-d 00:00:00', strtotime(current_time("Y-m-d") . " -6 day"));
                $date_to = date('Y-m-d 23:59:59', strtotime(current_time("Y-m-d") . " -0 day"));
                break;
            case 'last30days':
                $display_date = 'Last 30 Days';
                $display_title = ' for ' . $display_date;

                $date_from = date('Y-m-d 00:00:00', strtotime(current_time("Y-m-d") . " -29 day"));
                $date_to = date('Y-m-d 23:59:59', strtotime(current_time("Y-m-d") . " -0 day"));
                break;
            case 'thismonth':
                $display_date = 'This Month';
                $display_title = ' for ' . $display_date;

                $date_from = current_time('Y-m-01 00:00:00');
                $date_to = current_time('Y-m-t 23:59:59');
                break;
            case 'lastmonth':
                $display_date = 'Last Month';
                $display_title = ' for ' . $display_date;

                $date_from = date('Y-m-01 00:00:00', strtotime(current_time("Y-m-d") . " -1 month"));
                $date_to = date('Y-m-t 23:59:59', strtotime(current_time("Y-m-d") . " -1 month"));
                break;
            case 'thisyear':
                $display_date = 'This Year';
                $display_title = ' for ' . $display_date;

                $date_from = current_time('Y-01-01 00:00:00');
                $date_to = current_time('Y-12-31 23:59:59');
                break;
            case 'lastyear':
                $display_date = 'Last Year';
                $display_title = ' for ' . $display_date;

                $date_from = date('Y-01-01 00:00:00', strtotime(current_time("Y-m-d") . " -1 year"));
                $date_to = date('Y-12-31 23:59:59', strtotime(current_time("Y-m-d") . " -1 year"));
                break;
            case 'custom':
                $display_date = 'Custom Date';
                $display_title = 'from ' . date('d M Y', strtotime($date_from)) . ' - ' . date('d M Y', strtotime($date_to));
                break;
            default:
                $display_date = 'Last 30 Days';
                $display_title = ' for ' . $display_date;
                $date_from = date('Y-m-d 00:00:00', strtotime(current_time("Y-m-d") . " -29 day"));
                $date_to = date('Y-m-d 23:59:59', strtotime(current_time("Y-m-d") . " -0 day"));
                break;
        }

        if ($action == 'report-medium') {
            $html = '<button id="today" type="button" class="btn btn-default"><i class="fa fa-calendar"></i> ' . $display_date . '</button>';

            $html .= '<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"> <span class="caret"></span></button>';
            $html .= '<ul class="dropdown-menu pull-right" style="min-width:200px;">';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&source=' . $source . '&dtselect=last24hours">Last 24 Hours</a></li>';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&source=' . $source . '&dtselect=today">Today</a></li>';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&source=' . $source . '&dtselect=yesterday">Yesterday</a></li>';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&source=' . $source . '&dtselect=last7days">Last 7 Days</a></li>';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&source=' . $source . '&dtselect=last30days">Last 30 Days</a></li>';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&source=' . $source . '&dtselect=thismonth">This Month</a></li>';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&source=' . $source . '&dtselect=lastmonth">Last Month</a></li>';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&source=' . $source . '&dtselect=thisyear">This Year</a></li>';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&source=' . $source . '&dtselect=lastyear">Last Year</a></li>';
            $html .= '<li class="divider"></li>';
            $html .= '<li class="dropdown-header">Custom Date</li>';
            $html .= '<li>';
            $html .= '<form class="date_selection" method="get" style="padding:20px;">';
            $html .= '<div class="form-group">';
            $html .= '<input type="hidden" name="page" value="' . $page . '">';
            $html .= '<input type="hidden" name="action" value="' . $action . '">';
            $html .= '<input type="hidden" name="source" value="' . $source . '">';
            $html .= '<input type="hidden" name="dtselect" value="custom">';
            $html .= '<input type="text" class="form-control datepickerfrom" name="date_from" value="' . $date_from . '">';
            $html .= '</div>';
            $html .= '<div class="form-group">';
            $html .= '<input type="text" class="form-control datepickerto" name="date_to" value="' . $date_to . '">';
            $html .= '</div>';
            $html .= '<div>';
            $html .= '<button type="submit" class="btn btn-default btn-sm">Apply</button>';
            $html .= '</div>';
            $html .= '</form>';
            $html .= '</li>';
            $html .= '</ul>';
        } else if ($action == 'report-campaign') {
            $html = '<button id="today" type="button" class="btn btn-default"><i class="fa fa-calendar"></i> ' . $display_date . '</button>';

            $html .= '<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"> <span class="caret"></span></button>';
            $html .= '<ul class="dropdown-menu pull-right" style="min-width:200px;">';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&source=' . $source . '&medium=' . $medium . '&dtselect=last24hours">Last 24 Hours</a></li>';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&source=' . $source . '&medium=' . $medium . '&dtselect=today">Today</a></li>';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&source=' . $source . '&medium=' . $medium . '&dtselect=yesterday">Yesterday</a></li>';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&source=' . $source . '&medium=' . $medium . '&dtselect=last7days">Last 7 Days</a></li>';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&source=' . $source . '&medium=' . $medium . '&dtselect=last30days">Last 30 Days</a></li>';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&source=' . $source . '&medium=' . $medium . '&dtselect=thismonth">This Month</a></li>';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&source=' . $source . '&medium=' . $medium . '&dtselect=lastmonth">Last Month</a></li>';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&source=' . $source . '&medium=' . $medium . '&dtselect=thisyear">This Year</a></li>';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&source=' . $source . '&medium=' . $medium . '&dtselect=lastyear">Last Year</a></li>';
            $html .= '<li class="divider"></li>';
            $html .= '<li class="dropdown-header">Custom Date</li>';
            $html .= '<li>';
            $html .= '<form class="date_selection" method="get" style="padding:20px;">';
            $html .= '<div class="form-group">';
            $html .= '<input type="hidden" name="page" value="' . $page . '">';
            $html .= '<input type="hidden" name="action" value="' . $action . '">';
            $html .= '<input type="hidden" name="source" value="' . $source . '">';
            $html .= '<input type="hidden" name="medium" value="' . $medium . '">';
            $html .= '<input type="hidden" name="dtselect" value="custom">';
            $html .= '<input type="text" class="form-control datepickerfrom" name="date_from" value="' . $date_from . '">';
            $html .= '</div>';
            $html .= '<div class="form-group">';
            $html .= '<input type="text" class="form-control datepickerto" name="date_to" value="' . $date_to . '">';
            $html .= '</div>';
            $html .= '<div>';
            $html .= '<button type="submit" class="btn btn-default btn-sm">Apply</button>';
            $html .= '</div>';
            $html .= '</form>';
            $html .= '</li>';
            $html .= '</ul>';
        } else if ($action == 'report-content') {
            $html = '<button id="today" type="button" class="btn btn-default"><i class="fa fa-calendar"></i> ' . $display_date . '</button>';

            $html .= '<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"> <span class="caret"></span></button>';
            $html .= '<ul class="dropdown-menu pull-right" style="min-width:200px;">';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&source=' . $source . '&medium=' . $medium . '&campaign=' . $campaign . '&dtselect=last24hours">Last 24 Hours</a></li>';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&source=' . $source . '&medium=' . $medium . '&campaign=' . $campaign . '&dtselect=today">Today</a></li>';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&source=' . $source . '&medium=' . $medium . '&campaign=' . $campaign . '&dtselect=yesterday">Yesterday</a></li>';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&source=' . $source . '&medium=' . $medium . '&campaign=' . $campaign . '&dtselect=last7days">Last 7 Days</a></li>';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&source=' . $source . '&medium=' . $medium . '&campaign=' . $campaign . '&dtselect=last30days">Last 30 Days</a></li>';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&source=' . $source . '&medium=' . $medium . '&campaign=' . $campaign . '&dtselect=thismonth">This Month</a></li>';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&source=' . $source . '&medium=' . $medium . '&campaign=' . $campaign . '&dtselect=lastmonth">Last Month</a></li>';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&source=' . $source . '&medium=' . $medium . '&campaign=' . $campaign . '&dtselect=thisyear">This Year</a></li>';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&source=' . $source . '&medium=' . $medium . '&campaign=' . $campaign . '&dtselect=lastyear">Last Year</a></li>';
            $html .= '<li class="divider"></li>';
            $html .= '<li class="dropdown-header">Custom Date</li>';
            $html .= '<li>';
            $html .= '<form class="date_selection" method="get" style="padding:20px;">';
            $html .= '<div class="form-group">';
            $html .= '<input type="hidden" name="page" value="' . $page . '">';
            $html .= '<input type="hidden" name="action" value="' . $action . '">';
            $html .= '<input type="hidden" name="source" value="' . $source . '">';
            $html .= '<input type="hidden" name="medium" value="' . $medium . '">';
            $html .= '<input type="hidden" name="campaign" value="' . $campaign . '">';
            $html .= '<input type="hidden" name="dtselect" value="custom">';
            $html .= '<input type="text" class="form-control datepickerfrom" name="date_from" value="' . $date_from . '">';
            $html .= '</div>';
            $html .= '<div class="form-group">';
            $html .= '<input type="text" class="form-control datepickerto" name="date_to" value="' . $date_to . '">';
            $html .= '</div>';
            $html .= '<div>';
            $html .= '<button type="submit" class="btn btn-default btn-sm">Apply</button>';
            $html .= '</div>';
            $html .= '</form>';
            $html .= '</li>';
            $html .= '</ul>';
        } else if ($action == 'report-term') {
            $html = '<button id="today" type="button" class="btn btn-default"><i class="fa fa-calendar"></i> ' . $display_date . '</button>';

            $html .= '<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"> <span class="caret"></span></button>';
            $html .= '<ul class="dropdown-menu pull-right" style="min-width:200px;">';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&source=' . $source . '&medium=' . $medium . '&campaign=' . $campaign . '&content=' . $content . '&dtselect=last24hours">Last 24 Hours</a></li>';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&source=' . $source . '&medium=' . $medium . '&campaign=' . $campaign . '&content=' . $content . '&dtselect=today">Today</a></li>';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&source=' . $source . '&medium=' . $medium . '&campaign=' . $campaign . '&content=' . $content . '&dtselect=yesterday">Yesterday</a></li>';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&source=' . $source . '&medium=' . $medium . '&campaign=' . $campaign . '&content=' . $content . '&dtselect=last7days">Last 7 Days</a></li>';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&source=' . $source . '&medium=' . $medium . '&campaign=' . $campaign . '&content=' . $content . '&dtselect=last30days">Last 30 Days</a></li>';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&source=' . $source . '&medium=' . $medium . '&campaign=' . $campaign . '&content=' . $content . '&dtselect=thismonth">This Month</a></li>';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&source=' . $source . '&medium=' . $medium . '&campaign=' . $campaign . '&content=' . $content . '&dtselect=lastmonth">Last Month</a></li>';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&source=' . $source . '&medium=' . $medium . '&campaign=' . $campaign . '&content=' . $content . '&dtselect=thisyear">This Year</a></li>';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&source=' . $source . '&medium=' . $medium . '&campaign=' . $campaign . '&content=' . $content . '&dtselect=lastyear">Last Year</a></li>';
            $html .= '<li class="divider"></li>';
            $html .= '<li class="dropdown-header">Custom Date</li>';
            $html .= '<li>';
            $html .= '<form class="date_selection" method="get" style="padding:20px;">';
            $html .= '<div class="form-group">';
            $html .= '<input type="hidden" name="page" value="' . $page . '">';
            $html .= '<input type="hidden" name="action" value="' . $action . '">';
            $html .= '<input type="hidden" name="source" value="' . $source . '">';
            $html .= '<input type="hidden" name="medium" value="' . $medium . '">';
            $html .= '<input type="hidden" name="campaign" value="' . $campaign . '">';
            $html .= '<input type="hidden" name="content" value="' . $content . '">';
            $html .= '<input type="hidden" name="dtselect" value="custom">';
            $html .= '<input type="text" class="form-control datepickerfrom" name="date_from" value="' . $date_from . '">';
            $html .= '</div>';
            $html .= '<div class="form-group">';
            $html .= '<input type="text" class="form-control datepickerto" name="date_to" value="' . $date_to . '">';
            $html .= '</div>';
            $html .= '<div>';
            $html .= '<button type="submit" class="btn btn-default btn-sm">Apply</button>';
            $html .= '</div>';
            $html .= '</form>';
            $html .= '</li>';
            $html .= '</ul>';
        } else {

            $html = '<button id="today" type="button" class="btn btn-default"><i class="fa fa-calendar"></i> ' . $display_date . '</button>';
            $html .= '<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"> <span class="caret"></span></button>';
            $html .= '<ul class="dropdown-menu pull-right" style="min-width:200px;">';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&dtselect=last24hours">Last 24 Hours</a></li>';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&dtselect=today">Today</a></li>';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&dtselect=yesterday">Yesterday</a></li>';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&dtselect=last7days">Last 7 Days</a></li>';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&dtselect=last30days">Last 30 Days</a></li>';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&dtselect=thismonth">This Month</a></li>';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&dtselect=lastmonth">Last Month</a></li>';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&dtselect=thisyear">This Year</a></li>';
            $html .= '<li><a href="?page=' . $page . '&action=' . $action . '&dtselect=lastyear">Last Year</a></li>';
            $html .= '<li class="divider"></li>';
            $html .= '<li class="dropdown-header">Custom Date</li>';
            $html .= '<li>';
            $html .= '<form class="date_selection" method="get" style="padding:20px;">';
            $html .= '<div class="form-group">';
            $html .= '<input type="hidden" name="page" value="' . $page . '">';
            $html .= '<input type="hidden" name="action" value="' . $action . '">';
            $html .= '<input type="hidden" name="dtselect" value="custom">';
            $html .= '<input type="text" class="form-control datepickerfrom" name="date_from" value="' . $date_from . '">';
            $html .= '</div>';
            $html .= '<div class="form-group">';
            $html .= '<input type="text" class="form-control datepickerto" name="date_to" value="' . $date_to . '">';
            $html .= '</div>';
            $html .= '<div>';
            $html .= '<button type="submit" class="btn btn-default btn-sm">Apply</button>';
            $html .= '</div>';
            $html .= '</form>';
            $html .= '</li>';
            $html .= '</ul>';
        }

        update_option(SH_PREFIX . 'date_selection', json_encode(
                        array(
                            'dtselect' => $dtselect,
                            'date_from' => $date_from,
                            'date_to' => $date_to,
                        )
        ));

        return array(
            'widget' => $html,
            'display_title' => $display_title,
            'date_selection' => array(
                'dtselect' => $dtselect,
                'date_from' => $date_from,
                'date_to' => $date_to,
            )
        );
    }

    public function add_http($url) {
        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "http://" . $url;
        }
        return $url;
    }

    public function get_preg_meta_tags($str) {
        $pattern = '
	~<\s*meta\s

	# using lookahead to capture type to $1
		(?=[^>]*?
		\b(?:name|property|http-equiv)\s*=\s*
		(?|"\s*([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|
		([^"\'>]*?)(?=\s*/?\s*>|\s\w+\s*=))
	)

	# capture content to $2
	[^>]*?\bcontent\s*=\s*
		(?|"\s*([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|
		([^"\'>]*?)(?=\s*/?\s*>|\s\w+\s*=))
	[^>]*>

	~ix';

        if (preg_match_all($pattern, $str, $out)) {
            return array_combine($out[1], $out[2]);
        }
        return array();
    }

    public function curl($url) {

        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);

        $response = curl_exec($curl);
        if (0 !== curl_errno($curl) || 200 !== curl_getinfo($curl, CURLINFO_HTTP_CODE)) {
            $response = null;
        } // end if
        curl_close($curl);
        return $response;
    }

    public function form_error_message($field_name = '') {
        return isset($this->view_data['error'][$field_name]) ? '<p class="help-block">' . $this->view_data['error'][$field_name] . '</p>' : '';
    }

    public function form_error_class($field_name = '') {
        return isset($this->view_data['error'][$field_name]) ? 'has-error' : '';
    }

    public function set_value($field_name = '', $default = '') {
        return isset($_POST[$field_name]) ? $_POST[$field_name] : $default;
    }

    protected function _set_cache($name, $data, $time) {
        $expired_time = strtotime(current_time("Y-m-d H:i:s") . ' +' . $time . ' seconds');
        update_option(SH_PREFIX . 'cache_time_' . $name, $expired_time);
        update_option(SH_PREFIX . 'cache_' . $name, $data);
    }

    protected function _get_cache($name) {
        $expired_time = get_option(SH_PREFIX . 'cache_time_' . $name);
        if ($expired_time !== FALSE) {
            $current_time = strtotime(current_time("Y-m-d H:i:s"));
            if ($expired_time > $current_time) {
                return get_option(SH_PREFIX . 'cache_' . $name);
            }
        }
        $this->_delete_cache($name);
        return FALSE;
    }

    protected function _delete_cache($name) {
        delete_option(SH_PREFIX . 'cache_time_' . $name);
        delete_option(SH_PREFIX . 'cache_' . $name);
    }

    /**
     * Ellipsize String
     *
     * This function will strip tags from a string, split it at its max_length and ellipsize
     *
     * @param	string	string to ellipsize
     * @param	int	max length of string
     * @param	mixed	int (1|0) or float, .5, .2, etc for position to split
     * @param	string	ellipsis ; Default '...'
     * @return	string	ellipsized string
     */
    public function ellipsize($str, $max_length = 40, $position = 0.8, $ellipsis = '...') {
// Strip tags
        $str = trim(strip_tags($str));
// Is the string long enough to ellipsize?
        if (mb_strlen($str) <= $max_length) {
            return $str;
        }
        $beg = mb_substr($str, 0, floor($max_length * $position));
        $position = ($position > 1) ? 1 : $position;
        if ($position === 1) {
            $end = mb_substr($str, 0, -($max_length - mb_strlen($beg)));
        } else {
            $end = mb_substr($str, -($max_length - mb_strlen($beg)));
        }
        return $beg . $ellipsis . $end;
    }

}
