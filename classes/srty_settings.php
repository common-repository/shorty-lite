<?php

class Srty_settings extends Srty_core {

    var $currency;
    var $currency_dropdown;

    public function __construct() {
        parent::__construct();
        $this->view_data['currency'] = new Srty_currency();
        $this->view_data['currency_dropdown'] = $this->view_data['currency']->iso4217_dropdown();
    }

    public function display() {

        if (isset($_POST['btnSave']) && check_admin_referer('change_settings')) {

            /**
             * Validation for tracking domain
             */
            $rules = array(
                'settings_tracking_domain' => 'alpha_dash',
                'downtime_alert_threshold' => 'required|min_numeric,1',
                'session_timeout' => 'required|min_numeric,1|max_numeric,240',
                'cookie_window' => 'required|min_numeric,1',
                'settings_akl_max_per_keyword' => 'required|min_numeric,1',
                'settings_akl_max_per_page' => 'required|min_numeric,1',
            );

            if ($this->_post('settings_enable_custom_domain', 0) == 1) {
                $rules['settings_custom_domain'] = 'required';
            }

            $this->gump->validation_rules($rules);

            $this->gump->filter_rules(array(
                'settings_tracking_domain' => 'trim'
            ));

            $validated_data = $this->gump->run($_POST);
            if ($validated_data !== FALSE) {
                /**
                 * automatic keyword links
                 */
                update_option(SH_PREFIX . 'settings_akl_status', $this->_post('settings_akl_status', 0));
                update_option(SH_PREFIX . 'settings_akl_on_homepage', $this->_post('settings_akl_on_homepage', 0));
                update_option(SH_PREFIX . 'settings_akl_on_singlepost', $this->_post('settings_akl_on_singlepost', 0));
                update_option(SH_PREFIX . 'settings_akl_on_singlepage', $this->_post('settings_akl_on_singlepage', 0));
                update_option(SH_PREFIX . 'settings_akl_on_comments', $this->_post('settings_akl_on_comments', 0));
                update_option(SH_PREFIX . 'settings_akl_on_archives', $this->_post('settings_akl_on_archives', 0));
                update_option(SH_PREFIX . 'settings_akl_max_per_page', $this->_post('settings_akl_max_per_page', 1));
                update_option(SH_PREFIX . 'settings_akl_max_per_keyword', $this->_post('settings_akl_max_per_keyword', 1));
                update_option(SH_PREFIX . 'settings_akl_new_window', $this->_post('settings_akl_new_window', 0));
                update_option(SH_PREFIX . 'settings_akl_no_follow', $this->_post('settings_akl_no_follow', 0));

                update_option(SH_PREFIX . 'settings_enable_custom_domain', $this->_post('settings_enable_custom_domain', 0));
                if ($this->_post('settings_enable_custom_domain', 0) == 1) {
                    update_option(SH_PREFIX . 'settings_custom_domain', $this->_post('settings_custom_domain', ''));
                }

                /**
                 * viral bar
                 */
                update_option(SH_PREFIX . 'settings_bar_theme', $_POST['settings_bar_theme']);
                update_option(SH_PREFIX . 'settings_socialButtons_facebook', isset($_POST['settings_socialButtons_facebook']) ? $_POST['settings_socialButtons_facebook'] : 0);
                update_option(SH_PREFIX . 'settings_socialButtons_twitter', isset($_POST['settings_socialButtons_twitter']) ? $_POST['settings_socialButtons_twitter'] : 0);
                update_option(SH_PREFIX . 'settings_earnMoney_enable', $this->_post('settings_earnMoney_enable', 0), 0);
                if ($this->_post('settings_earnMoney_enable', 0) == 1 && ($this->_post('settings_earnMoney_affiliateLink') == '')) {
                    update_option(SH_PREFIX . 'settings_earnMoney_affiliateLink', SHORTLY_AFFILIATE_URL);
                } else {
                    update_option(SH_PREFIX . 'settings_earnMoney_affiliateLink', $this->_post('settings_earnMoney_affiliateLink', SHORTLY_AFFILIATE_URL));
                }

                update_option(SH_PREFIX . 'settings_tracking_domain', sanitize_text_field($_POST['settings_tracking_domain']));
                update_option(SH_PREFIX . 'downtime_alert_threshold', sanitize_text_field($_POST['downtime_alert_threshold']));
                update_option(SH_PREFIX . 'settings_duplicate_handling', sanitize_text_field($_POST['settings_duplicate_handling']));
                update_option(SH_PREFIX . 'settings_currency', sanitize_text_field($_POST['settings_currency']));
                update_option(SH_PREFIX . 'session_timeout', $this->_post('session_timeout', 30));
                update_option(SH_PREFIX . 'cookie_window', $this->_post('cookie_window', 30));

                /**
                 * reset cron 
                 */
                wp_clear_scheduled_hook('srty_cron_event');

                $this->view_data['msg'] = array(
                    'status' => 'alert-success',
                    'text' => SRTY_MSG_SETTING_SAVED
                );
            } else {
                $this->view_data['error'] = $this->gump->get_errors_array();
                $this->view_data['msg'] = array(
                    'status' => 'alert-danger',
                    'text' => SRTY_MSG_SETTING_TOP_ERROR_MESSAGE
                );
            }
        }

        $this->view('v_settings', $this->view_data);
    }

}
