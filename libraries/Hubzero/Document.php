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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.document.document');

/**
 * Class for adding stylesheets from components, modules, and plugins to the document
 *
 * @package       hubzero-cms
 * @author        Shawn Rice <zooley@purdue.edu>
 * @copyright     Copyright 2005-2011 Purdue University. All rights reserved.
 * @license       http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */
class Hubzero_Document
{
	/**
	 * Adds a linked stylesheet from a component to the page
	 *
	 * @param	string  $component  Component name
	 * @param	string  $stylesheet Stylesheet name (optional, uses component name if left blank)
	 * @param	string  $type       Mime encoding type
	 * @param	string  $media      Media type that this stylesheet applies to
	 * @param	string  $attribs    Attributes to add to the link
	 * @return  void
	 */
	public static function addComponentStylesheet($component, $stylesheet = '', $type = 'text/css', $media = null, $attribs = array())
	{
		$mainframe =& JFactory::getApplication();

		$jdocument = &JFactory::getDocument();

		$template  = $mainframe->getTemplate();

		if (empty($stylesheet)) 
		{
			$stylesheet = substr($component, 4) . '.css';
		}

		$templatecss = DS . 'templates' . DS . $template . DS . 'html' . DS . $component . DS . $stylesheet;

		$assetcss = DS . 'components' . DS . $component . DS . 'assets' . DS . 'css' . DS . $stylesheet;

		$componentcss = DS . 'components' . DS . $component . DS . $stylesheet;

		if (file_exists(JPATH_SITE . $templatecss)) 
		{
			// Chech for CSS in /templates/$template/html/$component/
			$jdocument->addStyleSheet($templatecss . '?v=' . filemtime(JPATH_SITE . $templatecss), $type, $media, $attribs);
		}
		else if (file_exists(JPATH_SITE . $assetcss)) 
		{
			// Chech for CSS in /components/$component/assets/css/
			$jdocument->addStyleSheet($assetcss . '?v=' . filemtime(JPATH_SITE . $assetcss), $type, $media, $attribs);
		} 
		else if (file_exists(JPATH_SITE . $componentcss)) 
		{
			// Chech for CSS in /components/$component/
			$jdocument->addStyleSheet($componentcss . '?v=' . filemtime(JPATH_SITE . $componentcss), $type, $media, $attribs);
		}
	}

	/**
	 * Adds a linked script from a component to the page
	 *
	 * @param   string  $component  URL to the linked script
	 * @param	string  $script     Script name (optional, uses module name if left blank)
	 * @param   string  $type       Type of script. Defaults to 'text/javascript'
	 * @param   bool    $defer      Adds the defer attribute.
	 * @param   bool    $async      Adds the async attribute.
	 * @return  void
	 */
	public static function addComponentScript($component, $script = '', $type = "text/javascript", $defer = false, $async = false)
	{
		$mainframe = JFactory::getApplication();

		$jdocument = JFactory::getDocument();

		$template  = $mainframe->getTemplate();

		if (empty($script)) 
		{
			$script = substr($component, 4);
		}

		$base = DS . 'components' . DS . $component;

		$url = $base . DS . $script . '.js';
		$urlAlt = '';
		if (JPluginHelper::isEnabled('system', 'jquery'))
		{
			if (file_exists(JPATH_SITE . $base . DS . $script . '.jquery.js')) 
			{
				$urlAlt .= $base . DS . $script . '.jquery.js';
			}
		}

		if ($urlAlt && file_exists(JPATH_SITE . $urlAlt)) 
		{
			$jdocument->addScript($urlAlt . '?v=' . filemtime(JPATH_SITE . $urlAlt), $type, $defer, $async);
		} 
		else if (file_exists(JPATH_SITE . $url)) 
		{
			$jdocument->addScript($url . '?v=' . filemtime(JPATH_SITE . $url), $type, $defer, $async);
		}
	}

	/**
	 * Adds a linked stylesheet from the system to the page
	 *
	 * @param	string  $stylesheet Stylesheet name
	 * @param	string  $type       Mime encoding type
	 * @param	string  $media      Media type that this stylesheet applies to
	 * @param	string  $attribs    Attributes to add to the link
	 * @return  void
	 */
	public static function addSystemStylesheet($stylesheet, $type = 'text/css', $media = null, $attribs = array())
	{
		if (!$stylesheet)
		{
			return;
		}

		$mainframe =& JFactory::getApplication();

		$jdocument = &JFactory::getDocument();

		$template  = $mainframe->getTemplate();

		$templatecss = DS . 'templates' . DS . $template . DS . 'html' . DS . 'system' . DS . 'css' . DS . $stylesheet;

		$systemcss = DS . 'media' . DS . 'system' . DS . 'css' . DS . $stylesheet;

		if (file_exists(JPATH_SITE . $templatecss)) 
		{
			$jdocument->addStyleSheet($templatecss . '?v=' . filemtime(JPATH_SITE . $templatecss), $type, $media, $attribs);
		} 
		else 
		{
			$jdocument->addStyleSheet($systemcss . '?v=' . filemtime(JPATH_SITE . $systemcss), $type, $media, $attribs);
		}
	}

