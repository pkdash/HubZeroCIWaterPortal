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

$juser =& JFactory::getUser();
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div class="main section">
	<form action="<?php echo JRoute::_('index.php?option=' . $this->option); ?>" method="post">
		<div class="aside">
			<div class="container">
				<h3>Site Members</h3>
				<p class="starter"><span class="starter-point"></span>When people join this site and make their profiles public they will appear here.</p>
				<p>Use the sorting and filtering options to see members listed alphabetically, by their organization, or the number of contributions they have.</p>
				<p>Use the 'Search' to find specific members if you would like to check out their profiles, contributions or message them privately.</p>
			</div><!-- / .container -->
			
			<div class="container">
				<h3>Looking for groups?</h3>
				<p class="starter"><span class="starter-point"></span>Go to the <a href="<?php echo JRoute::_('index.php?option=com_groups'); ?>">Groups page</a>.</p>
			</div><!-- / .container -->
		</div><!-- / .aside -->
		<div class="subject">
			
			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="Search" />
				<fieldset class="entry-search">
					<legend>Search for Members</legend>
					<label for="entry-search-field">Enter keyword or phrase</label>
					<input type="text" name="search" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" />
					<input type="hidden" name="sortby" value="<?php echo $this->escape($this->filters['sortby']); ?>" />
					<input type="hidden" name="show" value="<?php echo $this->escape($this->filters['show']); ?>" />
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="index" value="<?php echo $this->escape($this->filters['index']); ?>" />
				</fieldset>
			</div><!-- / .container -->
			
<?php 
$qs = array();
foreach ($this->filters as $f=>$v)
{
	$qs[] = ($v != '' && $f != 'index' && $f != 'authorized' && $f != 'start') ? $f . '=' . $v : '';
}
$qs[] = 'limitstart=0';
$qs = implode(a,$qs);

$letters = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

$url  = 'index.php?option=' . $this->option;
$url .= ($qs != '') ? '&' . $qs : '';
$html  = '<a href="' . JRoute::_($url) . '"';
if ($this->filters['index'] == '') {
	$html .= ' class="active-index"';
}
$html .= '>' . JText::_('ALL') . '</a> ' . "\n";
foreach ($letters as $letter)
{
	$url  = 'index.php?option=' . $this->option . '&index=' . strtolower($letter);
	$url .= ($qs != '') ? '&' . $qs : '';

	$html .= "\t\t\t\t\t\t\t\t".'<a href="' . JRoute::_($url) . '"';
	if ($this->filters['index'] == strtolower($letter)) {
		$html .= ' class="active-index"';
	}
	$html .= '>' . $letter . '</a> ' . "\n";
}
?>
			<div class="container">
				<ul class="entries-menu order-options">
					<li><a<?php echo ($this->filters['sortby'] == 'name') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=browse&index=' . $this->filters['index'] . '&show='.$this->filters['show'] . '&sortby=name'); ?>" title="Sort by name">&darr; Name</a></li>
					<li><a<?php echo ($this->filters['sortby'] == 'organization') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=browse&index=' . $this->filters['index'] . '&show='.$this->filters['show'] . '&sortby=organization'); ?>" title="Sort by organization">&darr; Organization</a></li>
					<li><a<?php echo ($this->filters['sortby'] == 'contributions') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=browse&index=' . $this->filters['index'] . '&show='.$this->filters['show'] . '&sortby=contributions'); ?>" title="Sort by number of contributions">&darr; Contributions</a></li>
				</ul>
				
				<ul class="entries-menu filter-options">
					<li><a<?php echo ($this->filters['show'] != 'contributors') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=browse&index=' . $this->filters['index'] . '&sortby=' . $this->filters['sortby']); ?>" title="Show All members">All</a></li>
					<li><a<?php echo ($this->filters['show'] == 'contributors') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=browse&index=' . $this->filters['index'] . '&show=contributors&sortby=' . $this->filters['sortby']); ?>" title="Show only members with Contributions">Contributors</a></li>
				</ul>
				
				<table class="members entries" summary="<?php echo JText::_('TABLE_SUMMARY'); ?>">
					<caption>
						<?php
						$s = ($this->total > 0) ? $this->filters['start']+1 : $this->filters['start'];
						$e = ($this->total > ($this->filters['start'] + $this->filters['limit'])) ? ($this->filters['start'] + $this->filters['limit']) : $this->total;
						$e = ($this->filters['limit'] == 0) ? $this->total : $e;

						if ($this->filters['search'] != '') {
							echo JText::sprintf('Search for "%s" in ', $this->filters['search']);
						}
						?>
						<?php if ($this->filters['show'] != 'contributors') {
							echo JText::_('All Members');
						} else {
							echo JText::_('Contributors');
						}?> 
						<?php if ($this->filters['index']) { ?>
							<?php echo JText::_('starting with'); ?> "<?php echo strToUpper($this->filters['index']); ?>"
						<?php } ?>
						<span>(<?php echo $s . '-' . $e; ?> of <?php echo $this->total; ?>)</span>
					</caption>
					<thead>
						<tr>
							<th colspan="4">
								<span class="index-wrap">
									<span class="index">
										<?php echo $html; ?>
									</span>
								</span>
							</th>
						</tr>
					</thead>
					<tbody>
