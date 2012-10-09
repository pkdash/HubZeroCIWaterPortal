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

//import helper class
ximport('Hubzero_View_Helper_Html');
ximport('Hubzero_Wiki_Parser');

$wikiconfig = array(
	'option'   => $this->option,
	'scope'    => $this->group->get('gidNumber') . DS . 'blog',
	'pagename' => 'group',
	'pageid'   => '',
	'filepath' => $this->path,
	'domain'   => $this->group->get('cn')
);

$p =& Hubzero_Wiki_Parser::getInstance();
?>

<?php if ($this->canpost || ($this->authorized == 'manager' || $this->authorized == 'admin')) { ?>
<ul id="page_options">
<?php if ($this->canpost) { ?>
	<li>
		<a class="add btn" href="<?php echo JRoute::_('index.php?option=com_groups&gid='.$this->group->cn.'&active=blog&task=new'); ?>">
			<?php echo JText::_('New entry'); ?>
		</a>
	</li>
<?php } ?>
<?php if ($this->authorized == 'manager' || $this->authorized == 'admin') { ?>
	<li>
		<a class="config btn" href="<?php echo JRoute::_('index.php?option=com_groups&gid='.$this->group->cn.'&active=blog&task=settings'); ?>" title="<?php echo JText::_('Edit Settings'); ?>">
			<?php echo JText::_('Settings'); ?>
		</a>
	</li>
<?php } ?>
</ul>
<?php } ?>

<form method="get" action="<?php echo JRoute::_('index.php?option=com_groups&gid='.$this->group->cn.'&active=blog&task=browse'); ?>" id="blogentries">
	<div class="aside">
		<div class="container blog-entries-years">
			<h4><?php echo JText::_('Entries By Year'); ?></h4>
			<ol>
				<?php if ($this->firstentry) { ?>
					<?php 
						$start = intval(substr($this->firstentry,0,4));
						$now = date("Y");
					?>
					<?php for ($i=$now, $n=$start; $i >= $n; $i--) { ?>
						<li>
							<a href="<?php echo JRoute::_('index.php?option=com_groups&gid='.$this->group->cn.'&active=blog&scope='.$i); ?>">
								<?php echo $i; ?>
							</a>
							<?php if (($this->year && $i == $this->year) || (!$this->year && $i == $now)) { ?>
								<ol>
									<?php
										$m = array(
											'PLG_GROUPS_BLOG_JANUARY',
											'PLG_GROUPS_BLOG_FEBRUARY',
											'PLG_GROUPS_BLOG_MARCH',
											'PLG_GROUPS_BLOG_APRIL',
											'PLG_GROUPS_BLOG_MAY',
											'PLG_GROUPS_BLOG_JUNE',
											'PLG_GROUPS_BLOG_JULY',
											'PLG_GROUPS_BLOG_AUGUST',
											'PLG_GROUPS_BLOG_SEPTEMBER',
											'PLG_GROUPS_BLOG_OCTOBER',
											'PLG_GROUPS_BLOG_NOVEMBER',
											'PLG_GROUPS_BLOG_DECEMBER'
										);
										if ($i == $now) {
											$months = date("m");
										} else {
											$months = 12;
										}
									?>
									<?php for ($k=0, $z=$months; $k < $z; $k++) { ?>
										<li>
											<a<?php if ($this->month && $this->month == ($k+1)) { echo ' class="active"'; } ?> href="<?php echo JRoute::_('index.php?option=com_groups&gid='.$this->group->get('cn').'&active=blog&scope='.$i.'/'.sprintf( "%02d",($k+1),1)); ?>">
												<?php echo JText::_($m[$k]); ?>
											</a>
										</li>
									<?php } ?>
								</ol>
							<?php } ?>
						</li>
					<?php } ?>
				<?php } ?>
			</ol>
		</div>

