<?php
class HubConfig {
 var $hubLDAPMasterHost = 'ldap://127.0.0.1';
 var $hubLDAPSlaveHosts = '';
 var $hubLDAPBaseDN = 'dc=com';
 var $hubLDAPNegotiateTLS = '0';
 var $hubLDAPSearchUserDN = 'cn=search,dc=com';
 var $hubLDAPSearchUserPW = 'MY0arQlWYc';
 var $hubLDAPAcctMgrDN = 'cn=admin,dc=com';
 var $hubLDAPAcctMgrPW = 'X8S9KYEHh7';
 var $ipDBDriver = 'mysql';
 var $ipDBHost = '';
 var $ipDBPort = '';
 var $ipDBUsername = '';
 var $ipDBPassword = '';
 var $ipDBDatabase = '';
 var $ipDBPrefix = '';
 var $forgeName = 'example Forge';
 var $forgeURL = 'https://example.com';
 var $forgeRepoURL = 'https://example.com';
 var $svn_user = 'hubrepo';
 var $svn_password = 'dTdCqK8cr2';
 var $hubzero_ipgeo_url = 'http://hubzero.org/ipinfo/v1';
 var $hubzero_ipgeo_key = '_HUBZERO_OPNSRC_V1_';
?>