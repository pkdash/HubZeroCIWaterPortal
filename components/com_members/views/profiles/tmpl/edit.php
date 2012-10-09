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

$surname = stripslashes($this->profile->get('surname'));
$givenName = stripslashes($this->profile->get('givenName'));
$middleName = stripslashes($this->profile->get('middleName'));

if (!$surname) {
	$bits = explode(' ', $name);
	$surname = array_pop($bits);
	if (count($bits) >= 1) {
		$givenName = array_shift($bits);
	}
	if (count($bits) >= 1) {
		$middleName = implode(' ',$bits);
	}
}

$html  = '<div id="content-header">'."\n";
$html .= "\t".'<h2>'.$this->title.'</h2>'."\n";
$html .= '</div><!-- / #content-header-extra -->'."\n";


$html .= '<div id="content-header-extra">'."\n";
$html .= "\t".'<ul id="useroptions">'."\n";
$html .= "\t\t".'<li class="last"><a href="'.JRoute::_('index.php?option='.$this->option.'&task=cancel&id='. $this->profile->get('uidNumber')) .'">'.JText::_('CANCEL').'</a></li>'."\n";
$html .= "\t".'</ul>'."\n";
$html .= '</div><!-- / #content-header-extra -->'."\n";
$html .= '<div class="main section">'."\n"; 


$html .= "\t".'<form id="hubForm" class="edit-profile" method="post" action="index.php" enctype="multipart/form-data">'."\n";

if ($this->authorized === 'admin') {
	$html .= "\t".'<div class="explaination">'."\n";
	$html .= "\t\t".'<p>'.JText::_('The following options are available to administrators only.').'</p>'."\n";
	$html .= "\t".'</div>'."\n";
	$html .= "\t".'<fieldset>'."\n";
	$html .= "\t\t".'<legend>'.JText::_('Admin Options').'</legend>'."\n";
	$html .= "\t\t".'<label>'."\n";
	$html .= "\t\t\t".'<input type="checkbox" class="option" name="profile[vip]" value="1"';
	if ($this->profile->get('vip') == 1) {
		$html .= ' checked="checked"';
	}
	$html .= '/>'."\n";
	$html .= "\t\t\t".JText::_('VIP')."\n";
	$html .= "\t\t".'</label>'."\n";
	$html .= "<span class=\"hint\">".JText::_('**The following options are available to administrators only.')."</span>";
	$html .= "\t".'</fieldset><div class="clear"></div>'."\n";
} else {
	$html .= "\t\t".'<input type="hidden" name="profile[vip]" value="'. $this->profile->get('vip') .'" />'."\n";
}

//$html .= "\t".'<div class="explaination">'."\n";
//$html .= "\t\t".'<p class="help">'.JText::_('E-mail may be changed with <a href="/hub/registration/edit">this form</a>.')."\n";
//$html .= "\t\t".'<p class="help">'.JText::_('Passwords can be changed with <a href="'.JRoute::_('index.php?option='.$this->option.a.'id='.$this->profile->get('uidNumber').a.'task=changepassword').'">this form</a>.').'</p>'."\n";

//$mwconfig =& JComponentHelper::getParams( 'com_mw' );
//$enabled = $mwconfig->get('mw_on');
//if ($enabled) {
//	$html .= "\t\t".'<p class="help">'.JText::_('Request for more storage or sessions may be made with <a href="'.JRoute::_('index.php?option='.$this->option.a.'id='.$this->profile->get('uidNumber').a.'task=raiselimit').'">this form</a>.').'</p>'."\n";
//}
//$html .= "\t".'</div>'."\n";
$html .= "\t".'<fieldset>'."\n";
$html .= "\t\t".'<legend>'.JText::_('Contact Information').'</legend>'."\n";
$html .= "\t\t".'<input type="hidden" name="id" value="'. $this->profile->get('uidNumber') .'" />'."\n";
$html .= "\t\t".'<input type="hidden" name="option" value="'. $this->option .'" />'."\n";
$html .= "\t\t".'<input type="hidden" name="task" value="save" />'."\n";

$html .= "\t\t".'<label>'."\n";
$html .= "\t\t\t".'<input type="checkbox" class="option" name="profile[public]" value="1"';
if ($this->profile->get('public') == 1) {
	$html .= ' checked="checked"';
}
$html .= '/>'."\n";
$html .= "\t\t\t".JText::_('List me in the Members directory (others may view my profile)')."\n";
$html .= "\t\t".'</label>'."\n";

