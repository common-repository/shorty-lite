<?php

class Srty_shorty extends Srty_core {

    public $tbl_links;
    public $max_keyword_per_page = 1;

    public function __construct() {
        parent::__construct();
        $this->tbl_links = $this->wpdb->prefix . SH_PREFIX . 'links';

        add_action('admin_init', array($this, 'init'));
        add_action('admin_enqueue_scripts', array($this, 'srty_enqueue'));
        add_action('admin_menu', array($this, 'menu'));
        add_filter('the_content', array($this, 'the_content'));
        add_filter('comment_text', array($this, 'the_comment'));
    }

    public function init() {
        ob_start();
        add_meta_box('myplugin_sectionid', __(SH_MENU_DISPLAY, SH_PREFIX . 'content'), array($this, 'content_disable_keyword'), 'post', 'side');
        add_meta_box('myplugin_sectionid', __(SH_MENU_DISPLAY, SH_PREFIX . 'content'), array($this, 'content_disable_keyword'), 'page', 'side');
        add_action('save_post', array($this, 'save_content_disable_keyword'));

        $this->add_tinymce_buttons();

        $link = new Srty_links();
        $link->init();

        $split_tests = new Srty_split_tests();
        $split_tests->init();

        $goal = new Srty_goals();
        $goal->init();

        $report = new Srty_reports();
        $report->init();

        $conversion = new Srty_conversions();
        $conversion->init();
    }

    public function content_disable_keyword() {
        // Use nonce for verification
        wp_nonce_field(plugin_basename(__FILE__), 'shorty_noncename');
        $disable_keyword = '';
        $put_pixel = '';
        if (isset($_GET['post'])) {
            $post_id = $_GET['post'];
            $disable_keyword = (get_post_meta($post_id, SH_PREFIX . 'content_disable_keyword', true)) ? ' checked="checked" ' : '';
            $put_pixel = get_post_meta($post_id, SH_PREFIX . 'content_put_pixel', true);
        }
        // The actual fields for data entry
        echo '<label>Please insert your Pixel code here</label>';
        echo '<textarea id="' . SH_PREFIX . 'content_put_pixel" name="' . SH_PREFIX . 'content_put_pixel" class="widefat">';
        echo $put_pixel;
        echo '</textarea>';
        echo '<br/>';
        echo '<input type="checkbox" ' . $disable_keyword . ' value="1" id="' . SH_PREFIX . 'content_disable_keyword" name="' . SH_PREFIX . 'content_disable_keyword" /> <label for="' . SH_PREFIX . 'content_disable_keyword">' . __("Disable keyword conversion on this page", 'shorty') . '</label> ';
    }

    /* When the post is saved, saves our custom data */

    public function save_content_disable_keyword($post_id) {
        // verify this came from the our screen and with proper authorization,
        // because save_post can be triggered at other times
        $shorty_noncename = isset($_POST['shorty_noncename']) ? $_POST['shorty_noncename'] : FALSE;
        if (!wp_verify_nonce($shorty_noncename, plugin_basename(__FILE__))) {
            return $post_id;
        }
        // verify if this is an auto save routine. If it is our form has not been submitted, so we dont want
        // to do anything
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return $post_id;
        // Check permissions
        if ('page' == $_POST['post_type']) {
            if (!current_user_can('edit_page', $post_id))
                return $post_id;
        } else {
            if (!current_user_can('edit_post', $post_id))
                return $post_id;
        }
        // OK, we're authenticated: we need to find and save the data
        $meta_value = array();
        $meta_value[0] = $_POST[SH_PREFIX . 'content_disable_keyword'];
        $meta_value[1] = $_POST[SH_PREFIX . 'content_put_pixel'];
        update_post_meta($post_id, SH_PREFIX . 'content_disable_keyword', $meta_value[0]);
        update_post_meta($post_id, SH_PREFIX . 'content_put_pixel', $meta_value[1]);
        return $meta_value;
    }

