<?php

class Ldap {

	private $host;
	private $port;
	//private $searchBase = 'dc=be,dc=flat';
	//private $userFilter = '(uid={})';
	//private $baseDn = 'cn=users,dc=be,dc=flat';
	//private $userIdAttribute = 'uid';
	private $searchBase;
	private $userFilter;
	private $baseDn;
	private $userIdAttribute;
	private $connection;
	
	public function __construct($host, $port=389) {
		$this->host = $host;
		$this->port = $port;
	}

	public function connect() {
		$this->connection = ldap_connect($this->host, $this->port);
		if ($this->connection) {
			ldap_set_option($this->connection, LDAP_OPT_PROTOCOL_VERSION, 3);
		}
	}

	public function authenticate($user, $password) {
		$rdn = $this->userIdAttribute.'='.$user.','.$this->baseDn;
		$r = @ldap_bind($this->connection, $rdn, $password);
		if ($r) {
			$filter = str_replace('{}', $user, $this->userFilter);
			$search = ldap_search($this->connection, $this->baseDn, $filter);
			$info = ldap_get_entries($this->connection, $search);
			return $info;
		} else {
			return false;
		}
	}

	public function setSearchBase($searchBase) {
		$this->searchBase = $searchBase;
	}	

	public function setUserFilter($userFilter) {
		$this->userFilter = $userFilter;
	}

	public function setUserIdAttribute($userId) {
		$this->userIdAttribute = $userId;
	}

	public function setBaseDn($baseDn) {
		$this->baseDn = $baseDn;
	}
}

?>
