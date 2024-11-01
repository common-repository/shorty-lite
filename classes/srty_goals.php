<?php

class Srty_goals extends Srty_core {

    public $tbl_links;
    public $tbl_visits_log;
    public $tbl_conversions_log;

    public function __construct() {
        parent::__construct();
        $this->table_name = $this->wpdb->prefix . SH_PREFIX . 'goals';
        $this->tbl_links = $this->wpdb->prefix . SH_PREFIX . 'links';
        $this->tbl_visits_log = $this->wpdb->prefix . SH_PREFIX . 'visits_log';
        $this->tbl_conversions_log = $this->wpdb->prefix . SH_PREFIX . 'conversions_log';
    }

    public function init() {
        
    }

    public function conversion() {
        if (!is_admin()) {
            $request_uri = preg_replace('#/$#', '', urldecode($_SERVER['REQUEST_URI']));
            $url = get_site_url() . rtrim(trim(strtok($_SERVER["REQUEST_URI"], '?')), '/') . '/';
            $click_id = isset($_COOKIE['click_id']) ? $_COOKIE['click_id'] : 0;
            $crc32 = crc32($url . '-' . $click_id);

            if ((bool) get_option(SH_PREFIX . 'is_demo_mode') || in_array(get_option(SH_PREFIX . 'license_scheme_id'), array(SHORTY_STANDARD_LICENSE, SHORTY_PRO_LICENSE, SHORTY_AGENCY_LICENSE, AUDIENCEPRESS_LITE, AUDIENCEPRESS_UNLIMITED, AUDIENCEPRESS_DEVELOPER, AUDIENCEPRESS_WHITE_LABEL))) {
                $this->postback($crc32);
            }
            $this->goal_by_url($crc32);
            $this->goal_by_pixel($crc32);

            return 1;
        }
    }

