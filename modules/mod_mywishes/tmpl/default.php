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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_View_Helper_Html');
?>
<div<?php echo ($this->params->get('moduleclass')) ? ' class="' . $this->params->get('moduleclass') . '"' : ''; ?>>
	<h4><?php echo JText::_('Submitted Wishes'); ?></h4>
<?php if (count($this->rows1) <= 0) { ?>
	<p><?php echo JText::_('NO_WISHES'); ?></p>
<?php } else { ?>
	<ul class="expandedlist">
<?php
		foreach ($this->rows1 as $row) 
		{
			$when = Hubzero_View_Helper_Html::timeAgo($row->proposed);
?>
		<li class="wishlist">
			<a href="<?php echo JRoute::_('index.php?option=com_wishlist&task=wish&id=' . $row->wishlist . '&wishid=' . $row->id); ?>" class="tooltips" title="<?php echo htmlentities(stripslashes($row->subject), ENT_QUOTES) . ' :: ' . Hubzero_View_Helper_Html::shortenText(htmlentities(stripslashes($row->about), ENT_QUOTES), 160); ?>">
				#<?php echo $row->id; ?>: <?php echo Hubzero_View_Helper_Html::shortenText(stripslashes($row->subject), 35, 0); ?>
			</a>
			<span>
				<span class="<?php 
				echo ($row->status==3) ? 'rejected' : ''; 
				if ($row->status==0) { 
					echo ($row->accepted==1) ? 'accepted' : 'pending';
				}
				?>">
					<?php
					echo ($row->status==3) ? JText::_('REJECTED') : ''; 
					if ($row->status==0) { 
						echo ($row->accepted==1) ? JText::_('ACCEPTED') : JText::_('PENDING');
					}
					?>
				</span>
				<span>
					<?php echo JText::_('WISHLIST') . ': ' . stripslashes($row->listtitle); ?>
				</span>
			</span>
		</li>
<?php
		}
?>
	</ul>
<?php } ?>

	<h4><?php echo JText::_('Assigned Wishes'); ?></h4>
<?php if (count($this->rows2) <= 0) { ?>
	<p><?php echo JText::_('NO_WISHES'); ?></p>
<?php } else { ?>
	<ul class="expandedlist">
<?php
		foreach ($this->rows2 as $row) 
		{
			$when = Hubzero_View_Helper_Html::timeAgo($row->proposed);
?>
		<li class="wishlist">
			<a href="<?php echo JRoute::_('index.php?option=com_wishlist&task=wish&id=' . $row->wishlist . '&wishid=' . $row->id); ?>" class="tooltips" title="<?php echo htmlentities(stripslashes($row->subject), ENT_QUOTES) . ' :: ' . Hubzero_View_Helper_Html::shortenText(htmlentities(stripslashes($row->about), ENT_QUOTES), 160); ?>">
				#<?php echo $row->id; ?>: <?php echo Hubzero_View_Helper_Html::shortenText(stripslashes($row->subject), 35, 0); ?>
			</a>
			<span>
				<span class="<?php 
				echo ($row->status==3) ? 'rejected' : ''; 
				if ($row->status==0) { 
					echo ($row->accepted==1) ? 'accepted' : 'pending';
				}
				?>">
					<?php
					echo ($row->status==3) ? JText::_('REJECTED') : ''; 
					if ($row->status==0) { 
						echo ($row->accepted==1) ? JText::_('ACCEPTED') : JText::_('PENDING');
					}
					?>
				</span>
				<span>
					<?php echo JText::_('WISHLIST') . ': ' . stripslashes($row->listtitle); ?>
				</span>
			</span>
		</li>
<?php
		}
?>
	</ul>
<?php } ?>
	
	<ul class="module-nav">
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_wishlist&task=add&category=general&rid=1'); ?>">
				<?php echo JText::_('NEW_WISH'); ?>
			</a>
		</li>
	</ul>
</div>