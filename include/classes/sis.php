<?php

class sis {
	private $cat_id;
	private $type;
	public $last_update;
	public $last_update_formatted;
	public $liga;
	
	function get_xml() {
		global $site;
		
		$result = mysql_query('SELECT id, type, xml, DATE_FORMAT(last_update, \'%d.%m.%Y %H:%i:%s\') AS last_update_formatted, UNIX_TIMESTAMP(last_update) AS last_update FROM sis_cache WHERE cat_id = ' . $this->cat_id . ' AND type = ' . $this->type . ' LIMIT 1', $site['db']['connection']);
		$line = mysql_fetch_assoc($result);
		$this->last_update = $line['last_update'];
		$this->last_update_formatted = $line['last_update_formatted'];
		return simplexml_load_string($line['xml']);
	}
	
	function __construct($cat_id, $type = 4) {
		global $site;
		global $config;
		
		$this->cat_id = (int) $cat_id;
		$this->type = (int) $type;
		
		$result = mysql_query('SELECT sis_liga FROM categories WHERE id = ' . $this->cat_id . ' LIMIT 1', $site['db']['connection']);
		$line = mysql_fetch_assoc($result);
		$this->liga = $line['sis_liga'];
		
		$result = mysql_query('SELECT DATE_FORMAT(last_update, \'%d.%m.%Y %H:%i:%s\') AS last_update_formatted, UNIX_TIMESTAMP(last_update) AS last_update FROM sis_cache WHERE cat_id = ' . $this->cat_id . ' AND type = ' . $this->type . ' LIMIT 1', $site['db']['connection']);
		$line = mysql_fetch_assoc($result);
		
		if (mysql_num_rows($result) == 1) {
			$this->last_update = $line['last_update'];
			$this->last_update_formatted = $line['last_update_formatted'];
		}
		elseif (mysql_num_rows($result) > 1) {
			mysql_query('DELETE FROM sis_cache WHERE id = ' . $this->cat_id, $site['db']['connection']);
		}
		
		if (mysql_num_rows($result) != 1) {
			mysql_query('INSERT INTO sis_cache (cat_id, type) VALUES(' . $this->cat_id . ', ' . $this->type . ')', $site['db']['connection']);
		}
		
		if ((time() - $this->last_update) >= $config['sis']['update'])
			$this->update();
	}
	
	function update() {
		global $config;
		global $site;
		
		$url = 'http://web1.sis-handball.de/xmlexport/xml_dyn.aspx?art=' . $this->type . '&auf=' . $this->liga . '&user=' . $config['sis']['user'] . '&pass=' . $config['sis']['pw'];
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_FILETIME, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$content = curl_exec($ch);
		curl_close($ch);
		$doc = new DOMDocument;
		if ($doc->loadXML($content)) {
			$sql = 'UPDATE sis_cache SET xml = \'' . $doc->saveXml() . '\', type = ' . $this->type . ', cat_id = ' . $this->cat_id . ' WHERE cat_id = ' . $this->cat_id . ' AND type = ' .  $this->type;
			mysql_query ($sql, $site['db']['connection']);
		}
		else {
			trigger_error('Wrong SiS-XML syntax', E_USER_ERROR);
		}
	}
	
	function get_rank() {
		global $config;
		
		$xml = $this->get_xml();
		
		if (!empty($xml)) {
			if ($this->type == 4) {
				foreach ($xml->Platzierung as $rank) {
					if (strpos($rank->Name, $config['sis']['name']) !== false) {
						return $rank->Nr;
					}
				}
			}
			else {
				trigger_error('Invalid SIS type for ranking' , E_USER_NOTICE);
			}
		}
		else {
			trigger_error('No SiS data available', E_USER_NOTICE);
		}
	}
}
?>