if ($this->registration->Fullname != REG_HIDE) {
	$required = ($this->registration->Fullname == REG_REQUIRED) ? ' <span class="required">'.JText::_('REQUIRED').'</span>' : '';
	$message = (!empty($this->xregistration->_invalid['name'])) ? Hubzero_View_Helper_Html::error($this->xregistration->_invalid['name']) : '';
	$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';

	$html .= "\t\t".'<div class="threeup group">'."\n";
	$html .= "\t\t".'<label'.$fieldclass.'>'."\n";
	$html .= "\t\t\t".JText::_('FIRST_NAME').': '.$required."\n";
	$html .= "\t\t\t".'<input type="text" name="name[first]" value="'. $givenName .'" />'."\n";
	$html .= "\t\t".'</label>'."\n";

	$html .= "\t\t".'<label>'."\n";
	$html .= "\t\t\t".JText::_('MIDDLE_NAME').':'."\n";
	$html .= "\t\t\t".'<input type="text" name="name[middle]" value="'. $middleName .'" />'."\n";
	$html .= "\t\t".'</label>'."\n";

	$html .= "\t\t".'<label'.$fieldclass.'>'."\n";
	$html .= "\t\t\t".JText::_('LAST_NAME').': '.$required."\n";
	$html .= "\t\t\t".'<input type="text" name="name[last]" value="'. $surname .'" />'."\n";
	$html .= "\t\t".'</label>'."\n";
	$html .= "\t\t".'</div>'."\n";
	$html .= $message;
}

if ($this->registration->Email != REG_HIDE
 || $this->registration->ConfirmEmail != REG_HIDE) {
	$html .= "\t\t".'<div class="group twoup">'."\n";

	// Email
	if ($this->registration->Email != REG_HIDE) {
		$required = ($this->registration->Email == REG_REQUIRED) ? '<span class="required">'.JText::_('required').'</span>' : '';
		$message = (!empty($this->xregistration->_invalid['email'])) ? Hubzero_View_Helper_Html::error($this->xregistration->_invalid['email'],'span') : '';
		$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';

		$html .= "\t\t\t".'<label '.$fieldclass.'>'."\n";
		$html .= "\t\t\t\t".JText::_('Valid E-mail').': '.$required."\n";
		$html .= "\t\t\t\t".'<input name="email" id="email" type="text" value="'.htmlentities($this->profile->get('email'),ENT_COMPAT,'UTF-8').'" />'."\n";
		$html .= ($message) ? "\t\t\t\t".$message."\n" : '';
		$html .= "\t\t\t".'</label>'."\n";
	}

	// Confirm email
	if ($this->registration->ConfirmEmail != REG_HIDE) {
		$message = '';
		$confirmEmail = $this->profile->get('email');
		if (!empty($this->xregistration->_invalid['email'])) {
			$confirmEmail = '';
		}
		if (!empty($this->xregistration->_invalid['confirmEmail'])) {
			$message = Hubzero_View_Helper_Html::error($this->xregistration->_invalid['confirmEmail'],'span');
		}

		$required = ($this->registration->ConfirmEmail == REG_REQUIRED) ? '<span class="required">'.JText::_('required').'</span>' : '';
		$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';

		$html .= "\t\t\t".'<label'.$fieldclass.'>'."\n";
		$html .= "\t\t\t\t".JText::_('Confirm E-mail').': '.$required."\n";
		$html .= "\t\t\t\t".'<input name="email2" id="email2" type="text" value="'.htmlentities($confirmEmail,ENT_COMPAT,'UTF-8').'" />'."\n";
		$html .= ($message) ? "\t\t\t\t".$message."\n" : '';
		$html .= "\t\t\t".'</label>'."\n";
	}

	$html .= "\t\t".'</div>'."\n";

	if ($this->registration->Email != REG_HIDE) {
		$html .= "\t\t".Hubzero_View_Helper_Html::warning('Important! If you change your E-Mail address you <strong>must</strong> confirm receipt of the confirmation e-mail in order to re-activate your account.');
	}
}

if ($this->registration->URL != REG_HIDE) {
	$required = ($this->registration->URL == REG_REQUIRED) ? '<span class="required">'.JText::_('REQUIRED').'</span>' : '';
	$message = (!empty($this->xregistration->_invalid['web'])) ? Hubzero_View_Helper_Html::error($this->xregistration->_invalid['web']) : '';
	$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';

	$html .= "\t\t".'<label'.$fieldclass.'>'."\n";
	$html .= "\t\t\t".JText::_('WEBSITE').': '.$required."\n";
	$html .= "\t\t\t".'<input type="text" name="web" value="'. stripslashes($this->profile->get('url')) .'" /></td>'."\n";
	$html .= $message;
	$html .= "\t\t".'</label>'."\n";
}

if ($this->registration->Phone != REG_HIDE) {
	$required = ($this->registration->Phone == REG_REQUIRED) ? '<span class="required">'.JText::_('REQUIRED').'</span>' : '';
	$message = (!empty($this->xregistration->_invalid['phone'])) ? Hubzero_View_Helper_Html::error($this->xregistration->_invalid['phone']) : '';
	$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';

	$html .= "\t\t".'<label'.$fieldclass.'>'."\n";
	$html .= "\t\t\t".JText::_('Phone').': '.$required."\n";
	$html .= "\t\t\t".'<input type="text" name="phone" value="'. stripslashes($this->profile->get('phone')) .'" /></td>'."\n";
	$html .= $message;
	$html .= "\t\t".'</label>'."\n";
}