	/**
	 * Adds a linked script from the system to the page
	 *
	 * @param	string  $script     Script name (optional, uses module name if left blank)
	 * @param   string  $type       Type of script. Defaults to 'text/javascript'
	 * @param   bool    $defer      Adds the defer attribute.
	 * @param   bool    $async      Adds the async attribute.
	 * @return  void
	 */
	public static function addSystemScript($script, $type = 'text/javascript', $defer = false, $async = false)
	{
		if (!$script)
		{
			return;
		}

		$mainframe = JFactory::getApplication();

		$jdocument = JFactory::getDocument();

		$template  = $mainframe->getTemplate();

		$base = DS . 'media' . DS . 'system' . DS . 'js';

		$url = $base . DS . $script . '.js';
		$urlAlt = '';
		if (JPluginHelper::isEnabled('system', 'jquery'))
		{
			if (file_exists(JPATH_SITE . $base . DS . $script . '.jquery.js')) 
			{
				$urlAlt .= $base . DS . $script . '.jquery.js';
			}
		}

		if ($urlAlt && file_exists(JPATH_SITE . $urlAlt)) 
		{
			$jdocument->addScript($urlAlt . '?v=' . filemtime(JPATH_SITE . $urlAlt), $type, $defer, $async);
		} 
		else if (file_exists(JPATH_SITE . $url)) 
		{
			$jdocument->addScript($url . '?v=' . filemtime(JPATH_SITE . $url), $type, $defer, $async);
		}
	}

	/**
	 * Gets the path to a component image
	 * checks template overrides first, then component
	 *
	 * @param	string  $component	Component name
	 * @param	string  $image		Image to look for
	 * @return  string	Path to an image file
	 */
	public static function getComponentImage($component, $image)
	{
		$mainframe =& JFactory::getApplication();

		$template  = $mainframe->getTemplate();

		$templateimage = DS . 'templates' . DS . $template . DS . 'html' . DS . $component . DS . 'images' . DS . $image;

		$assetimage = DS . 'components' . DS . $component . DS . 'assets' . DS . 'img' . DS . $image;

		$componentimage = DS . 'components' . DS . $component . DS . 'images' . DS . $image;

		if (file_exists(JPATH_SITE . $templateimage)) 
		{
			return $templateimage;
		} 
		else if (file_exists(JPATH_SITE . $assetimage)) 
		{
			return $assetimage;
		}
		else 
		{
			return $componentimage;
		}
	}

	/**
	 * Gets the path to a component stylesheet
	 * checks template overrides first, then component
	 *
	 * @param	string  $component	Component name
	 * @param	string  $stylesheet	Stylesheet to look for
	 * @return  string	Path to a stylesheet
	 */
	public static function getComponentStylesheet($component, $stylesheet)
	{
		$mainframe =& JFactory::getApplication();

		$template  = $mainframe->getTemplate();

		$templatecss = DS . 'templates' . DS . $template . DS . 'html' . DS . $component . DS . 'css' . DS . $stylesheet;

		$assetcss = DS . 'components' . DS . $component . DS . 'assets' . DS . 'css' . DS . $stylesheet;

		$componentcss = DS . 'components' . DS . $component . DS . 'css' . DS . $stylesheet;

		if (file_exists(JPATH_SITE . $templatecss)) 
		{
			return $templatecss;
		} 
		else if (file_exists(JPATH_SITE . $assetcss)) 
		{
			return $assetcss;
		}
		else 
		{
			return $componentcss;
		}
	}

	/**
	 * Gets the path to a module image
	 * checks template overrides first, then module
	 *
	 * @param	string  $module	Module name
	 * @param	string  $image	Image to look for
	 * @return  string	Path to an image file
	 */
	public static function getModuleImage($module, $image)
	{
		$mainframe =& JFactory::getApplication();

		$template  = $mainframe->getTemplate();

		$templateimage = DS . 'templates' . DS . $template . DS . 'html' . DS . $module . DS . 'images' . DS . $image;

		$moduleimage = DS . 'modules' . DS . $module . DS . 'images' . DS . $image;

		if (file_exists(JPATH_SITE . $templateimage)) 
		{
			return $templateimage;
		} 
		else 
		{
			return $moduleimage;
		}
	}

