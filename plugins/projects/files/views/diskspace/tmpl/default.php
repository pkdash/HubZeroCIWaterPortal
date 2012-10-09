<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$class = $this->case == 'apps' ? 'apps' : 'files';

// Check used space against quota (percentage)
$inuse = round((($this->dirsize * 100 )/ $this->quota), 1);
if($this->total > 0 && $inuse < 1) {
	$inuse = round((($this->dirsize * 100 )/ $this->quota), 2);
	if($inuse < 0.1) {
		$inuse = 0.01;
	}
}
$inuse = ($inuse > 100) ? 100 : $inuse;
$quota = ProjectsHtml::formatSize($this->quota);
$used  = ProjectsHtml::formatSize($this->dirsize);
$unused = ProjectsHtml::formatSize($this->quota - $this->dirsize);
$unused = $unused <= 0 ? 'none' : $unused;
$approachingQuota = $this->config->get('approachingQuota', 85);
$approachingQuota = intval($approachingQuota) > 0 ? $approachingQuota : 85;
$warning = ($inuse > $approachingQuota) ? 1 : 0;

?>
<?php if($this->action != 'admin') { ?>
	<div id="plg-header">
		<h3 class="<?php echo $class; ?>"><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'alias='.$this->project->alias.a.'active=files'); ?>"><?php echo $this->title; ?></a> &raquo; <span class="subheader"><?php echo JText::_('COM_PROJECTS_FILES_DISK_USAGE'); ?></span></h3>
	</div>
<?php } ?>
	<div id="disk-usage" <?php if($warning) { echo 'class="quota-warning"'; } ?>>
		<div class="disk-usage-wrapper">
			<h3><?php echo ($this->action != 'admin') ? JText::_('COM_PROJECTS_FILES_QUOTA').': '.$quota : JText::_('COM_PROJECTS_FILES_DISK_USAGE') ; ?></h3>
			<div id="indicator-wrapper">
				<span id="indicator-area" class="used:<?php echo $inuse; ?>">&nbsp;</span><span id="indicator-value"><span><?php echo $inuse.'% '.JText::_('COM_PROJECTS_FILES_USED').' ('.$used.' '.JText::_('COM_PROJECTS_OUT_OF').' '.$quota.')'; ?></span></span>
			</div>
			
			<div id="usage-labels">
					<span class="l-regular">&nbsp;</span><?php echo JText::_('COM_PROJECTS_FILES_REGULAR_FILES').' ('.$used.')'; ?>
					<?php if($warning) { ?><span class="approaching-quota"><?php echo ($inuse == 100) ? JText::_('COM_PROJECTS_FILES_OVER_QUOTA')  : JText::_('COM_PROJECTS_FILES_APPROACHING_QUOTA') ; ?></span><?php } ?>
					<span class="l-unused">&nbsp;</span><?php echo JText::_('COM_PROJECTS_FILES_UNUSED_SPACE').' ('.$unused.')'; ?>
			</div>
		</div>
	</div>
