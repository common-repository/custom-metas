<?php
/*
Plugin Name: Custom Metas
Plugin URI: http://www.codelooms.com
Description: To assign meta description and keywords to each post of all post types to make wordpress website as search engine friendly.
Version: 1.5.1
Author: yuvaraj1986, stalinantoni
Author URI: http://www.codelooms.com
*/
$pluginDirPath  = plugin_dir_path( __FILE__ );
define("CM_PLUGIN_PATH", $pluginDirPath );
define("CM_PLUGIN_CLASS_PATH", CM_PLUGIN_PATH . "/class");
define("CM_PLUGINS_TPL_PATH", CM_PLUGIN_PATH . "/tpl" );
define("CM_PLUGIN_FILE", __FILE__ );
include_once(CM_PLUGIN_CLASS_PATH . "/custom-meta.php");
include_once(CM_PLUGIN_CLASS_PATH . "/settings.php");

// Add Meta fields into post and page editors
$metaTags = new CustomMeta();
?>