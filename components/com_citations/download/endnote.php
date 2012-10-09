<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

include_once(JPATH_ROOT . DS . 'components' . DS . 'com_citations' . DS . 'download' . DS . 'abstract.php');

/**
 * Citations download class for Endnote format
 */
class CitationsDownloadEndnote extends CitationsDownloadAbstract
{
	/**
	 * Mime type
	 * 
	 * @var string
	 */
	protected $_mime = 'application/x-endnote-refer';

	/**
	 * File extension
	 * 
	 * @var string
	 */
	protected $_extension = 'enw';

	/**
	 * Format the file
	 * 
	 * @param      object $row Record to format
	 * @return     string
	 */
	public function format($row)
	{
		//get fields to not include for all citations
		$config = JComponentHelper::getParams('com_citations');
		$exclude = $config->get('citation_download_exclude', '');
		if (strpos($exclude,",") !== false)
		{
			$exclude = str_replace(',', "\n", $exclude);
		}
		$exclude = array_values(array_filter(array_map('trim', explode("\n", $exclude))));

		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}

		//get fields to not include for specific citation
		$cparams = new $paramsClass($row->params);
		$citation_exclude = $cparams->get('exclude', '');
		if (strpos($citation_exclude, ',') !== false)
		{
			$citation_exclude = str_replace(',', "\n", $citation_exclude);
		}
		$citation_exclude = array_values(array_filter(array_map('trim', explode("\n", $citation_exclude))));

		//merge overall exclude and specific exclude
		$exclude = array_values(array_unique(array_merge($exclude, $citation_exclude)));

		//var to hold document conetnt
		$doc = '';

		//get all the citation types
		$db =& JFactory::getDBO();
		$ct = new CitationsType($db);
		$types = $ct->getType();

		$type = '';
		foreach($types as $t) 
		{
			if ($t['id'] == $row->type) 
			{
				$type = $t['type_title'];
			}
		}

		//set the type to generic if we dont have one
		$type = ($type != '') ? $type : 'Generic';

		//set the type
		$doc .= "%0 {$type}" . "\r\n";