$html .= "\t".'</fieldset><div class="clear"></div>'."\n";
$html .= "\t".'<fieldset>'."\n";
$html .= "\t\t".'<legend>'.JText::_('Personal Information').'</legend>'."\n";

if ($this->registration->Employment != REG_HIDE) {
	$required = ($this->registration->Employment == REG_REQUIRED) ? '<span class="required">'.JText::_('REQUIRED').'</span>' : '';
	$message = (!empty($this->xregistration->_invalid['orgtype'])) ? Hubzero_View_Helper_Html::error($this->xregistration->_invalid['orgtype']) : '';
	$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';

	$orgtype = stripslashes($this->profile->get('orgtype'));

	$html .= "\t\t".'<label'.$fieldclass.'>'.JText::_('Employment Status').': '.$required."\n";
	$html .= "\t\t".'<select name="orgtype" id="orgtype">'."\n";
	if (empty($orgtype)) {
		$html .= "\t\t\t".'<option value="" selected="selected">'.JText::_('(select from list)').'</option>'."\n";
	}
	$html .= "\t\t\t".'<option value="nationallab"';
	if ($orgtype == 'nationallab') {
		$html .= ' selected="selected"';
	}
	$html .= '>'.JText::_('National Laboratory').'</option>'."\n";
	$html .= "\t\t\t".'<option value="universityundergraduate"';
	if ($orgtype == 'universityundergraduate') {
		$html .= ' selected="selected"';
	}
	$html .= '>'.JText::_('University / College Undergraduate').'</option>'."\n";
	$html .= "\t\t\t".'<option value="universitygraduate"';
	if ($orgtype == 'universitygraduate') {
		$html .= ' selected="selected"';
	}
	$html .= '>'.JText::_('University / College Graduate Student').'</option>'."\n";
	$html .= "\t\t\t".'<option value="universityfaculty"';
	if ($orgtype == 'universityfaculty' || $orgtype == 'university') {
		$html .= ' selected="selected"';
	}
	$html .= '>'.JText::_('University / College Faculty').'</option>'."\n";
	$html .= "\t\t\t".'<option value="universitystaff"';
	if ($orgtype == 'universitystaff') {
		$html .= ' selected="selected"';
	}
	$html .= '>'.JText::_('University / College Staff').'</option>'."\n";
	$html .= "\t\t\t".'<option value="precollegestudent"';
	if ($orgtype == 'precollegestudent') {
		$html .= ' selected="selected"';
	}
	$html .= '>'.JText::_('K-12 (Pre-College) Student').'</option>'."\n";
	$html .= "\t\t\t".'<option value="precollegefacultystaff"';
	if ($orgtype == 'precollege' || $orgtype == 'precollegefacultystaff') {
		$html .= ' selected="selected"';
	}
	$html .= '>'.JText::_('K-12 (Pre-College) Faculty/Staff').'</option>'."\n";
	$html .= "\t\t\t".'<option value="industry"';
	if ($orgtype == 'industry') {
		$html .= ' selected="selected"';
	}
	$html .= '>'.JText::_('Industry / Private Company').'</option>'."\n";
	$html .= "\t\t\t".'<option value="government"';
	if ($orgtype == 'government') {
		$html .= ' selected="selected"';
	}
	$html .= '>'.JText::_('Government Agency').'</option>'."\n";
	$html .= "\t\t\t".'<option value="military"';
	if ($orgtype == 'military') {
		$html .= ' selected="selected"';
	}
	$html .= '>'.JText::_('Military').'</option>'."\n";
	$html .= "\t\t\t".'<option value="unemployed"';
	if ($orgtype == 'unemployed') {
		$html .= ' selected="selected"';
	}
	$html .= '>'.JText::_('Retired / Unemployed').'</option>'."\n";
	$html .= "\t\t".'</select>'."\n";
	$html .= $message;
	$html .= "\t\t\t".'</label>'."\n";
}

