<?php

if (!defined('ABSPATH'))
    die('You are not allowed to call this page directly.');

define('SH_PREFIX', 'srty_');
define('SH_MENU_DISPLAY', 'Shorty <em>LITE</em>');

/**
 * always point this to freegeoip url
 */
define('SH_FREEGEOIP_URL', 'https://freegeoip.net/json/');

define('SH_MENU_SLUG',sanitize_title(SH_MENU_DISPLAY));
define('SH_PLUGIN_NAME', dirname(plugin_basename(__FILE__)));
define('SH_PATH', WP_PLUGIN_DIR . '/' . SH_PLUGIN_NAME);
define('SH_CLASS_PATH', SH_PATH . '/classes');
define('SH_VIEW_PATH', SH_PATH . '/views');


define('SH_URL', plugins_url($path = '/' . SH_PLUGIN_NAME));
define('SH_CSS_URL', SH_URL . '/assets/css');
define('SH_JS_URL', SH_URL . '/assets/js');
define('SH_IMAGE_URL', SH_URL . '/assets/images');

define('SHORTLY_API_URL', 'http://www.kreydle.com/clients/softsale/api');
define('SHORTLY_AFFILIATE_URL', 'http://www.shortywp.com/?utm_source=wordpress_plugin&utm_medium=referral&utm_campaign=viral_bar');

define('REMOTE_PLUGIN_UPDATE_CALLBACK', 'http://www.kreydle.com/rest/api.php');

define("SHORTLY_BAR_THEME_GREY", 'Grey');
define("SHORTLY_BAR_THEME_BLACK", 'Black');
define("SHORTLY_BAR_POSITION_TOP", 'Page Top');
define("SHORTLY_BAR_POSITION_BOTTOM", 'Page Bottom');
define("SHORTLY_DUPLICATE_HANDLING_IGNORE", 'IGNORE');
define("SHORTLY_DUPLICATE_HANDLING_COUNT", 'COUNT');
define("SHORTLY_LINK_REDIRECT_TYPE_307", 307);
define("SHORTLY_LINK_REDIRECT_TYPE_301", 301);
define("SHORTLY_LINK_REDIRECT_TYPE_METAREFRESH", 'META_REFRESH');
define("SHORTLY_LINK_REDIRECT_TYPE_JAVASCRIPT", 'JAVASCRIPT');
define("SHORTLY_CLOAKING_TYPE_BASIC", 'Basic Cloaking');
define("SHORTLY_CLOAKING_TYPE_VIRAL", 'Cloaking with Viral Bar');
define("SHORTLY_FRAME_CONTENT_VISIBLE", 'INDEX');
define("SHORTLY_FRAME_CONTENT_HIDDEN", 'NOINDEX');

define("AFFILIATE_NETWORK_AFF_SUB", 'aff_sub');
define("AFFILIATE_NETWORK_AFFTRACK", 'afftrack');
define("AFFILIATE_NETWORK_TID", 'tid');
define("AFFILIATE_NETWORK_SID", 'sid');
define("AFFILIATE_NETWORK_S1", 's1');
define("AFFILIATE_NETWORK_U1", 'u1');
define("AFFILIATE_NETWORK_C1", 'c1');
define("AFFILIATE_NETWORK_SUBID", 'subid');
define("AFFILIATE_NETWORK_SUBID1", 'subid1');

define("MMDDYYYY", 'MM/DD/YYYY');
define("MMDDYY", 'MM/DD/YY');
define("DDMMYYYY", 'DD/MM/YYYY');
define("DDMMYY", 'DD/MM/YY');

define("TWENTY_FOUR_HOURS", 24);
define("TWELVE_HOURS", 12);

define('GOAL_TYPE_LEAD', 'LEAD');
define('GOAL_TYPE_SALE', 'SALE');

define('STATUS_ACCEPTED', 'Accepted');
define('STATUS_REJECTED', 'Rejected');
define('STATUS_PENDING', 'pending');

define('SHORTY_STANDARD_LICENSE', 1);
define('SHORTY_PRO_LICENSE', 2);
define('SHORTY_AGENCY_LICENSE', 3);
define('SHORTY_JV', 4);

define('AUDIENCEPRESS_LITE', 5);
define('AUDIENCEPRESS_UNLIMITED', 7);
define('AUDIENCEPRESS_DEVELOPER', 8);
define('AUDIENCEPRESS_WHITE_LABEL', 9);

define('SHORTY_WHITE_LABEL', 10);