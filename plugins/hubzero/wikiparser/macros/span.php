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
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'SpanMacro'
 * 
 * Long description (if any) ...
 */
class SpanMacro extends WikiMacro
{

	/**
	 * Short description for 'description'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     mixed Return description (if any) ...
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = "Wraps text or other elements inside a `<span>` tag.";
		$txt['html'] = "<p>Wraps text or other elements inside a <code>&lt;span&gt;</code> tag.</p>";
		return $txt['html'];
	}

	/**
	 * Short description for 'render'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     string Return description (if any) ...
	 */
	public function render()
	{
		$et = $this->args;

		if (!$et) {
			return '';
		}

		$attribs = explode(',', $et);
		$text = array_shift($attribs);

		$atts = array();
		if (!empty($attribs) && count($attribs) > 0) {
			foreach ($attribs as $a)
			{
				$a = preg_split('#=#',$a);
				$key = $a[0];
				$val = end($a);

				$atts[] = $key.'="'.$val.'"';
			}
		}

		$span  = '<span';
		$span .= (!empty($atts)) ? ' '.implode(' ',$atts).'>' : '>';
		$span .= trim($text).'</span>';

		return $span;
	}
}