if ($this->registration->Organization != REG_HIDE) {
	$required = ($this->registration->Organization == REG_REQUIRED) ? '<span class="required">'.JText::_('REQUIRED').'</span>' : '';
	$message = (!empty($this->xregistration->_invalid['org'])) ? Hubzero_View_Helper_Html::error($this->xregistration->_invalid['org']) : '';
	$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';

	$organization = stripslashes($this->profile->get('organization'));
	$orgtext = $organization;
	$org_known = 0;

	//$orgs = array();
	//include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_hub'.DS.'xorganization.php' );
	include_once( JPATH_ROOT.DS.'components'.DS.'com_register'.DS.'tables'.DS.'organization.php' );
	$database =& JFactory::getDBO();
	//$xo = new XOrganization( $database );
	$xo = new RegisterOrganization( $database );
	$orgs = $xo->getOrgs();

	if (count($orgs) <= 0) {
		$orgs[0] = 'Purdue University';
		$orgs[1] = 'University of Pennsylvania';
		$orgs[2] = 'University of California at Berkeley';
		$orgs[3] = 'Vanderbilt University';
	}

	foreach ($orgs as $org)
	{
		$org_known = ($org == $organization) ? 1 : 0;
	}

	$html .= "\t\t".'<label'.$fieldclass.'>'."\n";
	$html .= "\t\t\t".JText::_('ORG').': '.$required."\n";
	$html .= "\t\t\t".'<select name="org">'."\n";
	$html .= "\t\t\t\t".'<option value=""';
	if (!$org_known) {
		$html .= ' selected="selected"';
	}
	$html .= '>';
	if ($org_known) {
		$html .= JText::_('(other / none)');
	} else {
		$html .= JText::_('(select from list or enter below)');
	}
	$html .= '</option>'."\n";
	foreach ($orgs as $org)
	{
		$html .= "\t\t\t\t".'<option value="'. htmlentities($org,ENT_COMPAT,'UTF-8') .'"';
		if ($org == $organization) {
			$orgtext = '';
			$html .= ' selected="selected"';
		}
		$html .= '>' . htmlentities($org) . '</option>'."\n";
	}
	$html .= "\t\t\t".'</select>'."\n";
	$html .= $message;
	$html .= "\t\t".'</label>'."\n";
	$html .= "\t\t".'<label for="orgtext" id="orgtextlabel">'.JText::_('Enter organization below').'</label>'."\n";
	$html .= "\t\t".'<input type="text" name="orgtext" id="orgtext" value="'. htmlentities($orgtext,ENT_COMPAT,'UTF-8') .'" />'."\n";
}

if ($this->registration->Interests != REG_HIDE) {
	$required = ($this->registration->Interests == REG_REQUIRED) ? '<span class="required">'.JText::_('REQUIRED').'</span>' : '';
	$message = (!empty($this->xregistration->_invalid['interests'])) ? Hubzero_View_Helper_Html::error($this->xregistration->_invalid['interests']) : '';
	$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';

	JPluginHelper::importPlugin( 'hubzero' );
	$dispatcher =& JDispatcher::getInstance();
	$tf = $dispatcher->trigger( 'onGetMultiEntry', array(array('tags', 'tags', 'actags','',stripslashes($this->tags))) );

	$html .= "\t\t".'<label'.$fieldclass.'>'."\n";
	$html .= "\t\t\t".JText::_('MEMBER_FIELD_TAGS').': '.$required."\n";
	if (count($tf) > 0) {
		$html .= $tf[0];
	} else {
		$html .= "\t\t\t".'<input type="text" name="tags" value="'. $this->tags .'" />'."\n";
	}
	$html .= "\t\t\t".'<span>'.JText::_('MEMBER_FIELD_TAGS_HINT').'</span>'."\n";
	$html .= $message;
	$html .= "\t\t".'</label>'."\n";
}

ximport('Hubzero_Wiki_Editor');
$editor =& Hubzero_Wiki_Editor::getInstance();

$html .= "\t\t".'<label for="profilebio">'."\n";
$html .= "\t\t\t".JText::_('BIO').':'."\n";
$html .= "\t\t\t".$editor->display('profile[bio]', 'profilebio', stripslashes($this->profile->get('bio')), '', '40', '10');
$html .= "\t\t\t".'<span class="hint"><a class="popup" href="'.JRoute::_('index.php?option=com_topics&scope=&pagename=Help:WikiFormatting').'">Wiki formatting</a> is allowed for Bios.</span>'."\n";
$html .= "\t\t".'</label>'."\n";
$html .= "\t".'</fieldset><div class="clear"></div>'."\n";

