<?php

/**
 * update plugin
 */
class Srty_update extends Srty_core {

    private $remote_callback;
    private $current_version;
    private $plugin_activated;
    private $plugin_info;

    public function __construct() {
        parent::__construct();


        if (!function_exists('wp_remote_post')) {
            require_once ABSPATH . 'wp-includes/http.php';
        }

        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
    }

    public function auto_update() {
        $this->plugin_slug = 'shortywp';
        $this->plugin = $this->plugin_slug . '/shortywp.php';

        $plugin_info = get_plugins('/shortywp');
        $this->plugin_info = isset($plugin_info['shortywp.php']) ? $plugin_info['shortywp.php'] : array();

        $this->current_version = (isset($this->plugin_info['Version']) ? $this->plugin_info['Version'] : '0');
        $this->remote_callback = REMOTE_PLUGIN_UPDATE_CALLBACK;

        // define the alternative API for updating checking
        add_filter('pre_set_site_transient_update_plugins', array($this, 'check_update'));
        // Define the alternative response for information checking
        add_filter('plugins_api', array($this, 'check_info'), 10, 3);
        add_filter("upgrader_post_install", array($this, "post_install"), 10, 3);
    }

    public function check_update($transient) {
        if (empty($transient->checked)) {
            return $transient;
        }
        // Get the remote version
        $remote_version = $this->_get_remote_meta();

        // If a newer version is available, add the update
        if (version_compare($this->current_version, $remote_version->new_version, '<')) {
            $obj = new stdClass();
            $obj->slug = $this->plugin_slug;
            $obj->new_version = $remote_version->new_version;
            $obj->url = $remote_version->url;
            $obj->plugin = $this->plugin;
            $obj->package = $remote_version->package;
            $obj->upgrade_notice = 'hooray';
            $transient->response[$this->plugin] = $obj;
        }

        return $transient;
    }

    public function check_info($false, $action, $arg) {
        if (isset($arg->slug) && $arg->slug === $this->plugin_slug) {
            // Get the remote version
            $remote_version = $this->_get_remote_meta();
            if ($remote_version !== FALSE) {
                $obj = new stdClass();
                $obj = $remote_version;
                $obj->slug = $this->plugin_slug;
                $obj->plugin = $this->plugin;
                $obj->upgrade_notice = 'hooray';

                $obj->plugin_name = $this->plugin_info['Name'];
                $obj->version = $remote_version->new_version;
                $obj->author = $this->plugin_info['Author'];
                $obj->homepage = $this->plugin_info['PluginURI'];


                return $obj;
            }
        }
        return false;
    }

    public function post_install($true, $hook_extra, $result) {
        global $wp_filesystem;

        $pluginFolder = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . dirname($this->plugin);
        $wp_filesystem->move($result['destination'], $pluginFolder);
        
        $result['destination'] = $pluginFolder;

        //check if plugin is activated
        $is_activated = is_plugin_active($this->plugin);
        if ($is_activated) {
            $activate = activate_plugin($this->plugin);
        }

        return $result;
    }

    private function _get_remote_meta() {
        $params = array(
            'body' => array(
                'action' => 'version',
                'license' => get_option(SH_PREFIX . 'license_key'),
                'license_activation' => get_option(SH_PREFIX . 'license_activation'),
            ),
        );
        $request = wp_remote_post($this->remote_callback, $params);
        if (!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {
            return unserialize($request['body']);
        }
        return false;
    }

}
