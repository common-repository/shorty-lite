<?php

/*
  Plugin Name: Shorty Lite
  Plugin URI: http://www.shortywp.com/?utm_source=wordpress_plugin&utm_medium=referral&utm_campaign=plugin_meta
  Description: The best affiliate link cloaking and click tracking plugin ever made for WordPress. Works with all major affiliate networks and PPC ad platforms.
  Author: Shorty
  Author URI: http://www.shortywp.com/?utm_source=shorty&utm_medium=referral&utm_term=plugin_meta&utm_campaign=wordpress_plugin
  Version: 1.4.1
  Text Domain: shorty-lite
  License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

global $sh_db_version;
$sh_db_version = '1.3.6';

register_activation_hook(__FILE__, 'srty_install');
register_deactivation_hook(__FILE__, 'srty_deactivation');

require_once('sh-config.php');
require_once('srty_lang.php');
require_once('autoload.php');

function srty_install() {
    global $sh_db_version;

    _create_db();

    /**
     * set default option
     */
    $date_from = date('Y-m-d 00:00:00', strtotime(current_time("Y-m-d") . " -29 day"));
    $date_to = date('Y-m-d 23:59:59', strtotime(current_time("Y-m-d") . " -0 day"));
    add_option(SH_PREFIX . 'date_selection', json_encode(
                    array(
                        'dtselect' => 'last30days',
                        'date_from' => $date_from,
                        'date_to' => $date_to,
                    )
    ));

    add_option(SH_PREFIX . 'license_key', '');
    add_option(SH_PREFIX . 'license_activation', '');
    add_option(SH_PREFIX . 'license_scheme_id', '');
    add_option(SH_PREFIX . 'license_scheme_name', 'Shorty Standard License');

    add_option(SH_PREFIX . 'top_message', '');

    add_option(SH_PREFIX . 'settings_akl_status', 1);
    add_option(SH_PREFIX . 'settings_akl_on_homepage', 1);
    add_option(SH_PREFIX . 'settings_akl_on_singlepost', 1);
    add_option(SH_PREFIX . 'settings_akl_on_singlepage', 1);
    add_option(SH_PREFIX . 'settings_akl_on_comments', 1);
    add_option(SH_PREFIX . 'settings_akl_on_archives', 1);
    add_option(SH_PREFIX . 'settings_akl_max_per_page', 2);
    add_option(SH_PREFIX . 'settings_akl_max_per_keyword', 1);
    add_option(SH_PREFIX . 'settings_akl_new_window', 1);
    add_option(SH_PREFIX . 'settings_akl_no_follow', 1);

    /**
     * viral bar
     */
    add_option(SH_PREFIX . 'settings_bar_theme', SHORTLY_BAR_THEME_GREY);
    add_option(SH_PREFIX . 'settings_socialButtons_facebook', 1);
    add_option(SH_PREFIX . 'settings_socialButtons_twitter', 1);
    add_option(SH_PREFIX . 'settings_earnMoney_enable', 1);
    add_option(SH_PREFIX . 'settings_earnMoney_affiliateLink', SHORTLY_AFFILIATE_URL);
    add_option(SH_PREFIX . 'settings_tracking_domain', '');
    add_option(SH_PREFIX . 'settings_duplicate_handling', SHORTLY_DUPLICATE_HANDLING_IGNORE);
    add_option(SH_PREFIX . 'settings_currency', 'USD');

    add_option(SH_PREFIX . 'settings_enable_roles', 0);
    add_option(SH_PREFIX . 'settings_enable_custom_domain', 0);
    add_option(SH_PREFIX . 'settings_custom_domain', '');

    add_option(SH_PREFIX . 'tools_pixel_name', '');
    add_option(SH_PREFIX . 'tools_pixel_type', 'LEAD');
    add_option(SH_PREFIX . 'tools_pixel_value', '');
    add_option(SH_PREFIX . 'tools_pixel_ssl', 0);

    add_option(SH_PREFIX . 'tools_network', '');
    add_option(SH_PREFIX . 'tools_goal_type', 'LEAD');
    add_option(SH_PREFIX . 'tools_ssl', 0);

    add_option(SH_PREFIX . 'downtime_alert_threshold', 10);
    add_option(SH_PREFIX . 'session_timeout', 30);
    add_option(SH_PREFIX . 'cookie_window', 30);

    /**
     * put this to the last
     */
    add_option(SH_PREFIX . 'db_version', $sh_db_version);
}

