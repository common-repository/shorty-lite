<?php

/**
 * general function & static page
 */
class Srty_general extends Srty_core {

    /**
     * overview page
     */
    public function overview() {
        $this->view('v_overview');
        exit();
    }

    /**
     * help page
     */
    public function helps() {
        global $wpdb;
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        if ($this->_get('action') == 'download') {
            /**
             * clean up output buffer. so we can save only related csv file
             */
            ob_end_clean();
            $zipname = 'shorty_' . current_time("YmdHis") . '.zip';





            $zip = new ZipArchive();
            if ($zip->open($zipname, ZIPARCHIVE::CREATE) !== TRUE) {
                die('unable to create zip');
            }


            $output_handle = @fopen('php://temp', 'w');
            if (false === $output_handle) {
                die('Failed to create temporary file');
            }
            $tbllinks = $wpdb->prefix . SH_PREFIX . 'links';
            $links = $wpdb->get_results("SELECT * FROM {$tbllinks}");
            $output_handle = $this->generate_csv($links, $output_handle);
            $zip->addFromString('links.csv', stream_get_contents($output_handle));
            fclose($output_handle);
            $zip->close();

            header('Content-Type: application/zip');
            header('Content-disposition: attachment; filename=' . $zipname);
            header('Content-Length: ' . filesize($zipname));
            readfile($zipname);

// remove the zip archive
// you could also use the temp file method above for this.
            unlink($zipname);

            die();
        } elseif ($this->_get('action') == 'clear_reports') {


            $charset_collate = $wpdb->get_charset_collate();
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

            $table_name = $wpdb->prefix . SH_PREFIX . 'visits_log';
            $sql = "DELETE FROM $table_name WHERE 1;";
            $wpdb->query($sql);

            $table_name = $wpdb->prefix . SH_PREFIX . 'conversions_log';
            $sql = "DELETE FROM $table_name WHERE 1;";
            $wpdb->query($sql);

            $this->view_data['msg'] = array(
                'status' => 'alert-success',
                'text' => SRTY_MSG_REPORT_CLEARED
            );
            $this->set_top_message($this->view_data['msg']);
        } elseif ($this->_get('action') == 'nuke') {

            $charset_collate = $wpdb->get_charset_collate();
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

            $table_name = $wpdb->prefix . SH_PREFIX . 'links';
            $sql = "DELETE FROM $table_name WHERE 1;";
            $wpdb->query($sql);

            $table_name = $wpdb->prefix . SH_PREFIX . 'campaigns';
            $sql = "DELETE FROM $table_name WHERE 1;";
            $wpdb->query($sql);

            $table_name = $wpdb->prefix . SH_PREFIX . 'goals';
            $sql = "DELETE FROM $table_name WHERE 1;";
            $wpdb->query($sql);

            $table_name = $wpdb->prefix . SH_PREFIX . 'visits_log';
            $sql = "DELETE FROM $table_name WHERE 1;";
            $wpdb->query($sql);

            $table_name = $wpdb->prefix . SH_PREFIX . 'conversions_log';
            $sql = "DELETE FROM $table_name WHERE 1;";
            $wpdb->query($sql);

            $table_name = $wpdb->prefix . SH_PREFIX . 'import_temp';
            $sql = "DELETE FROM $table_name WHERE 1;";
            $wpdb->query($sql);
            
            $table_name = $wpdb->prefix . SH_PREFIX . 'split_tests';
            $sql = "DELETE FROM $table_name WHERE 1;";
            $wpdb->query($sql);
            
            $table_name = $wpdb->prefix . SH_PREFIX . 'split_test_allocations';
            $sql = "DELETE FROM $table_name WHERE 1;";
            $wpdb->query($sql);

            /**
             * set default option
             */
            $date_from = date('Y-m-d 00:00:00', strtotime(current_time("Y-m-d") . " -29 day"));
            $date_to = date('Y-m-d 23:59:59', strtotime(current_time("Y-m-d") . " -0 day"));
            update_option(SH_PREFIX . 'date_selection', json_encode(
                            array(
                                'dtselect' => 'last30days',
                                'date_from' => $date_from,
                                'date_to' => $date_to,
                            )
            ));

            update_option(SH_PREFIX . 'license_key', '');
            update_option(SH_PREFIX . 'license_activation', '');

            update_option(SH_PREFIX . 'top_message', '');

            update_option(SH_PREFIX . 'settings_akl_status', 1);
            update_option(SH_PREFIX . 'settings_akl_on_homepage', 1);
            update_option(SH_PREFIX . 'settings_akl_on_singlepost', 1);
            update_option(SH_PREFIX . 'settings_akl_on_singlepage', 1);
            update_option(SH_PREFIX . 'settings_akl_on_comments', 1);
            update_option(SH_PREFIX . 'settings_akl_on_archives', 1);
            update_option(SH_PREFIX . 'settings_akl_max_per_page', 2);
            update_option(SH_PREFIX . 'settings_akl_max_per_keyword', 1);
            update_option(SH_PREFIX . 'settings_akl_new_window', 1);
            update_option(SH_PREFIX . 'settings_akl_no_follow', 1);

            /**
             * viral bar
             */
            update_option(SH_PREFIX . 'settings_bar_theme', SHORTLY_BAR_THEME_GREY);
            update_option(SH_PREFIX . 'settings_socialButtons_facebook', 1);
            update_option(SH_PREFIX . 'settings_socialButtons_twitter', 1);
            update_option(SH_PREFIX . 'settings_earnMoney_enable', 1);
            update_option(SH_PREFIX . 'settings_earnMoney_affiliateLink', SHORTLY_AFFILIATE_URL);
            update_option(SH_PREFIX . 'settings_tracking_domain', '');
            update_option(SH_PREFIX . 'settings_duplicate_handling', SHORTLY_DUPLICATE_HANDLING_IGNORE);
            update_option(SH_PREFIX . 'settings_currency', 'USD');

            $this->_delete_cache(SH_PREFIX . 'license_cache');

            $this->view_data['msg'] = array(
                'status' => 'alert-success',
                'text' => SRTY_MSG_NUKED
            );
            $this->set_top_message($this->view_data['msg']);
        }
        
        preg_match("/[^\/]+$/", SH_PATH, $matches);
        $plugin_info = get_plugins('/'.$matches[0]);
        $plugin_info = isset($plugin_info['shortywp.php']) ? $plugin_info['shortywp.php'] : array();
        $info = '';
        $info .= 'Web Server : ' . (isset($_SERVER["SERVER_SOFTWARE"]) ? $_SERVER["SERVER_SOFTWARE"] : 'Unknown') . PHP_EOL;
        $info .= 'Server Signature : ' . (isset($_SERVER["SERVER_SIGNATURE"]) ? $_SERVER["SERVER_SIGNATURE"] : 'Unknown') . PHP_EOL;
        $info .= 'PHP Version : ' . phpversion() . PHP_EOL;
        $info .= 'Operating System : ' . php_uname('s r v m') . PHP_EOL;
        $info .= 'CURL : ' . (function_exists('curl_version') ? 'Enabled' : 'Disabled') . PHP_EOL;
        $info .= 'WP Version : ' . get_bloginfo('version') . PHP_EOL;
        $info .= 'Shorty Version : ' . (isset($plugin_info['Version']) ? $plugin_info['Version'] : '') . PHP_EOL;
        $info .= 'DB Prefix : ' . (isset($this->wpdb->prefix) ? $this->wpdb->prefix : '') . PHP_EOL;
        $info .= 'Shorty Prefix : ' . SH_PREFIX . PHP_EOL;
        $info .= 'Tables : ' . PHP_EOL;
        $table_name = $this->wpdb->prefix . SH_PREFIX . 'links';
        $info .= ' - Links : ' . ($wpdb->get_var("SHOW TABLES LIKE '$table_name'")) . PHP_EOL;
        $table_name = $this->wpdb->prefix . SH_PREFIX . 'campaigns';
        $info .= ' - Campaigns : ' . ($wpdb->get_var("SHOW TABLES LIKE '$table_name'")) . PHP_EOL;
        $table_name = $this->wpdb->prefix . SH_PREFIX . 'goals';
        $info .= ' - Goals : ' . ($wpdb->get_var("SHOW TABLES LIKE '$table_name'")) . PHP_EOL;
        $table_name = $this->wpdb->prefix . SH_PREFIX . 'visits_log';
        $info .= ' - Visits_log : ' . ($wpdb->get_var("SHOW TABLES LIKE '$table_name'")) . PHP_EOL;
        $table_name = $this->wpdb->prefix . SH_PREFIX . 'conversions_log';
        $info .= ' - Conversions_log : ' . ($wpdb->get_var("SHOW TABLES LIKE '$table_name'")) . PHP_EOL;
        $table_name = $this->wpdb->prefix . SH_PREFIX . 'import_temp';
        $info .= ' - Import_temp : ' . ($wpdb->get_var("SHOW TABLES LIKE '$table_name'")) . PHP_EOL;


        $info .= 'API Url : ' . SHORTLY_API_URL . PHP_EOL;
        $this->view_data['info'] = $info;

        $this->view('v_help', $this->view_data);
    }

    public function generate_csv($query_result, $output_handle) {


        $first = true;
        foreach ($query_result as $row) {
            if ($first) {
                $titles = array();
                foreach ($row as $key => $val) {
                    $titles[] = $key;
                }
                fputcsv($output_handle, $titles);
                $first = false;
            }

            $leadArray = (array) $row;
            fputcsv($output_handle, $leadArray);
        }
        rewind($output_handle);

        return $output_handle;
    }

}
