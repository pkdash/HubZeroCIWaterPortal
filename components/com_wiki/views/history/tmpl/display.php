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

$mode = $this->page->params->get('mode', 'wiki');

$first = end($this->revisions);
?>
	<div id="<?php echo ($this->sub) ? 'sub-content-header' : 'content-header'; ?>">
		<h2><?php echo $this->escape($this->title); ?></h2>
<?php
	if (!$mode || ($mode && $mode != 'static')) 
	{
		$view = new JView(array(
			'base_path' => $this->base_path, 
			'name'      => 'page',
			'layout'    => 'authors'
		));
		$view->option   = $this->option;
		$view->page     = $this->page;
		$view->task     = $this->task;
		$view->config   = $this->config;
		$view->revision = $first;
		$view->display();
	}
?>
	</div><!-- /#content-header -->

<?php if ($mode == 'static' && $this->config->get('access-admin')) { ?>
	<div id="<?php echo $hid; ?>-extra">
		<ul id="<?php echo $uid; ?>">
			<li><a class="edit" href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename='.$this->page->pagename.'&task=edit'); ?>">Edit</a></li>
			<li class="last"><a class="history" href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename='.$this->page->pagename.'&task=history'); ?>">History</a></li>
		</ul>
	</div><!-- /#content-header-extra -->
<?php } ?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<?php if ($this->message) { ?>
	<p class="passed"><?php echo $this->message; ?></p>
<?php } ?>

<?php /*if ($this->warning) { ?>
	<p class="warning"><?php echo $this->warning; ?></p>
<?php }*/ ?>

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
<div class="section">
	<div class="two columns first">
		<p>Versions are listed in reverse-chronological order (newest to oldest). For any version listed below, click on its date to view it. For more help, see <a href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename=Help:PageHistory'); ?>">Help:Page history</a>.</p> 
	</div><!-- / .aside -->
	<div class="two columns second">
		<p>(cur) = difference from current version<br />(last) = difference from preceding version</p>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / .section -->

<div class="main section">
	<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename='.$this->page->pagename.'&task=compare'); ?>" method="post">
		<p class="info">
			This article has <?php echo count($this->revisions); ?> versions, was created on <time datetime="<?php echo $first->created; ?>"><?php echo $first->created; ?></time> and last edited on <time datetime="<?php echo $this->revisions[0]->created; ?>"><?php echo $this->revisions[0]->created; ?></time>.
		</p>
			<div class="container">
				<p><input type="submit" value="<?php echo JText::_('WIKI_HISTORY_COMPARE'); ?>" /></p>
				<table class="entries" id="revisionhistory" summary="<?php echo JText::_('WIKI_HISTORY_TBL_SUMMARY'); ?>">
					<caption><?php echo JText::_('WIKI_HISTORY_TBL_SUMMARY'); ?></caption>
					<thead>
						<tr>
							<th scope="col"><?php echo JText::_('WIKI_HISTORY_COL_VERSION'); ?></th>
							<th scope="col" colspan="2"><?php echo JText::_('WIKI_HISTORY_COL_COMPARE'); ?></th>
							<th scope="col"><?php echo JText::_('WIKI_HISTORY_COL_WHEN'); ?></th>
							<th scope="col"><?php echo JText::_('WIKI_HISTORY_COL_MADE_BY'); ?></th>
							<th scope="col"><?php echo JText::_('WIKI_HISTORY_COL_STATUS'); ?></th>
<?php if ($this->config->get('access-manage')) { ?>
							<th scope="col"></th>
<?php } ?>
						</tr>
					</thead>
					<tbody>
<?php
$i = 0;
$cur = 0;
$cls = 'even';
foreach ($this->revisions as $revision)
{
	$i++;
	$cls = ($cls == 'odd') ? 'even' : 'odd';
	$level = ($revision->minor_edit) ? 'minor' : 'major';

	$xname = JText::_('WIKI_AUTHOR_UNKNOWN');
	$juser =& JUser::getInstance($revision->created_by);
	if (is_object($juser)) 
	{
		$xname = $juser->get('name');
	}

	$summary = ($revision->summary) ? $this->escape($revision->summary) : JText::_('WIKI_REVISION_NO_SUMMARY');

	switch ($revision->approved)
	{
		case 1: $status = 'approved'; break;
		case 0:
		default:
			$status = 'suggested';
			break;
	}
?>
						<tr class="<?php echo $cls; ?>">
							<td>
<?php if ($i == 1) { 
		$lastedit = $revision->created; 
		$cur = $revision->version;
?>
								( cur )
<?php } else { ?>
								(<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename='.$this->page->pagename.'&task=compare&oldid='.$revision->version.'&diff='.$cur); ?>">
									<?php echo JText::_('WIKI_HISTORY_CURRENT'); ?>
								</a>)
<?php } ?>
								&nbsp;
<?php if ($revision->version != 1) { ?>
								(<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename='.$this->page->pagename.'&task=compare&oldid='.($revision->version - 1).'&diff='.$revision->version); ?>">
									<?php echo JText::_('WIKI_HISTORY_LAST'); ?>
								</a>)
<?php } else { ?>
								( last )
<?php } ?>
							</td>
<?php if ($i == 1) { ?>
							<td>
								
							</td>
							<td>
								<input type="radio" name="diff" value="<?php echo $revision->version; ?>" checked="checked" />
							</td>
<?php } else { ?>
							<td>
								<input type="radio" name="oldid" value="<?php echo $revision->version; ?>"<?php if ($i == 2) { echo ' checked="checked"'; } ?> />
							</td>
							<td>
								
							</td>
<?php } ?>
							<td>
								<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename='.$this->page->pagename.'&version='.$revision->version); ?>" class="tooltips" title="<?php echo JText::_('WIKI_REVISION_SUMMARY').' :: '.$summary; ?>">
									<time datetime="<?php echo $revision->created; ?>"><?php echo $this->escape($revision->created); ?></time>
								</a>
							</td>
							<td>
								<?php echo $this->escape($xname); ?>
							</td>
							<td>
								<?php echo $this->escape($status); ?>
					<?php if (!$revision->approved && $this->config->get('access-manage')) { ?>
								<br />
								<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename='.$this->page->pagename.'&task=approve&oldid='.$revision->id); ?>">
									<?php echo JText::_('WIKI_ACTION_APPROVED'); ?>
								</a>
					<?php } ?>
							</td>
					<?php if ($this->config->get('access-manage')) { ?>
							<td>
								<a class="delete" href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename='.$this->page->pagename.'&task=deleterevision&oldid='.$revision->id); ?>" title="<?php echo JText::_('WIKI_REVISION_DELETE'); ?>">
									<?php echo JText::_('DELETE'); ?>
								</a>
							</td>
					<?php } ?>
						</tr>
<?php } ?>
					</tbody>
				</table>
				<p><input type="submit" value="<?php echo JText::_('WIKI_HISTORY_COMPARE'); ?>" /></p>
			</div><!-- / .container -->

		<div class="clear"></div>

		<input type="hidden" name="pagename" value="<?php echo $this->page->pagename; ?>" />
		<input type="hidden" name="scope" value="<?php echo $this->page->scope; ?>" />
		<input type="hidden" name="pageid" value="<?php echo $this->page->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
<?php if ($this->sub) { ?>
		<input type="hidden" name="active" value="<?php echo $this->sub; ?>" />
<?php } ?>
		<input type="hidden" name="task" value="compare" />
	</form>
</div><!-- / .main section -->