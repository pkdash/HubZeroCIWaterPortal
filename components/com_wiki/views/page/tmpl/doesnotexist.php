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
?>
<div id="<?php echo ($this->page->group_cn) ? 'sub-content-header' : 'content-header'; ?>" class="full">
	<h2><?php echo $this->escape($this->title); ?></h2>
</div><!-- /#content-header -->

<?php
	$view = new JView(array(
		'base_path' => $this->base_path, 
		'name'      => 'page',
		'layout'    => 'submenu'
	));
	$view->option = $this->option;
	$view->controller = $this->controller;
	$view->page   = $this->page;
	$view->task   = $this->task;
	$view->config = $this->config;
	$view->sub    = $this->sub;
	$view->display();
?>

<div class="main section">
	<p class="warning">
		This page does not exist. Would you like to <a href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename='.$this->page->pagename.'&task=new'); ?>">create it?</a>
	</p>
	<p>
		<?php echo JText::_('Or choose a page template to create an already-formatted page:'); ?>
	</p>
	<ul>
<?php 
if ($templates = $this->page->getTemplates()) {
	foreach ($templates as $template)
	{
?>
		<li>
			<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename='.$this->page->pagename.'&task=new&tplate='.stripslashes($template->pagename)); ?>">
				<?php echo stripslashes($template->pagename); ?>
			</a>
		</li>
<?php
	}
} else {
?>
		<li>
			<?php echo JText::_('No templates available.'); ?>
		</li>
<?php
}
?>
	</ul>
	<div class="clear"></div>
</div><!-- / .main section -->
