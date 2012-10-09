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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   GNU General Public License, version 2 (GPLv2) 
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

	/* Intro */
	$jconfig = JFactory::getConfig();
	$sitename = $jconfig->getValue('config.sitename');
	
	$promoline = $this->config->get('promoline') ? $this->config->get('promoline') : '';
	$infolink = $this->config->get('infolink') ? $this->config->get('infolink') : '';
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div id="content-header-extra">
	<ul id="useroptions">
	<?php if ($this->guest) { ?> 
		<li><?php echo JText::_('PLEASE').' <a href="'.JRoute::_('index.php?option='.$option . '&task=view').'?action=login">'.JText::_('ACTION_LOGIN').'</a> '.JText::_('ACTION_LOGIN_TO_VIEW_OPTIONS'); ?></li>
	<?php } else if ($this->emp && $this->allowsubscriptions) {  ?>
		<li><a class="myjobs" href="<?php echo JRoute::_('index.php?option='.$option . '&task=dashboard'); ?>"><?php echo JText::_('JOBS_EMPLOYER_DASHBOARD'); ?></a></li>
		<li><a class="shortlist" href="<?php echo JRoute::_('index.php?option='.$option . '&task=resumes').'?filterby=shortlisted'; ?>"><?php echo JText::_('JOBS_SHORTLIST'); ?></a></li>
	<?php } else if ($this->admin) { ?>
		<li>
			<!-- <?php echo JText::_('NOTICE_YOU_ARE_ADMIN'); ?> -->
			<a class="myjobs" href="<?php echo JRoute::_('index.php?option='.$option . '&task=dashboard'); ?>"><?php echo JText::_('JOBS_ADMIN_DASHBOARD'); ?></a>
		</li>
	<?php } else { ?> 
		<li><a class="myresume" href="<?php echo JRoute::_('index.php?option='.$option . '&task=addresume'); ?>"><?php echo JText::_('JOBS_MY_RESUME'); ?></a></li>
	<?php } ?> 
	</ul>
</div><!-- / #content-header-extra -->
<?php if ($this->msg) { ?>
	<p class="help"><?php echo $this->msg; ?></p>
<?php } ?>  

<?php if ($this->allowsubscriptions) { ?>
<div id="introduction" class="section">
	<div class="aside">
		<h3><?php echo JText::_('JOBS_QUESTIONS_LINK'); ?></h3>
		<p><?php echo '<a href="'.$infolink.'">'.JText::_('LEARN_MORE').'</a> '.JText::_('ABOUT_THE_PROCESS'); ?></p>
	</div><!-- / .aside -->
	<div class="subject">
		<div class="three columns first">
			<p class="intronote"><?php echo JText::_('TIP_ENJOY_COMMUNITY_EXPOSURE').' '.$sitename.'. '.JText::_('TIP_SERVICES_FREE'); echo ' '.JText::_('TIP_EMPLOYERS_SUBSCRIPTION_REQUIRED'); ?></p>
		</div>
		<div class="three columns second">
			<h3><?php echo JText::_('EMPLOYERS'); ?></h3>		   
			<ul>
				<li><a href="<?php echo JRoute::_('index.php?option='.$option . '&task=resumes'); ?>"><?php echo JText::_('ACTION_BROWSE_RESUMES'); ?></a></li>
				<li><a href="<?php echo JRoute::_('index.php?option='.$option . '&task=addjob'); ?>"><?php echo JText::_('ACTION_POST_JOB'); ?></a></li>				
			</ul>
			 <?php if ($promoline) { ?> 
			<p class="promo"><?php echo $promoline; ?></p>   
			<?php } ?>	 
		</div>
		<div class="three columns third">
			<h3><?php echo JText::_('SEEKERS'); ?></h3>
		   
			<ul>
		   		<li><a href="<?php echo JRoute::_('index.php?option='.$option . '&task=browse'); ?>"><?php echo JText::_('ACTION_BROWSE_JOBS'); ?></a></li>
				<li><a href="<?php echo JRoute::_('index.php?option='.$option . '&task=addresume'); ?>"><?php echo JText::_('ACTION_POST_RESUME'); ?></a></li>
			</ul>
		</div>
		<div class="clear"></div>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / #introduction.section -->
<?php } ?>