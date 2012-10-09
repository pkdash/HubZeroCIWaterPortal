<?php

class Hubzero_Ldap
{
	public static function getLDO($debug = 0)
	{
		static $conn = false;
		
		if ($conn !== false)
		{
			return $conn;
		}
	
		$ldap_params = JComponentHelper::getParams('com_system');

		$acctman   = $ldap_params->get('ldap_managerdn','cn=admin');
		$acctmanPW = $ldap_params->get('ldap_managerpw','');
		$pldap     = $ldap_params->get('ldap_primary', 'ldap://localhost');
	
		$negotiate_tls = $ldap_params->get('ldap_tls', 0);
		$port          = '389';

		if (!is_numeric($port))
		{
			$port = '389';

			$pattern = "/^\s*(ldap[s]{0,1}:\/\/|)([^:]*)(\:(\d+)|)\s*$/";

			if (preg_match($pattern, $pldap, $matches))
			{
				$pldap = $matches[2];

				if ($matches[1] == 'ldaps://')
				{
					$negotiate_tls = false;
				}

				if (isset($matches[4]) && is_numeric($matches[4]))
				{
					$port = $matches[4];
				}
			}
		}

		$conn = @ldap_connect($pldap, $port);

		if ($conn === false)
		{
			if ($debug)
			{
				Hubzero_Factory::getLogger()->logDebug("getLDO(): ldap_connect($pldap,$port) failed. [" . posix_getpid() . "] " . ldap_error($conn));
			}
			
			return false;
		}

		if ($debug)
		{
			Hubzero_Factory::getLogger()->logDebug("getLDO(): ldap_connect($pldap,$port) success. ");
		}

		if (@ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3) == false)
		{
			if ($debug)
			{
				Hubzero_Factory::getLogger()->logDebug("getLDO(): ldap_set_option(LDAP_OPT_PROTOCOL_VERSION, 3) failed: " . ldap_error($conn));
			}
				
			$conn = false;
			return false;
		}

		if ($debug)
		{
			Hubzero_Factory::getLogger()->logDebug("getLDO(): ldap_set_option(LDAP_OPT_PROTOCOL_VERSION, 3) success.");
		}

		if (@ldap_set_option($conn, LDAP_OPT_RESTART, 1) == false)
		{
			if ($debug)
			{
				Hubzero_Factory::getLogger()->logDebug("getLDO(): ldap_set_option(LDAP_OPT_RESTART, 1) failed: " . ldap_error($conn));
			}
			
			$conn = false;
			return false;
		}

		if ($debug)
		{
			Hubzero_Factory::getLogger()->logDebug("getLDO(): ldap_set_option(LDAP_OPT_RESTART, 1) success.");
		}

		if (!@ldap_set_option($conn, LDAP_OPT_REFERRALS, false))
		{
			if ($debug)
			{
				Hubzero_Factory::getLogger()->logDebug("getLDO(): ldap_set_option(LDAP_OPT_REFERRALS, 0) failed: " . ldap_error($conn));
			}
			
			$conn = false;	
			return false;
		}

		if ($debug)
		{
			Hubzero_Factory::getLogger()->logDebug("getLDO(): ldap_set_option(LDAP_OPT_REFERRALS, 0) success.");
		}

		if ($negotiate_tls)
		{
			if (!@ldap_start_tls($conn))
			{
				if ($debug)
				{
					Hubzero_Factory::getLogger()->logDebug("getLDO(): ldap_start_tls() failed: " . ldap_error($conn));
				}
				
				$conn = false;	
				return false;
			}

			if ($debug)
			{
				Hubzero_Factory::getLogger()->logDebug("getLDO(): ldap_start_tls() success.");
			}
		}

		if (@ldap_bind($conn, $acctman, $acctmanPW) == false)
		{
			$err     = ldap_errno($conn);
			$errstr  = ldap_error($conn);
			$errstr2 = ldap_err2str($err);
			
			if ($debug)
			{
				Hubzero_Factory::getLogger()->logDebug("getLDO(): ldap_bind($acctman) failed. [" . posix_getpid() . "] " .  $errstr);
			}
			
			$conn = false;	
			return false;
		}

