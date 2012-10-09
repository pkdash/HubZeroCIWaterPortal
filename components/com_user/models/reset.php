<?php
/**
 * @version		$Id: reset.php 21046 2011-03-31 16:11:40Z dextercowley $
 * @package		Joomla
 * @subpackage	User
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.model');

/**
 * User Component Reset Model
 *
 * @package		Joomla
 * @subpackage	User
 * @since		1.5
 */
class UserModelReset extends JModel
{
	/**
	 * Registry namespace prefix
	 *
	 * @var	string
	 */
	var $_namespace	= 'com_user.reset.';

	/**
	 * Verifies the validity of a username/e-mail address
	 * combination and creates a token to verify the request
	 * was initiated by the account owner.  The token is
	 * sent to the account owner by e-mail
	 *
	 * @since	1.5
	 * @param	string	Username string
	 * @param	string	E-mail address
	 * @return	bool	True on success/false on failure
	 */
	function requestReset($username)
	{
		jimport('joomla.mail.helper');
		jimport('joomla.user.helper');

		$db = &JFactory::getDBO();

		// Build a query to find the user
		$query	= 'SELECT id, email FROM #__users'
				. ' WHERE username = '.$db->Quote($username)
				. ' AND block = 0';

		$db->setQuery($query);
		$result = $db->loadAssoc();

		// Check the results
		if (empty($result))
		{
			$this->setError(JText::_('COULD_NOT_FIND_USER'));
			return false;
		}

		$id = $result['id'];
		$email = $result['email'];
		
		// Generate a new token
		$token = JUtility::getHash(JUserHelper::genRandomPassword());
		$salt = JUserHelper::getSalt('crypt-md5');
		$hashedToken = md5($token.$salt).':'.$salt;

		$query	= 'UPDATE #__users'
				. ' SET activation = '.$db->Quote($hashedToken)
				. ' WHERE id = '.(int) $id
				. ' AND block = 0';

		$db->setQuery($query);

		// Save the token
		if (!$db->query())
		{
			$this->setError(JText::_('DATABASE_ERROR'));
			return false;
		}

		// Send the token to the user via e-mail
		if (!$this->_sendConfirmationMail($email, $token))
		{
			return false;
		}

		return true;
	}

	/**
	 * Checks a user supplied token for validity
	 * If the token is valid, it pushes the token
	 * and user id into the session for security checks.
	 *
	 * @since	1.5
	 * @param	token	An md5 hashed randomly generated string
	 * @return	bool	True on success/false on failure
	 */
	function confirmReset($token, $username)
	{
		global $mainframe;

		jimport('joomla.user.helper');

		if(strlen($token) != 32) {
			$this->setError(JText::_('INVALID_TOKEN'));
			return false;
		}

		$db	= &JFactory::getDBO();
		$db->setQuery('SELECT id, activation FROM #__users WHERE block = 0 AND username = '.$db->Quote($username));

		$row = $db->loadObject();

		// Verify the token
		if (!$row)
		{
			$this->setError(JText::_('INVALID_TOKEN'));
			return false;
		}

		$parts	= explode( ':', $row->activation );
		$crypt	= $parts[0];
		if (!isset($parts[1])) {
			$this->setError(JText::_('INVALID_TOKEN'));
			return false;
		}
		$salt	= $parts[1];
		$testcrypt = JUserHelper::getCryptedPassword($token, $salt);

		// Verify the token
		if (!($crypt == $testcrypt))
		{
			$this->setError(JText::_('INVALID_TOKEN'));
			return false;
		}

		// Push the token and user id into the session
		$mainframe->setUserState($this->_namespace.'token',	$crypt.':'.$salt);
		$mainframe->setUserState($this->_namespace.'id',	$row->id);

		return true;
	}

