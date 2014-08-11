<?php 

/**
* The class for auth with an active directory services.
*/

class Ldap {

    public $ldapserver;
    public $ldapport;
    public $basedn;
    public $basepass;
    public $connection;

	/**
	* Constructor method
	* @param array $params is an array contains parameters to connection
	* 
	* @return
	*/
	
    function __construct ($params = array())
    {
		$this->ldapserver = $params['ldapserver'];
		$this->ldapport = $params['ldapport'];
		$this->basedn = $params['basedn'];
		$this->basepass = $params['basepass'];
		
		$this->connection = $this->connect($this->ldapserver, $this->ldapport);
		$this->bind();
    }
        
    /**
	* private method for connect with a AD server
	* @param string $server Need to contain an ip address of a domain controller.
	* @param string $port Need to contain a port of a domain controller.
	* 
	* @return bool
	*/
    
    private function connect($server, $port)
    {
		$connection = ldap_connect($server, $port);	
		ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
		return $connection;
    }
    
    /**
	* private method for bind a domain controller. Return integer, if all good is 1, if something wrong is 0
	* 
	* @return ineger
	*/
    
    private function bind()
    {
		$bind = ldap_bind($this->connection, $this->basedn , $this->basepass);
		if (!empty($bind))
		{
		    return 1;
		}
		else
		{
		    return 0;
		}
    }
    
    /**
	* Search public method, return first and one found entry
	* @param string $searchdn is a search path, an example: 'OU=Пользователи и компьютеры,DC=megafon,DC=ru'
	* @param string $filter an AD filter, an example: "(&(objectClass=user)(sAMAccountName=login))"
	* @param array $attributes contains an active directory attributes.
	* 
	* @return array if results exists
	*/
    
    public function search($searchdn, $filter, $attributes = array())
    {
		$sr = ldap_search($this->connection, $searchdn, $filter, $attributes);
		if ($sr)
		{
		    $info = ldap_get_entries($this->connection, $sr);
		    if ($info["count"] == 1)
		    {
	                return $info;
		    }
		    else
		    {
				return 0;
		    }
	    }    
    }

    /**
	* Search public method. distinction with the $this->search() can return more than 1 result
	* @param string $searchdn is a search path, an example: 'OU=Пользователи и компьютеры,DC=megafon,DC=ru'
	* @param string $filter an AD filter, an example: "(&(objectClass=user)(sAMAccountName=login))"
	* @param array $attributes contains an active directory attributes.
	* 
	* @return array if results exists
	*/
	
    public function search2($searchdn, $filter, $attributes = array())
    {
		$sr = ldap_search($this->connection, $searchdn, $filter, $attributes);
		if ($sr)
		{
		    $info = ldap_get_entries($this->connection, $sr);
	        return $info;
	    }
		else
		{
		    return 0;
		}
    }
    
    /**
	* The close method
	* @param undefined $connection
	* 
	* @return
	*/
    public function close($connection)
    {
		ldap_close($connection);
    }

}

?>