    public function add_tinymce_buttons() {
        add_action('wp_ajax_srty_shorty_tinymce_form', array($this, 'display_tinymce_form'));
        add_action('wp_ajax_srty_shorty_add_link', array($this, 'add_link'));
        add_action('wp_ajax_srty_shorty_get_existing_link', array($this, 'get_existing_link'));

        // Don't bother doing this stuff if the current user lacks permissions
        if (!current_user_can('edit_posts') && !current_user_can('edit_pages'))
            return;

        // Add only in Rich Editor mode
        if (get_user_option('rich_editing') == 'true') {
            // filter the tinyMCE buttons and add our own
            add_filter("mce_external_plugins", array($this, "add_tinymce_plugin"));
            add_filter('mce_buttons', array($this, 'register_buttons'));
        }
    }

    public function add_tinymce_plugin($plugin_array) {
        $plugin_array['srty_shorty_tinymce'] = SH_JS_URL . '/tinymce_form.js';
        return $plugin_array;
    }

    public function register_buttons($buttons) {
        array_push($buttons, "srty_shorty_tinymce_button");
        array_push($buttons, "code");
        return $buttons;
    }

    public function display_tinymce_form() {

        $file_path = SH_VIEW_PATH . '/v_tinymce.php';
        if (file_exists($file_path)) {
            include($file_path);
        }

        die();
    }

    public function add_link() {

        $link_name = $this->_post('link_name');
        $destination_url = $this->add_http($this->_post('destination_url'));
        $tracking_link = $this->_post('tracking_link');

        $cloaking_status_enable = $this->_post('cloaking_status_enable', 0);
        $cloaking_type = $this->_post('cloaking_type', SHORTLY_CLOAKING_TYPE_BASIC);
        $bar_position = $this->_post('bar_position', SHORTLY_BAR_POSITION_TOP);
        $frame_content = $this->_post('frame_content', SHORTLY_FRAME_CONTENT_VISIBLE);
        $meta_title = $this->_post('meta_title');
        $meta_description = $this->_post('meta_description');

        $param_tag_forward_param = $this->_post('param_tag_forward_param', 0);
        $param_tag_forward_campaign = $this->_post('param_tag_forward_campaign', 0);
        $param_tag_affiliate_tracking = $this->_post('param_tag_affiliate_tracking', 0);
        $param_tag_affiliate_network = $this->_post('param_tag_affiliate_network', AFFILIATE_NETWORK_TID);
        $param_tag_affiliate_network_custom = $this->_post('param_tag_affiliate_network_custom');

        $auto_keyword_linking_enable = $this->_post('auto_keyword_linking_enable', 0);
        $meta_keyword = $this->_post('meta_keyword');

        /**
         * check tracking link if exist
         */
        $sql = "SELECT COUNT(*) AS total FROM {$this->tbl_links} WHERE tracking_link = %s;";
        $result = $this->wpdb->get_row($this->wpdb->prepare($sql, array($tracking_link)), OBJECT);
        if ($result->total > 0) {
            $tracking_link = $tracking_link . '_' . uniqid();
        }

        $this->wpdb->insert(
                $this->tbl_links, array(
            'link_name' => $link_name,
            'destination_url' => $destination_url,
            'tracking_link' => $tracking_link,
            'cloaking_status_enable' => $cloaking_status_enable,
            'cloaking_type' => $cloaking_type,
            'bar_position' => $bar_position,
            'frame_content' => $frame_content,
            'meta_title' => $meta_title,
            'meta_description' => $meta_description,
            'param_tag_forward_param' => $param_tag_forward_param,
            'param_tag_forward_campaign' => $param_tag_forward_campaign,
            'param_tag_affiliate_tracking' => $param_tag_affiliate_tracking,
            'param_tag_affiliate_network' => $param_tag_affiliate_network,
            'param_tag_affiliate_network_custom' => $param_tag_affiliate_network_custom,
            'auto_keyword_linking_enable' => $auto_keyword_linking_enable,
            'meta_keyword' => $meta_keyword,
                )
        );
        $link_id = $this->wpdb->insert_id;

        if ($link_id > 0) {
            wp_send_json(array(
                'result' => TRUE,
                'url' => $this->current_domain(TRUE, FALSE, TRUE) . $tracking_link
            ));
        } else {
            wp_send_json(array('result' => FALSE));
        }
        wp_die();
    }

