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

$juser =& JFactory::getUser();
if (!$juser->get('guest')) { ?>
<div id="<?php echo ($this->sub) ? 'sub-content-header-extra' : 'content-header-extra'; ?>">
	<ul id="<?php echo ($this->sub) ? 'page_options' : 'useroptions'; ?>">
	<?php if ($this->page->pagename != 'MainPage') { ?>
		<li>
			<a class="main-page btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope); ?>">
				<?php echo JText::_('Main Page'); ?>
			</a>
		</li>
	<?php } ?>
	<?php if ($this->config->get('access-create')) { ?>
		<li>
			<a class="add btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&task=new'); ?>">
				<?php echo JText::_('WIKI_NEW_PAGE'); ?>
			</a>
		</li>
	<?php } ?>
	</ul>
</div><!-- / #content-header-extra -->
<?php } ?>
<div id="sub-menu" class="sub-menu">
	<ul>
		<li class="page-text<?php if ($this->controller == 'page' && ($this->task == 'display' || !$this->task)) { echo ' active'; } ?>">
			<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename='.$this->page->pagename); ?>">
				<span><?php echo JText::_('WIKI_TAB_ARTICLE'); ?></span>
			</a>
		</li>
<?php if ($this->page->id) { ?>
<?php if (($this->page->state == 1 && $this->config->get('access-manage')) || ($this->page->state != 1 && $this->config->get('access-edit'))) { ?>
		<li class="page-edit<?php if ($this->controller == 'page' && $this->task == 'edit') { echo ' active'; } ?>">
			<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename='.$this->page->pagename.'&task=edit'); ?>">
				<span><?php echo JText::_('WIKI_TAB_EDIT'); ?></span>
			</a>
		</li>
<?php } ?>
<?php if ($this->config->get('access-comment-view')) { ?>
		<li class="page-comments<?php if ($this->controller == 'comments') { echo ' active'; } ?>">
			<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename='.$this->page->pagename.'&task=comments'); ?>">
				<span><?php echo JText::_('WIKI_TAB_COMMENTS'); ?></span>
			</a>
		</li>
<?php } ?>
		<li class="page-history<?php if ($this->controller == 'history') { echo ' active'; } ?>">
			<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename='.$this->page->pagename.'&task=history'); ?>">
				<span><?php echo JText::_('WIKI_TAB_HISTORY'); ?></span>
			</a>
		</li>
<?php if ($this->config->get('access-delete')) { ?>
		<li class="page-delete<?php if ($this->controller == 'page' && $this->task == 'delete') { echo ' active'; } ?>">
			<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename='.$this->page->pagename.'&task=delete'); ?>">
				<span><?php echo JText::_('WIKI_DELETE_PAGE'); ?></span>
			</a>
		</li>
<?php } ?>
<?php } ?>
	</ul>
	<div class="clear"></div>
</div><!-- / .page-options -->