<?php
if (count($this->rows) > 0) 
{
	// Get plugins
	JPluginHelper::importPlugin('members');
	$dispatcher =& JDispatcher::getInstance();

	$areas = array();
	$activeareas = $dispatcher->trigger('onMembersContributionsAreas', array($this->authorized));
	foreach ($activeareas as $area)
	{
		$areas = array_merge($areas, $area);
	}

	$cols = 2;

	$cls = ''; //'even';

	// Default thumbnail
	$config =& JComponentHelper::getParams('com_members');
	$thumb = DS . trim($config->get('webpath', '/site/members'), DS);

	$dfthumb = DS . ltrim($config->get('defaultpic'), DS);
	$dfthumb = Hubzero_View_Helper_Html::thumbit($dfthumb);

	// User messaging
	$messaging = false;
	if ($this->config->get('user_messaging') > 0 && !$juser->get('guest')) 
	{
		ximport('Hubzero_User_Helper');

		switch ($this->config->get('user_messaging'))
		{
			case 1:
				// Get the groups the visiting user
				$xgroups = Hubzero_User_Helper::getGroups($juser->get('id'), 'all');
				$usersgroups = array();
				if (!empty($xgroups)) 
				{
					foreach ($xgroups as $group)
					{
						if ($group->regconfirmed) 
						{
							$usersgroups[] = $group->cn;
						}
					}
				}
			break;

			case 2:
			case 0:
			default:
			break;
		}
		$messaging = true;
	}

	foreach ($this->rows as $row)
	{
		//$cls = ($cls == 'odd') ? 'even' : 'odd';
		$cls = '';
		if ($row->public != 1) 
		{
			$cls = 'private';
		}

		if ($row->uidNumber < 0) 
		{
			$id = 'n' . -$row->uidNumber;
		} 
		else 
		{
			$id = $row->uidNumber;
		}

		if ($row->uidNumber == $juser->get('id')) 
		{
			$cls .= ($cls) ? ' me' : 'me';
		}

		// User name
		$row->name       = stripslashes($row->name);
		$row->surname    = stripslashes($row->surname);
		$row->givenName  = stripslashes($row->givenName);
		$row->middelName = stripslashes($row->middleName);

		if (!$row->surname) 
		{
			$bits = explode(' ', $row->name);
			$row->surname = array_pop($bits);
			if (count($bits) >= 1) 
			{
				$row->givenName = array_shift($bits);
			}
			if (count($bits) >= 1) 
			{
				$row->middleName = implode(' ', $bits);
			}
		}

		$name = ($row->surname) ? stripslashes($row->surname) : '';
		if ($row->givenName) 
		{
			$name .= ($row->surname) ? ', ' : '';
			$name .= stripslashes($row->givenName);
			$name .= ($row->middleName) ? ' ' . stripslashes($row->middleName) : '';
		}
		if (!trim($name)) 
		{
			$name = 'Unknown (' . $row->username . ')';
		}

		// User picture
		$uthumb = '';
		if ($row->picture) {
			$uthumb = $thumb . DS . Hubzero_View_Helper_Html::niceidformat($row->uidNumber) . DS . $row->picture;
			$uthumb = Hubzero_View_Helper_Html::thumbit($uthumb);
		}

		if ($uthumb && is_file(JPATH_ROOT . $uthumb)) 
		{
			$p = $uthumb;
		} 
		else 
		{
			$p = $dfthumb;
		}

		// User messaging
		$messageuser = false;
		if ($messaging && $row->uidNumber > 0 && $row->uidNumber != $juser->get('id')) 
		{
			switch ($this->config->get('user_messaging'))
			{
				case 1:
					// Get the groups of the profile
					$pgroups = Hubzero_User_Helper::getGroups($row->uidNumber, 'all');
					// Get the groups the user has access to
					$profilesgroups = array();
					if (!empty($pgroups)) 
					{
						foreach ($pgroups as $group)
						{
							if ($group->regconfirmed) 
							{
								$profilesgroups[] = $group->cn;
							}
						}
					}

					// Find the common groups
					$common = array_intersect($usersgroups, $profilesgroups);

					if (count($common) > 0) 
					{
						$messageuser = true;
					}
				break;

				case 2:
					$messageuser = true;
				break;

				case 0:
				default:
					$messageuser = false;
				break;
			}
		}
?>
						<tr<?php echo ($cls) ? ' class="'.$cls.'"' : ''; ?>>
							<th class="entry-img">
								<img width="50" height="50" src="<?php echo $p; ?>" alt="Avatar for <?php echo $this->escape($name); ?>" />
							</th>
							<td>
								<a class="entry-title" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&id=' . $id); ?>"><?php echo $name; ?></a><br />
								<span class="entry-details">
									<span class="organization"><?php echo Hubzero_View_Helper_Html::xhtml(stripslashes($row->organization)); ?></span>
								</span>
							</td>
							<td>
								<!-- rcount: <?php echo $row->rcount; ?> --> 
								<span class="activity"><?php echo $row->resource_count . ' Resources, ' . $row->wiki_count . ' Topics'; ?></span>
							</td>
							<td class="message-member">
<?php if ($messageuser) { ?>
								<a class="message tooltips" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&id=' . $juser->get('id') . '&active=messages&task=new&to[]=' . $row->uidNumber); ?>" title="Message :: Send a message to <?php echo $this->escape($name); ?>"><?php echo JText::_('Send a message to ' . $this->escape($name)); ?></a></td>
<?php } ?>
							</td>
						</tr>
<?php
	}
} else { ?>
						<tr>
							<td colspan="4">
								<p class="warning"><?php echo JText::_('NO_MEMBERS_FOUND'); ?></p>
							</td>
						</tr>
<?php } ?>
					</tbody>
				</table>
<?php
	$this->pageNav->setAdditionalUrlParam('index', $this->filters['index']);
	$this->pageNav->setAdditionalUrlParam('sortby', $this->filters['sortby']);
	$this->pageNav->setAdditionalUrlParam('show', $this->filters['show']);
	echo $this->pageNav->getListFooter();
?>
				<div class="clearfix"></div>
			</div><!-- / .container -->
		</div><!-- / .subject -->
		<div class="clear"></div>
	</form>
</div><!-- / .main section -->