    public function get_existing_link() {
        $tracking_name = $this->_post('tracking_name');

        $sql = "SELECT COUNT(*) AS total FROM {$this->tbl_links} WHERE tracking_link = %s;";
        $result = $this->wpdb->get_row($this->wpdb->prepare($sql, array($tracking_name)), OBJECT);
        if ($result->total > 0) {
            wp_send_json(array('result' => TRUE));
        } else {
            wp_send_json(array('result' => FALSE));
        }
        wp_die();
    }

    public function routes() {
        if (isset($_GET['action']) && $_GET['action'] == 'add') {
            $this->add();
        } else if (isset($_GET['action']) && $_GET['action'] == 'edit') {
            $this->edit();
        } else if (isset($_GET['action']) && $_GET['action'] == 'srty_shorty_tinymce_form') {
            
        } else if (isset($_GET['action']) && $_GET['action'] == 'delete') {
            $this->delete();
        } else {
            $this->display();
        }
    }

    public function srty_enqueue($hook) {
//        echo $hook;
//        echo SH_MENU_SLUG;
        if (!in_array($hook, array(
                    'toplevel_page_' . SH_MENU_SLUG,
                    'admin_page_sh_import_page',
                    'links_page_sh_import_page',
                    SH_MENU_SLUG . '_page_overview',
                    SH_MENU_SLUG . '_page_sh_trackers',
                    SH_MENU_SLUG . '_page_sh_reports_page',
                    SH_MENU_SLUG . '_page_sh_conversions_page',
                    SH_MENU_SLUG . '_page_sh_links_page',
                    SH_MENU_SLUG . '_page_sh_split_tests',
                    SH_MENU_SLUG . '_page_sh_campaigns_page',
                    SH_MENU_SLUG . '_page_sh_goals_page',
                    'admin_page_sh_goals_page',
                    'admin_page_sh_campaigns_page',
                    SH_MENU_SLUG . '_page_sh_tools_page',
                    SH_MENU_SLUG . '_page_sh_settings_page',
                    SH_MENU_SLUG . '_page_sh_help',
                ))) {
            return;
        }

        wp_enqueue_script('bootstrap', SH_JS_URL . '/bootstrap.min.js', array(), '3.3.5', TRUE);
        wp_enqueue_script('datatables', SH_JS_URL . '/datatables.min.js', array(), '1.10.8', TRUE);
        wp_enqueue_script('typeahead', SH_JS_URL . '/typeahead.bundle.js', array(), '0.11.1.1.1', TRUE);
        wp_enqueue_script('handlebars', SH_JS_URL . '/handlebars-v3.0.3.js', array(), '3.0.3', TRUE);
        wp_enqueue_script('moment', SH_JS_URL . '/moment.min.js', array(), '2.10.6', TRUE);
        wp_enqueue_script('datetimepicker', SH_JS_URL . '/bootstrap-datetimepicker.min.js', array(), '4.17.37', TRUE);
        wp_enqueue_script('clippy', SH_JS_URL . '/ZeroClipboard.min.js', array(), '1.0.0', TRUE);
        wp_enqueue_script('chart_js', SH_JS_URL . '/Chart.bundle.min.js', array(), '4.1.5', TRUE);
        wp_enqueue_script('chosen', SH_JS_URL . '/chosen.jquery.min.js', array(), '1.5.0', TRUE);
        wp_enqueue_script('srty_js', SH_JS_URL . '/js.js', array(), '1.1.0', TRUE);


        wp_enqueue_style('srty_bootstrap', SH_CSS_URL . '/bootstrap.css', '3.3.5.1.1');
        wp_enqueue_style('srty_datatables', SH_CSS_URL . '/dataTables.bootstrap.css', '3.3.5.1');
        wp_enqueue_style('srty_fontawesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css', '4.3.0');
        wp_enqueue_style('srty_datetimepicker', SH_CSS_URL . '/bootstrap-datetimepicker.min.css', '4.17.37');
        wp_enqueue_style('srty_chosen', SH_CSS_URL . '/chosen.css', '1.5.0');
        wp_enqueue_style('srty_style', SH_CSS_URL . '/style.css', '2.1.0');
    }

