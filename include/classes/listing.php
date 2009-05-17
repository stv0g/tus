<?php

class listing {
	private $results;
	private $query_time;
	private $query;
	private $season;
	private $cat_id;
	private $editor_id;
	private $type;
	private $sort;
	private $start;
	private $count;
	private $result;

	function __construct($query = '', $season = 0, $editor_id = 0, $cat_id = 0, $type = array('home', 'outwards', 'article', 'tournament', 'preview'), $count = 0, $start = 0, $sort = 'date', $order = 'desc' , $bool = true) {
		global $connection;
		global $head;
		global $config;
		global $site;

		$head['css_listing'] = '<link rel="stylesheet" type="text/css" href="' . $site['path']['web'] . '/include/template/' . $config['site']['template'] . '/css/listing.css" />';
		$head['script_listing'] = '<script type="text/javascript" src="' . $site['path']['web'] . '/include/javascript/listing.js"></script>';
		
		$this->query = stripslashes(rawurldecode($query));
		$this->season = (int) $season;
		$this->editor_id = (int) $editor_id;
		$this->cat_id = (int) $cat_id;
		$this->type = $type;
		$this->start = (int) $start;
		$this->count = (int) $count;
		$this->sort = $sort;
		$this->order = $order;
		$this->boolean = (boolean) $bool;
		
		$sql = 'SELECT * FROM (
							SELECT
								articles.id AS id,
								articles.type AS type,
								articles.title AS title,
								articles.text AS text,
								categories.id AS cat_id,
								categories.name AS cat_name,
								categories.season AS season,
								articles.editor_id AS editor_id,
								CONCAT_WS(\' \', users.prename, users.lastname) AS editor_name,
								users.mail AS editor_mail,
								NULL AS editor_web,
								NULL AS editor_city,
								UNIX_TIMESTAMP(articles.date) AS date,
								DATE_FORMAT(articles.date, GET_FORMAT(DATE,\'EUR\')) AS date_formated,
								DATE_FORMAT(articles.last_update, \'%d.%m.%Y %H:%i:%s\') AS last_update_formatted,
								UNIX_TIMESTAMP(articles.last_update) AS last_update,
								NULL AS gal_id,
								NULL AS gal_name,
								NULL AS gal_path,
								NULL AS pic_file,
								articles.view_count AS view_count
								' . (($this->query) ? ', 1.3 * MATCH (articles.title, articles.text) AGAINST (\'' . mysql_real_escape_string($this->query) . '\'' . ($this->boolean ? ' IN BOOLEAN MODE' : '') . ') AS score' : '') . '
							FROM articles
							LEFT JOIN categories ON categories.id = articles.cat_id
							LEFT JOIN users ON users.id = articles.editor_id'
							. (($this->query) ? ' WHERE MATCH (articles.title, articles.text) AGAINST (\'' . mysql_real_escape_string($this->query) . '\'' . ($this->boolean ? ' IN BOOLEAN MODE' : '') . ')' : '') . '
						UNION
							SELECT
								gbook.id AS id,
								\'gbook\' AS type,
								NULL AS title,
								gbook.text AS text,
								NULL AS cat_id,
								NULL AS cat_name,
								NULL AS season,
								NULL AS editor_id,
								gbook.name AS editor_name,
								gbook.mail AS editor_mail,
								gbook.web AS editor_web,
								gbook.city AS editor_city,
								UNIX_TIMESTAMP(gbook.date) AS date,
								DATE_FORMAT(gbook.date, \'%d.%m.%Y %H:%i:%s\') AS date_formated,
								DATE_FORMAT(gbook.last_update, \'%d.%m.%Y %H:%i:%s\') AS last_update_formatted,
								UNIX_TIMESTAMP(gbook.last_update) AS last_update,
								NULL AS gal_id,
								NULL AS gal_name,
								NULL AS gal_path,
								NULL AS pic_file,
								NULL AS view_count
								' . (($this->query) ? ', 1.1 * MATCH (gbook.name, gbook.mail, gbook.web, gbook.city, gbook.text) AGAINST (\'' . mysql_real_escape_string($this->query) . '\'' . ($this->boolean ? ' IN BOOLEAN MODE' : '') . ') AS score' : '') . '
							FROM gbook'
							. (($this->query) ? ' WHERE MATCH (gbook.name, gbook.mail, gbook.web, gbook.city, gbook.text) AGAINST (\'' . mysql_real_escape_string($this->query) . '\'' . ($this->boolean ? ' IN BOOLEAN MODE' : '') . ')' : '') . '
						UNION
							SELECT
								pictures.id AS id,
								\'picture\' AS type,
								pictures.title AS title,
								pictures.description AS text,
								categories.id AS cat_id,
								categories.name AS cat_name,
								categories.season AS season,
								pictures.editor_id AS editor_id,
								CONCAT_WS(\' \', users.prename, users.lastname) AS editor_name,
								users.mail AS editor_mail,
								NULL AS editor_web,
								NULL AS editor_city,
								UNIX_TIMESTAMP(pictures.date) AS date,
								DATE_FORMAT(pictures.date, \'%d.%m.%Y %H:%i:%s\') AS date_formated,
								DATE_FORMAT(pictures.last_update, \'%d.%m.%Y %H:%i:%s\') AS last_update_formatted,
								UNIX_TIMESTAMP(pictures.last_update) AS last_update,
								picture_categories.id AS gal_id,
								picture_categories.name AS gal_name,
								picture_categories.path AS gal_path,
								pictures.full AS pic_file,
								pictures.view_count AS view_count
								' . (($this->query) ? ', 1.2 * MATCH (pictures.title, pictures.description) AGAINST (\'' . mysql_real_escape_string($this->query) . '\'' . ($this->boolean ? ' IN BOOLEAN MODE' : '') . ') AS score' : '') . '
							FROM pictures
							LEFT JOIN picture_categories ON picture_categories.id = pictures.gal_id
							LEFT JOIN categories ON categories.id = picture_categories.cat_id
							LEFT JOIN users ON users.id = pictures.editor_id'
							. (($this->query) ? ' WHERE MATCH (pictures.title, pictures.description) AGAINST (\'' . mysql_real_escape_string($this->query) . '\'' . ($this->boolean ? ' IN BOOLEAN MODE' : '') . ' )' : '') . '
						) temp
				WHERE';
		if ($this->season)
			$sql .= ' season = ' . $this->season . ' AND';
		if ($this->cat_id)
			$sql .= ' cat_id = ' . $this->cat_id . ' AND';
		if ($this->editor_id)
			$sql .= ' editor_id = ' . $this->editor_id . ' AND';
		
		$sql .= ' (';
		foreach ($this->type as $type) {
			$sql .= 'type = \'' . mysql_real_escape_string($type) . '\' OR ';
		}
		$sql .= ' FALSE)';
		if ($this->sort) {
			$sql .= ' ORDER BY ' . mysql_real_escape_string($this->sort);
			if ($this->order)
				$sql .= ' ' . strtoupper(mysql_real_escape_string($this->order));
		}
		
		if ($this->count > 0) {
			$sql .= ' LIMIT ' . $this->count;
			if ($this->start > 0)
				$sql .= ' OFFSET ' . $this->start;
		}
		
		$start = microtime(true);
		$this->result = mysql_query($sql, $connection);
		$end = microtime(true);
		$this->query_time = $end - $start;
		$this->results = mysql_num_rows($this->result);
		
		echo mysql_error();
		
	}

	function get_html($details = true, $extras = false) {
		global $connection;
		global $site;
		global $config;

		if ($this->results > 0) {
			$results = '';
			$escaped_query = escape_search_words($this->query);
			
			if(!empty($details))
				$header = '<tr><td class="column_title">{lang:general:title} / {lang:general:type} / {lang:general:category}</td><td class="column_title">{lang:general:season} / {lang:general:date}</td><td class="column_title">{lang:general:editor}</td>';
			else
				$header = '<tr><td class="column_title">{lang:general:date}</td><td class="column_title">{lang:general:type}</td><td class="column_title">{lang:general:title}</td>';
				
			while ($line = mysql_fetch_array($this->result)) {
				if ($line['type'] == 'home' || $line['type'] == 'outwards' || $line['type'] == 'article' || $line['type'] == 'tournament') {
					if(!empty($details)) {
						$results .= '<tr><td colspan="4"><a target="_blank" href="' . $site['path']['web'] . '/index.php?module=article&amp;id=' . $line['id'] . '&amp;cat_id=' . $line['cat_id'] . ((empty($this->query)) ? '' : '&amp;hl=' . rawurlencode($this->query)) . '">' . hl($line['title'], escape_search_words($this->query)) . '</a></td></tr>';
						$results .= '<tr class="listing_result_bottom"><td>{icon:' . $config['types'][$line['type']] . ':{lang:type:' . $line['type'] . '}} <a href="' . $site['path']['web'] . '/index.php?cat_id=' . $line['cat_id'] . '"  onclick="getElementById(\'search_cat_id\').value = ' . $line['cat_id'] . '; window.search(); return false;">' . $line['cat_name'] . '</a></td><td><a href="' . $site['path']['web'] . '/index.php?season=' . $line['season'] . '" onclick="getElementById(\'search_season\').value = ' . $line['season'] . '; window.search(); return false;">' . $line['season'] . '/' . ($line['season'] + 1) . '</a> (' . $line['date_formated'] . ')</td><td><a href="' . $site['path']['web'] . '/index.php?module=contact&amp;usr_id=' . $line['editor_id'] . '" onclick="getElementById(\'editor_id\').value = ' . $line['editor_id'] . '; window.search(); return false;">' . $line['editor_name'] . '</a></td>';
					}
					else {
						$results .= '<tr><td>' . $line['date_formated'] . '</td><td>{icon:' . $config['types'][$line['type']] . ':{lang:type:' . $line['type'] . '}}</td><td><a href="' . $site['path']['web'] . '/index.php?module=article&amp;id=' . $line['id'] . '&amp;cat_id=' . $line['cat_id'] . ((empty($this->query)) ? '' : '&amp;hl=' . rawurlencode($this->query)) . '">' . hl(stripslashes($line['title']), escape_search_words($this->query)) . '</a></td>';
					}
					
					if(!empty($extras)) {
						$rights = access($site['usr']['id'], 'article', $line['cat_id']);
						if($rights['del'] || $rights['edit']) {
							$show_extras = true;
							$results .= '<td>';
							if ($rights['del']) $results .= '<a onclick="return confirm(\'{lang:article:confirm_del}\')" href="' . $site['path']['web'] . '/index.php?module=article&amp;command=del&amp;cat_id=' . $line['cat_id'] . '&amp;id=' . $line['id'] . '">{icon:delete:{lang:general:del}}</a>';
							if ($rights['edit']) $results .= '<a href="' . $site['path']['web'] . '/index.php?module=article&amp;command=edit&amp;cat_id=' . $line['cat_id'] . '&amp;id=' . $line['id'] . '">{icon:pencil:{lang:general:edit}}</a>';
							$results .= '</td>';
						}
					}
				}
				elseif ($line['type'] == 'gbook') {
					if (stripos($line['text'], $escaped_query[0])) {
						$pos = stripos($line['text'], $escaped_query[0]);
						echo $pos . ' ';
						$start = $pos - 50;
						$length = 50;
					}
					else {
						$start = 0;
						$length = 100;
					}
					
					if (!empty($details)) {
						$results .= '<tr><td colspan="4"><a target="_blank" href="' . $site['path']['web'] . '/index.php?module=gbook' . ((empty($this->query)) ? '' : '&amp;hl=' . rawurlencode($this->query)) . '#' . $line['id'] .'">... ' . hl(htmlspecialchars(subwords($line['text'], $start, $length)), escape_search_words($this->query)) . ' ...</a></td></tr>';
						$results .= '<tr class="listing_result_bottom"><td>{icon:' . $config['types'][$line['type']] . ':{lang:type:' . $line['type'] . '}} {lang:type:' . $line['type'] . '}</td><td>' . $line['date_formated'] . '</td><td><a href="mailto:' . $line['editor_mail'] . '">' . hl(htmlspecialchars($line['editor_name']), escape_search_words($this->query)) . '</a></td>';
					}
					
					if(!empty($extras)) {
						$rights = access($site['usr']['id'], 'gbook', $line['cat_id']);
						if($rights['del'] || $rights['edit']) {
							$show_extras = true;
							$results .= '<td>';
							if ($rights['del']) $results .= '<a onclick="return confirm(\'{lang:gbook:confirm_del}\')" href="' . $site['path']['web'] . '/index.php?module=gbook&amp;command=del&amp;id=' . $line['id'] . '">{icon:delete:{lang:general:del}}</a>';
							if ($rights['edit']) $results .= '<a href="' . $site['path']['web'] . '/index.php?module=gbook&amp;command=edit&amp;cat_id=' . $line['cat_id'] . '&amp;id=' . $line['id'] . '">{icon:pencil:{lang:general:edit}}</a>';
							$results .= '</td>';
						}
					}
				}
				elseif ($line['type'] == 'picture') {
					if (!empty($details)) {
						$results .= '<tr><td colspan="4"><a target="_blank" href="' . $site['path']['web'] . '/modules/gallery/picture.php?id=' . $line['id'] . '">' . hl($line['title'], escape_search_words($this->query)) . '</a></td></tr>';
						$results .= '<tr class="listing_result_bottom"><td>{icon:' . $config['types'][$line['type']] . ':{lang:type:' . $line['type'] . '}} <a href="' . $site['path']['web'] . '/index.php?cat_id=' . $line['cat_id'] . '" onclick="getElementById(\'search_cat_id\').value = ' . $line['cat_id'] . '; window.search(); return false;">' . $line['cat_name'] . '</a></td><td><a href="' . $config['weppath'] . '/index.php?season=' . $line['season'] . '" onclick="getElementById(\'search_season\').value = ' . $line['season'] . '; window.search(); return false;">' . $line['season'] . '/' . ($line['season'] + 1) . '</a> (' . $line['date_formated'] . ')</td><td><a href="' . $site['path']['web'] . '/index.php?module=contact&amp;usr_id=' . $line['editor_id'] . '" onclick="getElementById(\'editor_id\').value = ' . $line['editor_id'] . '; window.search(); return false;">' . $line['editor_name'] . '</a></td>';
					}
				}
				
				$results .= '</tr>';
			}
			
			if($extras)
				if($show_extras)
					$header .= '<td class="column_title">{lang:general:extras}</td>';
					
			$header .= '</tr>';	
					
			$return = '<table id="listing_results_table">' . $header . $results .  '</table>';
					
			if(!empty($details)) $return .= '<div id="listing_details">' . $this->results . ' {lang:search:results} - {lang:search:query_time}: ' . round(($this->query_time), 4) . ' {lang:general:seconds}</div>';
		}
		else
		$return = '<div class="error">{lang:search:no_results}</div>';

		return $return;
	}
	
	function get_newsfeed() {
		global $config;
		global $site;
		
		$search_result = '<?xml version="1.0" encoding="utf-8"?>
			<rss version="2.0">
				<channel>
					<language>' . lang('general', 'page_language') . '</language>
					<docs>http://blogs.law.harvard.edu/tech/rss</docs>
					<copyright><![CDATA[' . lang('general', 'page_copy') . ']]></copyright>
					<managingEditor>' . $config['mail']['tus'] . '</managingEditor>
					<webMaster>' . $config['mail']['admin'] . '</webMaster>';
		
		if ($cat_id != 0) {
			$search_result .= '<title><![CDATA[' . $config['site']['name'] . ': ' . $cat_name . ']]></title>
			<link>http://' . $_SERVER['HTTP_HOST'] . $site['path']['web'] . '/index.php?cat_id=' . $cat_id . '</link>
			<description><![CDATA[' . lang('general', 'page_description') . ']]></description>
			<image>
				<title><![CDATA[' . lang('general', 'newsfeed_image_title') . ']]></title>
				<url>http://' . $config['newsfeed']['image_url'] . '</url>
				<link>http://' . $_SERVER['HTTP_HOST'] . $site['path']['web'] . '/index.php?cat_id=' . $cat_id . '</link>
			</image>';
		}
		else {
			$search_result .= '<title><![CDATA[' . $config['site']['name'] . ']]></title>
			<link>http://' . $_SERVER['HTTP_HOST'] . $site['path']['web'] . '/index.php</link>
			<description><![CDATA[' . lang('general', 'page_description') . ']]></description>
			<image>
				<title><![CDATA[' . lang('general', 'newsfeed_image_title') . ']]></title>
				<url>http://' . $config['newsfeed']['image_url'] . '</url>
				<link>http://' . $_SERVER['HTTP_HOST'] . $site['path']['web'] . '/index.php</link>
			</image>';
		}
			
		while ($line = mysql_fetch_array($this->result)) {
			$search_result .= '<item>
									<title><![CDATA[' . $line['title'] . ']]></title>
									<link><![CDATA[http://' . $_SERVER['HTTP_HOST'] . $site['path']['web'] . '/index.php?cat_id=' . $line['cat_id'] . '&id=' . $line['id'] . ']]></link>
									<description><![CDATA[' . $line['text'] . ']]></description>
									<author><![CDATA[' . $line['editor_mail'] . ' (' . $line['editor_name'] . ')]]></author>
									<pubDate>' . date('D, d M Y H:i:s', $line['last_update']) . ' +0200</pubDate>
									<category domain="http://' . $_SERVER['HTTP_HOST'] . $site['path']['web'] . '/index.php?cat_id=' . $line['cat_id'] . '"><![CDATA[' . htmlentities($line['cat_name']) . ']]></category>
								</item>';
		}
		
		$search_result .= '</channel>
		</rss>';
		
		return $search_result;
	}

	function show_filters() {
		global $connection;
		global $site;
		global $config;
		
		echo '<div id="filter_container">
				<form accept-charset="utf-8" action="' . $site['path']['web'] . '/index.php?module=search" method="post" onsubmit="search(); return false;">
					<input id="search_query" type="text" value="' . $this->query . '" name="search_query" onkeyup="delay_search()" onblur="check_string(this, true)" /><br />
					<span style="display: none" class="error">{lang:general:invalid_string}<br /></span>';
					foreach ($config['types'] as $type => $icon)
						echo '<input onchange="search();" type="checkbox" name="type[]" value="' . $type . '" ' . (in_array($type, $this->type) ? 'checked="checked"' : '') . '" />{icon:' . $icon . ':{lang:type:' . $type .'}} {lang:type:' . $type . '}';
					echo '<br /><select id="search_season" name="season" size="1" onchange="search();"><option value="0">{lang:general:season}</option>';
						foreach (get_seasons() as $season)
							echo '<option ' . (($season == $this->season) ? 'selected="selected"' : '') . ' value="' . $season . '">' . $season . '/' . ($season + 1) . '</option>';
					echo '</select>
					<select id="cat_id" name="cat_id" size="1" onchange="search();"><option value="0">{lang:general:category}</option>';
						foreach (get_cats() as $category)
							echo '<option ' . (($category['id'] == $this->cat_id) ? 'selected="selected"' : '') . ' value="' . $category['id'] . '">' . htmlspecialchars($category['name']) . '</option>';
					echo '</select>
					<select id="editor_id" name="editor_id" size="1" onchange="search();"><option value="0">{lang:general:editor}</option>';
						foreach (get_users() as $user)
							echo '<option ' . (($user['id'] == $this->editor_id) ? 'selected="selected"' : '') . ' value="' . $user['id'] . '">' . $user['prename'] . ' ' . $user['lastname'] . '</option>';
					echo '</select>
					<select id="sort" name="sort" size="1" onchange="search();"><option value="0">{lang:general:sort}</option>
						<option ' . (($this->sort == 'view_count') ? 'selected="selected"' : '') . ' value="view_count">{lang:general:view_count}</option>
						<option ' . (($this->sort == 'date') ? 'selected="selected"' : '') . ' value="date">{lang:general:date}</option>
						<option ' . (($this->sort == 'last_update') ? 'selected="selected"' : '') . ' value="last_update">{lang:general:last_update}</option>
						<option ' . (($this->sort == 'score') ? 'selected="selected"' : '') . ' value="score">{lang:general:relevance}</option>
						<option ' . (($this->sort == 'title') ? 'selected="selected"' : '') . ' value="title">{lang:general:title}</option>
						<option ' . (($this->sort == 'cat_name') ? 'selected="selected"' : '') . ' value="cat_name">{lang:general:category}</option>
						<option ' . (($this->sort == 'editor_name') ? 'selected="selected"' : '') . ' value="editor_name">{lang:general:editor}</option>
						<option ' . (($this->sort == 'type') ? 'selected="selected"' : '') . ' value="type">{lang:general:type}</option>
						
					</select>
					<select id="order" name="order" size="1" onchange="search();" ><option value="0">{lang:general:order}</option>
						<option ' . (($this->order == 'desc') ? 'selected="selected"' : '') . ' value="desc">{lang:general:desc}</option>
						<option ' . (($this->order == 'asc') ? 'selected="selected"' : '') . ' value="asc">{lang:general:asc}</option>
					</select>
					<input value="{lang:search:do_search}" type="submit" />
					<a id="listing_newsfeed" href="modules/article/newsfeed.php?cat_id=' . $this->cat_id . '&amp;search_query=' . $this->query . '&amp;season=' . $this->season . '&amp;editor_id=' . $this->editor_id;
					foreach ($this->type as $type)
						echo '&amp;type[]=' . $type;
					echo '">{icon:feed:{lang:general:newsfeed}}</a>
				</form>
			</div>';
	}
}
?>