function srty_deactivation() {
    wp_clear_scheduled_hook('srty_cron_event');
}

/**
 * CREATE AND UPDATE DB STRUCTURE
 * @global type $wpdb
 * REF: http://codex.wordpress.org/Creating_Tables_with_Plugins
 */
function _create_db() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    add_option(SH_PREFIX . 'downtime_alert_threshold', 10);
    add_option(SH_PREFIX . 'session_timeout', 30);
    add_option(SH_PREFIX . 'cookie_window', 30);
    add_option(SH_PREFIX . 'settings_enable_custom_domain', 0);
    add_option(SH_PREFIX . 'settings_custom_domain', '');

    /**
     * create table links
     */
    $table_name = $wpdb->prefix . SH_PREFIX . 'links';

    $sql = "CREATE TABLE $table_name (
		id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                link_name varchar(200)NOT NULL,
                destination_url varchar(2083) NOT NULL,
                backup_url varchar(2083) NOT NULL,
                mobile_url varchar(2083) NOT NULL,
                tracking_link varchar(100) NOT NULL,
                link_redirect_type varchar(100) NOT NULL,
                cloaking_status_enable tinyint(4) NOT NULL,
                cloaking_type varchar(100) NOT NULL,
                bar_position varchar(100) NOT NULL,
                frame_content varchar(100) NOT NULL,
                meta_title varchar(200) NOT NULL,
                meta_description text NOT NULL,
                meta_image text NOT NULL,
                retargeting_code text NOT NULL,
                param_tag_forward_param tinyint(4) NOT NULL DEFAULT 0,
                param_tag_forward_campaign tinyint(4) NOT NULL DEFAULT 0,
                param_tag_affiliate_tracking tinyint(4) NOT NULL DEFAULT 0,
                param_tag_affiliate_network varchar(100) NOT NULL,
                param_tag_affiliate_network_custom varchar(12) NOT NULL,
                auto_keyword_linking_enable tinyint(4) NOT NULL DEFAULT 0,
                meta_keyword varchar(200) NOT NULL,
                geo_redirect_enable tinyint(4) NOT NULL DEFAULT 0,
                geo_redirect_option varchar(100) NOT NULL DEFAULT 'ALL_EXCEPT',
                geo_redirect_countries text NOT NULL,
                geo_redirect_destination_url varchar(2083) NOT NULL,
                link_expired_enable tinyint(4) NOT NULL DEFAULT 0,
                link_expired_date DATETIME NULL,
                link_expired_url varchar(2083) NOT NULL,
                reference_tags varchar(2083) NOT NULL,
                uptime_monitoring_enabled tinyint(4) NOT NULL,
                uptime_is_online tinyint(4) NOT NULL DEFAULT 1,
                uptime_last_check DATETIME NULL,
                click_limiter_enable tinyint(4) NOT NULL,
                click_limiter_max_clicks INT SIGNED  NOT NULL,
                click_lifetime_clicks INT SIGNED  NOT NULL DEFAULT 0,
                click_limiter_url varchar(2083) NOT NULL,
                blank_referrer tinyint(4) NOT NULL DEFAULT 0,
                retargeting_adwords text NOT NULL,
                retargeting_fb text NOT NULL,
                retargeting_adroll text NOT NULL,
                retargeting_perfect text NOT NULL,
                retargeter_code text NOT NULL,
                param_tag_retargeting tinyint(4) NOT NULL DEFAULT 0,
		PRIMARY KEY  (id)
            ) $charset_collate;";
    dbDelta($sql);

    /**
     * create table campaigns
     */
    $table_name = $wpdb->prefix . SH_PREFIX . 'campaigns';
    $sql = "CREATE TABLE $table_name (
		id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                tracking_campaign varchar(100) NOT NULL,
                link_id INT UNSIGNED NULL,
                source varchar(2000) NOT NULL,
                medium varchar(2000) NULL,
                campaign varchar(2000) NULL,
                content varchar(2000) NULL,
                term varchar(2000) NULL,
                calculate_cost tinyint(4) NOT NULL DEFAULT 0,
                cpc double(10,2) NOT NULL,
		PRIMARY KEY  (id)
            ) $charset_collate;";
    dbDelta($sql);

    /**
     * create table goals
     */
    $table_name = $wpdb->prefix . SH_PREFIX . 'goals';
    $sql = "CREATE TABLE $table_name (
		id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                goal_name varchar(200) NOT NULL,
                goal_type varchar(10) NULL,
                goal_value double(10,2) NOT NULL,
                reference_id varchar(100),
                use_ssl_tracking tinyint(4) NOT NULL DEFAULT 0,
                goal_tracking_url varchar(2083) NOT NULL,
                page_id INT UNSIGNED NULL,
		PRIMARY KEY  (id)
            ) $charset_collate;";
    dbDelta($sql);

    /**
     * create table visits_log
     */
    $table_name = $wpdb->prefix . SH_PREFIX . 'visits_log';
    $sql = "CREATE TABLE $table_name (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                link_id INT UNSIGNED NULL,
                split_test_id INT UNSIGNED NULL DEFAULT 0,
                visitor_id INT UNSIGNED NULL,
                visitor_session INT UNSIGNED NULL,
                tracking_link VARCHAR(100) NULL,
                destination_url VARCHAR(2083) NULL,
                referrer_url VARCHAR(2083) NULL,
                ip_address VARCHAR(30) NULL,
                ip_country_code CHAR(2) NULL,
                ip_country_name VARCHAR(50) NULL,
                ip_city_name VARCHAR(200) NULL,
                ip_latitude Decimal(9,6) NULL DEFAULT 0,
                ip_longitude Decimal(9,6) NULL DEFAULT 0,
                user_agent_string TEXT NULL,
                campaign_id INT UNSIGNED NULL,
                tracking_campaign varchar(100) NULL,
                source varchar(2000) NULL,
                medium varchar(2000) NULL,
                campaign varchar(2000) NULL,
                content varchar(2000) NULL,
                term varchar(2000) NULL,
                cpc double(10,2) NOT NULL,
                created_date DATETIME NULL,
                PRIMARY KEY  (id),
                KEY idx_visits_log (link_id,visitor_id,visitor_session) 
            ) $charset_collate;";
    dbDelta($sql);

    /**
     * create table conversions_log
     */
    $table_name = $wpdb->prefix . SH_PREFIX . 'conversions_log';
    $sql = "CREATE TABLE $table_name (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                link_id INT UNSIGNED NULL,
                visits_log_id INT UNSIGNED NULL,
                goal_id INT UNSIGNED NULL,
                conversion_date DATETIME NULL,
                goal_name VARCHAR(200) NULL,
                goal_type varchar(10) NULL,
                goal_value DECIMAL(10,2) NULL DEFAULT 0.00,
                goal_reference VARCHAR(100) NULL,
                referrer_url VARCHAR(2083) NULL,
                ip_address VARCHAR(30) NULL,
                ip_country_code CHAR(2) NULL,
                ip_country_name VARCHAR(50) NULL,
                ip_city_name VARCHAR(200) NULL,
                ip_latitude Decimal(9,6) NULL DEFAULT 0,
                ip_longitude Decimal(9,6) NULL DEFAULT 0,
                user_agent_string TEXT NULL,
                status VARCHAR(10) NULL,
                message TEXT NULL,
                created_date DATETIME NULL,
                PRIMARY KEY  (id),
                KEY idx_vlid (visits_log_id)
            ) $charset_collate;";
    dbDelta($sql);

    /**
     * create table temp_import
     */
    $table_name = $wpdb->prefix . SH_PREFIX . 'import_temp';
    $sql = "CREATE TABLE $table_name (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                link_id INT UNSIGNED NULL,
                visits_log_id INT UNSIGNED NULL,
                goal_id INT UNSIGNED NULL,
                conversion_date DATETIME NULL,
                goal_name VARCHAR(200) NULL,
                goal_type varchar(10) NULL,
                goal_value DECIMAL(10,2) NULL DEFAULT 0.00,
                goal_reference VARCHAR(100) NULL,
                referrer_url VARCHAR(2083) NULL,
                ip_address VARCHAR(30) NULL,
                ip_country_code CHAR(2) NULL,
                ip_country_name VARCHAR(50) NULL,
                ip_city_name VARCHAR(200) NULL,
                ip_latitude Decimal(9,6) NULL DEFAULT 0,
                ip_longitude Decimal(9,6) NULL DEFAULT 0,
                user_agent_string TEXT NULL,
                status VARCHAR(10) NULL,
                force_import tinyint(4) NOT NULL DEFAULT 0,
                message TEXT NULL,
                created_date DATETIME NULL,
                PRIMARY KEY  (id)
            ) $charset_collate;";
    dbDelta($sql);

    /**
     * create table split_tests
     */
    $table_name = $wpdb->prefix . SH_PREFIX . 'split_tests';

    $sql = "CREATE TABLE $table_name (
		id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                split_test_name varchar(200)NOT NULL,
                tracking_link varchar(100) NOT NULL,
                score LONGTEXT NOT NULL,
		PRIMARY KEY  (id)
            ) $charset_collate;";
    dbDelta($sql);

    /**
     * create table split_test_allocations
     */
    $table_name = $wpdb->prefix . SH_PREFIX . 'split_test_allocations';

    $sql = "CREATE TABLE $table_name (
		id INT UNSIGNED NOT NULL AUTO_INCREMENT,
		split_test_id INT UNSIGNED NULL,
		link_id INT UNSIGNED NULL,
                tracker_url varchar(2083) NOT NULL,
		traffic INT UNSIGNED NULL DEFAULT 0,
		PRIMARY KEY  (id)
            ) $charset_collate;";
    dbDelta($sql);
}