    public function menu() {
        add_menu_page(SH_MENU_SLUG, SH_MENU_DISPLAY, 'read', SH_MENU_SLUG, array(new Srty_general(), 'overview'), SH_IMAGE_URL . '/favicon-20x20.png', 26);
        add_submenu_page(SH_MENU_SLUG, SH_MENU_DISPLAY . ' - Overview', 'Overview', 'read', SH_MENU_SLUG, array(new Srty_general(), 'overview'));
        add_submenu_page(SH_MENU_SLUG, SH_MENU_DISPLAY . ' - Trackers', 'Trackers', 'read', 'sh_trackers', array(new Srty_links(), 'routes'));
        add_submenu_page(SH_MENU_SLUG, SH_MENU_DISPLAY . ' - Split Tests', 'Split Tests', 'read', 'sh_split_tests', array(new Srty_split_tests(), 'routes'));
        add_submenu_page(SH_MENU_SLUG, SH_MENU_DISPLAY . ' - Conversions', 'Conversions', 'read', 'sh_conversions_page', array(new Srty_conversions(), 'routes'));
        add_submenu_page(SH_MENU_SLUG, SH_MENU_DISPLAY . ' - Tools', 'Tools', 'read', 'sh_tools_page', array(new Srty_tools(), 'routes'));
        add_submenu_page(SH_MENU_SLUG, SH_MENU_DISPLAY . ' - Reports', 'Reports', 'read', 'sh_reports_page', array(new Srty_reports(), 'routes'));
        add_submenu_page(SH_MENU_SLUG, SH_MENU_DISPLAY . ' - Settings', 'Settings', 'read', 'sh_settings_page', array(new Srty_settings(), 'routes'));
        add_submenu_page(SH_MENU_SLUG, SH_MENU_DISPLAY . ' - Help', 'Help', 'read', 'sh_help', array(new Srty_general(), 'helps'));
    }

    public function the_content($content) {
        $content = $this->_content_add_pixel($content);
        if (get_option(SH_PREFIX . 'settings_akl_status')) {
            if ((is_home() AND get_option(SH_PREFIX . 'settings_akl_on_homepage')) || ( is_archive() AND get_option(SH_PREFIX . 'settings_akl_on_archives')) || (is_single() AND get_option(SH_PREFIX . 'settings_akl_on_singlepost')) || (is_page() AND get_option(SH_PREFIX . 'settings_akl_on_singlepage'))) {
                $content = $this->_process_content($content);
            }
        }
        return $content;
    }

    public function the_comment($content) {
        if (get_option(SH_PREFIX . 'settings_akl_status')) {
            if (get_option(SH_PREFIX . 'settings_akl_on_comments') AND ( ( is_archive() AND get_option(SH_PREFIX . 'settings_akl_on_archives')) || (is_single() AND get_option(SH_PREFIX . 'settings_akl_on_singlepost')) || (is_page() AND get_option(SH_PREFIX . 'settings_akl_on_singlepage')))) {
                $content = $this->_process_content($content);
            }
        }
        return $content;
    }

    private function _content_add_pixel($content) {
        global $wpdb, $post;

        if (trim(get_post_meta($post->ID, SH_PREFIX . 'content_put_pixel', true)) != '') {
            if (is_single() || is_page()) {
                $content = get_post_meta($post->ID, SH_PREFIX . 'content_put_pixel', true) . $content;
            }
        }
        return $content;
    }