	/**
	 * Takes the new password and saves it to the database.
	 * It will only save the password if the user has the
	 * correct user id and token stored in her session.
	 *
	 * @since	1.5
	 * @param	string	New Password
	 * @param	string	New Password
	 * @return	bool	True on success/false on failure
	 */
	function completeReset($password1, $password2)
	{
		jimport('joomla.user.helper');

		global $mainframe;

		// Get the necessary variables
		$id	= $mainframe->getUserState($this->_namespace.'id');

		// Load some needed Hubzero libraries
		ximport('Hubzero_User_Password');
		ximport('Hubzero_Registration_Helper');
		ximport('Hubzero_User_Helper');
		ximport('Hubzero_User_Profile');
		ximport('Hubzero_Password_Rule');

		// Initiate profile classs
		$profile = new Hubzero_User_Profile();
		$profile->load( $id );
		
		// Get the juser object
		$user = new JUser($id);

		// Fire the onBeforeStoreUser trigger
		JPluginHelper::importPlugin('user');
		$dispatcher =& JDispatcher::getInstance();
		$dispatcher->trigger('onBeforeStoreUser', array($user->getProperties(), false));
		
		if (Hubzero_User_Helper::isXDomainUser($user->get('id'))) {
			JError::raiseError( 403, JText::_('This is a linked account. To change your password you must change it using the procedures available where the account you are linked to is managed.') );
			return;
		}

		$password_rules = Hubzero_Password_Rule::getRules();

		if (!empty($password1)) {
			$msg = Hubzero_Password_Rule::validate($password1,$password_rules,$profile->get('username'));
		} 
		else {
                        $msg = array();
		}

		if (!$password1 || !$password2) {
			$this->setError( JText::_('you must enter your new password twice to ensure we have it correct') );
		} elseif ($password1 != $password2) {
			$this->setError( JText::_('the new password and confirmation you entered do not match. Please try again') );
		} elseif (!Hubzero_Registration_Helper::validpassword($password1)) {
			$this->setError( JText::_('the password you entered was invalid password. You may be using characters that are not allowed') );
		} elseif (!empty($msg)) {
			$this->setError( JText::_('the password does not meet site password requirements. Please choose a password meeting all the requirements listed below.') );
                }

		if ($this->getError()) {
			$this->setError( $this->getError() );
			return false;
		}

		// Encrypt the password and update the profile
		$result = Hubzero_User_Password::changePassword($profile->get('username'), $password1);

		// Save the changes
		if (!$result) {
			$this->setError( JText::_('There was an error changing your password.') );
			return false;
		}

		// Fire the onAfterStoreUser trigger
		$dispatcher->trigger('onAfterStoreUser', array($user->getProperties(), false, $result, $this->getError()));

		// Flush the variables from the session
		$mainframe->setUserState($this->_namespace.'id',	null);
		$mainframe->setUserState($this->_namespace.'token',	null);

		return true;
	}

	/**
	 * Sends a password reset request confirmation to the
	 * specified e-mail address with the specified token.
	 *
	 * @since	1.5
	 * @param	string	An e-mail address
	 * @param	string	An md5 hashed randomly generated string
	 * @return	bool	True on success/false on failure
	 */
	function _sendConfirmationMail($email, $token)
	{
		$config		= &JFactory::getConfig();
		$uri		= &JFactory::getURI();
		$url		= rtrim(JURI::base(),'/').JRoute::_('index.php?option=com_user&view=reset&layout=confirm');
		$sitename	= $config->getValue('sitename');

		// Set the e-mail parameters
		$from		= $config->getValue('mailfrom');
		$fromname	= $config->getValue('fromname');
		$subject	= JText::sprintf('PASSWORD_RESET_CONFIRMATION_EMAIL_TITLE', $sitename);
		$body		= JText::sprintf('PASSWORD_RESET_CONFIRMATION_EMAIL_TEXT', $sitename, $token, $url);

		// Send the e-mail
		if (!JUtility::sendMail($from, $fromname, $email, $subject, $body))
		{
			$this->setError('ERROR_SENDING_CONFIRMATION_EMAIL');
			return false;
		}

		return true;
	}
}
