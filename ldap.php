<?php 

/**
* The class for auth with an active directory services. 
* 
*/

class Ldap {

    /**
     * @var string
     */
    private $ldapServer = '';

    /**
     * @var int
     */
    private $ldapPort = 0;

    /**
     * @var string
     */
    private $baseDn = '';

    /**
     * @var string
     */
    private $basePassword = '';

    /**
     * @var resource
     */
    private $connection = null;

    /**
     * Constructor method
     * @param array $params is an array contains parameters to connection
     */

    public function __construct($params = [])
    {
        $this->setLdapServer($params['ldapserver'])
            ->setLdapPort($params['ldapport'])
            ->setBaseDn($params['basedn'])
            ->setBasePassword($params['basepass']);
        $this->connection = $this->connect($this->ldapServer, $this->ldapPort);
        $this->bind();
    }

    /**
     * Search public method, return first and one found entry
     * @param string $searchdn is a search path, an example: 'OU=Пользователи и компьютеры,DC=megafon,DC=ru'
     * @param string $filter an AD filter, an example: "(&(objectClass=user)(sAMAccountName=login))"
     * @param array $attributes contains an active directory attributes.
     *
     * @return array if results exists
     */
    public function search(string $searchdn, string $filter, array $attributes = [])
    {
        if (empty($this->connection)) {
            return [];
        }
        $sr = ldap_search($this->connection, $searchdn, $filter, $attributes);
        if (!$sr) {
            return [];
        }
        $info = ldap_get_entries($this->connection, $sr);
        if ($info["count"] == 1) {
            return $info;
        }
        return [];
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
        if (empty($this->connection)) {
            return [];
        }
        $sr = ldap_search($this->connection, $searchdn, $filter, $attributes);
        if (!$sr) {
            return [];
        }
        $info = ldap_get_entries($this->connection, $sr);
        return $info;
    }

    /**
     * The close method
     * @param $connection
     *
     * @return bool
     */
    public function close($connection)
    {
        ldap_close($connection);
        return true;
    }


    /**
     * @return string
     */
    public function getLdapServer()
    {
        return $this->ldapServer;
    }

    /**
     * @param string $ldapServer
     * @return $this
     */
    public function setLdapServer($ldapServer)
    {
        $this->ldapServer = $ldapServer;
        return $this;
    }

    /**
     * @return int
     */
    public function getLdapPort()
    {
        return $this->ldapPort;
    }

    /**
     * @param int $ldapPort
     * @return $this
     */
    public function setLdapPort($ldapPort)
    {
        $this->ldapPort = $ldapPort;
        return $this;
    }

    /**
     * @return string
     */
    public function getBaseDn()
    {
        return $this->baseDn;
    }

    /**
     * @param string $baseDn
     * @return $this
     */
    public function setBaseDn($baseDn)
    {
        $this->baseDn = $baseDn;
        return $this;
    }

    /**
     * @return string
     */
    public function getBasePassword()
    {
        return $this->basePassword;
    }

    /**
     * @param string $basePassword
     * @return $this
     */
    public function setBasePassword($basePassword)
    {
        $this->basePassword = $basePassword;
        return $this;
    }

    /**
	* private method for connect with a AD server
	* @param string $server ip address of a domain controller.
	* @param int $port a port of a domain controller.
	*
	* @return self
	*/

    private function connect(string $server, int $port)
    {
        if (empty($this->connection)) {
            $connection = ldap_connect($server, $port);
            ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
        }
        return $this;
    }

    /**
	* private method for bind a domain controller. Return integer, if all good is 1, if something wrong is 0
	*
	* @return bool
	*/

    private function bind()
    {
        $bind = ldap_bind($this->connection, $this->baseDn, $this->basePassword);
        if (!empty($bind)) {
            return true;
        }
        return false;
    }

}