    private function _process_content($content) {
        global $wpdb, $post;
        if (get_post_meta($post->ID, SH_PREFIX . 'content_disable_keyword', true) != 1) {
            $this->max_keyword_per_page = (int) get_option(SH_PREFIX . 'settings_akl_max_per_page');
            $rows = wp_cache_get(SH_PREFIX . 'cache_the_content');
            if (false === $rows) {
                $rows = $wpdb->get_results("SELECT * FROM {$this->tbl_links} WHERE auto_keyword_linking_enable=1 AND meta_keyword!='' ;");
                wp_cache_set(SH_PREFIX . 'cache_the_content', $rows);
            }

            if (count($rows) > 0) {
                $links = array();
                /**
                 * seperate into individual keyword
                 */
                foreach ($rows as $value) {
                    $keywords = explode(',', $value->meta_keyword);
                    foreach ($keywords as $keyword) {
                        if (!$this->_deep_in_array(trim($keyword), $links) AND ( trim($keyword) != ''))
                            $links[] = array('id' => $value->id, 'tracking' => $value->tracking_link, 'keyword' => trim($keyword));
                    }
                }
                shuffle($links);

                /**
                 * filter content to exclude conversion for the following tags
                 */
                $skip_filters = array();
                $skip_filters = array_merge($skip_filters, $this->_skip_filters($content, '<a', '</a>'));
                $skip_filters = array_merge($skip_filters, $this->_skip_filters($content, '<h1', '</h1>'));
                $skip_filters = array_merge($skip_filters, $this->_skip_filters($content, '<h2', '</h2>'));
                $skip_filters = array_merge($skip_filters, $this->_skip_filters($content, '<h3', '</h3>'));
                $skip_filters = array_merge($skip_filters, $this->_skip_filters($content, '<h4', '</h4>'));
                $skip_filters = array_merge($skip_filters, $this->_skip_filters($content, '<h5', '</h5>'));
                $skip_filters = array_merge($skip_filters, $this->_skip_filters($content, '<code', '</code>'));
                $skip_filters = array_merge($skip_filters, $this->_skip_filters($content, '<img', '/>'));
                $skip_filters = array_merge($skip_filters, $this->_skip_filters($content, '<object', '</object>'));
                $skip_filters = array_merge($skip_filters, $this->_skip_filters($content, '<script', '</script>'));
                $skip_filters = array_merge($skip_filters, $this->_skip_filters($content, '<embed', '>'));
                $skip_filters = array_merge($skip_filters, $this->_skip_filters($content, '[tube]', '[/tube]'));
                $skip_filters = array_merge($skip_filters, $this->_skip_filters($content, '<pre', '</pre>'));
                $skip_filters = array_merge($skip_filters, $this->_skip_filters($content, '<blockquote', '</blockquote>'));
                $skip_filters = array_merge($skip_filters, $this->_skip_filters($content, '<cite', '</cite>'));
                /**
                 * EO: filter content to exclude conversion for the following tags
                 */
                /**
                 * process links allowed per keyword based on user setting "settings_max_per_keyword"
                 */
                $keyword_matches = array();
                foreach ($links as $key => $val) {
                    if (trim($val['keyword']) != '') {
                        $pattern = '%\b' . trim($val['keyword']) . '\b%i';
                        preg_match_all($pattern, $content, $matches[$key], PREG_OFFSET_CAPTURE);
                        //shuffle keyword matches to make random choices
                        shuffle($matches[$key][0]);
                        //filter matches up to max links per keyword rule
                        $max_link_per_keyword = get_option(SH_PREFIX . 'settings_akl_max_per_keyword') ? get_option(SH_PREFIX . 'settings_akl_max_per_keyword') : 1;
                        array_splice($matches[$key][0], $max_link_per_keyword);
                        $keyword_matches = array_merge($keyword_matches, $matches[$key]);
                    }
                }


                //cleanup matches
                $link_matches = array();
                foreach ($keyword_matches as $k => $matches) {
                    foreach ($matches as $key => $value) {
                        //check if matches is widthin skip_filters
                        if ($this->_is_valid($value[1], $skip_filters)) {
                            //check if keyword postion already in the list to prevent overlap keyword
                            if ($this->_no_overlap($value[1], $link_matches)) {
                                $link_matches[] = array(
                                    'link_index' => $k,
                                    'keyword' => $value[0],
                                    'start' => $value[1]
                                );
                            }
                        }
                    }
                }
                shuffle($link_matches);

                /**
                 * convert match keyword into link
                 */
                $open_new_window = (bool) get_option(SH_PREFIX . 'settings_akl_new_window') ? ' target="_blank" ' : '';
                $no_follow = (bool) get_option(SH_PREFIX . 'settings_akl_no_follow') ? ' rel="nofollow" ' : '';
                foreach ($link_matches as $key => $value) {
                    $post_id = 'post_' . $post->ID;
                    $auto_campaign = '?akl=true&source=shorty&medium=referral&campaign=keyword_conversion&content=' . $post_id . '&term=' . $value['keyword'];
                    $link = '<a ' . $open_new_window . $no_follow . ' href="' . $this->current_domain(TRUE) . $links[$value['link_index']]['tracking'] . $auto_campaign . '">' . $value['keyword'] . '</a>';
                    $content = substr_replace($content, $link, $link_matches[$key]['start'], strlen($value['keyword']));
                    $link_matches = $this->_update_start($link_matches, $link_matches[$key]['start'], strlen($link) - strlen($value['keyword']));
                    $this->max_keyword_per_page--;
                    if ($this->max_keyword_per_page <= 0)
                        break;
                }
            }
        }
        return $content;
    }