    private function postback($crc32) {
        $request_uri = preg_replace('#/$#', '', urldecode($_SERVER['REQUEST_URI']));

        $site_url_subfolder = parse_url(site_url());
        $site_url_subfolder = ltrim($site_url_subfolder['path'], '/');
        $site_url_subfolder = trim($site_url_subfolder) != '' ? $site_url_subfolder . '/' : '';

        preg_match('#^/' . $site_url_subfolder . 'srty/postback#', $request_uri, $match);
        if (isset($match[0]) && ($match[0] == (trim($site_url_subfolder) != '' ? '/' . rtrim($site_url_subfolder, '/') : '') . '/srty/postback')) {
//        if (preg_match('#^/srty/postback#', $request_uri, $match) && $match[0] == '/srty/postback') {

            if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
                $_SERVER['HTTPS'] = 'on';
            }
            $http = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 'https://' : 'http://';

            header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
            header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
            header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
            header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
//            header("Content-type: image/gif");
            $gn = $this->_get('gn');
            $gt = $this->_get('gt');
            $gv = $this->_get('gv');
            $rid = $this->_get('rid');
            $click_id = $this->_get('ctid');

            /**
             * check visitor log
             */
            if ($click_id > 0) {
                $sql = "SELECT id,link_id FROM {$this->tbl_visits_log} WHERE id = %d";
                $visitor_log = $this->wpdb->get_row($this->wpdb->prepare($sql, array($click_id)), OBJECT);
            }

            if (isset($visitor_log->id)) {
                $status = STATUS_ACCEPTED;
                $message = 'Postback URL' . PHP_EOL;

                if (get_option(SH_PREFIX . 'settings_duplicate_handling') == SHORTLY_DUPLICATE_HANDLING_IGNORE) {
                    if (trim($rid) != '') {
                        $sql = "SELECT COUNT(*) AS total FROM {$this->tbl_conversions_log} WHERE goal_reference = %s";
                        $conversion_log = $this->wpdb->get_row($this->wpdb->prepare($sql, array($rid)), OBJECT);
                        if ($conversion_log->total > 0) {
                            $status = STATUS_REJECTED;
                            $message .= 'Duplicate Goal Reference ID ' . $rid . PHP_EOL;
                        }
                    }
                }

                /**
                 * check for duplicate 
                 */
                if (trim($rid) == '') {
                    $sql = "SELECT COUNT(*) AS total FROM {$this->tbl_conversions_log} WHERE goal_reference = '' AND visits_log_id=%d AND goal_name = %s AND goal_type = %s AND goal_value = %.2f ";
                    $conversion_log = $this->wpdb->get_row($this->wpdb->prepare($sql, array(isset($visitor_log->id) ? $visitor_log->id : 0, $gn, $gt, $gv)), OBJECT);
                    if ($conversion_log->total > 0) {
                        $status = STATUS_REJECTED;
                        $message .= 'Duplicate CTID' . $rid . PHP_EOL;
                    }
                }


                $country = $this->ip_info();
                $this->wpdb->insert(
                        $this->tbl_conversions_log, array(
                    'link_id' => isset($visitor_log->link_id) ? $visitor_log->link_id : 0,
                    'goal_id' => 0,
                    'visits_log_id' => isset($visitor_log->id) ? $visitor_log->id : 0,
                    'goal_name' => $gn,
                    'goal_value' => $gv,
                    'goal_type' => $gt,
                    'goal_reference' => $rid,
                    'referrer_url' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
                    'ip_address' => $_SERVER['REMOTE_ADDR'],
                    'ip_country_code' => isset($country['country_code']) ? $country['country_code'] : '',
                    'ip_country_name' => isset($country['country']) ? $country['country'] : '',
                    'ip_city_name' => isset($country['city']) ? $country['city'] : '',
                    'ip_latitude' => isset($country['latitude']) ? $country['latitude'] : '',
                    'ip_longitude' => isset($country['longitude']) ? $country['longitude'] : '',
                    'user_agent_string' => $_SERVER['HTTP_USER_AGENT'],
                    'status' => $status,
                    'message' => $message,
                    'conversion_date' => current_time("Y-m-d H:i:s"),
                    'created_date' => current_time("Y-m-d H:i:s"),
                        )
                );
                echo 'OK';
                exit;
            } else {
                echo 'Invalid CTID';
                exit;
            }
        }
    }

    private function goal_by_pixel($crc32) {
        $site_url_subfolder = parse_url(site_url());
        $site_url_subfolder = ltrim($site_url_subfolder['path'], '/');
        $site_url_subfolder = trim($site_url_subfolder) != '' ? $site_url_subfolder . '/' : '';

        $request_uri = preg_replace('#/$#', '', urldecode($_SERVER['REQUEST_URI']));
        preg_match('#^/' . $site_url_subfolder . 'srty/pixel#', $request_uri, $match);
        if (isset($match[0]) && ($match[0] == (trim($site_url_subfolder) != '' ? '/' . rtrim($site_url_subfolder, '/') : '') . '/srty/pixel')) {
            if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
                $_SERVER['HTTPS'] = 'on';
            }
            $http = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 'https://' : 'http://';

            header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
            header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
            header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
            header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
            header("Content-type: image/gif");
            $gn = $this->_get('gn');
            $gt = $this->_get('gt');
            $gv = $this->_get('gv');
            $rid = $this->_get('rid');

            /**
             * check visitor log
             */
            $click_id = isset($_COOKIE['click_id']) ? $_COOKIE['click_id'] : $click_id;
            if ($click_id > 0) {
                $sql = "SELECT id,link_id FROM {$this->tbl_visits_log} WHERE id = %d";
                $visitor_log = $this->wpdb->get_row($this->wpdb->prepare($sql, array($click_id)), OBJECT);
            }

            if (isset($visitor_log->id)) {
                $status = STATUS_ACCEPTED;
                $message = '';

                if (get_option(SH_PREFIX . 'settings_duplicate_handling') == SHORTLY_DUPLICATE_HANDLING_IGNORE) {
                    if (trim($rid) != '') {
                        $sql = "SELECT COUNT(*) AS total FROM {$this->tbl_conversions_log} WHERE goal_reference = %s";
                        $conversion_log = $this->wpdb->get_row($this->wpdb->prepare($sql, array($rid)), OBJECT);
                        if ($conversion_log->total > 0) {
                            $status = STATUS_REJECTED;
                            $message .= 'Duplicate Goal Reference ID ' . $rid . PHP_EOL;
                        }
                    }
                }

                /**
                 * check for duplicate 
                 */
                if (trim($rid) == '') {
                    $sql = "SELECT COUNT(*) AS total FROM {$this->tbl_conversions_log} WHERE goal_reference = '' AND visits_log_id=%d AND referrer_url=%s AND ip_address = %s ";
                    $conversion_log = $this->wpdb->get_row($this->wpdb->prepare($sql, array(isset($visitor_log->id) ? $visitor_log->id : 0, isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '', $_SERVER['REMOTE_ADDR'])), OBJECT);
                    if ($conversion_log->total > 0) {
                        $status = STATUS_REJECTED;
                        $message .= 'Duplicate CTID and IP address for this goal' . $rid . PHP_EOL;
                    }
                }


                $country = $this->ip_info();
                $this->wpdb->insert(
                        $this->tbl_conversions_log, array(
                    'link_id' => isset($visitor_log->link_id) ? $visitor_log->link_id : 0,
                    'goal_id' => 0,
                    'visits_log_id' => isset($visitor_log->id) ? $visitor_log->id : 0,
                    'goal_name' => $gn,
                    'goal_value' => $gv,
                    'goal_type' => $gt,
                    'goal_reference' => $rid,
                    'referrer_url' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
                    'ip_address' => $_SERVER['REMOTE_ADDR'],
                    'ip_country_code' => isset($country['country_code']) ? $country['country_code'] : '',
                    'ip_country_name' => isset($country['country']) ? $country['country'] : '',
                    'ip_city_name' => isset($country['city']) ? $country['city'] : '',
                    'ip_latitude' => isset($country['latitude']) ? $country['latitude'] : '',
                    'ip_longitude' => isset($country['longitude']) ? $country['longitude'] : '',
                    'user_agent_string' => $_SERVER['HTTP_USER_AGENT'],
                    'status' => $status,
                    'message' => $message,
                    'conversion_date' => current_time("Y-m-d H:i:s"),
                    'created_date' => current_time("Y-m-d H:i:s"),
                        )
                );

                $fp = fopen("php://output", "wb");
                fwrite($fp, "GIF89a\x01\x00\x01\x00\x80\x00\x00\xFF\xFF", 15);
                fwrite($fp, "\xFF\x00\x00\x00\x21\xF9\x04\x01\x00\x00\x00\x00", 12);
                fwrite($fp, "\x2C\x00\x00\x00\x00\x01\x00\x01\x00\x00\x02\x02", 12);
                fwrite($fp, "\x44\x01\x00\x3B", 4);
                fclose($fp);
                exit;
            }
        }
    }

    private function goal_by_url($crc32) {
        $request_uri = preg_replace('#/$#', '', urldecode($_SERVER['REQUEST_URI']));

        $url = get_site_url() . rtrim(trim(strtok($_SERVER["REQUEST_URI"], '?')), '/') . '/';
        $sql = "SELECT * FROM {$this->table_name} WHERE goal_tracking_url = %s ";
        $goal = $this->wpdb->get_row($this->wpdb->prepare($sql, array($url)), OBJECT);
        if (isset($goal->id)) {

            /**
             * check visitor log
             */
            $click_id = isset($_COOKIE['click_id']) ? $_COOKIE['click_id'] : 0;
            if ($click_id > 0) {
                $sql = "SELECT id,link_id FROM {$this->tbl_visits_log} WHERE id = %d";
                $visitor_log = $this->wpdb->get_row($this->wpdb->prepare($sql, array($click_id)), OBJECT);
            }
            if (isset($visitor_log->id)) {
                $status = STATUS_ACCEPTED;
                $message = '';

                if (get_option(SH_PREFIX . 'settings_duplicate_handling') == SHORTLY_DUPLICATE_HANDLING_IGNORE) {
                    if (trim($goal->reference_id) != '') {
                        $sql = "SELECT COUNT(*) AS total FROM {$this->tbl_conversions_log} WHERE goal_reference = %s";
                        $conversion_log = $this->wpdb->get_row($this->wpdb->prepare($sql, array($goal->reference_id)), OBJECT);
                        if ($conversion_log->total > 0) {
                            $status = STATUS_REJECTED;
                            $message .= 'Duplicate Goal Reference ID ' . $goal->reference_id . PHP_EOL;
                        }
                    }
                }

                /**
                 * check for duplicate 
                 */
                if (trim($goal->reference_id) == '') {
                    $sql = "SELECT COUNT(*) AS total FROM {$this->tbl_conversions_log} WHERE goal_reference = '' AND visits_log_id=%d AND referrer_url=%s AND ip_address = %s ";
                    $conversion_log = $this->wpdb->get_row($this->wpdb->prepare($sql, array(isset($visitor_log->id) ? $visitor_log->id : 0, $url, $_SERVER['REMOTE_ADDR'])), OBJECT);
                    if ($conversion_log->total > 0) {
                        $status = STATUS_REJECTED;
                        $message .= 'Duplicate CTID and IP address for this goal' . PHP_EOL;
                    }
                }

                $country = $this->ip_info();
                $this->wpdb->insert(
                        $this->tbl_conversions_log, array(
                    'link_id' => isset($visitor_log->link_id) ? $visitor_log->link_id : 0,
                    'goal_id' => $goal->id,
                    'visits_log_id' => isset($visitor_log->id) ? $visitor_log->id : 0,
                    'goal_name' => $goal->goal_name,
                    'goal_value' => $goal->goal_value,
                    'goal_type' => $goal->goal_type,
                    'goal_reference' => $goal->reference_id,
                    'referrer_url' => $url, //isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
                    'ip_address' => $_SERVER['REMOTE_ADDR'],
                    'ip_country_code' => isset($country['country_code']) ? $country['country_code'] : '',
                    'ip_country_name' => isset($country['country']) ? $country['country'] : '',
                    'ip_city_name' => isset($country['city']) ? $country['city'] : '',
                    'ip_latitude' => isset($country['latitude']) ? $country['latitude'] : '',
                    'ip_longitude' => isset($country['longitude']) ? $country['longitude'] : '',
                    'user_agent_string' => $_SERVER['HTTP_USER_AGENT'],
                    'status' => $status,
                    'message' => $message,
                    'conversion_date' => current_time("Y-m-d H:i:s"),
                    'created_date' => current_time("Y-m-d H:i:s"),
                        )
                );
            }
        }
    }

}
