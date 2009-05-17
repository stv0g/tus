<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * TuS Griesheim Handball CMS
 *
 * Code file for search module
 *
 * @category	Module
 * @name		access.php
 * @author		Steffen Vogel <info@steffenvogel.de>
 * @copyright	2006-2008 Steffen Vogel
 * @license		http://creativecommons.org/licenses/by-nc-nd/2.0/de/
 * @link		http://tusgriesheim.de/handball/
 * @since		File available since 21.11.2007 
 */

$rights = access($usr_id, 'search', $cat_id);
	
echo '<div id="search"><span class="title">{lang:general:search}</span>';

if ($rights['show']) {
	if (empty($_REQUEST['search_query']) && empty($_REQUEST['type']) && empty($_REQUEST['editor_id']) && empty($_REQUEST['season']) && empty($_REQUEST['cat_id'])) {
		$first = true;
		$type = $config['search']['types'];
		$order = 'desc';
		$sort = 'date';
	}
	else {
		$first = false;
		$type = $_REQUEST['type'];
		$order = $_REQUEST['order'];
		$sort = $_REQUEST['sort'];
	}
	
	$search = new listing(stripslashes($_REQUEST['search_query']), $_REQUEST['season'], $_REQUEST['editor_id'], $_REQUEST['cat_id'], $type, 0, 0, $sort, $order);
	$search->show_filters();
			
	echo '<div id="search_results">';
	if ($first)
		echo '{lang:search:help}';	
	else
		echo $search->get_html(true, false);
	echo '</div>';
}			
else no_rights();

echo '</div>';
?>