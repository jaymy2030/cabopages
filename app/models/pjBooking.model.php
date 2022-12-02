<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjBookingModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'bookings';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'uuid', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'client_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'fleet_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'price_id', 'type' => 'int', 'default' => ':NULL'),
	    array('name' => 'booking_type', 'type' => 'enum', 'default' => 'from'),
	    array('name' => 'from_location_id', 'type' => 'int', 'default' => ':NULL'),
	    array('name' => 'to_location_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'pickup_address', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'return_address', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'booking_date', 'type' => 'datetime', 'default' => ':NULL'),
		array('name' => 'return_date', 'type' => 'datetime', 'default' => ':NULL'),
		array('name' => 'distance', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'passengers', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'luggage', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'sub_total', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'tax', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'total', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'deposit', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'payment_method', 'type' => 'enum', 'default' => ':NULL'),
		array('name' => 'status', 'type' => 'enum', 'default' => ':NULL'),
		array('name' => 'txn_id', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'processed_on', 'type' => 'datetime', 'default' => ':NULL'),
		array('name' => 'created', 'type' => 'datetime', 'default' => ':NOW()'),
		array('name' => 'ip', 'type' => 'varchar', 'default' => ':NULL'),
		
		array('name' => 'c_title', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_fname', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_lname', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_phone', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_email', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_company', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_notes', 'type' => 'text', 'default' => ':NULL'),
		array('name' => 'c_address', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_city', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_state', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_zip', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_country', 'type' => 'int', 'default' => ':NULL'),
		
		array('name' => 'c_airline_company', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_departure_airline_company', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_flight_number', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_flight_time', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_departure_flight_number', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_departure_flight_time', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'c_terminal', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'booking_option', 'type' => 'enum', 'default' => 'oneway'),
		
		array('name' => 'cc_type', 'type' => 'blob', 'default' => ':NULL', 'encrypt' => 'AES'),
		array('name' => 'cc_num', 'type' => 'blob', 'default' => ':NULL', 'encrypt' => 'AES'),
		array('name' => 'cc_exp_month', 'type' => 'blob', 'default' => ':NULL', 'encrypt' => 'AES'),
		array('name' => 'cc_exp_year', 'type' => 'blob', 'default' => ':NULL', 'encrypt' => 'AES'),
		array('name' => 'cc_code', 'type' => 'blob', 'default' => ':NULL', 'encrypt' => 'AES')
	);
	
	public static function factory($attr=array())
	{
		return new pjBookingModel($attr);
	}
}
?>