	/**
	 * Adds a linked stylesheet from a module to the page
	 *
	 * @param	string  $module		Module name
	 * @param	string  $stylesheet	Stylesheet name (optional, uses module name if left blank)
	 * @param	string  $type   	Mime encoding type
	 * @param	string  $media  	Media type that this stylesheet applies to
	 * @param	string  $attribs  	Attributes to add to the link
	 * @return  void
	 */
	public static function addModuleStyleSheet($module, $stylesheet = '', $type = 'text/css', $media = null, $attribs = array())
	{
		$mainframe =& JFactory::getApplication();

		$jdocument = &JFactory::getDocument();

		$template  = $mainframe->getTemplate();

		if (empty($stylesheet)) 
		{
			$stylesheet = $module . '.css';
		}

		$templatecss = DS . 'templates' . DS . $template . DS . 'html' . DS . $module . DS . $stylesheet;

		$modulecss = DS . 'modules' . DS . $module . DS . $stylesheet;

		if (file_exists(JPATH_SITE . $templatecss)) 
		{
			$jdocument->addStyleSheet($templatecss . '?v=' . filemtime(JPATH_SITE . $templatecss), $type, $media, $attribs);
		} 
		else 
		{
			$jdocument->addStyleSheet($modulecss . '?v=' . filemtime(JPATH_SITE . $modulecss), $type, $media, $attribs);
		}
	}
	
	/**
	 * Adds a linked script to the page
	 *
	 * @param   string  $module  	URL to the linked script
	 * @param	string  $script  	Script name (optional, uses module name if left blank)
	 * @param   string  $type		Type of script. Defaults to 'text/javascript'
	 * @param   bool    $defer		Adds the defer attribute.
	 * @param   bool    $async		Adds the async attribute.
	 * @return  void
	 */
	public static function addModuleScript($module, $script = '', $type = "text/javascript", $defer = false, $async = false)
	{
		$mainframe = JFactory::getApplication();

		$jdocument = JFactory::getDocument();

		$template  = $mainframe->getTemplate();

		if (empty($script)) 
		{
			$script = $module;
		}

		$url = DS . 'modules' . DS . $module . DS . $script . '.js';
		$urlAlt = '';
		if (JPluginHelper::isEnabled('system', 'jquery'))
		{
			$urlAlt = DS . 'modules' . DS . $module . DS . $script . '.jquery.js';
		}

		if ($urlAlt && file_exists(JPATH_SITE . $urlAlt)) 
		{
			$jdocument->addScript($urlAlt . '?v=' . filemtime(JPATH_SITE . $urlAlt), $type, $defer, $async);
		} 
		else 
		{
			$jdocument->addScript($url . '?v=' . filemtime(JPATH_SITE . $url), $type, $defer, $async);
		}
	}

	/**
	 * Adds a linked stylesheet from a plugin to the page
	 *
	 * @param	string  $folder		Plugin folder name
	 * @param	string  $plugin		Plugin name
	 * @param	string  $stylesheet	Stylesheet name (optional, uses module name if left blank)
	 * @param	string  $type   	Mime encoding type
	 * @param	string  $media  	Media type that this stylesheet applies to
	 * @param	string  $attribs  	Attributes to add to the link
	 * @return  void
	 */
	public static function addPluginStyleSheet($folder, $plugin, $stylesheet = '', $type = 'text/css', $media = null, $attribs = array())
	{
		$mainframe =& JFactory::getApplication();

		$jdocument =& JFactory::getDocument();

		$template  = $mainframe->getTemplate();

		if (empty($stylesheet)) 
		{
			$stylesheet = $plugin . '.css';
		}
		$templatecss = DS . 'templates' . DS . $template . DS . 'html' . DS . 'plg_' . $folder . '_' . $plugin . DS . $stylesheet;

		$plugincss = DS . 'plugins' . DS . $folder . DS . $plugin . DS . $stylesheet;

		if (file_exists(JPATH_SITE . $templatecss)) 
		{
			$jdocument->addStyleSheet($templatecss . '?v=' . filemtime(JPATH_SITE . $templatecss), $type, $media, $attribs);
		} 
		else 
		{
			$jdocument->addStyleSheet($plugincss . '?v=' . filemtime(JPATH_SITE . $plugincss), $type, $media, $attribs);
		}
	}
	