/**
 * Uninstall DB
 * @global type $wpdb
 * @global string $sh_db_version
 */
function srty_uninstall() {
    global $wpdb;
    global $sh_db_version;

    $charset_collate = $wpdb->get_charset_collate();
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );


    /**
     * DROP TABLES
     */
    $table_name = $wpdb->prefix . SH_PREFIX . 'links';
    $sql = "DROP TABLE $table_name;";
    $wpdb->query($sql);

    $table_name = $wpdb->prefix . SH_PREFIX . 'campaigns';
    $sql = "DROP TABLE $table_name;";
    $wpdb->query($sql);

    $table_name = $wpdb->prefix . SH_PREFIX . 'goals';
    $sql = "DROP TABLE $table_name;";
    $wpdb->query($sql);

    $table_name = $wpdb->prefix . SH_PREFIX . 'visits_log';
    $sql = "DROP TABLE $table_name;";
    $wpdb->query($sql);

    $table_name = $wpdb->prefix . SH_PREFIX . 'conversions_log';
    $sql = "DROP TABLE $table_name;";
    $wpdb->query($sql);

    $table_name = $wpdb->prefix . SH_PREFIX . 'daily_tracking_link';
    $sql = "DROP TABLE $table_name;";
    $wpdb->query($sql);

    $table_name = $wpdb->prefix . SH_PREFIX . 'import_temp';
    $sql = "DROP TABLE $table_name;";
    $wpdb->query($sql);

    $table_name = $wpdb->prefix . SH_PREFIX . 'split_tests';
    $sql = "DROP TABLE $table_name;";
    $wpdb->query($sql);

    $table_name = $wpdb->prefix . SH_PREFIX . 'split_test_allocations';
    $sql = "DROP TABLE $table_name;";
    $wpdb->query($sql);

    delete_option(SH_PREFIX . 'cache_time_license_cache');
    delete_option(SH_PREFIX . 'cache_license_cache');

    delete_option(SH_PREFIX . 'date_selection');
    delete_option(SH_PREFIX . 'license_key');
    delete_option(SH_PREFIX . 'license_activation');
    delete_option(SH_PREFIX . 'license_scheme_id');
    delete_option(SH_PREFIX . 'top_message');
    delete_option(SH_PREFIX . 'settings_akl_status');
    delete_option(SH_PREFIX . 'settings_akl_on_homepage');
    delete_option(SH_PREFIX . 'settings_akl_on_singlepost');
    delete_option(SH_PREFIX . 'settings_akl_on_singlepage');
    delete_option(SH_PREFIX . 'settings_akl_on_comments');
    delete_option(SH_PREFIX . 'settings_akl_on_archives');
    delete_option(SH_PREFIX . 'settings_akl_max_per_page');
    delete_option(SH_PREFIX . 'settings_akl_max_per_keyword');
    delete_option(SH_PREFIX . 'settings_akl_new_window');
    delete_option(SH_PREFIX . 'settings_akl_no_follow');
    delete_option(SH_PREFIX . 'settings_bar_theme');
    delete_option(SH_PREFIX . 'settings_socialButtons_facebook');
    delete_option(SH_PREFIX . 'settings_socialButtons_twitter');
    delete_option(SH_PREFIX . 'settings_earnMoney_enable');
    delete_option(SH_PREFIX . 'settings_earnMoney_affiliateLink');
    delete_option(SH_PREFIX . 'settings_tracking_domain');
    delete_option(SH_PREFIX . 'settings_duplicate_handling');
    delete_option(SH_PREFIX . 'settings_currency');
    delete_option(SH_PREFIX . 'db_version');
}

