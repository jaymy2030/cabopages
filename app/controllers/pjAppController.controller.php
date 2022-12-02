<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAppController extends pjController
{
	public $models = array();
	
	public $defaultLocale = 'admin_locale_id';
  
	public $defaultFields = 'fields';
	
	public $defaultFieldsIndex = 'fields_index';
  
	protected function loadSetFields($force=FALSE, $locale_id=NULL, $fields=NULL)
	{
		if (is_null($locale_id))
		{
			$locale_id = $this->getLocaleId();
		}
		
		if (is_null($fields))
		{
			$fields = $this->defaultFields;
		}
		
		$registry = pjRegistry::getInstance();
		if ($force
				|| !isset($_SESSION[$this->defaultFieldsIndex])
				|| $_SESSION[$this->defaultFieldsIndex] != $this->option_arr['o_fields_index']
				|| !isset($_SESSION[$fields])
				|| empty($_SESSION[$fields]))
		{
			pjAppController::setFields($locale_id);
	
			# Update session
			if ($registry->is('fields'))
			{
				$_SESSION[$fields] = $registry->get('fields');
			}
			$_SESSION[$this->defaultFieldsIndex] = $this->option_arr['o_fields_index'];
		}
	
		if (isset($_SESSION[$fields]) && !empty($_SESSION[$fields]))
		{
			# Load fields from session
			$registry->set('fields', $_SESSION[$fields]);
		}
		
		return TRUE;
	}
	
	public function isCountryReady()
    {
    	return $this->isAdmin();
    }
    
	public function isOneAdminReady()
    {
    	return $this->isAdmin();
    }
	
	public static function setTimezone($timezone="UTC")
    {
    	if (in_array(version_compare(phpversion(), '5.1.0'), array(0,1)))
		{
			date_default_timezone_set($timezone);
		} else {
			$safe_mode = ini_get('safe_mode');
			if ($safe_mode)
			{
				putenv("TZ=".$timezone);
			}
		}
    }

	public static function setMySQLServerTime($offset="-0:00")
    {
		pjAppModel::factory()->prepare("SET SESSION time_zone = :offset;")->exec(compact('offset'));
    }
    public function getDirection()
    {
    	$dir = 'ltr';
    	if($this->getLocaleId() != false)
    	{
    		$locale_arr = pjLocaleModel::factory()->find($this->getLocaleId())->getData();
    		$dir = $locale_arr['dir'];
    	}
    	return $dir;
    }
	public function setTime()
	{
		if (isset($this->option_arr['o_timezone']))
		{
			$offset = $this->option_arr['o_timezone'] / 3600;
			if ($offset > 0)
			{
				$offset = "-".$offset;
			} elseif ($offset < 0) {
				$offset = "+".abs($offset);
			} elseif ($offset === 0) {
				$offset = "+0";
			}
	
			pjAppController::setTimezone('Etc/GMT' . $offset);
			if (strpos($offset, '-') !== false)
			{
				$offset = str_replace('-', '+', $offset);
			} elseif (strpos($offset, '+') !== false) {
				$offset = str_replace('+', '-', $offset);
			}
			pjAppController::setMySQLServerTime($offset . ":00");
		}
	}
    
    public function beforeFilter()
    {
    	$this->appendJs('jquery.min.js', PJ_THIRD_PARTY_PATH . 'jquery/');
		$dm = new pjDependencyManager(PJ_INSTALL_PATH, PJ_THIRD_PARTY_PATH);
		$dm->load(PJ_CONFIG_PATH . 'dependencies.php')->resolve();
		$this->appendJs('jquery-migrate.min.js', $dm->getPath('jquery_migrate'), FALSE, FALSE);
		$this->appendJs('pjAdminCore.js');
		$this->appendCss('reset.css');
		 
		$this->appendJs('js/jquery-ui.custom.min.js', PJ_THIRD_PARTY_PATH . 'jquery_ui/');
		$this->appendCss('css/smoothness/jquery-ui.min.css', PJ_THIRD_PARTY_PATH . 'jquery_ui/');
				
		$this->appendCss('pj-all.css', PJ_FRAMEWORK_LIBS_PATH . 'pj/css/');
		$this->appendCss('admin.css');
		
    	if ($_GET['controller'] != 'pjInstaller')
		{
			$this->models['Option'] = pjOptionModel::factory();
			$this->option_arr = $this->models['Option']->getPairs($this->getForeignId());
			$this->set('option_arr', $this->option_arr);
			$this->setTime();
			
			if (!isset($_SESSION[$this->defaultLocale]))
			{
				$locale_arr = pjLocaleModel::factory()->where('is_default', 1)->limit(1)->findAll()->getData();
				if (count($locale_arr) === 1)
				{
					$this->setLocaleId($locale_arr[0]['id']);
				}
			}
			$this->loadSetFields();
		}
    }
    
    public function isEditor()
    {
    	return $this->getRoleId() == 2;
    }
    public function isTeacher()
    {
    	if(isset($_SESSION[$this->defaultUser]))
    	{
    		$teacher = $_SESSION[$this->defaultUser];
    		if(isset($teacher['is_teacher']))
    		{
    			return true;
    		}else{
    			return false;
    		}
    	}else{
    		return false;
    	}
    }
    public function isStudent()
    {
    	if(isset($_SESSION[$this->defaultUser]))
    	{
    		$student = $_SESSION[$this->defaultUser];
    		if(isset($student['is_student']))
    		{
    			return true;
    		}else{
    			return false;
    		}
    	}else{
    		return false;
    	}
    }
    public function getForeignId()
    {
    	return 1;
    }
    
    public static function setFields($locale)
    {
    if(isset($_SESSION['lang_show_id']) && (int) $_SESSION['lang_show_id'] == 1)
		{
			$fields = pjMultiLangModel::factory()
				->select('CONCAT(t1.content, CONCAT(":", t2.id, ":")) AS content, t2.key')
				->join('pjField', "t2.id=t1.foreign_id", 'inner')
				->where('t1.locale', $locale)
				->where('t1.model', 'pjField')
				->where('t1.field', 'title')
				->findAll()
				->getDataPair('key', 'content');
		}else{
			$fields = pjMultiLangModel::factory()
				->select('t1.content, t2.key')
				->join('pjField', "t2.id=t1.foreign_id", 'inner')
				->where('t1.locale', $locale)
				->where('t1.model', 'pjField')
				->where('t1.field', 'title')
				->findAll()
				->getDataPair('key', 'content');
		}
		$registry = pjRegistry::getInstance();
		$tmp = array();
		if ($registry->is('fields'))
		{
			$tmp = $registry->get('fields');
		}
		$arrays = array();
		foreach ($fields as $key => $value)
		{
			if (strpos($key, '_ARRAY_') !== false)
			{
				list($prefix, $suffix) = explode("_ARRAY_", $key);
				if (!isset($arrays[$prefix]))
				{
					$arrays[$prefix] = array();
				}
				$arrays[$prefix][$suffix] = $value;
			}
		}
		require PJ_CONFIG_PATH . 'settings.inc.php';
		$fields = array_merge($tmp, $fields, $settings, $arrays);
		$registry->set('fields', $fields);
    }

    public static function jsonDecode($str)
	{
		$Services_JSON = new pjServices_JSON();
		return $Services_JSON->decode($str);
	}
	
	public static function jsonEncode($arr)
	{
		$Services_JSON = new pjServices_JSON();
		return $Services_JSON->encode($arr);
	}
	
	public static function jsonResponse($arr)
	{
		header("Content-Type: application/json; charset=utf-8");
		echo pjAppController::jsonEncode($arr);
		exit;
	}

	public function getLocaleId()
	{
		return isset($_SESSION[$this->defaultLocale]) && (int) $_SESSION[$this->defaultLocale] > 0 ? (int) $_SESSION[$this->defaultLocale] : false;
	}
	
	public function setLocaleId($locale_id)
	{
		$_SESSION[$this->defaultLocale] = (int) $locale_id;
	}
	
	public function pjActionCheckInstall()
	{
		$this->setLayout('pjActionEmpty');
		
		$result = array('status' => 'OK', 'code' => 200, 'text' => 'Operation succeeded', 'info' => array());
		$folders = array(
							'app/web/upload'
						);
		foreach ($folders as $dir)
		{
			if (!is_writable($dir))
			{
				$result['status'] = 'ERR';
				$result['code'] = 101;
				$result['text'] = 'Permission requirement';
				$result['info'][] = sprintf('Folder \'<span class="bold">%1$s</span>\' is not writable. You need to set write permissions (chmod 777) to directory located at \'<span class="bold">%1$s</span>\'', $dir);
			}
		}
		
		return $result;
	}
	
	public function friendlyURL($str, $divider='-')
	{
		$str = mb_strtolower($str, mb_detect_encoding($str));
		$str = trim($str);
		$str = preg_replace('/[_|\s]+/', $divider, $str);
		$str = preg_replace('/\x{00C5}/u', 'AA', $str);
		$str = preg_replace('/\x{00C6}/u', 'AE', $str);
		$str = preg_replace('/\x{00D8}/u', 'OE', $str);
		$str = preg_replace('/\x{00E5}/u', 'aa', $str);
		$str = preg_replace('/\x{00E6}/u', 'ae', $str);
		$str = preg_replace('/\x{00F8}/u', 'oe', $str);
		$str = preg_replace('/[^a-z\x{0400}-\x{04FF}0-9-]+/u', '', $str);
		$str = preg_replace('/[-]+/', $divider, $str);
		$str = preg_replace('/^-+|-+$/', '', $str);
		return $str;
	}
	
	public function getAdminEmail()
	{
		$arr = pjUserModel::factory()
			->findAll()
			->orderBy("t1.id ASC")
			->limit(1)
			->getData();
		return !empty($arr) ? $arr[0]['email'] : null;	
	}
	
	public function getAllAdminEmails()
	{
		$arr = pjUserModel::factory()
			->where('role_id', 1)
			->orderBy("t1.id ASC")
			->findAll()
			->getDataPair(null, 'email');
		return $arr;
	}
	
	public function getAdminPhone()
	{
		$arr = pjUserModel::factory()
			->findAll()
			->orderBy("t1.id ASC")
			->limit(1)
			->getData();
		return !empty($arr) ? (!empty($arr[0]['phone']) ? $arr[0]['phone'] : null) : null;	
	}
	
	public static function calPrice($fleet_id, $from_location_id, $to_location_id, $passengers, $extra_ids, $option_arr, $booking_option)
	{
		$subtotal = 0;
		$tax = 0;
		$total = 0;
		$deposit = 0;
		$extra = 0;
		$price_id = 0;
		if (is_null($booking_option)) {
			$booking_option = 'oneway';
		}
		if((int) $fleet_id > 0 && (int) $from_location_id > 0 && (int) $to_location_id > 0 && (int) $passengers > 0)
		{
			$fleet = pjFleetModel::factory()->find($fleet_id)->getData();
			$price_arr = pjPriceModel::factory()->where('fleet_id', $fleet_id)->where('from_location_id', $from_location_id)->where('to_location_id', $to_location_id)->limit(1)->findAll()->getData();
			$subtotal = $fleet['start_fee'] + $passengers * $fleet['fee_per_person'];
			if(count($price_arr) == 1)
			{
				if ($booking_option == 'roundtrip') {
					$price = (float) $price_arr[0]['price_roundtrip'];
				} else {
					$price = (float) $price_arr[0]['price'];
				}
				if($price > 0)
				{
					$subtotal += $price;
					$price_id = $price_arr[0]['id'];
				}
			}  
		}
		if(!empty($extra_ids))
		{
			$avail_extra_arr = pjFleetExtraModel::factory()
				->join('pjExtra', "t1.extra_id=t2.id", 'left')
				->select("t1.*, t2.price, t2.per")
				->where('t1.fleet_id', $fleet_id)
				->findAll()->getData();
			foreach($avail_extra_arr as $k => $v)
			{
				if(in_array($v['extra_id'], $extra_ids))
				{
					if($v['per'] == 'person')
					{
						$subtotal += $v['price'] * $passengers;
						$extra += $v['price'] * $passengers;
					}else{
						$subtotal += $v['price'];
						$extra += $v['price'];
					}
				}
			}
		}
		
		$tax = $subtotal * (float) $option_arr['o_tax_payment'] / 100;
		$total = $subtotal + $tax;
		$deposit = $total * (float) $option_arr['o_deposit_payment'] / 100;
		
		return compact('subtotal', 'tax', 'total', 'deposit', 'extra', 'price_id');
	}
	public static function getLocation($id, $locale_id)
	{
	    $location = pjLocationModel::factory()
	    ->select('t1.*, t2.content AS name')
	    ->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjLocation' AND t2.locale = '".$locale_id."' AND t2.field = 'name'", 'left')
	    ->find($id)
	    ->getData();
	    return $location['name'];
	}
	public function getTokens($option_arr, $booking_arr, $salt, $locale_id)
	{
		$country = NULL;
		if (isset($booking_arr['c_country']) && !empty($booking_arr['c_country']))
		{
			$country_arr = pjCountryModel::factory()
						->select('t1.id, t2.content AS country_title')
						->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$locale_id."'", 'left outer')
						->find($booking_arr['c_country'])->getData();
			if (!empty($country_arr))
			{
				$country = $country_arr['country_title'];
			}
		}
		
		$title = $booking_arr['c_title'];
		$first_name = pjSanitize::clean($booking_arr['c_fname']);
		$last_name = pjSanitize::clean($booking_arr['c_lname']);
		$phone = pjSanitize::clean($booking_arr['c_phone']);
		$email = pjSanitize::clean($booking_arr['c_email']);
		if (isset($booking_arr['client_id']) && (int) $booking_arr['client_id'] > 0)
		{
			$client = pjClientModel::factory()->find($booking_arr['client_id'] )->getData();
			$title = $client['title'];
			$first_name = pjSanitize::clean($client['fname']);
			$last_name = pjSanitize::clean($client['lname']);
			$phone = pjSanitize::clean($client['phone']);
			$email = pjSanitize::clean($client['email']);
		}
		
		$sub_total = pjUtil::formatCurrencySign($booking_arr['sub_total'], $option_arr['o_currency']);
		$tax = pjUtil::formatCurrencySign($booking_arr['tax'], $option_arr['o_currency']);
		$total = pjUtil::formatCurrencySign($booking_arr['total'], $option_arr['o_currency']);
		$deposit = pjUtil::formatCurrencySign($booking_arr['deposit'], $option_arr['o_currency']);
		
		$booking_date = $return_date = NULL;
		if (isset($booking_arr['booking_date']) && !empty($booking_arr['booking_date']))
		{
			$tm = strtotime(@$booking_arr['booking_date']);
			$booking_date = date($option_arr['o_date_format'], $tm) . ', ' . date($option_arr['o_time_format'], $tm);
		}
		if ($booking_arr['booking_option'] == 'roundtrip' && isset($booking_arr['return_date']) && !empty($booking_arr['return_date']))
		{
			$return_tm = strtotime(@$booking_arr['return_date']);
			$return_date = date($option_arr['o_date_format'], $return_tm) . ', ' . date($option_arr['o_time_format'], $return_tm);
		}

		$extras = NULL;
		$extra_arr = array();
		$avail_extra_arr = pjBookingExtraModel::factory()
			->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.extra_id AND t2.field='name' AND t2.locale='".$locale_id."'", 'left outer')
			->join('pjExtra', "t1.extra_id=t3.id", 'left')
			->select("t1.*, t2.content as name, t3.price, t3.per")
			->where('t1.booking_id', $booking_arr['id'])
			->orderBy("name ASC")
			->findAll()->getData();
		
		foreach($avail_extra_arr as $k => $v)
		{
			$extra_arr[] = pjSanitize::html($v['name']) . " (" . pjUtil::formatCurrencySign($v['price'], $option_arr['o_currency']) .  ($v['per'] == 'person' ? ' ' . __('lblPerPerson', true) : '') . ')';
		}
		
		$extras = join("<br/>", $extra_arr);
		
		$flight_time = null;
		if(!empty($booking_arr['c_flight_time']))
		{
			$flight_time = date($option_arr['o_time_format'], strtotime($booking_arr['c_flight_time']));
		}
		$distance = (int) $booking_arr['distance'] . ' km';
		
		$cancelURL = PJ_INSTALL_URL . 'index.php?controller=pjFrontEnd&action=pjActionCancel&id='.@$booking_arr['id'].'&hash='.sha1(@$booking_arr['id'].@$booking_arr['created'].$salt);
		$cancelURL = '<a href="'.$cancelURL.'">'.$cancelURL.'</a>';
		
		$From = NULL;
		$To = NULL;
		
		$from_location_arr = pjLocationModel::factory()
		->select('t1.*, t2.content AS name')
		->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjLocation' AND t2.locale = '".$locale_id."' AND t2.field = 'name'", 'left')
		->find($booking_arr['from_location_id'])
		->getData();
		$to_location_arr = pjLocationModel::factory()
		->select('t1.*, t2.content AS name')
		->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjLocation' AND t2.locale = '".$locale_id."' AND t2.field = 'name'", 'left')
		->find($booking_arr['to_location_id'])
		->getData();
		
		if($booking_arr['booking_type'] == 'from')
		{
		    $From = pjSanitize::html($from_location_arr['name']);
		    $To = pjSanitize::html($to_location_arr['name']);
		}else{
		    $From = pjSanitize::html($to_location_arr['name']);
		    $To = pjSanitize::html($from_location_arr['name']);
		}
		$booking_options_yesno = __('booking_options_yesno', true);
		
		$search = array(
			'{Title}', '{FirstName}', '{LastName}', '{Email}', '{Password}', '{Phone}', '{Country}',
			'{City}', '{State}', '{Zip}', '{Address}',
			'{Airline}', '{FlightNumber}', '{ArrivalTime}', '{Terminal}',
			'{Company}', '{CCType}', '{CCNum}', '{CCExp}','{CCSec}', '{PaymentMethod}', 
			'{UniqueID}', '{DateTime}', '{From}', '{To}', '{Vehicle}', '{Distance}', '{Passengers}', '{Luggage}', '{Extras}',
			'{SubTotal}', '{Tax}', '{Total}', '{Deposit}', '{Notes}',
			'{CancelURL}', '{IsRoundTrip}', '{ReturnDateTime}');
		$replace = array(
			$title, $first_name, $last_name, $email, $booking_arr['password'], $phone, $country,
			$booking_arr['c_city'], $booking_arr['c_state'], $booking_arr['c_zip'], $booking_arr['c_address'],
			$booking_arr['c_airline_company'], $booking_arr['c_flight_number'], $flight_time, $booking_arr['c_terminal'],
			$booking_arr['c_company'], @$booking_arr['cc_type'], @$booking_arr['cc_num'], (@$booking_arr['payment_method'] == 'creditcard' ? @$booking_arr['cc_exp_month'] . '/' . substr(@$booking_arr['cc_exp_year'], -2) : NULL), @$booking_arr['cc_code'], @$booking_arr['payment_method'],
		    @$booking_arr['uuid'], $booking_date, $From, $To, @$booking_arr['fleet'], $distance, @$booking_arr['passengers'], @$booking_arr['luggage'], $extras,
			@$sub_total, @$tax, @$total, @$deposit, @$booking_arr['c_notes'],
			$cancelURL, @$booking_options_yesno[@$booking_arr['booking_option']], $return_date);

		return compact('search', 'replace');
	}
	public function getClientTokens($option_arr, $client, $salt, $locale_id)
	{
		$name_titles = __('personal_titles', true, false);
		
		$first_name = $client['fname'];
		$last_name = $client['lname'];
		$phone = $client['phone'];
		$email = $client['email'];
		$password = $client['password'];
		$title = !empty($client['title']) ? $name_titles[$client['title']] : NULL;
	
		$search = array('{Title}', '{FirstName}', '{LastName}', '{Email}', '{Password}', '{Phone}');
		$replace = array($title, $first_name, $last_name, $email, $password, $phone);
	
		return compact('search', 'replace');
	}
	
	public function pjActionAccountSend($option_arr, $client_id, $salt, $locale_id)
	{
		$Email = new pjEmail();
		if ($option_arr['o_send_email'] == 'smtp')
		{
			$Email
			->setTransport('smtp')
			->setSmtpHost($option_arr['o_smtp_host'])
			->setSmtpPort($option_arr['o_smtp_port'])
			->setSmtpUser($option_arr['o_smtp_user'])
			->setSmtpPass($option_arr['o_smtp_pass'])
			;
		}
		$Email->setContentType('text/html');
	
		$client = pjClientModel::factory()->find($client_id)->getData();
		$tokens = pjAppController::getClientTokens($option_arr, $client, PJ_SALT, $locale_id);
			
		$pjMultiLangModel = pjMultiLangModel::factory();
	
		$locale_id = $this->getLocaleId();
	
		$admin_email = $this->getAdminEmail();
	
		if ($option_arr['o_email_client_account'] == 1)
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $locale_id)
			->where('t1.field', 'o_email_client_account_message')
			->limit(0, 1)
			->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $locale_id)
			->where('t1.field', 'o_email_client_account_subject')
			->limit(0, 1)
			->findAll()->getData();
	
			if (count($lang_message) === 1 && count($lang_subject) === 1)
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
	
				$Email
				->setTo($client['email'])
				->setFrom($admin_email)
				->setSubject($lang_subject[0]['content'])
				->send(pjUtil::textToHtml($message));
			}
		}
		if ($option_arr['o_admin_email_client_account'] == 1)
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $locale_id)
			->where('t1.field', 'o_admin_email_client_account_message')
			->limit(0, 1)
			->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $locale_id)
			->where('t1.field', 'o_admin_email_client_account_subject')
			->limit(0, 1)
			->findAll()->getData();
	
			if (count($lang_message) === 1 && count($lang_subject) === 1)
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
	
				$Email
				->setTo($admin_email)
				->setFrom($admin_email)
				->setSubject($lang_subject[0]['content'])
				->send(pjUtil::textToHtml($message));
			}
		}
	}
	public function pjActionForgotSend($option_arr, $client_id, $salt, $locale_id)
	{
		$Email = new pjEmail();
		if ($option_arr['o_send_email'] == 'smtp')
		{
			$Email
			->setTransport('smtp')
			->setSmtpHost($option_arr['o_smtp_host'])
			->setSmtpPort($option_arr['o_smtp_port'])
			->setSmtpUser($option_arr['o_smtp_user'])
			->setSmtpPass($option_arr['o_smtp_pass'])
			->setSender($option_arr['o_smtp_user'])
			;
		}
		$Email->setContentType('text/html');
	
		$client = pjClientModel::factory()->find($client_id)->getData();
		$tokens = pjAppController::getClientTokens($option_arr, $client, PJ_SALT, $locale_id);
			
		$pjMultiLangModel = pjMultiLangModel::factory();
	
		$locale_id = $this->getLocaleId();
	
		$admin_email = $this->getAdminEmail();
	
		$lang_message = $pjMultiLangModel->reset()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $locale_id)
			->where('t1.field', 'o_email_forgot_message')
			->limit(0, 1)
			->findAll()->getData();
		$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $locale_id)
			->where('t1.field', 'o_email_forgot_subject')
			->limit(0, 1)
			->findAll()->getData();
	
		if (count($lang_message) === 1 && count($lang_subject) === 1)
		{
			$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);

			$Email
			->setTo($client['email'])
			->setFrom($admin_email)
			->setSubject($lang_subject[0]['content'])
			->send(pjUtil::textToHtml($message));
		}
	}
	
	public function pjActionConfirmSend($option_arr, $booking_arr, $salt, $opt, $locale_id)
	{
		$Email = new pjEmail();
		if ($option_arr['o_send_email'] == 'smtp')
		{
			$Email
			->setTransport('smtp')
			->setSmtpHost($option_arr['o_smtp_host'])
			->setSmtpPort($option_arr['o_smtp_port'])
			->setSmtpUser($option_arr['o_smtp_user'])
			->setSmtpPass($option_arr['o_smtp_pass'])
			->setSender($option_arr['o_smtp_user'])
			;
		}
		$Email->setContentType('text/html');
	
		$tokens = pjAppController::getTokens($option_arr, $booking_arr, PJ_SALT, $locale_id);
		
		$pjMultiLangModel = pjMultiLangModel::factory();
	
		$admin_email = $this->getAdminEmail();
		$admin_phone = $this->getAdminPhone();
	
		if ($option_arr['o_email_payment'] == 1 && $opt == 'payment')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $locale_id)
			->where('t1.field', 'o_email_payment_message')
			->limit(0, 1)
			->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $locale_id)
			->where('t1.field', 'o_email_payment_subject')
			->limit(0, 1)
			->findAll()->getData();
	
			if (count($lang_message) === 1 && count($lang_subject) === 1)
			{
				if ($booking_arr['booking_option'] == 'roundtrip') {
					$message = str_replace(array('[Roundtrip]', '[/Roundtrip]'), array('', ''), $lang_message[0]['content']);
				} else {
					$message = preg_replace('/\[Roundtrip\].*\[\/Roundtrip\]/s', '', $lang_message[0]['content']);
				}
				$message = str_replace($tokens['search'], $tokens['replace'], $message);
	
				$Email
				->setTo($booking_arr['c_email'])
				->setFrom($admin_email)
				->setSubject($lang_subject[0]['content'])
				->send(pjUtil::textToHtml($message));
			}
		}
		if ($option_arr['o_admin_email_payment'] == 1 && $opt == 'payment')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $locale_id)
			->where('t1.field', 'o_admin_email_payment_message')
			->limit(0, 1)
			->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $locale_id)
			->where('t1.field', 'o_admin_email_payment_subject')
			->limit(0, 1)
			->findAll()->getData();
	
			if (count($lang_message) === 1 && count($lang_subject) === 1)
			{
				if ($booking_arr['booking_option'] == 'roundtrip') {
					$message = str_replace(array('[Roundtrip]', '[/Roundtrip]'), array('', ''), $lang_message[0]['content']);
				} else {
					$message = preg_replace('/\[Roundtrip\].*\[\/Roundtrip\]/s', '', $lang_message[0]['content']);
				}
				$message = str_replace($tokens['search'], $tokens['replace'], $message);
	
				$Email
				->setTo($admin_email)
				->setFrom($admin_email)
				->setSubject($lang_subject[0]['content'])
				->send(pjUtil::textToHtml($message));
			}
		}
		if(!empty($admin_phone) && $opt == 'payment')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $locale_id)
			->where('t1.field', 'o_admin_sms_payment_message')
			->limit(0, 1)
			->findAll()->getData();
			if (count($lang_message) === 1)
			{
				if ($booking_arr['booking_option'] == 'roundtrip') {
					$message = str_replace(array('[Roundtrip]', '[/Roundtrip]'), array('', ''), $lang_message[0]['content']);
				} else {
					$message = preg_replace('/\[Roundtrip\].*\[\/Roundtrip\]/s', '', $lang_message[0]['content']);
				}
				$message = str_replace($tokens['search'], $tokens['replace'], $message);
				$params = array(
						'text' => $message,
						'type' => 'unicode',
						'key' => md5($option_arr['private_key'] . PJ_SALT)
				);
				$params['number'] = $admin_phone;
				$this->requestAction(array('controller' => 'pjSms', 'action' => 'pjActionSend', 'params' => $params), array('return'));
			}
		}
		
		if ($option_arr['o_email_confirmation'] == 1 && $opt == 'confirm')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $locale_id)
			->where('t1.field', 'o_email_confirmation_message')
			->limit(0, 1)
			->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $locale_id)
			->where('t1.field', 'o_email_confirmation_subject')
			->limit(0, 1)
			->findAll()->getData();
			if (count($lang_message) === 1 && count($lang_subject) === 1)
			{
				if ($booking_arr['booking_option'] == 'roundtrip') {
					$message = str_replace(array('[Roundtrip]', '[/Roundtrip]'), array('', ''), $lang_message[0]['content']);
				} else {
					$message = preg_replace('/\[Roundtrip\].*\[\/Roundtrip\]/s', '', $lang_message[0]['content']);
				}
				$message = str_replace($tokens['search'], $tokens['replace'], $message);
				
				$Email
				->setTo($booking_arr['c_email'])
				->setFrom($admin_email)
				->setSubject($lang_subject[0]['content'])
				->send(pjUtil::textToHtml($message));
			}
		}
		if ($option_arr['o_admin_email_confirmation'] == 1 && $opt == 'confirm')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $locale_id)
			->where('t1.field', 'o_admin_email_confirmation_message')
			->limit(0, 1)
			->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $locale_id)
			->where('t1.field', 'o_admin_email_confirmation_subject')
			->limit(0, 1)
			->findAll()->getData();
				
			if (count($lang_message) === 1 && count($lang_subject) === 1)
			{
				if ($booking_arr['booking_option'] == 'roundtrip') {
					$message = str_replace(array('[Roundtrip]', '[/Roundtrip]'), array('', ''), $lang_message[0]['content']);
				} else {
					$message = preg_replace('/\[Roundtrip\].*\[\/Roundtrip\]/s', '', $lang_message[0]['content']);
				}
				$message = str_replace($tokens['search'], $tokens['replace'], $message);
	
				$Email
				->setTo($admin_email)
				->setFrom($admin_email)
				->setSubject($lang_subject[0]['content'])
				->send(pjUtil::textToHtml($message));
			}
		}
		if(!empty($admin_phone) && $opt == 'confirm')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $locale_id)
			->where('t1.field', 'o_admin_sms_confirmation_message')
			->limit(0, 1)
			->findAll()->getData();
			if (count($lang_message) === 1)
			{
				if ($booking_arr['booking_option'] == 'roundtrip') {
					$message = str_replace(array('[Roundtrip]', '[/Roundtrip]'), array('', ''), $lang_message[0]['content']);
				} else {
					$message = preg_replace('/\[Roundtrip\].*\[\/Roundtrip\]/s', '', $lang_message[0]['content']);
				}
				$message = str_replace($tokens['search'], $tokens['replace'], $message);
				$params = array(
						'text' => $message,
						'type' => 'unicode',
						'key' => md5($option_arr['private_key'] . PJ_SALT)
				);
				$params['number'] = $admin_phone;
				$this->requestAction(array('controller' => 'pjSms', 'action' => 'pjActionSend', 'params' => $params), array('return'));
			}
		}
	
		if ($option_arr['o_email_cancel'] == 1 && $opt == 'cancel')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $locale_id)
			->where('t1.field', 'o_email_cancel_message')
			->limit(0, 1)
			->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $locale_id)
			->where('t1.field', 'o_email_cancel_subject')
			->limit(0, 1)
			->findAll()->getData();
				
			if (count($lang_message) === 1 && count($lang_subject) === 1)
			{
				if ($booking_arr['booking_option'] == 'roundtrip') {
					$message = str_replace(array('[Roundtrip]', '[/Roundtrip]'), array('', ''), $lang_message[0]['content']);
				} else {
					$message = preg_replace('/\[Roundtrip\].*\[\/Roundtrip\]/s', '', $lang_message[0]['content']);
				}
				$message = str_replace($tokens['search'], $tokens['replace'], $message);
	
				$Email
				->setTo($booking_arr['c_email'])
				->setFrom($admin_email)
				->setSubject($lang_subject[0]['content'])
				->send(pjUtil::textToHtml($message));
			}
		}
		if ($option_arr['o_admin_email_cancel'] == 1 && $opt == 'cancel')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $locale_id)
			->where('t1.field', 'o_admin_email_cancel_message')
			->limit(0, 1)
			->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $locale_id)
			->where('t1.field', 'o_admin_email_cancel_subject')
			->limit(0, 1)
			->findAll()->getData();
				
			if (count($lang_message) === 1 && count($lang_subject) === 1)
			{
				if ($booking_arr['booking_option'] == 'roundtrip') {
					$message = str_replace(array('[Roundtrip]', '[/Roundtrip]'), array('', ''), $lang_message[0]['content']);
				} else {
					$message = preg_replace('/\[Roundtrip\].*\[\/Roundtrip\]/s', '', $lang_message[0]['content']);
				}
				$message = str_replace($tokens['search'], $tokens['replace'], $message);
	
				$Email
				->setTo($admin_email)
				->setFrom($admin_email)
				->setSubject($lang_subject[0]['content'])
				->send(pjUtil::textToHtml($message));
			}
		}
	}
}
?>