	/**
	 * Adds a linked script to the page
	 *
	 * @param	string  $folder		Plugin folder name
	 * @param	string  $plugin		Plugin name
	 * @param	string  $script  	Script name (optional, uses module name if left blank)
	 * @param   string  $type		Type of script. Defaults to 'text/javascript'
	 * @param   bool    $defer		Adds the defer attribute.
	 * @param   bool    $async		Adds the async attribute.
	 * @return  void
	 */
	public static function addPluginScript($folder, $plugin, $script = '', $type = "text/javascript", $defer = false, $async = false)
	{
		$mainframe = JFactory::getApplication();

		$jdocument = JFactory::getDocument();

		$template  = $mainframe->getTemplate();

		if (empty($script)) 
		{
			$script = $plugin;
		}

		$url = DS . 'plugins' . DS . $folder . DS . $plugin . DS . $script . '.js';
		$urlAlt = '';
		if (JPluginHelper::isEnabled('system', 'jquery'))
		{
			$urlAlt = DS . 'plugins' . DS . $folder . DS . $plugin . DS . $script . '.jquery.js';
		}

		if ($urlAlt && file_exists(JPATH_SITE . $urlAlt)) 
		{
			$jdocument->addScript($urlAlt . '?v=' . filemtime(JPATH_SITE . $urlAlt), $type, $defer, $async);
		} 
		else 
		{
			$jdocument->addScript($url . '?v=' . filemtime(JPATH_SITE . $url), $type, $defer, $async);
		}
	}

	/**
	 * Returns the path to a system stylesheet
	 * Accepts either an array or string of comma-separated file names
	 * If more than one stylesheet is called for, it will combine, compress, return path to cached file
	 *
	 * @param	mixed  $elements An array or string of comma-separated file names
	 * @return  string
	 */
	public static function getSystemStylesheet($elements = null)
	{
		// Anything passed?
		if (!$elements)
		{
			return '';
		}
		// Is it a string?
		if (is_string($elements))
		{
			$elements = explode(',', $elements);
		}
		if (count($elements) <= 0)
		{
			return '';
		}
		// Trim items
		$elements = array_map('trim', $elements);

		// Path to system cache
		$cachedir = JPATH_ROOT . DS . 'cache';
		// Path to system CSS
		$thispath = JPATH_ROOT . DS . 'media' . DS . 'system' . DS . 'css';

		// Determine last modification date of the files
		$lastmodified = 0;

		foreach ($elements as $k => $element) 
		{
			if (!$element) 
			{
				$elements[$k] = false;
				continue;
			}

			// Strip file extension to normalize data
			$element = basename($element, '.css');

			$elements[$k] = $element;

			// Check if the file exists
			$path = $thispath . DS . $element . '.css';

			if (!file_exists($path)) 
			{
				$elements[$k] = false;
				continue;
			}

			// Get the last modified time
			// We take the max time so $lastmodified should be different if any of the files have changed.
			$lastmodified += filemtime($path);
		}

		// Remove any empty items
		$elements = array_filter($elements);

		// Build hash
		$hash = $lastmodified; // . '-' . md5(implode(',', $elements));

		// Only one stylesheet called for so return it as is
		if (count($elements) == 1)
		{
			return $thispath . DS . $elements[0] . '.css';
		}

		// Try the cache first to see if the combined files were already generated
		$cachefile = 'system-' . $hash . '.css';

		if (!file_exists($cachedir . DS . $cachefile)) 
		{
			$contents = '';
			reset($elements);

			foreach ($elements as $k => $element) 
			{
				$contents .= "\n\n" . file_get_contents($thispath . DS . $element . '.css');
			}
			$patterns = array(
				'!/\*[^*]*\*+([^/][^*]*\*+)*/!',  /* remove comments */
				'/[\n\r \t]/',                    /* remove tabs, spaces, newlines, etc. */
				'/ +/'                           /* collapse multiple spaces to a single space */
				/* '/ ?([,:;{}]) ?/'                 remove space before and after , : ; { }     [!] apparently, IE 7 doesn't like this and won't process the stylesheet */
			);
			$replacements = array(
				'',
				' ',
				' '/*,
				'$1'*/
			);
			$contents = preg_replace($patterns, $replacements, $contents);

			if ($fp = fopen($cachedir . DS . $cachefile, 'wb')) 
			{
				fwrite($fp, $contents);
				fclose($fp);
			}
		}

		return DS . 'cache' . DS . $cachefile;
	}
}
