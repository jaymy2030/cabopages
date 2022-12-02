<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjBookingExtraModel extends pjAppModel
{
	protected $primaryKey = null;
	
	protected $table = 'bookings_extras';
	
	protected $schema = array(
		array('name' => 'booking_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'extra_id', 'type' => 'int', 'default' => ':NULL')
	);
	
	public static function factory($attr=array())
	{
		return new pjBookingExtraModel($attr);
	}
}
?>