		if ($row->booktitle && !in_array('booktitle', $exclude)) 
		{
			$bt = html_entity_decode($row->booktitle);
			$bt = (!preg_match('!\S!u', $bt)) ? utf8_encode($bt) : $bt;
			$doc .= "%B " . $bt . "\r\n";
		}
		if ($row->journal && !in_array('journal', $exclude)) 
		{
			$j = html_entity_decode($row->journal);
			$j = (!preg_match('!\S!u', $j)) ? utf8_encode($j) : $j;
			$doc .= "%J " . $j . "\r\n";
		}
		if ($row->year && !in_array('year', $exclude))
		{
			$doc .= "%D " . trim($row->year) . "\r\n";
		}
		if ($row->title && !in_array('title', $exclude))
		{
			$t = html_entity_decode($row->title);
			$t = (!preg_match('!\S!u', $t)) ? utf8_encode($t) : $t;
			$doc .= "%T " . $t . "\r\n";
		}
		if (!in_array('authors', $exclude))
		{
			$author = html_entity_decode($row->author);
			$author = (!preg_match('!\S!u', $author)) ? utf8_encode($author) : $author;
			
			$author_array = explode(';', stripslashes($author));
			foreach ($author_array as $auth)
			{
				$auth = preg_replace('/{{(.*?)}}/s', '', $auth);
				if (!strstr($auth, ',')) 
				{
					$bits = explode(' ', $auth);
					$n = array_pop($bits) . ', ';
					$bits = array_map('trim', $bits);
					$auth = $n.trim(implode(' ', $bits));
				}
				$doc .= "%A " . trim($auth) . "\r\n";
			}
		}
		if ($row->address && !in_array('address', $exclude))
		{
			$doc .= "%C " . htmlspecialchars_decode(trim(stripslashes($row->address))) . "\r\n";
		}
		if ($row->editor && !in_array('editor', $exclude)) 
		{
			$editor = html_entity_decode($row->editor);
			$editor = (!preg_match('!\S!u', $editor)) ? utf8_encode($editor) : $editor;
			
			$author_array = explode(';', stripslashes($editor));
			foreach ($author_array as $auth)
			{
				$doc .= "%E " . trim($auth) . "\r\n";
			}
		}
		if ($row->publisher && !in_array('publisher', $exclude)) 
		{
			$p = html_entity_decode($row->publisher);
			$p = (!preg_match('!\S!u', $p)) ? utf8_encode($p) : $p;
			$doc .= "%I " . $p . "\r\n";
		}
		if ($row->number && !in_array('number', $exclude))
		{
			$doc .= "%N " . trim($row->number) . "\r\n";
		}
		if ($row->pages && !in_array('pages', $exclude))
		{
			$doc .= "%P " . trim($row->pages) . "\r\n";
		}
		if ($row->url && !in_array('url', $exclude))
		{
			$doc .= "%U " . trim($row->url) . "\r\n";
		}
		if ($row->volume && !in_array('volume', $exclude))
		{
			$doc .= "%V " . trim($row->volume) . "\r\n";
		}
		if ($row->note && !in_array('note', $exclude))
		{
			$n = html_entity_decode($row->note);
			$n = (!preg_match('!\S!u', $n)) ? utf8_encode($n) : $n;
			$doc .= "%Z " . $n . "\r\n";
		}
		if ($row->edition && !in_array('edition', $exclude))
		{
			$doc .= "%7 " . trim($row->edition) . "\r\n";
		}
		if ($row->month && !in_array('month', $exclude))
		{
			$doc .= "%8 " . trim($row->month) . "\r\n";
		}
		if ($row->isbn && !in_array('isbn', $exclude))
		{
			$doc .= "%@ " . trim($row->isbn) . "\r\n";
		}
		if ($row->doi && !in_array('doi', $exclude))
		{
			$doc .= "%1 " . trim($row->doi) . "\r\n";
		}
		if ($row->keywords && !in_array('keywords', $exclude))
		{
			$k = html_entity_decode($row->keywords);
			$k = (!preg_match('!\S!u', $k)) ? utf8_encode($k) : $k;
			$doc .= "%K " . $k . "\r\n";
		}
		if ($row->research_notes && !in_array('research_notes', $exclude))
		{
			$rn = html_entity_decode($row->research_notes);
			$rn = (!preg_match('!\S!u', $rn)) ? utf8_encode($rn) : $rn;
			$doc .= "%< " . $rn . "\r\n";
		}
		if ($row->abstract && !in_array('abstract', $exclude))
		{
			$a = html_entity_decode($row->abstract);
			$a = (!preg_match('!\S!u', $a)) ? utf8_encode($a) : $a;
			$doc .= "%X " . $a . "\r\n";
		}
		if ($row->label && !in_array('label', $exclude))
		{
			$l = html_entity_decode($row->label);
			$l = (!preg_match('!\S!u', $l)) ? utf8_encode($l) : $l;
			$doc .= "%F " . $label . "\r\n";
		}
		if ($row->language && !in_array('language', $exclude))
		{
			$lan = html_entity_decode($row->language);
			$lan = (!preg_match('!\S!u', $lan)) ? utf8_encode($lan) : $lan;
			$doc .= "%G " . $lan . "\r\n";
		}
		if ($row->author_address && !in_array('author_address', $exclude))
		{
			$aa = html_entity_decode($row->author_address);
			$aa = (!preg_match('!\S!u', $aa)) ? utf8_encode($aa) : $aa;
			$doc .= "%+ " . $aa . "\r\n";
		}
		if ($row->accession_number && !in_array('accession_number', $exclude))
		{
			$an = html_entity_decode($row->accession_number);
			$an = (!preg_match('!\S!u', $an)) ? utf8_encode($an) : $an;
			$doc .= "%M " . trim($an) . "\r\n";
		}
		if ($row->call_number && !in_array('callnumber', $exclude))
		{
			$doc .= "%L " . trim($row->call_number) . "\r\n";
		}
		if ($row->short_title && !in_array('short_title', $exclude))
		{
			$st = html_entity_decode($row->short_title);
			$st = (!preg_match('!\S!u', $st)) ? utf8_encode($st) : $st;
			$doc .= "%! " . htmlspecialchars_decode(trim($st)) . "\r\n";
		}
		$doc .= "\r\n";
		return $doc;
	}
}