if ($this->registration->Citizenship != REG_HIDE
 || $this->registration->Residency != REG_HIDE
 || $this->registration->Sex != REG_HIDE
 || $this->registration->Disability != REG_HIDE
 || $this->registration->Hispanic != REG_HIDE
 || $this->registration->Race != REG_HIDE)
{
	$html .= t.'<fieldset>'."\n";
	$html .= "\t\t".'<legend>'.JText::_('Demographics').'</legend>'."\n";

	if ($this->registration->Citizenship != REG_HIDE
	 || $this->registration->Residency != REG_HIDE) {
		ximport('Hubzero_Geo');
		$countries = Hubzero_Geo::getcountries();
	}

	if ($this->registration->Citizenship != REG_HIDE) {
		$required = ($this->registration->Citizenship == REG_REQUIRED) ? ' <span class="required">'.JText::_('REQUIRED').'</span>' : '';
		$message = (!empty($this->xregistration->_invalid['countryorigin'])) ? Hubzero_View_Helper_Html::error($this->xregistration->_invalid['countryorigin']) : '';
		$fieldclass = ($message) ? ' class="fieldsWithErrors"' : '';

		$countryorigin = $this->profile->get('countryorigin');

		$html .= "\t\t".'<fieldset'.$fieldclass.'>'."\n";
		$html .= "\t\t\t".'<legend>'.JText::_('Are you a Legal Citizen or Permanent Resident of the <abbr title="United States">US</abbr>?').$required.'</legend>'."\n";
		$html .= $message;
		$html .= "\t\t\t".'<label><input type="radio" class="option" name="corigin_us" id="corigin_usyes" value="yes"';
		if (strcasecmp($countryorigin,'US') == 0) {
			$html .= ' checked="checked"';
		}
		$html .= ' /> '.JText::_('Yes').'</label>'."\n";
		$html .= "\t\t\t\t".'<label><input type="radio" class="option" name="corigin_us" id="corigin_usno" value="no"';
		if (!empty($countryorigin) && (strcasecmp($countryorigin,'US') != 0)) {
			$html .= ' checked="checked"';
		}
		$html .= ' /> '.JText::_('No').'</label>'."\n";
		$html .= "\t\t\t\t".'<label>'.JText::_('Citizen or Permanent Resident of').':'."\n";
		$html .= "\t\t\t\t".'<select name="corigin" id="corigin">'."\n";
		if (!$countryorigin || $countryorigin == 'US') {
			$html .= "\t\t\t\t".' <option value="">'.JText::_('(select from list)').'</option>'."\n";
		}
		foreach ($countries as $country)
		{
			if ($country['code'] != 'US') {
				$html .= "\t\t\t\t".' <option value="' . $country['code'] . '"';
				if ($countryorigin == $country['code']) {
					$html .= ' selected="selected"';
				}
				$html .= '>' . htmlentities($country['name'],ENT_COMPAT,'UTF-8') . '</option>'."\n";
			}
		}
		$html .= "\t\t\t\t".'</select></label>'."\n";
		$html .= "\t\t".'</fieldset>'."\n";
	}

	if ($this->registration->Residency != REG_HIDE) {
		$required = ($this->registration->Residency == REG_REQUIRED) ? ' <span class="required">'.JText::_('REQUIRED').'</span>' : '';
		$message = (!empty($this->xregistration->_invalid['countryresident'])) ? Hubzero_View_Helper_Html::error($this->xregistration->_invalid['countryresident']) : '';
		$fieldclass = ($message) ? ' class="fieldsWithErrors"' : '';

		$countryresident = $this->profile->get('countryresident');

		$html .= "\t\t".'<fieldset'.$fieldclass.'>';
		$html .= "\t\t\t".'<legend>'.JText::_('Do you Currently Live in the <abbr title="United States">US</abbr>?').$required.'</legend>'."\n";
		$html .= $message;
		$html .= "\t\t\t".'<label><input type="radio" class="option" name="cresident_us" id="cresident_usyes" value="yes"';
		if (strcasecmp($countryresident,'US') == 0) {
			$html .= ' checked="checked"';
		}
		$html .= ' /> '.JText::_('Yes').'</label>'."\n";
		$html .= "\t\t\t\t".'<label><input type="radio" class="option" name="cresident_us" id="cresident_usno" value="no"';
		if (!empty($countryresident) && strcasecmp($countryresident,'US') != 0) {
			$html .= ' checked="checked"';
		}
		$html .= ' /> '.JText::_('No').'</label>'."\n";
		$html .= "\t\t\t\t".'<label>'.JText::_('Currently Living in').':'."\n";
		$html .= "\t\t\t\t".'<select name="cresident" id="cresident">'."\n";
		if (!$countryresident || strcasecmp($countryresident,'US') == 0) {
			$html .= "\t\t\t"."\t\t".' <option value="">'.JText::_('(select from list)').'</option>'."\n";
		}
		foreach ($countries as $country)
		{
			if (strcasecmp($country['code'],"US") != 0) {
				$html .= "\t\t\t"."\t\t".'<option value="' . $country['code'] . '"';
				if (strcasecmp($countryresident,$country['code']) == 0) {
					$html .= ' selected="selected"';
				}
				$html .= '>' . htmlentities($country['name'],ENT_COMPAT,'UTF-8') . '</option>'."\n";
			}
		}
		$html .= "\t\t\t\t".'</select></label>'."\n";
		$html .= "\t\t".'</fieldset>'."\n";
	}

	if ($this->registration->Sex != REG_HIDE) {
		$message = (!empty($this->xregistration->_invalid['countryresident'])) ? Hubzero_View_Helper_Html::error($this->xregistration->_invalid['countryresident']) : '';
		$fieldclass = ($message) ? ' class="fieldsWithErrors"' : '';
		$required = ($this->registration->Sex == REG_REQUIRED) ? ' <span class="required">'.JText::_('REQUIRED').'</span>' : '';

		$html .= "\t".'<fieldset'.$fieldclass.'>'."\n";
		$html .= $message;
		$html .= "\t\t".'<legend>'.JText::_('Gender').':'.$required.'</legend>'."\n";
		$html .= "\t\t".'<input type="hidden" name="sex" value="unspecified" />'."\n";
		$html .= "\t\t".'<label><input type="radio" name="sex" value="male" class="option"';
		$html .= ($this->profile->get('gender') == 'male') ? ' checked="checked"' : '';
		$html .= ' /> '.JText::_('Male').'</label>'."\n";
		$html .= "\t\t".'<label><input type="radio" name="sex" value="female" class="option"';
		$html .= ($this->profile->get('gender') == 'female') ? ' checked="checked"' : '';
		$html .= ' /> '.JText::_('Female').'</label>'."\n";
		$html .= "\t\t".'<label><input type="radio" name="sex" value="refused" class="option"';
		$html .= ($this->profile->get('gender') == 'refused') ? ' checked="checked"' : '';
		$html .= ' /> '.JText::_('Do not wish to reveal').'</label>'."\n";
		$html .= "\t".'</fieldset>'."\n";
	}

	// Disability
	if ($this->registration->Disability != REG_HIDE) {
		$message = (!empty($this->xregistration->_invalid['disability'])) ? Hubzero_View_Helper_Html::error($this->xregistration->_invalid['disability']) : '';
		$fieldclass = ($message) ? ' class="fieldsWithErrors"' : '';
		$required = ($this->registration->Disability == REG_REQUIRED) ? ' <span class="required">'.JText::_('REQUIRED').'</span>' : '';

		$disabilities = $this->profile->get('disability');
		if (!is_array($disabilities)) {
			$disabilities = array();
		}

		$disabilityyes = false;
		$disabilityother = '';
		foreach ($disabilities as $disabilityitem)
		{
			if ($disabilityitem != 'no'
			 && $disabilityitem != 'refused') {
				if (!$disabilityyes) {
					$disabilityyes = true;
				}

				if ($disabilityitem != 'blind'
				 && $disabilityitem != 'deaf'
				 && $disabilityitem != 'physical'
				 && $disabilityitem != 'learning'
				 && $disabilityitem != 'vocal'
				 && $disabilityitem != 'yes') {
					$disabilityother = $disabilityitem;
				}
			}
		}

		$html .= "\t\t".'<fieldset'.$fieldclass.'>'."\n";
		$html .= $message;
		$html .= "\t\t\t".'<legend>'.JText::_('Disability').':'.$required.'</legend>'."\n";
		$html .= "\t\t\t\t".'<label><input type="radio" class="option" name="disability" id="disabilityyes" value="yes"';
		if ($disabilityyes) {
			$html .= ' checked="checked"';
		}
		$html .= ' /> '.JText::_('Yes').'</label>'."\n";
		$html .= "\t\t\t".'<fieldset>'."\n";
		$html .= "\t\t\t\t".'<label><input type="checkbox" class="option" name="disabilityblind" id="disabilityblind" ';
		if (in_array('blind', $disabilities)) {
			$html .= 'checked="checked" ';
		}
		$html .= '/> '.JText::_('Blind / Visually Impaired').'</label>'."\n";
		$html .= "\t\t\t\t".'<label><input type="checkbox" class="option" name="disabilitydeaf" id="disabilitydeaf" ';
		if (in_array('deaf', $disabilities)) {
			$html .= 'checked="checked" ';
		}
		$html .= '/> '.JText::_('Deaf / Hard of Hearing').'</label>'."\n";
		$html .= "\t\t\t\t".'<label><input type="checkbox" class="option" name="disabilityphysical" id="disabilityphysical" ';
		if (in_array('physical', $disabilities)) {
			$html .= 'checked="checked" ';
		}
		$html .= '/> '.JText::_('Physical / Orthopedic Disability').'</label>'."\n";
		$html .= "\t\t\t\t".'<label><input type="checkbox" class="option" name="disabilitylearning" id="disabilitylearning" ';
		if (in_array('learning', $disabilities)) {
			$html .= 'checked="checked" ';
		}
		$html .= '/> '.JText::_('Learning / Cognitive Disability').'</label>'."\n";
		$html .= "\t\t\t\t".'<label><input type="checkbox" class="option" name="disabilityvocal" id="disabilityvocal" ';
		if (in_array('vocal', $disabilities)) {
			$html .= 'checked="checked" ';
		}
		$html .= '/> '.JText::_('Vocal / Speech Disability').'</label>'."\n";
		$html .= "\t\t\t\t".'<label>'.JText::_('Other (please specify)').':'."\n";
		$html .= "\t\t\t\t".'<input name="disabilityother" id="disabilityother" type="text" value="'. htmlentities($disabilityother,ENT_COMPAT,'UTF-8') .'" /></label>'."\n";
		$html .= "\t\t\t".'</fieldset>'."\n";
		$html .= "\t\t\t".'<label><input type="radio" class="option" name="disability" id="disabilityno" value="no"';
		if (in_array('no', $disabilities)) {
			$html .= ' checked="checked"';
		}
		$html .= '> '.JText::_('No (none)').'</label>'."\n";
		$html .= "\t\t\t".'<label><input type="radio" class="option" name="disability" id="disabilityrefused" value="refused"';
		if (in_array('refused', $disabilities)) {
			$html .= ' checked="checked"';
		}
		$html .= '> '.JText::_('Do not wish to reveal').'</label>'."\n";
		$html .= "\t\t".'</fieldset>'."\n";
	}

	// Hispanic
	if ($this->registration->Hispanic != REG_HIDE) {
		$message = (!empty($this->xregistration->_invalid['hispanic'])) ? Hubzero_View_Helper_Html::error($this->xregistration->_invalid['hispanic']) : '';
		$fieldclass = ($message) ? ' class="fieldsWithErrors"' : '';
		$required = ($this->registration->Hispanic == REG_REQUIRED) ? ' <span class="required">'.JText::_('REQUIRED').'</span>' : '';

		$hispanic = $this->profile->get('hispanic');
		if (!is_array($hispanic)) {
			$hispanic = array();
		}

		$hispanicyes = false;
		$hispanicother = '';
		foreach ($hispanic as $hispanicitem)
		{
			if ($hispanicitem != 'no'
			 && $hispanicitem != 'refused') {
				if (!$hispanicyes) {
					$hispanicyes = true;
				}

				if ($hispanicitem != 'cuban'
				 && $hispanicitem != 'mexican'
				 && $hispanicitem != 'puertorican') {
					$hispanicother = $hispanicitem;
				}
			}
		}

		$html .= "\t\t".'<fieldset'.$fieldclass.'>'."\n";
		$html .= $message;
		$html .= "\t\t\t".'<legend>'.JText::_('Hispanic or Latino').':'.$required.'</legend>'."\n";
		$html .= "\t\t\t\t".'<label><input type="radio" class="option" name="hispanic" id="hispanicyes" value="yes" ';
		if ($hispanicyes) {
			$html .= 'checked="checked"';
		}
		$html .= ' /> '.JText::_('Yes (Hispanic Origin or Descent)').'</label>'."\n";
		$html .= "\t\t\t".'<fieldset>'."\n";
		$html .= "\t\t\t\t".'<label><input type="checkbox" class="option" name="hispaniccuban" id="hispaniccuban" ';
		if (in_array('cuban', $hispanic)) {
			$html .= 'checked="checked" ';
		}
		$html .= '/> '.JText::_('Cuban').'</label>'."\n";
		$html .= "\t\t\t\t".'<label><input type="checkbox" class="option" name="hispanicmexican" id="hispanicmexican" ';
		if (in_array('mexican', $hispanic)) {
			$html .= 'checked="checked" ';
		}
		$html .= '/> '.JText::_('Mexican American or Chicano').'</label>'."\n";
		$html .= "\t\t\t\t".'<label><input type="checkbox" class="option" name="hispanicpuertorican" id="hispanicpuertorican" ';
		if (in_array('puertorican', $hispanic)) {
			$html .= 'checked="checked" ';
		}
		$html .= '/> '.JText::_('Puerto Rican').'</label>'."\n";
		$html .= "\t\t\t\t".'<label>'.JText::_('Other Hispanic or Latino').':'."\n";
		$html .= "\t\t\t\t".'<input name="profile[hispanic][other]" id="hispanicother" type="text" value="'. htmlentities($hispanicother,ENT_COMPAT,'UTF-8') .'" /></label>'."\n";
		$html .= "\t\t\t".'</fieldset>'."\n";
		$html .= "\t\t\t".'<label><input type="radio" class="option" name="hispanic" id="hispanicno" value="no"';
		if (in_array('no', $hispanic)) {
			$html .= ' checked="checked"';
		}
		$html .= '> '.JText::_('No (not Hispanic or Latino)').'</label>'."\n";
		$html .= "\t\t\t".'<label><input type="radio" class="option" name="hispanic" id="hispanicrefused" value="refused"';
		if (in_array('refused', $hispanic)) {
			$html .= ' checked="checked"';
		}
		$html .= '> '.JText::_('Do not wish to reveal').'</label>'."\n";
		$html .= "\t\t".'</fieldset>'."\n";
	}

	// Race
	if ($this->registration->Race != REG_HIDE) {
		$message = (!empty($this->xregistration->_invalid['race'])) ? Hubzero_View_Helper_Html::error($this->xregistration->_invalid['race']) : '';
		$fieldclass = ($message) ? ' class="fieldsWithErrors"' : '';
		$required = ($this->registration->Race == REG_REQUIRED) ? ' <span class="required">'.JText::_('REQUIRED').'</span>' : '';

		$race = $this->profile->get('race');
		if (!is_array($race)) {
			$race = array();
		}

		$html .= "\t\t".'<fieldset'.$fieldclass.'>'."\n";
		$html .= $message;
		$html .= "\t\t\t".'<legend>'.JText::_('Racial Background').':'.$required.'</legend>'."\n";
		$html .= "\t\t\t".'<p class="hint">'.JText::_('Select one or more that apply.').'</p>'."\n";

		$html .= "\t\t\t".'<label><input type="checkbox" class="option" name="racenativeamerican" id="racenativeamerican" value="nativeamerican" ';
		if (in_array('nativeamerican', $race)) {
			$html .= 'checked="checked" ';
		}
		$html .= '/> '.JText::_('American Indian or Alaska Native').'</label>'."\n";
		$html .= "\t\t\t".'<label class="indent">'.JText::_('Tribal Affiliation(s)').':'."\n";
		$html .= "\t\t\t".'<input name="profile[nativetribe]" id="racenativetribe" type="text" value="'. htmlentities($this->profile->get('nativeTribe'),ENT_COMPAT,'UTF-8') .'" /></label>'."\n";
		$html .= "\t\t\t".'<label><input type="checkbox" class="option" name="raceasian" id="raceasian" ';
		if (in_array('asian', $race)) {
			$html .= 'checked="checked" ';
		}
		$html .= '/> '.JText::_('Asian').'</label>'."\n";
		$html .= "\t\t\t".'<label><input type="checkbox" class="option" name="raceblack" id="raceblack" ';
		if (in_array('black', $race)) {
			$html .= 'checked="checked" ';
		}
		$html .= '/> '.JText::_('Black or African American').'</label>'."\n";
		$html .= "\t\t\t".'<label><input type="checkbox" class="option" name="racehawaiian" id="racehawaiian" ';
		if (in_array('hawaiian', $race)) {
			$html .= 'checked="checked" ';
		}
		$html .= '/> '.JText::_('Native Hawaiian or Other Pacific Islander').'</label>'."\n";
		$html .= "\t\t\t".'<label><input type="checkbox" class="option" name="racewhite" id="racewhite" ';
		if (in_array('white', $race)) {
			$html .= 'checked="checked" ';
		}
		$html .= '/> '.JText::_('White').'</label>'."\n";
		$html .= "\t\t\t".'<label><input type="checkbox" class="option" name="racerefused" id="racerefused" ';
		if (in_array('refused', $race)) {
			$html .= 'checked="checked" ';
		}
		$html .= '/> '.JText::_('Do not wish to reveal').'</label>'."\n";
		$html .= "\t\t".'</fieldset>'."\n";
	}

	$html .= "\t".'</fieldset><div class="clear"></div>'."\n";
}