		if ($debug)
		{
			Hubzero_Factory::getLogger()->logDebug("getLDO(): ldap_bind() success.");
		}
		
		return $conn;
	}
	
	public static function syncUser($user)
	{
		$db = JFactory::getDBO();
	
		if (empty($db))
		{
			return false;
		}
		
		$conn = self::getLDO();
	
		if (empty($conn))
		{
			return false;
		}
		
		$query = "SELECT u.id AS uidNumber, u.username AS uid, u.name AS cn, " .
				" p.gidNumber, p.homeDirectory, p.loginShell, " .
				" pwd.passhash AS userPassword, pwd.shadowLastChange, pwd.shadowMin, pwd.shadowMax, pwd.shadowWarning, " .
				" pwd.shadowInactive, pwd.shadowExpire, pwd.shadowFlag " .
				" FROM #__users AS u " .
				" LEFT JOIN #__users_password AS pwd ON u.id = pwd.user_id " .
				" LEFT JOIN #__xprofiles AS p ON u.id = p.uidNumber ";
	
		if (is_numeric($user) && $user >= 0)
		{
			$query .= " WHERE u.id = " . $db->Quote($user) . " LIMIT 1;";
		}
		else
		{
			$query .= " WHERE u.username = " . $db->Quote($user) . " LIMIT 1;";
		}
	
		$db->setQuery($query);
		$dbinfo = $db->loadAssoc();
		
		if (!empty($dbinfo))
		{
			$query = "SELECT host FROM #__xprofiles_host WHERE uidNumber = " . $db->Quote($dbinfo['uidNumber']) . ";";
			$db->setQuery($query);
			$dbinfo['host'] = $db->loadResultArray();
		}

		$ldap_params = JComponentHelper::getParams('com_system');
		$hubLDAPBaseDN = $ldap_params->get('ldap_basedn','');
	
		if (is_numeric($user) && $user >= 0)
		{
			$dn = 'ou=users,' . $hubLDAPBaseDN;
			$filter = '(uidNumber=' . $user . ')';
		}
		else
		{
			$dn = "uid=$user,ou=users," . $hubLDAPBaseDN;
			$filter = '(objectclass=*)';
		}
	
		$reqattr = array('uidNumber','uid','cn','gidNumber','homeDirectory','loginShell','userPassword','shadowLastChange',
				'shadowMin','shadowMax','shadowWarning','shadowInactive','shadowExpire','shadowFlag', 'host');
	
		$entry = @ldap_search($conn, $dn, $filter, $reqattr, 0, 1, 0);
		
		$count = ($entry) ? @ldap_count_entries($conn, $entry) : 0;
			
		/* If there was a database entry, but there was no ldap entry, create the ldap entry */
	
		if (!empty($dbinfo) && ($count <= 0))
		{
			$dn = "uid=" . $dbinfo['uid'] . ",ou=users," . $hubLDAPBaseDN;
			
			$entry = array();
			$entry['objectclass'][] = 'top';
			$entry['objectclass'][] = 'account';  // MUST uid
			$entry['objectclass'][] = 'posixAccount'; // MUST cn,gidNumber,homeDirectory,uidNumber
			$entry['objectclass'][] = 'shadowAccount';
	
			foreach($dbinfo as $key=>$value)
			{
				if (is_array($value) && $value != array())
				{
					$entry[$key] = $value;
				}
				else if (!is_array($value) && $value != '')
				{
					$entry[$key] = $value;
				}
			}

			if (empty($entry['uid']) || empty($entry['cn']) || empty($entry['gidNumber']))
			{
				return false;
			}
				
			if (empty($entry['homeDirectory']) || empty($entry['uidNumber']))
			{
				return false;
			}
			
			return @ldap_add($conn, $dn, $entry);
		}
	
		$ldapinfo = null;
			
		if ($count > 0)
		{
			$firstentry = @ldap_first_entry($conn, $entry);
	
			$attr = @ldap_get_attributes($conn, $firstentry);

			if (!empty($attr))
			{
				foreach ($reqattr as $key)
				{
					unset($attr[$key]['count']);
						
					if (isset($attr[$key][0]))
					{
						if (count($attr[$key]) <= 2)
						{
							$ldapinfo[$key] = $attr[$key][0];
						}
						else
						{
							$ldapinfo[$key] = $attr[$key];
						}
					}
					else
					{
						$ldapinfo[$key] = null;
					}
				}
			}
		}
		
		/* If there was no database entry, and there was no ldap entry, nothing to do */
	
		if (empty($dbinfo) && empty($ldapinfo))
		{
			return true;
		}
	
		/* If there was no database entry, but there was an ldap entry, delete the ldap entry */
	
		if (!empty($ldapinfo) && empty($dbinfo))
		{
			$dn = "uid=" . $ldapinfo['uid'] . ",ou=users," . $hubLDAPBaseDN;
	
			return @ldap_delete($conn, $dn);
		}
	
		/* Otherwise update the ldap entry */
	
		$entry = array();
	
		foreach ($dbinfo as $key=>$value)
		{
			if ($ldapinfo[$key] != $dbinfo[$key])
			{
				if ($dbinfo[$key] === null)
				{
					$entry[$key] = array();
				}
				else
				{
					$entry[$key] = is_array($dbinfo[$key]) ? $dbinfo[$key] : array($dbinfo[$key]);
				}
			}
		}
	
		$dn = "uid=" . $ldapinfo['uid'] . ",ou=users," . $hubLDAPBaseDN;
		
		return @ldap_modify($conn, $dn, $entry);
	}
	
	static function syncGroup($group)
	{
		$db = JFactory::getDBO();
	
		if (empty($db))
		{
			return false;
		}
	
		$conn = self::getLDO();
	
		if (empty($conn))
		{
			return false;
		}
	
		$query = "SELECT g.gidNumber, g.cn, g.description FROM #__xgroups AS g ";
	
		if (is_numeric($group) && ($group >= 0))
		{
			$query .= " WHERE g.gidNumber = " . $db->Quote($group) . " LIMIT 1;";
		}
		else
		{
			$query .= " WHERE g.cn = " . $db->Quote($group) . " LIMIT 1;";
		}

		$db->setQuery($query);
		$dbinfo = $db->loadAssoc();

		if (!empty($dbinfo))
		{
			$query = "SELECT u.username AS memberUid FROM #__xgroups_members AS gm, #__users AS u WHERE gm.gidNumber = " . $db->Quote($dbinfo['gidNumber']) . " AND gm.uidNumber=u.id;";
			$db->setQuery($query);
			$dbinfo['memberUid'] = $db->loadResultArray();
		}

		$ldap_params = JComponentHelper::getParams('com_system');
		$hubLDAPBaseDN = $ldap_params->get('ldap_basedn','');

		if (is_numeric($group) && $group >= 0)
		{
			$dn = 'ou=groups,' . $hubLDAPBaseDN;
			$filter = '(gidNumber=' . $group . ')';
		}
		else
		{
			$dn = "cn=$group,ou=groups," . $hubLDAPBaseDN;
			$filter = '(objectclass=*)';
		}

		$reqattr = array('gidNumber','cn','description','memberUid');

		$entry = @ldap_search($conn, $dn, $filter, $reqattr, 0, 1, 0);
		
		$count = ($entry) ? @ldap_count_entries($conn, $entry) : 0;

		/* If there was a database entry, but there was no ldap entry, create the ldap entry */

		if (!empty($dbinfo) && ($count <= 0))
		{
			$dn = "cn=" . $dbinfo['cn'] . ",ou=groups," . $hubLDAPBaseDN;

			$entry = array();
			$entry['objectclass'][] = 'top';
			$entry['objectclass'][] = 'posixGroup';

			foreach($dbinfo as $key=>$value)
			{
				if (is_array($value) && $value != array())
				{
					$entry[$key] = $value;
				}
				else if (!is_array($value) && $value != '')
				{
					$entry[$key] = $value;
				}
			}

			return @ldap_add($conn, $dn, $entry);
		}

		$ldapinfo = null;
			
		$count = ($entry) ? @ldap_count_entries($conn, $entry) : 0;

		if ($count > 0)
		{
			$firstentry = @ldap_first_entry($conn, $entry);

			$attr = @ldap_get_attributes($conn, $firstentry);

			if (!empty($attr) && $attr['count'] > 0)
			{
				foreach ($reqattr as $key)
				{
					unset($attr[$key]['count']);

					if (isset($attr[$key][0]))
					{
						if (count($attr[$key]) <= 2)
						{
							$ldapinfo[$key] = $attr[$key][0];
						}
						else
						{
							$ldapinfo[$key] = $attr[$key];
						}
					}
					else
					{
						$ldapinfo[$key] = null;
					}
				}
			}
		}

		/* If there was no database entry, and there was no ldap entry, nothing to do */

		if (empty($dbinfo) && empty($ldapinfo))
		{
			return true;
		}

		/* If there was no database entry, but there was an ldap entry, delete the ldap entry */

		if (!empty($ldapinfo) && empty($dbinfo))
		{
			$dn = "cn=" . $ldapinfo['cn'] . ",ou=groups," . $hubLDAPBaseDN;

			return @ldap_delete($conn, $dn);
		}

		/* Otherwise update the ldap entry */

		$entry = array();

		foreach ($dbinfo as $key=>$value)
		{
			if ($ldapinfo[$key] != $dbinfo[$key])
			{
				if ($dbinfo[$key] === null)
				{
					$entry[$key] = array();
				}
				else
				{
					$entry[$key] = $dbinfo[$key];
				}
			}
		}

		return @ldap_modify($conn, $dn, $entry);
	}
	
	static function addGroupMemberships($group, $members)
	{
		self::changeGroupMemberships($group, $members, array());
	}
	
	static function removeGroupMemberships($group, $members)
	{
		self::changeGroupMemberships($group, array(), $members);
	}
	
	static function changeGroupMemberships($group,$add,$delete)
	{
		$db = JFactory::getDBO();
		
		if (empty($db))
		{
			return false;
		}
		
		$conn = self::getLDO();
	
		if (empty($conn))
		{
			return false;
		}
		
		$ldap_params = JComponentHelper::getParams('com_system');
		$hubLDAPBaseDN = $ldap_params->get('ldap_basedn','');
	
		if (is_numeric($group) && $group >= 0)
		{
			$dn = 'ou=groups,' . $hubLDAPBaseDN;
			$filter = '(gidNumber=' . $group . ')';
		}
		else
		{
			$dn = "cn=$group,ou=groups," . $hubLDAPBaseDN;
			$filter = '(objectclass=*)';
		}
	
		$reqattr = array('gidNumber','cn');
	
		$entry = @ldap_search($conn, $dn, $filter, $reqattr, 0, 1, 0);
		
		$count = @ldap_count_entries($conn, $entry);
		
		/* If there was a database entry, but there was no ldap entry, create the ldap entry */
	
		if ($count <= 0)
		{
			return false;
		}
	
		$ldapinfo = null;
			
		if ($count > 0)
		{
			$firstentry = @ldap_first_entry($conn, $entry);
	
			$attr = @ldap_get_attributes($conn, $firstentry);
	
			if (!empty($attr) && $attr['count'] > 0)
			{
				foreach ($reqattr as $key)
				{
					unset($attr[$key]['count']);
	
					if (isset($attr[$key][0]))
					{
						if (count($attr[$key]) <= 2)
						{
							$ldapinfo[$key] = $attr[$key][0];
						}
						else
						{
							$ldapinfo[$key] = $attr[$key];
						}
					}
					else
					{
						$ldapinfo[$key] = null;
					}
				}
			}
		}
	
		if (empty($ldapinfo))
		{
			return false;
		}
	
		if (!empty($add))
		{
			$add = array_map( array($db, "Quote"), $add);
			$addin = implode(",", $add);
			
			if (!empty($addin))
			{
				$query = "SELECT username FROM #__users WHERE id IN ($addin) OR username IN ($addin);";
				$db->setQuery($query);
				$add = $db->loadResultArray();
			}
			
			$adds = array();
			
			foreach($add as $memberUid)
			{
				$adds['memberUid'][] = $memberUid;
			}
			
			if (@ldap_mod_add($conn, $dn, $adds) == false)
			{
				// if bulk add fails, try individual
				foreach($add as $memberUid)
				{
					@ldap_mod_add($conn, $dn, array('memberUid' => $memberUid));
				}
			}
		}
		
		if (!empty($delete))
		{
			$delete = array_map( array($db, "Quote"), $delete);
			$deletein = implode(",", $delete);
			
			if (!empty($deletein))
			{
				$query = "SELECT username FROM #__users WHERE id IN ($deletein) OR username IN ($deletein);";
				$db->setQuery($query);
				$delete = $db->loadResultArray();
			}
			
			$deletes = array();
			
			foreach($delete as $memberUid)
			{
				$deletes['memberUid'][] = $memberUid;
			}
	
			@ldap_mod_del($conn, $dn, $deletes);
		}
	}
	
	/**
	 * Short description for 'syncAllGroups'
	 *
	 * Long description (if any) ...
	 *
	 * @return     boolean Return description (if any) ...
	 */
	public function syncAllGroups()
	{
		// @TODO: chunk this to 1000 groups at a time
		
		$db = JFactory::getDBO();
	
		$query = "SELECT gidNumber FROM #__xgroups;";
	
		$db->setQuery($query);
	
		$result = $db->loadResultArray();
	
		if ($result === false) 
		{
			return false;
		}
	
		foreach($result as $row) 
		{
			self::syncGroup($row);	
		}
	}				

	/**
	 * Short description for 'syncAllGroups'
	 *
	 * Long description (if any) ...
	 *
	 * @return     boolean Return description (if any) ...
	 */
	public function syncAllUsers()
	{
		// @TODO: chunk this to 1000 users at a time
		
		$db = JFactory::getDBO();
	
		$query = "SELECT id FROM #__users;";
	
		$db->setQuery($query);
	
		$result = $db->loadResultArray();
	
		if ($result === false)
		{
			return false;
		}
	
		foreach($result as $row)
		{
			self::syncUser($row);
		}
	}
	
	public static function deleteAllGroups()
	{
	    $conn = self::getLDO();
	    
	    if (empty($conn))
	    {
	    	return false;
	    }
	    
	    /* delete all old hubGroup schema based group entries */
	    
	    $ldap_params = JComponentHelper::getParams('com_system');
	    $hubLDAPBaseDN = $ldap_params->get('ldap_basedn','');
	    
    	$dn = "ou=groups," . $hubLDAPBaseDN;
	    $filter = '(objectclass=hubGroup)';

        $sr = @ldap_search($conn, $dn, $filter, array('gid','cn'), 0, 0, 0);

        $gids = array();
        
        if ($sr !== false) 
        {
	        if (@ldap_count_entries($conn, $sr) !== false) 
        	{
		        $entry = @ldap_first_entry($conn, $sr);

        		while ($entry !== false) 
        		{
        			$attr = @ldap_get_attributes($conn, $entry);
        			
					if (array_key_exists('gid', $attr))
					{
						$gids[] = "gid=" . $attr['gid'][0] . "," .  "ou=groups," . $hubLDAPBaseDN;
					}
					else if (array_key_exists('cn',$attr))
					{
						$gids[] = "cn=" . $attr['cn'][0] . "," .  "ou=groups," . $hubLDAPBaseDN;
					}

        			$entry = @ldap_next_entry($conn, $entry);
            	}
        	}
        }
        
        foreach($gids as $giddn)
        {
        	@ldap_delete($conn, $giddn);
        }
        
        /* delete all entries that have mysql counterparts */
        
        // @TODO: chunk this to 1000 groups at a time
        
        $db = JFactory::getDBO();
        
        $query = "SELECT cn FROM #__xgroups;";
        
        $db->setQuery($query);
        
        $result = $db->loadResultArray();
        
        if ($result === false)
        {
        	return false;
        }
        
        foreach($result as $row)
        {
        	$dn = "cn=$row," .  "ou=groups," . $hubLDAPBaseDN;
        	@ldap_delete($conn, $dn);
        }
        
        /* delete any remaining items with gid > 1000 */
        
	    $dn = "ou=groups," . $hubLDAPBaseDN;
	    $filter = '(&(objectclass=posixGroup)(gidNumber>=1000))';

        $sr = @ldap_search($conn, $dn, $filter, array('gid'), 0, 0, 0);
        
        $gids = array();
        
        if ($sr !== false) 
        {
	        if (@ldap_count_entries($conn, $sr) !== false) 
        	{
		        $entry = @ldap_first_entry($conn, $sr);

        		while ($entry !== false) 
        		{
        			$attr = @ldap_get_attributes($conn, $firstentry);
        			
        			$gids[] = $attr['gid'][0];
        			
        			$entry = @ldap_next_entry($conn, $entry);
        		}
        	}
        }
        
        foreach($gids as $gid)
        {
        	$dn = "gid=$gid," . "ou=groups," . $hubLDAPBaseDN;
        	@ldap_delete($conn, $dn);
        }
        
        return true;
   	}
	
	public static function deleteAllUsers()
	{
		$conn = self::getLDO();
	    
	    if (empty($conn))
	    {
	    	return false;
	    }
	    
	    /* delete all old hubAccount schema based user entries */
	    
	    $ldap_params = JComponentHelper::getParams('com_system');
	    $hubLDAPBaseDN = $ldap_params->get('ldap_basedn','');
	    
    	$dn = "ou=users," . $hubLDAPBaseDN;
	    $filter = '(objectclass=hubAccount)';

        $sr = @ldap_search($conn, $dn, $filter, array('uid'), 0, 0, 0);

        $uids = array();
        
        if ($sr !== false) 
        {
	        if (@ldap_count_entries($conn, $sr) !== false) 
        	{
		        $entry = @ldap_first_entry($conn, $sr);

        		while ($entry !== false) 
        		{
        			$attr = @ldap_get_attributes($conn, $entry);
        			
        			$uids[] = $attr['uid'][0];
        			
        			$entry = @ldap_next_entry($conn, $entry);
        		}
        	}
        }
        
        foreach($uids as $uid)
        {
        	$dn = "uid=$uid," . "ou=users," . $hubLDAPBaseDN;
        	@ldap_delete($conn, $dn);
        }
        
        /* delete all entries that have mysql counterparts */
        
        // @TODO: chunk this to 1000 groups at a time
        
        $db = JFactory::getDBO();
        
        $query = "SELECT username FROM #__users;";
        
        $db->setQuery($query);
        
        $result = $db->loadResultArray();
        
        if ($result === false)
        {
        	return false;
        }
        
        foreach($result as $row)
        {
        	$dn = "uid=$row," .  "ou=users," . $hubLDAPBaseDN;
        	@ldap_delete($conn, $dn);
        }
        
        /* delete any remaining items with gid > 1000 */
        
	    $dn = "ou=users," . $hubLDAPBaseDN;
	    $filter = '(&(objectclass=posixAccoiunt)(uidNumber>=1000))';

        $sr = @ldap_search($conn, $dn, $filter, array('uid'), 0, 0, 0);
        
        $uids = array();
        
        if ($sr !== false) 
        {
	        if (@ldap_count_entries($conn, $sr) !== false) 
        	{
		        $entry = ldap_first_entry($conn, $sr);

        		while ($entry !== false) 
        		{
        			$attr = @ldap_get_attributes($conn, $firstentry);
        			
        			$uids[] = $attr['uid'][0];
        			
        			$entry = @ldap_next_entry($conn, $entry);
            	}
        	}
        }
        
        foreach($uids as $uid)
        {
        	$dn = "uid=$uid," . "ou=users," . $hubLDAPBaseDN;
        	@ldap_delete($conn, $dn);
        }
	}
}