/**
 * Check DB version
 * @global string $sh_db_version
 */
function srty_update_db_check() {
    global $sh_db_version;
    $db_version = get_option(SH_PREFIX . 'db_version', $sh_db_version);
    if ($db_version != $sh_db_version) {
        _create_db();
        update_option(SH_PREFIX . 'db_version', $sh_db_version);
    }
}

function srty_cron() {
    global $wpdb;
    $table = $wpdb->prefix . SH_PREFIX . 'links';
    $links = $wpdb->get_results("SELECT * FROM {$table} WHERE uptime_monitoring_enabled=1");
    //uptime_is_online
    $downlists = array();
    $send_new_email = 0;
    foreach ($links as $row) {
        if ($headers = wp_remote_head($row->destination_url)) {
            $prev_uptime_is_online = $row->uptime_is_online;
            if (!isset($headers->errors)) {
                if (isset($headers['response']['code']) && in_array($headers['response']['code'], array(200, 301, 302))) {
                    $wpdb->update($table, array('uptime_is_online' => 1, 'uptime_last_check' => current_time("Y-m-d H:i:s")), array('id' => $row->id));
                } else {
                    $wpdb->update($table, array('uptime_is_online' => 0, 'uptime_last_check' => current_time("Y-m-d H:i:s")), array('id' => $row->id));
                    /**
                     * sent email only if previus check is live
                     */
                    if ($prev_uptime_is_online == 1) {
                        $send_new_email++;
                    }
                    $downlists[] = array(
                        'link_name' => $row->link_name,
                        'destination_url' => $row->destination_url,
                        'backup_url' => $row->backup_url,
                        'backup_url_enabled' => (trim($row->backup_url) != '') ? 'Yes' : 'No',
                    );
                }
            } else {
                $wpdb->update($table, array('uptime_is_online' => 0, 'uptime_last_check' => current_time("Y-m-d H:i:s")), array('id' => $row->id));
                /**
                 * sent email only if previus check is live
                 */
                if ($prev_uptime_is_online == 1) {
                    $send_new_email++;
                }

                $downlists[] = array(
                    'link_name' => $row->link_name,
                    'destination_url' => $row->destination_url,
                    'backup_url' => $row->backup_url,
                    'backup_url_enabled' => (trim($row->backup_url) != '') ? 'Yes' : 'No',
                );
            }
        }
    }
    if ($send_new_email > 0) {
        $downtime_alert_threshold = get_option(SH_PREFIX . 'downtime_alert_threshold');
        $msg = <<<S
The following tracked URLs were inaccessible for longer than {$downtime_alert_threshold} minutes:
S;

        foreach ($downlists as $r) {
            $msg.=<<<S
Link Name: {$r['link_name']}
Primary URL: {$r['destination_url']}
Backup Enabled: {$r['backup_url_enabled']}
Backup URL: {$r['backup_url']}

S;
        }

        $msg .= <<<S

If you have enabled a backup, Shorty should be redirecting all visitors to the URL you have specified. 

Login to your WP admin to check and replace the links mentioned above. 

ShortyWP.com
S;

        wp_mail(get_bloginfo('admin_email'), wp_title() . ' Shorty Downtime Alert!', $msg);
    }
}

function srty_custom_cron_interval($schedules) {
    $schedules['ten_minutes'] = array(
        'interval' => 10 * 60,
        'display' => esc_html__('Every 10 Minutes'),
    );

    return $schedules;
}

add_action('plugins_loaded', 'srty_update_db_check');
//add_action('init', array(new Srty_update(), 'auto_update'));
add_action('init', array(new Srty_redirect(), 'redirect'));
add_action('init', array(new Srty_goals(), 'conversion'));
add_filter('cron_schedules', 'srty_custom_cron_interval');
add_action('srty_cron_event', 'srty_cron');

//check if event scheduled before
if (!wp_next_scheduled('srty_cron_event')) {
    wp_schedule_event(time(), 'ten_minutes', 'srty_cron_event');
}


$shorty = new Srty_shorty();
