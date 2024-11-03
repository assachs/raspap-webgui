<?php
/**
 * Functions for plugin handling
 */
require_once 'includes/functions.php';
require_once 'config.php';

$pluginpath = "plugins";

/**
 * Renders a template from inside a plugin directory
 * @param $plugin
 * @param $name
 * @param $__template_data
 * @return false|string
 */
function pluginRenderTemplate($plugin, $name, $__template_data = [])
{
    $file = realpath(dirname(__FILE__) . "/../plugins/$plugin/templates/$name.php");
    if (!file_exists($file)) {
        return "template $name ($file) not found";
    }

    if (is_array($__template_data)) {
        extract($__template_data);
    }

    ob_start();
    include $file;
    return ob_get_clean();
}

/**
 * forwards the page to the responsible plugin
  * @param $page name of the page. Format: plugin__<pluginname>__pagename
 * @return void
 */
function pluginHandlePageAction($page)
{
	global $pluginpath;
	foreach (plugin_plugins() as $plugin) {
		if (str_starts_with($page,"/plugin__".$plugin."__")) {
			require_once($pluginpath."/".$plugin."/functions.php");
			$function='\\'.$plugin.'\\pluginHandlePageAction';
			$function($page);
		}
	}
}

/**
 * Renders the sidebar for each plugin
 * @return void
 */
function pluginSidebar()
{
	global $pluginpath;
	foreach (plugin_plugins() as $plugin) {
		if (file_exists($pluginpath."/".$plugin."/sidebar.php")) {
			require_once($pluginpath."/".$plugin."/sidebar.php");
		}
	}
}

/**
 * Returns all installed plugins
 * @return array
 */
function plugin_plugins()
{
	global $pluginpath;
	$plugins = array();
	if (file_exists($pluginpath)) {
		$files = scandir($pluginpath);
		foreach ($files as $file) {
			if ($file == ".") continue;
			if ($file == "..") continue;
			$filePath = $pluginpath . '/' . $file;
			if (is_dir($filePath)) {
				$plugins[] = $file;
			}
		}
	}
	return $plugins;
}
