<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjLocationModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'locations';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'address', 'type' => 'text', 'default' => ':NULL'),
		array('name' => 'type', 'type' => 'enum', 'default' => 'da'),
	);
	
	public $i18n = array('name');
	
	public static function factory($attr=array())
	{
	    return new pjLocationModel($attr);
	}
}
?>