    private function _update_start($link_matches, $start, $length) {
        foreach ($link_matches as $key => $value) {
            if ($value['start'] > $start) {
                $link_matches[$key]['start'] = $value['start'] + $length;
            }
        }
        return $link_matches;
    }

    private function _is_valid($value, $skip_filters = array()) {
        foreach ($skip_filters as $val) {
            if (($value > $val['start']) AND ( $value < $val['end'])) {
                return false;
                break;
            }
        }
        return true;
    }

    private function _no_overlap($value, $link_matches = array()) {
        foreach ($link_matches as $val) {
            if (($value >= $val['start']) AND ( $value <= ($val['start'] + strlen($val['keyword'])))) {
                return false;
                break;
            }
        }
        return true;
    }

    private function _skip_filters($content, $open_tag, $close_tag) {
        $i = 0;
        $open_tag_pos = true;
        $result = array();
        while ($open_tag_pos) {
            $open_tag_pos = stripos($content, $open_tag, $i);
            if ($open_tag_pos !== FALSE) {
                $close_tag_pos = stripos($content, $close_tag, $open_tag_pos + 1);
                /**
                 * closing tag not found
                 */
                if ($close_tag_pos === FALSE) {
//                    echo '===========>'.$close_tag.'<===========';
                    return $result;
                }
                $i = $close_tag_pos + 1;
                $result[] = array('start' => $open_tag_pos, 'end' => $close_tag_pos + strlen($close_tag));
            }
        }
        return $result;
    }

    private function _deep_in_array($value, $array, $case_insensitive = false) {
        foreach ($array as $item) {
            if (is_array($item))
                $ret = $this->_deep_in_array($value, $item, $case_insensitive);
            else
                $ret = ($case_insensitive) ? strtolower($item) == $value : $item == $value;
            if ($ret)
                return $ret;
        }
        return false;
    }

    private function _keyword_deep_in_array($keyword, $array, $case_insensitive = false) {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $ret = $this->_keyword_deep_in_array($keyword, $value, $case_insensitive);
            } else if ($key === "keyword") {
                $ret = $case_insensitive ? strtolower($value) ==
                        $keyword : $value == $keyword;
            } else {
                continue;
            }

            if ($ret && $key === "keyword")
                return true;
        }
        return false;
    }

}