<?php if ($this->popular) { ?>
		<div class="container blog-popular-entries">
			<h4><?php echo JText::_('Popular Entries'); ?></h4>
			<ol>
			<?php foreach ($this->popular as $row) { ?>
				<li>
					<a href="<?php echo JRoute::_('index.php?option=com_groups&gid='.$this->group->cn.'&active=blog&scope='.JHTML::_('date',$row->publish_up, $this->yearFormat, $this->tz).'/'.JHTML::_('date',$row->publish_up, $this->monthFormat, $this->tz).'/'.$row->alias); ?>">
						<?php echo $this->escape(stripslashes($row->title)); ?>
					</a>
				</li>
			<?php } ?>
			</ol>
		</div><!-- / .blog-popular-entries -->
<?php } ?>

<?php if ($this->recent) { ?>
		<div class="container blog-recent-entries">
			<h4><?php echo JText::_('Recent Entries'); ?></h4>
			<ol>
			<?php foreach ($this->recent as $row) { ?>
				<li>
					<a href="<?php echo JRoute::_('index.php?option=com_groups&gid='.$this->group->cn.'&active=blog&scope='.JHTML::_('date',$row->publish_up, $this->yearFormat, $this->tz).'/'.JHTML::_('date', $row->publish_up, $this->monthFormat, $this->tz).'/'.$row->alias); ?>">
						<?php echo $this->escape(stripslashes($row->title)); ?>
					</a>
				</li>
			<?php } ?>
			</ol>
		</div><!-- / .blog-recent-entries -->
<?php } ?>
	</div><!-- / .aside -->
	
	<div class="subject">
	<?php if ($this->getError()) : ?>
		<p class="error"><?php echo $this->getError(); ?></p>
	<?php endif; ?>

		<div class="container data-entry">
			<input class="entry-search-submit" type="submit" value="Search" />
			<fieldset class="entry-search">
				<legend>Search for articles</legend>
				<label for="entry-search-field">Enter keyword or phrase</label>
				<input type="text" name="search" id="entry-search-field" value="<?php echo $this->escape(utf8_encode(stripslashes($this->search))); ?>" />
			</fieldset>
		</div><!-- / .container -->

		<div class="container">
			<h3>
