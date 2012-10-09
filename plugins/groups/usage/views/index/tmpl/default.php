<?php
/**
 * @package		HUBzero                                  CMS
 * @author		Christopher                               Smoak <csmoak@purdue.edu>
 * @copyright	Copyright                               2005-2011 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *                                                    
 *                                                    Copyright 2005-2011 by Purdue Research Foundation, West Lafayette, IN 47906.
 *                                                    All rights reserved.
 *                                                    
 *                                                    This program is free software; you can redistribute it and/or
 *                                                    modify it under the terms of the GNU General Public License,
 *                                                    version 2 as published by the Free Software Foundation.
 *                                                    
 *                                                    This program is distributed in the hope that it will be useful,
 *                                                    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *                                                    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *                                                    GNU General Public License for more details.
 *                                                    
 *                                                    You should have received a copy of the GNU General Public License
 *                                                    along with this program; if not, write to the Free Software
 *                                                    Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//get group logs
$xlog = new XGroupLog( $this->database );

//parse the logs
$group_edits          = $xlog->logCount($this->group->get('gidNumber'), 'group_edited');
$group_customize      = $xlog->logCount($this->group->get('gidNumber'), 'group_customized');
$membership_requests  = $xlog->logCount($this->group->get('gidNumber'), 'membership_requested');
$membership_accepted  = $xlog->logCount($this->group->get('gidNumber'), 'membership_approved');
$membership_denied    = $xlog->logCount($this->group->get('gidNumber'), 'membership_denied');
$membership_cancelled = $xlog->logCount($this->group->get('gidNumber'), 'membership_cancelled');
$invites_sent         = $xlog->logCount($this->group->get('gidNumber'), 'membership_invites_sent');
$invites_accepted     = $xlog->logCount($this->group->get('gidNumber'), 'membership_invite_accepted');
$promotions           = $xlog->logCount($this->group->get('gidNumber'), 'membership_promoted');
$demotions            = $xlog->logCount($this->group->get('gidNumber'), 'membership_demoted');
?>
<a name="usage"></a>
<h3 class="heading"><?php echo JText::_('USAGE'); ?></h3>

<div class="main section">
	<div id="page_views">
	
		<div id="page_views_heading">
			<h3>Group Page Views</h3>
			<div id="page_view_settings">
				<form name="page_selector" action="/groups/<?php echo $this->group->get('cn'); ?>/usage" method="POST">
					<select name="pid" id="page_view_selector">
						<option value=""<?php if($this->pid == '') { echo "selected"; } ?>>All Group Page Views</option>
						<?php foreach($this->pages as $page) : ?>
							<?php $sel = ($this->pid == $page['id']) ? "selected" : ""; ?>
							<option <?php echo $sel; ?> value="<?php echo $page['id']; ?>"><?php echo $page['title']; ?></option>
						<?php endforeach; ?>
					</select>
					<input type="text" id="page_view_dates" value="<?php echo date("m/d/Y", strtotime($this->start)) . " - " . date("m/d/Y", strtotime($this->end)); ?>" />
					<input type="hidden" name="start" id="date_start" value="<?php echo date("Y-m-d",strtotime($this->start)); ?>" />
					<input type="hidden" name="end" id="date_end" value="<?php echo date("Y-m-d",strtotime($this->end)); ?>" />
					<input type="submit" id="submit" value="Go" />
				</form>
			</div><!-- /.end page_view_settings -->
		</div><!-- /.end page_views_heading -->
		
		<div id="page_views_chart">
			<noscript>
				<p class="info" style="margin:15px;">To view this page views graph, Javascript must be enabled.</p>
			</noscript>
		</div>
	</div>
	
	<table class="data" summary="<?php echo JText::_('TBL_SUMMARY_OVERVIEW'); ?>">
		<caption><?php echo JText::_('TBL_CAPTION_OVERVIEW'); ?></caption>
		<thead>
			<tr>
				<th scope="col" class="textual-data"><?php echo JText::_('TBL_TH_ITEM'); ?></th>
				<th scope="col" class="numerical-data"><?php echo JText::_('TBL_TH_VALUE'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr class="even">
				<th scope="row"><?php echo JText::_('TBL_TH_PAGES'); ?>:</th>
				<td><?php echo plgGroupsUsage::getGroupPagesCount($this->group->get('gidNumber')); ?></td>
			</tr>
			<tr class="odd">
				<th scope="row"><?php echo JText::_('TBL_TH_MEMBERS'); ?>:</th>
				<td><?php echo count($this->group->get('members')); ?></td>
			</tr>
			<tr class="even">
				<th scope="row"><?php echo JText::_('TBL_TH_RESOURCES'); ?>:</th>
				<td><?php echo plgGroupsUsage::getResourcesCount($this->group->get('cn'), $this->authorized); ?></td>
			</tr>
			<tr class="odd">
				<th scope="row"><?php echo JText::_('TBL_TH_OPEN_DISCUSSIONS'); ?>:</th>
				<td><?php echo plgGroupsUsage::getForumCount($this->group->get('gidNumber'), $this->authorized, 'open'); ?></td>
			</tr>
			<tr class="even">
				<th scope="row"><?php echo JText::_('TBL_TH_CLOSED_DISCUSSIONS'); ?>:</th>
				<td><?php echo plgGroupsUsage::getForumCount($this->group->get('gidNumber'), $this->authorized, 'closed'); ?></td>
			</tr>
			<tr class="odd">
				<th scope="row"><?php echo JText::_('TBL_TH_STICKY_DISCUSSIONS'); ?>:</th>
				<td><?php echo plgGroupsUsage::getForumCount($this->group->get('gidNumber'), $this->authorized, 'sticky'); ?></td>
			</tr>
			<tr class="even">
				<th scope="row"><?php echo JText::_('TBL_TH_WIKI_PAGES'); ?>:</th>
				<td><?php echo plgGroupsUsage::getWikipageCount($this->group->get('cn'), $this->authorized); ?></td>
			</tr>
			<tr class="odd">
				<th scope="row"><?php echo JText::_('TBL_TH_WIKI_FILES'); ?>:</th>
				<td><?php echo plgGroupsUsage::getWikifileCount($this->group->get('cn'), $this->authorized); ?></td>
			</tr>
			<tr class="even">
				<th scope="row"><?php echo JText::_('TBL_TH_BLOG'); ?>:</th>
				<td><?php echo plgGroupsUsage::getGroupBlogCount($this->group->get('gidNumber')); ?></td>
			</tr>
			<tr class="odd">
				<th scope="row"><?php echo JText::_('TBL_TH_BLOG_COMMENTS'); ?>:</th>
				<td><?php echo plgGroupsUsage::getGroupBlogCommentCount($this->group->get('gidNumber')); ?></td>
			</tr>
			<tr class="even">
				<th scope="row"><?php echo JText::_('TBL_TH_CALENDAR'); ?>:</th>
				<td><?php echo plgGroupsUsage::getGroupCalendarCount($this->group->get('gidNumber')); ?></td>
			</tr>
		</tbody>
	</table>
	
	<table class="data" summary="<?php echo JText::_('TBL_SUMMARY_ACTIVITY'); ?>">
		<caption><?php echo JText::_('TBL_CAPTION_ACTIVITY'); ?></caption>
		<thead>
			<tr>
				<th scope="col" class="textual-data"><?php echo JText::_('TBL_TH_ITEM'); ?></th>
				<th scope="col" class="numerical-data"><?php echo JText::_('TBL_TH_VALUE'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr class="even">
				<th scope="row"><?php echo JText::_('TBL_GROUP_EDITS'); ?>:</th>
				<td><?php echo $group_edits; ?></td>
			</tr>
			<tr class="odd">
				<th scope="row"><?php echo JText::_('TBL_GROUP_CUSTOMIZE'); ?>:</th>
				<td><?php echo $group_customize; ?></td>
			</tr>
			<tr class="even">
				<th scope="row"><?php echo JText::_('TBL_MEMBERSHIP_REQUESTS'); ?>:</th>
				<td><?php echo $membership_requests; ?></td>
			</tr>
			<tr class="odd">
				<th scope="row"><?php echo JText::_('TBL_MEMBERSHIP_ACCEPTED'); ?>:</th>
				<td><?php echo $membership_accepted; ?></td>
			</tr>
			<tr class="even">
				<th scope="row"><?php echo JText::_('TBL_MEMBERSHIP_DENIED'); ?>:</th>
				<td><?php echo $membership_denied; ?></td>
			</tr>
			<tr class="odd">
				<th scope="row"><?php echo JText::_('TBL_MEMBERSHIP_CANCELLED'); ?>:</th>
				<td><?php echo $membership_cancelled; ?></td>
			</tr>
			<tr class="even">
				<th scope="row"><?php echo JText::_('TBL_INVITES_SENT'); ?>:</th>
				<td><?php echo $invites_sent; ?></td>
			</tr>
			<tr class="odd">
				<th scope="row"><?php echo JText::_('TBL_INVITES_ACCEPTED'); ?>:</th>
				<td><?php echo $invites_accepted; ?></td>
			</tr>
			<tr class="even">
				<th scope="row"><?php echo JText::_('TBL_PROMOTIONS'); ?>:</th>
				<td><?php echo $promotions; ?></td>
			</tr>
			<tr class="odd">
				<th scope="row"><?php echo JText::_('TBL_DEMOTIONS'); ?>:</th>
				<td><?php echo $demotions; ?></td>
			</tr>
		</tbody>
	</table>
	
</div><!-- /.main section -->