<?php

/**
 * general function & static page
 */
class Srty_redirect extends Srty_core {

    public $tbl_links;
    public $tbl_split_tests;
    public $tbl_split_test_allocations;
    public $tbl_visits_log;
    public $tbl_campaigns;

    public function __construct() {
        parent::__construct();
        define('DONOTCACHEPAGE', 1);
        $this->tbl_links = $this->wpdb->prefix . SH_PREFIX . 'links';
        $this->tbl_visits_log = $this->wpdb->prefix . SH_PREFIX . 'visits_log';
        $this->tbl_campaigns = $this->wpdb->prefix . SH_PREFIX . 'campaigns';
        $this->tbl_split_tests = $this->wpdb->prefix . SH_PREFIX . 'split_tests';
        $this->tbl_split_test_allocations = $this->wpdb->prefix . SH_PREFIX . 'split_test_allocations';
    }

    public function redirect() {

        if (!is_admin()) {
            $this->_redirect_campaign();
            $this->_redirect_link();
        }
    }

    /**
     * handle campaign redirect
     */
    private function _redirect_campaign() {
        $request_uri = preg_replace('#/$#', '', urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
        $tracking_domain = get_option(SH_PREFIX . 'settings_tracking_domain');
        $tracking_domain = trim($tracking_domain) != '' ? $tracking_domain . '/' : '';

        $site_url_subfolder = parse_url(site_url());
        $site_url_subfolder = ltrim($site_url_subfolder['path'], '/');
        $site_url_subfolder = trim($site_url_subfolder) != '' ? $site_url_subfolder . '/' : '';
        $tracking = preg_replace('#^/' . $site_url_subfolder . $tracking_domain . 'c/#', '', $request_uri);
        $tracking = ltrim($tracking, '/');

        $campaign = $this->_by_campaign($tracking);
        if ($campaign !== NULL) {
            $link = $this->_by_link_id($campaign->link_id);
            /**
             * override campaign param with query string if exist
             */
            $campaign->tracking_campaign = $tracking;
            $campaign->source = $this->_get('source', $campaign->source);
            $campaign->medium = $this->_get('medium', $campaign->medium);
            $campaign->campaign = $this->_get('campaign', $campaign->campaign);
            $campaign->content = $this->_get('content', $campaign->content);
            $campaign->term = $this->_get('term', $campaign->term);
            $campaign->cpc = $this->_get('cpc', $campaign->cpc);

            if ($link !== NULL) {

                $link->backup_url = trim($link->backup_url) != '' ? $link->backup_url : $link->destination_url;

                /**
                 * check if from mobile and there is url for mobile, we redirect user to mobile url
                 */
                if (trim($link->mobile_url) != '') {
                    $useragent = $_SERVER['HTTP_USER_AGENT'];
                    if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4))) {
                        $link->destination_url = trim($link->mobile_url) != '' ? $link->mobile_url : $link->destination_url;
                    }
                }

                if ((bool) get_option(SH_PREFIX . 'is_demo_mode') || in_array(get_option(SH_PREFIX . 'license_scheme_id'), array(SHORTY_PRO_LICENSE, SHORTY_AGENCY_LICENSE, AUDIENCEPRESS_LITE, AUDIENCEPRESS_UNLIMITED))) {
                    /**
                     * check for the geoip redirect
                     */
                    if ((bool) $link->geo_redirect_enable) {
                        $country = $this->ip_info();
                        $country_code = isset($country['country_code']) ? $country['country_code'] : 'UNKNOWN';
                        if (($link->geo_redirect_option === 'ALL_EXCEPT') && (in_array($country_code, explode(',', $link->geo_redirect_countries)))) {
                            $link->destination_url = trim($link->geo_redirect_destination_url) != '' ? $link->geo_redirect_destination_url : $link->backup_url;
                        } elseif (($link->geo_redirect_option === 'SELECTED') && (!in_array($country_code, explode(',', $link->geo_redirect_countries)))) {
                            $link->destination_url = trim($link->geo_redirect_destination_url) != '' ? $link->geo_redirect_destination_url : $link->backup_url;
                        }
                    }
                }

                /**
                 * check if link expired is on and date expired
                 */
                if ((bool) $link->link_expired_enable && ($link->link_expired_date < date("Y-m-d H:i:s"))) {
                    $link->destination_url = $link->link_expired_url;
                }

                if ((bool) get_option(SH_PREFIX . 'is_demo_mode') || in_array(get_option(SH_PREFIX . 'license_scheme_id'), array(SHORTY_AGENCY_LICENSE, AUDIENCEPRESS_LITE, AUDIENCEPRESS_UNLIMITED))) {
                    /**
                     * check Uptime monitoring 
                     */
                    if ((bool) $link->uptime_monitoring_enabled && !(bool) $link->uptime_is_online) {
                        $link->destination_url = $link->backup_url;
                    }
                }

                /**
                 * check click limiter 
                 */
                if ((bool) $link->click_limiter_enable && ($link->click_lifetime_clicks > $link->click_limiter_max_clicks)) {
                    $link->destination_url = trim($link->click_limiter_url) != '' ? $link->click_limiter_url : $link->backup_url;
                }

                $this->view_data['link'] = $link;
                $this->view_data['campaign'] = $campaign;
                $click_log_id = $this->click_log($link, $campaign);

                /**
                 * forward param
                 */
                if ((bool) $link->param_tag_forward_param) {
                    $url_param = $_SERVER["QUERY_STRING"];
                    if (trim($url_param) != '') {
                        $p = parse_url($this->view_data['link']->destination_url, PHP_URL_QUERY);
                        if (isset($p)) {
                            $this->view_data['link']->destination_url .= '&' . $url_param;
                        } else {
                            $this->view_data['link']->destination_url .= '?' . $url_param;
                        }
                    }
                }

                /**
                 * parameter tagging
                 */
                if ((bool) $link->param_tag_forward_campaign) {
                    $utm_source = $campaign->source;
                    $utm_medium = $campaign->medium;
                    $utm_campaign = $campaign->campaign;
                    $utm_content = $campaign->content;
                    $utm_term = $campaign->term;

                    $utm_tracking = 'utm_source=' . trim($utm_source) .
                            '&utm_medium=' . trim($utm_medium) .
                            '&utm_campaign=' . trim($utm_campaign) .
                            '&utm_content=' . trim($utm_content) .
                            '&utm_term=' . trim($utm_term);
                    if (trim($utm_tracking) != '') {
                        $p = parse_url($this->view_data['link']->destination_url, PHP_URL_QUERY);
                        if (isset($p)) {
                            $this->view_data['link']->destination_url .= '&' . $utm_tracking;
                        } else {
                            $this->view_data['link']->destination_url .= '?' . $utm_tracking;
                        }
                    }
                }

                if ((bool) $link->param_tag_affiliate_tracking) {
                    $affiliate_tracking = '';
                    if ($link->param_tag_affiliate_network != 'Custom') {
                        $affiliate_tracking = $link->param_tag_affiliate_network . '=' . $click_log_id;
                    } else {
                        $affiliate_tracking = $link->param_tag_affiliate_network_custom . '=' . $click_log_id;
                    }

                    if (trim($affiliate_tracking) != '') {
                        $p = parse_url($this->view_data['link']->destination_url, PHP_URL_QUERY);
                        if (isset($p)) {
                            $this->view_data['link']->destination_url .= '&' . $affiliate_tracking;
                        } else {
                            $this->view_data['link']->destination_url .= '?' . $affiliate_tracking;
                        }
                    }
                }

                //campaign value forwarder
                $destination_url = $this->view_data['link']->destination_url;
                $destination_url = str_replace('(srty_source)', $campaign->source, $destination_url);
                $destination_url = str_replace('(srty_medium)', $campaign->medium, $destination_url);
                $destination_url = str_replace('(srty_campaign)', $campaign->campaign, $destination_url);
                $destination_url = str_replace('(srty_content)', $campaign->content, $destination_url);
                $destination_url = str_replace('(srty_term)', $campaign->term, $destination_url);
                $destination_url = str_replace('(srty_cid)', $click_log_id, $destination_url);
                $this->view_data['link']->destination_url = $destination_url;

                if ($this->_get('mode', FALSE) == 'test') {
                    wp_redirect($this->view_data['link']->destination_url, 302);
                }

                /**
                 * check for viral bar
                 */
                if ((bool) $link->cloaking_status_enable) {
                    //check if basic cloak or viral bar
                    if ($link->cloaking_type == SHORTLY_CLOAKING_TYPE_BASIC) {
                        $this->view('v_viral_basic', $this->view_data, FALSE);
                    } else {
                        //check if viral on top or bottom
                        if ($link->bar_position == SHORTLY_BAR_POSITION_TOP) {
                            $this->view('v_viral_top', $this->view_data, FALSE);
                        } else {
                            //bottom
                            $this->view('v_viral_bottom', $this->view_data, FALSE);
                        }
                    }

                    exit;
                } else {

                    //redirect
                    wp_redirect($this->view_data['link']->destination_url, 302);
                    exit;
                }
            }
        }
    }

    /**
     * handle link redirect
     */
    private function _redirect_link() {

        $request_uri = preg_replace('#/$#', '', urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
        $tracking_domain = get_option(SH_PREFIX . 'settings_tracking_domain');
        $tracking_domain = trim($tracking_domain) != '' ? $tracking_domain . '/' : '';
        //link tracking  
        $site_url_subfolder = parse_url(site_url());
        $site_url_subfolder = ltrim($site_url_subfolder['path'], '/');
        $site_url_subfolder = trim($site_url_subfolder) != '' ? $site_url_subfolder . '/' : '';
        $tracking = preg_replace('#^/' . $site_url_subfolder . $tracking_domain . '#', '', $request_uri);
        $tracking = ltrim($tracking, '/');

        $link = $this->_by_tracking($tracking);
        if ($link !== NULL) {
            $link->backup_url = trim($link->backup_url) != '' ? $link->backup_url : $link->destination_url;
            /**
             * check if from mobile and there is url for mobile, we redirect user to mobile url
             */
            if (trim($link->mobile_url) != '') {
                $useragent = $_SERVER['HTTP_USER_AGENT'];
                if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4))) {
                    $link->destination_url = trim($link->mobile_url) != '' ? $link->mobile_url : $link->destination_url;
                }
            }

            if ((bool) get_option(SH_PREFIX . 'is_demo_mode') || in_array(get_option(SH_PREFIX . 'license_scheme_id'), array(SHORTY_PRO_LICENSE, SHORTY_AGENCY_LICENSE, AUDIENCEPRESS_LITE, AUDIENCEPRESS_UNLIMITED))) {
                /**
                 * check for the geoip redirect
                 */
                if ((bool) $link->geo_redirect_enable) {
                    $country = $this->ip_info();
                    $country_code = isset($country['country_code']) ? $country['country_code'] : 'UNKNOWN';
                    if (($link->geo_redirect_option === 'ALL_EXCEPT') && (in_array($country_code, explode(',', $link->geo_redirect_countries)))) {
                        $link->destination_url = trim($link->geo_redirect_destination_url) != '' ? $link->geo_redirect_destination_url : $link->backup_url;
                    } elseif (($link->geo_redirect_option === 'SELECTED') && (!in_array($country_code, explode(',', $link->geo_redirect_countries)))) {
                        $link->destination_url = trim($link->geo_redirect_destination_url) != '' ? $link->geo_redirect_destination_url : $link->backup_url;
                    }
                }
            }

            /**
             * check if link expired is on and date expired
             */
            if ((bool) $link->link_expired_enable && ($link->link_expired_date < date("Y-m-d H:i:s"))) {
                $link->destination_url = trim($link->link_expired_url) != '' ? $link->link_expired_url : $link->backup_url;
            }

            if ((bool) get_option(SH_PREFIX . 'is_demo_mode') || in_array(get_option(SH_PREFIX . 'license_scheme_id'), array(SHORTY_AGENCY_LICENSE, AUDIENCEPRESS_LITE, AUDIENCEPRESS_UNLIMITED))) {
                /**
                 * check Uptime monitoring 
                 */
                if ((bool) $link->uptime_monitoring_enabled && !(bool) $link->uptime_is_online) {
                    $link->destination_url = $link->backup_url;
                }
            }

            /**
             * check click limiter 
             */
            if ((bool) $link->click_limiter_enable && ($link->click_lifetime_clicks > $link->click_limiter_max_clicks)) {
                $link->destination_url = trim($link->click_limiter_url) != '' ? $link->click_limiter_url : $link->backup_url;
            }


            $this->view_data['link'] = $link;

            $campaign = FALSE;
            $campaign = new stdClass();
            $campaign->id = 0;
            $campaign->tracking_campaign = '';
            $campaign->source = $this->_get('source', NULL);
            $campaign->medium = $this->_get('medium', NULL);
            $campaign->campaign = $this->_get('campaign', NULL);
            $campaign->content = $this->_get('content', NULL);
            $campaign->term = $this->_get('term', NULL);
            $campaign->cpc = $this->_get('cpc', 0);
            $campaign->calculate_cost = 0;
            if ($campaign->cpc != 0) {
                $campaign->calculate_cost = 1;
            }

            /**
             * set campaign param for autokeyword linking
             */
            if ($this->_get('akl', FALSE) !== FALSE) {
                $campaign = new stdClass();
                $campaign->id = 0;
                $campaign->tracking_campaign = 'Auto Keyword Linking';
                $campaign->source = $this->_get('source', NULL);
                $campaign->medium = $this->_get('medium', NULL);
                $campaign->campaign = $this->_get('campaign', NULL);
                $campaign->content = $this->_get('content', NULL);
                $campaign->term = $this->_get('term', NULL);
                $campaign->cpc = $this->_get('cpc', 0);
                $campaign->calculate_cost = 0;
                if ($campaign->cpc != 0) {
                    $campaign->calculate_cost = 1;
                }
            }
            $click_log_id = $this->click_log($link, $campaign);

            /**
             * forward param
             */
            if ((bool) $link->param_tag_forward_param) {
                $url_param = $_SERVER["QUERY_STRING"];
                if (trim($url_param) != '') {
                    $p = parse_url($this->view_data['link']->destination_url, PHP_URL_QUERY);
                    if (isset($p)) {
                        $this->view_data['link']->destination_url .= '&' . $url_param;
                    } else {
                        $this->view_data['link']->destination_url .= '?' . $url_param;
                    }
                }
            }

            /**
             * parameter tagging
             */
            if ((bool) $link->param_tag_forward_campaign) {
                $utm_source = '';
                $utm_medium = '';
                $utm_campaign = '';
                $utm_content = '';
                $utm_term = '';

                $utm_tracking = 'utm_source=' . trim($utm_source) .
                        '&utm_medium=' . trim($utm_medium) .
                        '&utm_campaign=' . trim($utm_campaign) .
                        '&utm_content=' . trim($utm_content) .
                        '&utm_term=' . trim($utm_term);
                if (trim($utm_tracking) != '') {
                    $p = parse_url($this->view_data['link']->destination_url, PHP_URL_QUERY);
                    if (isset($p)) {
                        $this->view_data['link']->destination_url .= '&' . $utm_tracking;
                    } else {
                        $this->view_data['link']->destination_url .= '?' . $utm_tracking;
                    }
                }
            }

            if ((bool) $link->param_tag_affiliate_tracking) {
                $affiliate_tracking = '';
                if ($link->param_tag_affiliate_network != 'Custom') {
                    $affiliate_tracking = $link->param_tag_affiliate_network . '=' . $click_log_id;
                } else {
                    $affiliate_tracking = $link->param_tag_affiliate_network_custom . '=' . $click_log_id;
                }

                if (trim($affiliate_tracking) != '') {
                    $p = parse_url($this->view_data['link']->destination_url, PHP_URL_QUERY);
                    if (isset($p)) {
                        $this->view_data['link']->destination_url .= '&' . $affiliate_tracking;
                    } else {
                        $this->view_data['link']->destination_url .= '?' . $affiliate_tracking;
                    }
                }
            }
            //campaign value forwarder
            $destination_url = $this->view_data['link']->destination_url;
            $destination_url = str_replace('(srty_source)', isset($campaign->source) ? $campaign->source : '', $destination_url);
            $destination_url = str_replace('(srty_medium)', isset($campaign->medium) ? $campaign->medium : '', $destination_url);
            $destination_url = str_replace('(srty_campaign)', isset($campaign->campaign) ? $campaign->campaign : '', $destination_url);
            $destination_url = str_replace('(srty_content)', isset($campaign->content) ? $campaign->content : '', $destination_url);
            $destination_url = str_replace('(srty_term)', isset($campaign->term) ? $campaign->term : '', $destination_url);
            $destination_url = str_replace('(srty_cid)', $click_log_id, $destination_url);
            $this->view_data['link']->destination_url = $destination_url;

            if ($this->_get('mode', FALSE) == 'test') {
                wp_redirect($this->view_data['link']->destination_url, 302);
            }

            /**
             * check for viral bar
             */
            if ((bool) $link->cloaking_status_enable) {
                //check if basic cloak or viral bar
                if ($link->cloaking_type == SHORTLY_CLOAKING_TYPE_BASIC) {
                    $this->view('v_viral_basic', $this->view_data, FALSE);
                } else {
                    //check if viral on top or bottom
                    if ($link->bar_position == SHORTLY_BAR_POSITION_TOP) {
                        $this->view('v_viral_top', $this->view_data, FALSE);
                    } else {
                        //bottom
                        $this->view('v_viral_bottom', $this->view_data, FALSE);
                    }
                }

                exit;
            } else {
                $this->view('v_refresh', $this->view_data, FALSE);
                exit;
            }
        }
    }

    private function _by_tracking($tracking = FALSE) {
        if ($tracking !== FALSE) {
            return $this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM {$this->tbl_links} WHERE tracking_link=%s", $tracking));
        } else {
            return NULL;
        }
    }

    private function _by_link_id($link_id = FALSE) {
        if ($link_id !== FALSE) {
            return $this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM {$this->tbl_links} WHERE id=%d", $link_id));
        } else {
            return NULL;
        }
    }

    private function _by_campaign($tracking = FALSE) {
        if ($tracking !== FALSE) {
            return $this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM {$this->tbl_campaigns} WHERE tracking_campaign=%s", $tracking));
        } else {
            return NULL;
        }
    }

    public function srty_enqueue_redirect() {
        wp_enqueue_script('jquery', SH_JS_URL . '/jquery-1.11.3.min.js', array(), '1.11.3', TRUE);
        wp_enqueue_script('bootstrap', SH_JS_URL . '/bootstrap.min.js', array(), '3.3.5', TRUE);
        wp_enqueue_script('datatables', SH_JS_URL . '/datatables.min.js', array(), '1.10.8', TRUE);
        wp_enqueue_script('typeahead', SH_JS_URL . '/typeahead.bundle.js', array(), '0.11.1.1', TRUE);
        wp_enqueue_script('handlebars', SH_JS_URL . '/handlebars-v3.0.3.js', array(), '3.0.3', TRUE);
        wp_enqueue_script('srty_js', SH_JS_URL . '/js.js', array(), '1.0.6', TRUE);

        wp_enqueue_style('srty_bootstrap', SH_CSS_URL . '/bootstrap.css', '3.3.5');
        wp_enqueue_style('srty_datatables', SH_CSS_URL . '/dataTables.bootstrap.css', '3.3.5.1');
        wp_enqueue_style('srty_fontawesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css', '4.3.0');
        wp_enqueue_style('srty_style', SH_CSS_URL . '/style.css', '1.0.1');
    }

    public function click_log($link = FALSE, $campaign = FALSE, $split_test_id = 0) {
        if ($link === FALSE) {
            return 0;
        }

        if (!isset($_COOKIE['visitor_id'])) {
            $visitor_id = crc32(uniqid() . time());
            setcookie('visitor_id', $visitor_id, strtotime('+100 years'), '/');
        } else {
            $visitor_id = $_COOKIE['visitor_id'];
        }

        if (!isset($_COOKIE['visitor_session'])) {
            $visitor_session = crc32(uniqid() . time());
            $session_timeout = get_option(SH_PREFIX . 'session_timeout', 30);
            setcookie('visitor_session', $visitor_session, strtotime("+$session_timeout minutes"), '/');
        } else {
//extend session
            $visitor_session = $_COOKIE['visitor_session'];
            $session_timeout = get_option(SH_PREFIX . 'session_timeout', 30);
            setcookie('visitor_session', $visitor_session, strtotime("+$session_timeout minutes"), '/');
        }

        $country = $this->ip_info();

        $insert_data = array(
            'link_id' => $link->id,
            'split_test_id' => $split_test_id,
            'visitor_id' => $visitor_id,
            'visitor_session' => $visitor_session,
            'tracking_link' => $link->tracking_link,
            'destination_url' => $link->destination_url,
            'referrer_url' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'ip_country_code' => isset($country['country_code']) ? $country['country_code'] : '',
            'ip_country_name' => isset($country['country']) ? $country['country'] : '',
            'ip_city_name' => isset($country['city']) ? $country['city'] : '',
            'ip_latitude' => isset($country['latitude']) ? $country['latitude'] : '',
            'ip_longitude' => isset($country['longitude']) ? $country['longitude'] : '',
            'user_agent_string' => $_SERVER['HTTP_USER_AGENT'],
            'created_date' => current_time("Y-m-d H:i:s"),
        );

        if (isset($campaign->id)) {
            $insert_data = array_merge($insert_data, array(
                'campaign_id' => $campaign->id,
                'tracking_campaign' => $campaign->tracking_campaign,
                'source' => $campaign->source,
                'medium' => $campaign->medium,
                'campaign' => $campaign->campaign,
                'content' => $campaign->content,
                'term' => $campaign->term,
                'cpc' => $campaign->calculate_cost == 1 ? $campaign->cpc : 0.00,
            ));
        }

        $this->wpdb->insert($this->tbl_visits_log, $insert_data);
        $click_id = $this->wpdb->insert_id;

        $cookie_window = get_option(SH_PREFIX . 'cookie_window', 30);
        setcookie('click_id', $click_id, strtotime("+$cookie_window days"), '/');

        $this->wpdb->update($this->tbl_links, array('click_lifetime_clicks' => $link->click_lifetime_clicks + 1), array('id' => $link->id));
        return $click_id;
    }

}
