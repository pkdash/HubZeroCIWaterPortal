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

//database object
$database =& JFactory::getDBO();

//declare vars
$citations_require_attention = $this->citations_require_attention;
$citations_require_no_attention = $this->citations_require_no_attention;

//dont show array
$no_show = array("errors","duplicate");
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div>

<div id="import" class="section">
	
	<?php
		foreach($this->messages as $message) {
			echo "<p class=\"{$message['type']}\">" . $message['message'] . "</p>";
		}
	?>
	
	<ul id="steps">
		<li><a href="/citations/import" class="passed">Step 1<span>Upload citations file</span></a></li>
		<li><a href="/citations/import_review" class="active">Step 2<span>Preview imported citations</span></a></li>
		<li><a href="#">Step 3<span>Browse uploaded citations</span></a></li>
	</ul><!-- / #steps -->
	
	<form method="post" action="<?php echo JRoute::_('index.php?option='. $this->option . '&task=import_save'); ?>">
		<?php if($citations_require_attention) : ?>
			<table class="upload-list require-action">
				<thead>
					<tr>
						<!--<th></th>-->
						<th><?php echo count($citations_require_attention); ?> Pending Citation(s) Requiring Attention - (Click to Resolve Issue)</th>
					</tr>
				</thead>
				<tbody>
					<?php $counter = 0; ?>
					<?php foreach($citations_require_attention as $c) : ?>
						<?php
							//load the duplicate citation
							$cc = new CitationsCitation($database);
							$cc->load($c['duplicate']);
							
							//get the type
							$ct = new CitationsType($database);
							$type = $ct->getType($cc->type);
							$type_title = $type[0]['type_title'];

							//get citations tags
							$th = new TagsHandler($database);
							$th->_tbl = "citations";
							$tags = $th->get_tag_string($cc->id, 0, 0, NULL, 0, "");
							$badges = $th->get_tag_string($cc->id, 0, 0, NULL, 0, "badges");
						?>
						<tr>
							<!--<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>-->
							<td>
								<span class="citation-title"><u>Duplicate Record</u>: <?php echo html_entity_decode($c['title']); ?></span>
								<span class="click-more"><?php echo JText::_('&larr; Click to show citation details'); ?></span>
<?php if (1) { ?>
								<table class="citation-details hide">
									<thead>
										<tr>
											<th>Citation Details</th>
											<th class="options">
												<label title="Overwrite old version of citation in database and replace with new one.">
													<input 
														type="radio" 
														class="citation_require_attention_option" 
														name="citation_action_attention[<?php echo $counter; ?>]" 
														value="overwrite"
														checked="checked" /> Replace Old Version with Uploaded One
												</label>
												<label title="Keep old version and also upload new version.">
													<input 
														type="radio" 
														class="citation_require_attention_option" 
														name="citation_action_attention[<?php echo $counter; ?>]" 
														value="both" /> Keep Old and Import Uploaded Version
												</label>
												<label title="Keep old version and do nothing with new uploaded version.">
													<input 
														type="radio" 
														class="citation_require_attention_option" 
														name="citation_action_attention[<?php echo $counter; ?>]" 
														value="discard" /> Don't Import Uploaded Version
												</label>
											</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach(array_keys($c) as $k) : ?>
											<?php if(!in_array($k, $no_show)) : ?>
												<tr>
													<td class="key">
														<?php echo str_replace("_", " ", $k); ?>
													</td>
													<td>
														<table class="citation-differences">
															<tr>
																<td>Just Uploaded:</td>
																<td>
																	<span class="new insert"><?php echo html_entity_decode(nl2br($c[$k])); ?></span>
																</td>
															</tr>
															<tr>
																<td>Citation on file:</td>
																<td>
																	<span class="old delete">
																		<?php 
																			switch($k)
																			{   
																				case 'type':	echo $type_title;		break;
																				case 'tags':	echo $tags;				break;
																				case 'badges':	echo $badges;			break;
																				default:		echo html_entity_decode(nl2br($cc->$k));
																			}
																		?>
																	</span>
																</td>
															</tr>
														</table>
													</td>
												</tr>
											<?php endif; ?>
										<?php endforeach; ?>
									</tbody>
								</table>
<?php
}
?>
							</td>
						</tr>
						<?php $counter++; ?>
					<?php endforeach; ?>
				<tbody>
			</table>
		<?php endif; ?>
	
		<!-- /////////////////////////////////////// -->
		
		<?php if($citations_require_no_attention) : ?>
			<table class="upload-list no-action">
				<thead>
					<tr>
						<th><input type="checkbox" class="checkall" name="select-all-no-attention" checked="checked" /></th>
						<th><?php echo count($citations_require_no_attention); ?> Pending Citation(s) - Ready to be Imported</th>
					</tr>
				</thead>
				<tbody>
					<?php $counter = 0; ?>
						<?php foreach($citations_require_no_attention as $c) : ?>
						<tr>
							<td><input type="checkbox" class="check-single" name="citation_action_no_attention[<?php echo $counter++; ?>]" checked="checked" value="1" /></td>
							<td>
								<span class="citation-title">
									<?php 
										if(array_key_exists("title", $c))
										{
											echo html_entity_decode($c['title']);
										} 
										else 
										{
											echo "NO TITLE FOUND";
										}
									?>
								</span>
								<span class="click-more"><?php echo JText::_('&larr; Click to show citation details'); ?></span>
								<table class="citation-details hide">
									<thead>
										<tr>
											<th colspan="2"><?php echo JText::_('Citation Details'); ?></th>
										</tr>
									</thead>
									<tbody>
										<?php foreach(array_keys($c) as $k) : ?>
											<?php if(!in_array($k, $no_show)) : ?>
												<tr>
													<td class="key"><?php echo str_replace("_", " ", $k); ?></td>
													<td><?php echo html_entity_decode(nl2br($c[$k])); ?></td>
												</tr>
											<?php endif; ?>
										<?php endforeach; ?>
									</tbody>
								</table>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	
		<p class="submit">
			<input type="submit" name="submit" value="Submit Imported Citations" />
		</p>
		
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="task" value="import_save" />
	</form>
</div><!-- / .section -->
