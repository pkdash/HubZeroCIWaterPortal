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

	/* Jobs List */
	$jobs = $this->jobs;
	$option = $this->option;
	$jobs = $this->jobs;
	$filters = $this->filters;
	$allowsubscriptions = $this->allowsubscriptions;

	if($this->subscriptioncode && $this->thisemployer) {
		$this->title .= ' '.JText::_('FROM').' '.$this->thisemployer->companyName;
	}

	$html  = '';
	$now = date( 'Y-m-d H:i:s', time() );

if(!$this->mini) {
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div id="content-header-extra">
    <ul id="useroptions">
    <?php if($this->guest) { ?> 
    	<li><?php echo JText::_('PLEASE').' <a href="'.JRoute::_('index.php?option='.$option.a.'task=view').'?action=login">'.JText::_('ACTION_LOGIN').'</a> '.JText::_('ACTION_LOGIN_TO_VIEW_OPTIONS'); ?></li>
    <?php } else if($this->emp && $this->allowsubscriptions) {  ?>
    	<li><a class="myjobs" href="<?php echo JRoute::_('index.php?option='.$option.a.'task=dashboard'); ?>"><?php echo JText::_('JOBS_EMPLOYER_DASHBOARD'); ?></a></li>
        <li><a class="shortlist" href="<?php echo JRoute::_('index.php?option='.$option.a.'task=resumes').'?filterby=shortlisted'; ?>"><?php echo JText::_('JOBS_SHORTLIST'); ?></a></li>
    <?php } else if($this->admin) { ?>
    	<li><?php echo JText::_('NOTICE_YOU_ARE_ADMIN'); ?>
        	<a class="myjobs" href="<?php echo JRoute::_('index.php?option='.$option.a.'task=dashboard'); ?>"><?php echo JText::_('JOBS_ADMIN_DASHBOARD'); ?></a></li>
	<?php } else { ?>  
    	<li><a class="myresume" href="<?php echo JRoute::_('index.php?option='.$option.a.'task=addresume'); ?>"><?php echo JText::_('JOBS_MY_RESUME'); ?></a></li>
    <?php } ?>  		
	</ul>
</div><!-- / #content-header-extra -->
<?php 

		$html .= '<div class="main section">'.n;
		$html .= t.'<form method="get" action="'.JRoute::_('index.php?option='.$option.a.'task=browse').'">'.n;

		if($this->allowsubscriptions) {
			$html .= t.'<div class="aside minimenu">'.n;
			$html .= t.t.'<h3>'.JText::_('EMPLOYERS').' ';
			$html .= '</h3>'.n;
			$html .= t.t.'<ul>'.n;
			$html .= t.t.t.'<li><a href="'.JRoute::_('index.php?option='.$option.a.'task=addjob').'">'.JText::_('POST_JOB').'</a></li>'.n;
			$html .= t.t.t.'<li><a href="'.JRoute::_('index.php?option='.$option.a.'task=resumes').'">'.JText::_('BROWSE_RESUMES').'</a></li>'.n;
			$html .= t.t.'</ul>'.n;
			$html .= t.t.'<h3>'.JText::_('SEEKERS').'</h3>'.n;
			$html .= t.t.'<ul>'.n;
			$html .= t.t.t.'<li><a href="'.JRoute::_('index.php?option='.$option.a.'task=addresume').'">'.JText::_('POST_RESUME').'</a></li>'.n;
			$html .= t.t.'</ul>'.n;
			if($this->config->get('infolink'))
			{
				$html .= '<p><a href="'.$this->config->get('infolink').'">'.JText::_('LEARN_MORE').'</a> '.JText::_('ABOUT_THE_PROCESS').'.</p>'.n;
			}
		
			$html .= t.'</div><!-- / .aside -->'.n;
			$html .= t.'<div class="subject">'.n;
		}

		// show how many
		$totalnote = JText::_('NOTICE_DISPLAYING').' ';
		if($filters['start'] == 0) {
			$totalnote .= ($this->pageNav->total > count($jobs)) ? ' '.JText::_('NOTICE_TOP').' '.count($jobs).' '.JText::_('NOTICE_OUT_OF').' '.$this->pageNav->total : strtolower(JText::_('ALL')).' '.count($jobs) ;
		}
		else {
			$totalnote .= ($filters['start'] + 1);
			$totalnote .= ' - ';
			$totalnote .=$filters['start'] + count($jobs);
			$totalnote .=' out of '.$this->pageNav->total;
		}
		$totalnote .= ' '.JText::_('NOTICE_JOB_OPENINGS');

		$sortbys = array('category'=>JText::_('CATEGORY'),'opendate'=>JText::_('POSTED_DATE'),'type'=>JText::_('TYPE'));
		$filterbys = array('all'=>JText::_('ALL'),'open'=>JText::_('ACTIVE'),'closed'=>JText::_('EXPIRED'));

		$html .= '<div class="jobs_controls">'.n;
		$html .= t.t.'<fieldset>'.n;
		$html .= t.t.t.'<label> '.JText::_('ACTION_SEARCH_BY_KEYWORDS').':<span class="questionmark tooltips" title="'.JText::_('TIP_SEARCH_JOBS_BY_KEYWORDS').'"></span> '.n;
		$html .= t.t.t.'<input type="text" name="q" value="'.$filters['search'].'" />'.n;
		$html .= t.t.t.'</label> '.n;
		$html .= t.t.t.'<input type="submit" value="'.JText::_('GO').'" />'.n;
		$html .= t.t.t.'<input type="hidden" name="limitstart" value="0" />'.n;
		$html .= t.t.t.'<input type="hidden" name="performsearch" value="1" />'.n;
		$html .= t.t.'</fieldset>'.n;
		$html .= t.t.t.'<div class="note_total">'.$totalnote.'</div>'.n;
		$html .= '</div>'.n;

		}
		else {
		$html .= '<div class="main section">'.n;
		$html .= t.t.'<h3>'.JText::_('JOBS_LATEST_POSTINGS').'</h3> ';
		}

		if(count($jobs) > 0 ) {
			$jt = new JobType ( $this->database );
			$jc = new JobCategory ( $this->database );
	//		$curtype = $jobs[0]->type > 0 ? $jt->getType($jobs[0]->type) : '';
	//		$curcat = $jobs[0]->cid > 0 ? $jc->getCat($jobs[0]->cid) : '';
			$sortbyDir = $this->filters['sortdir'] == 'ASC' ? 'DESC' : 'ASC';

			$html .= t.t.'<table class="postings">'.n;
			$html .= t.t.t.'<thead>'.n;
			$html .= t.t.t.'<tr class="headings">'.n;
			
			$html .= t.t.t.t.'<th';
			if($this->filters['sortby'] == 'title') { 
				$html .= ' class="activesort"'; 
			} 
			$html .= '><a href="'. JRoute::_('index.php?option='.$this->option.a.'task=' . $this->task).'/?sortby=title'.a.'sortdir='.$sortbyDir. a . 'q=' . $this->filters['search'] .'" class="re_sort">';
			$html .= JText::_('JOBS_TABLE_JOB_TITLE').'</a></th>'.n;
			
			if($this->admin && !$this->emp && !$this->mini) {
			$html .= t.t.t.t.'<th>'.JText::_('JOBS_TABLE_STATUS').'</th>'.n;
			}
			$html .= t.t.t.t.'<th>'.JText::_('JOBS_TABLE_COMPANY').'</th>'.n;
			
			$html .= t.t.t.t.'<th>'.JText::_('JOBS_TABLE_LOCATION').'</th>'.n;
			$html .= t.t.t.t.'<th';
			if($this->filters['sortby'] == 'category') { 
				$html .= ' class="activesort"'; 
			} 
			$html .= '><a href="'. JRoute::_('index.php?option='.$this->option.a.'task=' . $this->task).'/?sortby=category'.a.'sortdir='.$sortbyDir. a . 'q=' . $this->filters['search'] . '" class="re_sort">';
			$html .= JText::_('JOBS_TABLE_CATEGORY').'</a></th>'.n;

			$html .= t.t.t.t.'<th';
			if($this->filters['sortby'] == 'type') { 
				$html .= ' class="activesort"'; 
			} 
			$html .= '><a href="'. JRoute::_('index.php?option='.$this->option.a.'task=' . $this->task).'/?sortby=type'.a.'sortdir='.$sortbyDir. a . 'q=' . $this->filters['search'] . '" class="re_sort">';
			$html .= JText::_('JOBS_TABLE_TYPE').'</a></th>'.n;

			$html .= t.t.t.t.'<th';
			if($this->filters['sortby'] == 'opendate') { 
				$html .= ' class="activesort"'; 
			} 
			$html .= '><a href="'. JRoute::_('index.php?option='.$this->option.a.'task=' . $this->task).'/?sortby=opendate'.a.'sortdir='.$sortbyDir. a . 'q=' . $this->filters['search'] . '" class="re_sort">';
			$html .= JText::_('JOBS_TABLE_POSTED').'</a></th>'.n;

			$html .= t.t.t.t.'<th>'.JText::_('JOBS_TABLE_APPLY_BY').'</th>'.n;
			if ($filters['search']) {
				$html .= t.t.t.t.'<th>'.JText::_('JOBS_TABLE_RELEVANCE').'</th>'.n;
			}
			$html .= t.t.t.'</tr>'.n;
			$html .= t.t.t.'</thead>'.n;

			$wikiconfig = array(
				'option'   => $this->option,
				'scope'    => 'jobs.browse',
				'pagename' => 'jobs',
				'pageid'   => 1,
				'filepath' => '',
				'domain'   => ''
			);
			ximport('Hubzero_Wiki_Parser');
			$p =& Hubzero_Wiki_Parser::getInstance();

			$maxscore = $filters['search'] && $jobs[0]->keywords > 0 ? $jobs[0]->keywords : 1;

			$html .= t.t.t.'<tbody>'.n;
			for ($i=0, $n=count( $jobs ); $i < $n; $i++)
			{
				//$txt = (is_object($p)) ? $p->parse( stripslashes($jobs[$i]->description) ) : nl2br(stripslashes($jobs[$i]->description));
				$txt = $p->parse(stripslashes($jobs[$i]->description), $wikiconfig);
				$closedate = ($jobs[$i]->closedate && $jobs[$i]->closedate !='0000-00-00 00:00:00') ? JHTML::_('date',$jobs[$i]->closedate, '%d&nbsp;%b&nbsp;%y',0) : 'ASAP';
				if($jobs[$i]->closedate !='0000-00-00 00:00:00' && $jobs[$i]->closedate < $now)
				{
					$closedate = 'closed';
				}
				$curtype = $jt->getType($jobs[$i]->type);
				$curcat = $jc->getCat($jobs[$i]->cid);

				// compute relevance to search keywords
				if ($filters['search']) {
					$relscore = $jobs[$i]->keywords > 0 ? floor(($jobs[$i]->keywords * 100) / $maxscore) : 0;
				}

				// what's the job status?
				if ($this->admin && !$this->emp && !$this->mini) {
					$status = '';
					$class =  '';
					switch ( $jobs[$i]->status )
					{
						case 0:    		$status =  JText::_('JOB_STATUS_PENDING');
										$class  = 'post_pending';
																				break;
						case 1:    		$status =  $jobs[$i]->inactive &&  $jobs[$i]->inactive < $now
										? JText::_('JOB_STATUS_INVALID')
										: JText::_('JOB_STATUS_ACTIVE');
										$class  = $jobs[$i]->inactive &&  $jobs[$i]->inactive < $now
										? 'post_invalidsub'
										: 'post_active';
																				break;
						case 3:    		$status =  JText::_('JOB_STATUS_INACTIVE');
										$class  = 'post_inactive';
										break;
						case 4:    		$status =  JText::_('JOB_STATUS_DRAFT');
										$class  = 'post_draft';
										break;
					}
				}
				$html .= t.t.t.'<tr>'.n;
				$html .= t.t.t.t.'<td class="jobtitle">'.n;
				$html .= t.t.t.t.'<a href="'.JRoute::_('index.php?option='.$option.a.'task=job'.a.'code='.$jobs[$i]->code).'" title="'.Hubzero_View_Helper_Html::shortenText($txt, 250, 0).'">';
				$html .= $jobs[$i]->title.'</a>'.n;
				$html .= t.t.t.t.'</td>'.n;
				if ($this->admin && !$this->emp && !$this->mini) {
					$html .= t.t.t.t.'<td ';
					$html .= $class ? 'class="'.$class.'"' : '';
					$html .='>'.n;
					$html .= t.t.t.t.t.$status;
					$html .= t.t.t.t.'</td>'.n;
				}
				$html .= t.t.t.t.'<td>'.n;
				$html .= t.t.t.t.t.$jobs[$i]->companyName;
				$html .= t.t.t.t.'</td>'.n;
				$html .= t.t.t.t.'<td>'.n;
				$html .= t.t.t.t.t.$jobs[$i]->companyLocation.', '.$jobs[$i]->companyLocationCountry.n;
				$html .= t.t.t.t.'</td>'.n;
				$html .= t.t.t.t.'<td class="secondary">'.$curcat.'</td>'.n;
				$html .= t.t.t.t.'<td class="secondary">'.$curtype.'</td>'.n;
				$html .= t.t.t.t.'<td class="secondary">'.n;
				$html .= t.t.t.t.t.'<span class="datedisplay">'.JHTML::_('date',$jobs[$i]->added, '%d&nbsp;%b&nbsp;%y',0).'</span>'.n;
				$html .= t.t.t.t.'</td>'.n;
				$html .= t.t.t.t.'<td>'.n;
				if ($jobs[$i]->applied) {
					$applieddate = JHTML::_('date',$jobs[$i]->applied, '%d&nbsp;%b&nbsp;%y',0);
					$html .= '<span class="alreadyapplied">'.JText::_('JOB_APPLIED_ON').' <span class="datedisplay">'.$applieddate.'</span></span>';
				}
				else if ($jobs[$i]->withdrawn) {
					$withdrew = JHTML::_('date',$jobs[$i]->withdrawn, '%d&nbsp;%b&nbsp;%y',0);
					$html .= '<span class="withdrawn">'.JText::_('JOB_WITHDREW_ON').' <span class="datedisplay">'.$withdrew.'</span></span>';
				}
				else {
					$html .= $closedate ? '<span class="datedisplay">'.$closedate.'</span>' : '';
				}
				$html .= t.t.t.t.'</td>'.n;
				if ($filters['search']) {
					$html .= t.t.t.t.'<td class="relevancescore ';
					$html .= $relscore > 0 ? 'yes' : 'no';
					$html .= '">'.$relscore.' %</td>'.n;
				}
				$html .= t.t.t.'</tr>'.n;
			}
			$html .= t.t.t.'</tbody>'.n;
			$html .= t.t.'</table>'.n;
		}
		else {
			$html .= t.t.t.'<p>'.JText::_('NO_JOBS_FOUND');
			if ($this->subscriptioncode) {
				if ($this->thisemployer) {
					$html .= ' '.JText::_('FROM').' '.JText::_('EMPLOYER').' '.$this->thisemployer->companyName.' ('.$this->subscriptioncode.')';
				}
				else {
					$html .= ' '.JText::_('FROM').' '.JText::_('REQUESTED_EMPLOYER').' ('.$this->subscriptioncode.')';
				}
				$html .= '. <a href="'.JRoute::_('index.php?option='.$option.a.'task=browse').'"">'.JText::_('ACTION_BROWSE_ALL_JOBS').'</a>';
			}
			$html .= '.</p>'.n;
		}

		if (!$this->mini) {
			// Insert page navigation
			$pagenavhtml = $this->pageNav->getListFooter();
			$pagenavhtml = str_replace('jobs/?','jobs/browse/?',$pagenavhtml);
			$html .= t.t.$pagenavhtml;
			if ($allowsubscriptions) {
				$html .= t.'</div><!-- / .subject -->'.n;
			}
			$html .= t.'</form>'.n;
		}
		$html .= t.'</div>'.n;

		echo $html;
?>