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

/**
 * Wiki macro class for linking footnotes
 */
class FootNoteMacro extends WikiMacro
{
	/**
	 * Returns description of macro, use, and accepted arguments
	 * 
	 * @return     array
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = 'Add a footnote, or explicitly display collected footnotes when no args (footnote text) are given.';
		$txt['html'] = '<p>Add a footnote, or explicitly display collected footnotes when no args (footnote text) are given.</p>';
		return $txt['html'];
	}

	/**
	 * Generate macro output
	 * 
	 * @return     string
	 */
	public function render()
	{
		static $wm;

		if (!is_object($wm)) 
		{
			$wm = new stdClass();
			$wm->footnotes = array();
			$wm->footnotes_notes = array();
		}

		$note = $this->args;

		$fn    = $wm->footnotes;
		$notes = $wm->footnotes_notes;

		if ($note) 
		{
			// Build and return the link
			if (!isset($notes)) 
			{
				$notes = array();
			}
			if (!isset($fn)) 
			{
				$fn = array();
			}

			$p = new WikiParser('Footnotes', $this->option, $this->scope, $this->pagename, $this->pageid, $this->filepath, $this->domain);

			$note = $p->parse(trim($note));

			$wm->footnotes_count++;

			if (in_array($note,$notes)) 
			{
				$i = array_search($note, $notes) + 1;
				$k = $wm->footnotes_count;

				return '<sup><a name="fndef-' . $k . '"></a><a href="'.JRoute::_('index.php?option=' . $this->option . '&scope=' . $this->scope . '&pagename=' . $this->pagename) . '#fnref-' . $i . '">&#91;' . $i . '&#93;</a></sup>';
			}

			$i = count($fn) + 1;
			$notes[] = $note;
			$fn[] = '<li><p><a name="fnref-' . $i . '"></a>' . substr($note, 3) . '</li>';

			$wm->footnotes_notes = $notes;
			$wm->footnotes = $fn;

			return '<sup><a name="fndef-' . $i . '"></a><a href="' . JRoute::_('index.php?option=' . $this->option . '&scope=' . $this->scope . '&pagename=' . $this->pagename).'#fnref-' . $i . '">&#91;' . $i . '&#93;</a></sup>';
		} 
		else 
		{
			$html  = '<ol class="footnotes">';
			$html .= implode("\n", $wm->footnotes);
			$html .= '</ol>';
			
			$wm = null;

			return $html;
		}
	}
}

