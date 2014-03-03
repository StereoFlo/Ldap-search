<?php 

// ����� �� ����� ���������� ������������
//error_reporting(0);

class Ldap {

    public $ldapserver;
    public $ldapport;
    public $basedn;
    public $basepass;
    public $connection;

    function __construct ($params = array())
    {
	$this->ldapserver = $params['ldapserver'];
	$this->ldapport = $params['ldapport'];
	$this->basedn = $params['basedn'];
	$this->basepass = $params['basepass'];
	
	$this->connection = $this->connect($this->ldapserver, $this->ldapport);
	$this->bind();
    }
    
    //�������������:  $connection = $this->ldap->connect("127.0.250.10","389");
    private function connect($server, $port)
    {
	$connection = ldap_connect($server,$port);	
	ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
	return $connection;
    }
    //������������� $test = $this->ldap->bind($connection,'user@domain.ru','you_password');
    //����������: 1 - ��; 0 - ���-�� �� ���
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
    //�������������
    //$sb = 'OU=������������ � ����������,DC=megafon,DC=ru';
    //$filter = "(&(objectClass=user)(sAMAccountName=login))";
    //$attr = array('samaccountname');
    //$test = $this->ldap->search($connection, $sb, $filter, $attr);
    //��������� ������ ������������, ���� ������ ���� � � ������� ���� ������� � int 0, ���� �� ���� �� ������� ��� ������� ����� ����� ������.
    function search($searchdn, $filter, $attributes = array())
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

    // ����� �������������. �������� ��� � $this->search, �� ����� ���������� �� ������ ���� �������
    function search2($searchdn, $filter, $attributes = array())
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
    
    // ���� �� ���������� � �������� ����������
    function close($connection)
    {
	ldap_close($connection);
    }

}

?>