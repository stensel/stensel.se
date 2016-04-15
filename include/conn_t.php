<?php

// Byt ut mot dina inloggningsuppgifter och databas 
class DB
{
	/*** Declare instance ***/
	private static $instance = NULL;

	private static $db_hostname = "localhost";
	private static $db_user = "user";
	private static $db_password = "pass";
	/**
	*
	* the constructor is set to private so
	* so nobody can create a new instance using new
	*
	*/
	private function __construct() {
	/*** maybe set the db name here later ***/
	}

	/**
	*
	* Return DB instance or create intitial connection
	*
	* @return object (PDO)
	*
	* @access public
	*
	*/
	public static function getInstance()
	{
		if (!self::$instance)
		{
			self::$instance = new PDO("mysql:host=".self::$db_hostname.";dbname=Stensel_admin;charset=utf8", self::$db_user, self::$db_password);
			self::$instance-> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		return self::$instance;
	}

	/**
	*
	* Like the constructor, we make __clone private
	* so nobody can clone the instance
	*
	*/
	private function __clone() {}
}

?>