<?php if (isset($this->search) && $this->search) { ?>
				<?php echo JText::sprintf('Search for "%s"', $this->escape($this->search)); ?>
<?php } else if (!isset($this->year) || !$this->year) { ?>
				<?php echo JText::_('Latest Entries'); ?>
<?php } else { 
		$format = '%b %Y';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$format = 'M Y';
		}
				$archiveDate  = $this->year;
				$archiveDate .= ($this->month) ? '-' . $this->month : '-01';
				$archiveDate .= '-01 00:00:00';
				echo JHTML::_('date', $archiveDate, $format, $this->tz);
} ?>
				<?php
					if ($this->config->get('feeds_enabled', 1)) :
						$path  = 'index.php?option='.$this->option.'&gid='.$this->group->cn.'&active=blog&scope=feed.rss';
						$path .= ($this->year)  ? '&year=' . $this->year   : '';
						$path .= ($this->month) ? '&month=' . $this->month : '';
						$feed = JRoute::_($path);
						if (substr($feed, 0, 4) != 'http') 
						{
							$feed = rtrim($live_site, DS) . DS . ltrim($feed, DS);
						}
						$feed = str_replace('https:://', 'http://', $feed);
				?>
				<a class="feed" href="<?php echo $feed; ?>">
					<?php echo JText::_('RSS Feed'); ?>
				</a>
				<?php endif; ?>
			</h3>
		<?php if ($this->rows) { ?>
			<ol class="blog-entries">
<?php 
			$cls = 'even';
			foreach ($this->rows as $row)
			{
				$cls = ($cls == 'even') ? 'odd' : 'even';
				
				switch ($row->state)
				{
					case 1:
						$state = JText::_('Public');
						$cls = "public";
						break;
					case 2:
						$state = JText::_('Registered members');
						$cls = "registered";
						break;
					case 0:
					default:
						$state = JText::_('Private');
						$cls = "private";
						break;
				}
?>
				<li class="<?php echo $cls; ?>" id="e<?php echo $row->id; ?>">
					<h4 class="entry-title">
						<a href="<?php echo JRoute::_('index.php?option=com_groups&gid='.$this->group->cn.'&active=blog&scope='.JHTML::_('date', $row->publish_up, $this->yearFormat, $this->tz) . '/' . JHTML::_('date', $row->publish_up, $this->monthFormat, $this->tz) . '/' . $row->alias); ?>">
							<?php echo $this->escape(stripslashes($row->title)); ?>
						</a>
					</h4>
					<dl class="entry-meta">
						<dt>
							<span>
								<?php echo JText::sprintf('Entry #%s', $row->id); ?>
							</span>
						</dt>
						<dd class="date">
							<time datetime="<?php echo $row->publish_up; ?>">
								<?php echo JHTML::_('date', $row->publish_up, $this->dateFormat, $this->tz); ?>
							</time>
						</dd>
						<dd class="time">
							<time datetime="<?php echo $row->publish_up; ?>">
								<?php echo JHTML::_('date', $row->publish_up, $this->timeFormat, $this->tz); ?>
							</time>
						</dd>
						<dd class="author">
							<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $row->created_by); ?>">
								<?php echo $this->escape(stripslashes($row->name)); ?>
							</a>
						</dd>
<?php if ($row->allow_comments == 1) { ?>
						<dd class="comments">
							<a href="<?php echo JRoute::_('index.php?option=com_groups&gid='.$this->group->cn.'&active=blog&scope='.JHTML::_('date', $row->publish_up, $this->yearFormat, $this->tz) . '/' . JHTML::_('date', $row->publish_up, $this->monthFormat, $this->tz) . '/' . $row->alias . '#comments'); ?>">
								<?php echo JText::sprintf('PLG_GROUPS_BLOG_NUM_COMMENTS', $row->comments); ?>
							</a>
						</dd>
<?php } else { ?>
						<dd class="comments">
							<span>
								<?php echo JText::_('PLG_GROUPS_BLOG_COMMENTS_OFF'); ?>
							</span>
						</dd>
<?php } ?>
<?php if ($this->juser->get('id') == $row->created_by || $this->authorized == 'manager' || $this->authorized == 'admin') { ?>
						<dd class="state <?php echo $cls; ?>">
							<?php echo $state; ?>
						</dd>
<?php } ?>
						<dd class="entry-options">
<?php if ($this->juser->get('id') == $row->created_by || $this->authorized == 'manager' || $this->authorized == 'admin') { ?>
							<a class="edit" href="<?php echo JRoute::_('index.php?option=com_groups&gid='.$this->group->cn.'&active=blog&task=edit&entry='.$row->id); ?>" title="<?php echo JText::_('Edit'); ?>">
								<?php echo JText::_('Edit'); ?>
							</a>
							<a class="delete" href="<?php echo JRoute::_('index.php?option=com_groups&gid='.$this->group->cn.'&active=blog&task=delete&entry='.$row->id); ?>" title="<?php echo JText::_('Delete'); ?>">
								<?php echo JText::_('Delete'); ?>
							</a>
<?php } ?>
						</dd>
					</dl>
					<div class="entry-content">
						<p>
							<?php 
							$content = plgGroupsBlog::stripWiki($row->content);
							echo Hubzero_View_Helper_Html::shortenText($content, 300, 0);
							?> 
						</p>
					</div>
				</li>
	<?php } ?>
			</ol>
<?php } else { ?>
			<ol class="blog-entries">
				<li>
					<p>Currently there are no blog entries.</p>
				</li>
			</ol>
<?php } ?>

			<?php echo $this->pagenavhtml; ?>
		</div>
	</div><!-- / .subject -->
</div><!-- /.main -->