if ($this->registration->OptIn != REG_HIDE) // newsletter Opt-In
{
	$required = ($this->registration->OptIn == REG_REQUIRED) ? '<span class="required">'.JText::_('required').'</span>' : '';
	$message = (!empty($this->xregistration->_invalid['mailPreferenceOption'])) ? Hubzero_View_Helper_Html::error($this->xregistration->_invalid['mailPreferenceOption']) : '';
	$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';

	$html .= "\t".'<fieldset>'."\n";
	$html .= "\t\t".'<legend>'.JText::_('Updates').'</legend>'."\n";
	$html .= "\t\t".'<input type="hidden" name="mailPreferenceOption" value="unset" />'."\n";
	$html .= "\t\t".'<label '.$fieldclass.'><input type="checkbox" class="option" id="mailPreferenceOption" name="mailPreferenceOption" value="1" ';
	if ($this->profile->get('mailPreferenceOption')) {
		$html .= 'checked="checked" ';
	}
	$html .= '/> '.$required.' '.JText::_('Yes, I would like to receive newsletters and other updates by e-mail.').'</label>'."\n";
	$html .= $message;
	$html .= "\t".'</fieldset><div class="clear"></div>'."\n";
}

$html .= "\t".'<fieldset>'."\n";
$html .= "<a name=\"memberpicture\"></a>";
$html .= "\t\t".'<legend>'.JText::_('MEMBER_PICTURE').'</legend>'."\n";
$html .= "\t\t".'<iframe width="100%" height="350" border="0" name="filer" id="filer" src="index.php?option='.$this->option.'&amp;controller=media&amp;tmpl=component&amp;file='.stripslashes($this->profile->get('picture')).'&amp;id='.$this->profile->get('uidNumber').'"></iframe>'."\n";
$html .= "\t".'</fieldset><div class="clear"></div>'."\n";

$html .= "\t".'<p class="submit"><input type="submit" name="submit" value="'.JText::_('SAVE').'" /></p>'."\n";
$html .= '</form>'."\n";
$html .= '</div>'."\n";

echo $html;
