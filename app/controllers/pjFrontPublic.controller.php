<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjFrontPublic extends pjFront
{
	public function __construct()
	{
		parent::__construct();
		
		$this->setAjax(true);
		
		$this->setLayout('pjActionEmpty');
	}
	public function pjActionGetToLocations()
	{
	    $this->setAjax(true);
	    
	    if ($this->isXHR() || isset($_GET['_escaped_fragment_']))
	    {
	        if(isset($_GET['from_location_id']) && (int) $_GET['from_location_id'] > 0)
	        {
	            $to_location_arr = pjLocationModel::factory()
	            ->select('t1.*, t2.content AS name')
	            ->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjLocation' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
	            ->where("t1.type", 'pd')
	            ->where(sprintf("(t1.id IN(SELECT `TP`.to_location_id FROM `%s` AS `TP` WHERE `TP`.from_location_id=%u))", pjPriceModel::factory()->getTable(), $_GET['from_location_id']))
	            ->findAll()
	            ->getData();
	            
	            $this->set('to_location_arr', $to_location_arr);
	        }
	    }
	}
	public function pjActionSearch()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() || isset($_GET['_escaped_fragment_']))
		{
			if(isset($_POST['tbs_search']))
			{
				$date_time = pjUtil::formatDate($_POST['booking_date'], $this->option_arr['o_date_format']) . ' ' . date("H:i:s", strtotime($_POST['booking_time']));
				$date_time_ts = strtotime($date_time);
				if(time() + $this->option_arr['o_hour_earlier'] * 3600 > $date_time_ts)
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 120));
				}
				if (isset($_POST['booking_option']) && $_POST['booking_option'] == 'roundtrip') {
					$return_date_time = pjUtil::formatDate($_POST['return_date'], $this->option_arr['o_date_format']) . ' ' . date("H:i:s", strtotime($_POST['return_time']));
					$return_date_time_ts = strtotime($return_date_time);
					if ($return_date_time_ts <= $date_time_ts) {
						pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 130));
					}
				}
					
				if($this->_is('search'))
				{
					$this->_unset('search');
				}
				$this->_set("search", $_POST);
				
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200));
			}else{
			    $from_location_arr = pjLocationModel::factory()
			    ->select('t1.*, t2.content AS name')
			    ->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjLocation' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
			    ->where("t1.type", 'da')
			    ->findAll()
			    ->getData();
			    $this->set('from_location_arr', $from_location_arr);
			    
			    $SEARCH = @$_SESSION[$this->defaultStore]['search'];
			    if(isset($SEARCH['from_location_id']) && (int) $SEARCH['from_location_id']> 0)
			    {
    			    $to_location_arr = pjLocationModel::factory()
    			    ->select('t1.*, t2.content AS name')
    			    ->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjLocation' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
    			    ->where("t1.type", 'pd')
    			    ->where(sprintf("(t1.id IN(SELECT `TP`.to_location_id FROM `%s` AS `TP` WHERE `TP`.from_location_id=%u))", pjPriceModel::factory()->getTable(), $SEARCH['from_location_id']))
    			    ->findAll()
    			    ->getData();
    			    $this->set('to_location_arr', $to_location_arr);
			    }
			}
		}
	}
	public function pjActionFleets()
	{
		if($this->isXHR())
		{
			if (isset($_SESSION[$this->defaultStore]) &&
					count($_SESSION[$this->defaultStore]) > 0 &&
					isset($_SESSION[$this->defaultStore]['search']))
			{
				$SEARCH = $this->_get('search');
				$passengers = !empty($SEARCH['passengers']) ? $SEARCH['passengers'] : 0;
				$luggage = !empty($SEARCH['luggage']) ? $SEARCH['luggage'] : 0;
				$distance = !empty($SEARCH['distance']) ? $SEARCH['distance'] : 0;
				$from_location_id  = $SEARCH['from_location_id'];
				$to_location_id  = $SEARCH['to_location_id'];
				
				$pjFleetModel = pjFleetModel::factory();
				$fleet_arr = $pjFleetModel
					->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjMultiLang', "t3.model='pjFleet' AND t3.foreign_id=t1.id AND t3.field='description' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
					->select("t1.*, t2.content as fleet, t3.content as description, 
						(SELECT `TP`.price FROM `".pjPriceModel::factory()->getTable()."` AS `TP` WHERE `TP`.fleet_id=t1.id AND (`TP`.`from_location_id` = $from_location_id AND `TP`.`to_location_id` = $to_location_id) LIMIT 1 ) AS price,
						(SELECT `TP`.price_roundtrip FROM `".pjPriceModel::factory()->getTable()."` AS `TP` WHERE `TP`.fleet_id=t1.id AND (`TP`.`from_location_id` = $from_location_id AND `TP`.`to_location_id` = $to_location_id) LIMIT 1 ) AS price_roundtrip")
					->where('t1.status', 'T')
					->where('t1.passengers >=', $passengers)
					->where('t1.luggage >=', $luggage)
					->where(sprintf("(t1.id IN(SELECT `TP`.fleet_id FROM `%s` AS `TP` WHERE `TP`.from_location_id=%u AND `TP`.to_location_id=%u))", pjPriceModel::factory()->getTable(), $from_location_id, $to_location_id))
					->orderBy("price ASC")
					->findAll()->getData();
				
				$this->set('fleet_arr', $fleet_arr);
			}else{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
			}				
		}
	}
	
	public function pjActionCheckout()
	{
		if($this->isXHR())
		{
			if (isset($_SESSION[$this->defaultStore]) &&
					count($_SESSION[$this->defaultStore]) > 0 &&
					isset($_SESSION[$this->defaultStore]['fleet_id']))
			{
				if(isset($_POST['lbs_checkout']))
				{
					if ((int) $this->option_arr['o_bf_include_captcha'] === 3 && (!isset($_POST['captcha']) ||
							!pjCaptcha::validate($_POST['captcha'], $_SESSION[$this->defaultCaptcha]) ))
					{
						pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 110));
					}
					
					$_SESSION[$this->defaultForm] = $_POST;
						
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200));
				}else{
					$SEARCH = $this->_get('search');
					$passengers = !empty($SEARCH['passengers']) ? $SEARCH['passengers'] : 0;
					$luggage = !empty($SEARCH['luggage']) ? $SEARCH['luggage'] : 0;
					$distance = !empty($SEARCH['distance']) ? $SEARCH['distance'] : 0;
					$booking_option = !empty($SEARCH['booking_option']) ? $SEARCH['booking_option'] : 'oneway';
					$from_location_id  = $SEARCH['from_location_id'];
					$to_location_id  = $SEARCH['to_location_id'];
					
					$fleet_arr = pjFleetModel::factory()
						->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->join('pjMultiLang', "t3.model='pjFleet' AND t3.foreign_id=t1.id AND t3.field='description' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
						->select("t1.*, t2.content as fleet, t3.content as description, 
							(SELECT `TP`.price FROM `".pjPriceModel::factory()->getTable()."` AS `TP` WHERE `TP`.fleet_id=t1.id AND (`TP`.`from_location_id` = $from_location_id AND `TP`.`to_location_id` = $to_location_id) LIMIT 1 ) AS price,
							(SELECT `TP`.price_roundtrip FROM `".pjPriceModel::factory()->getTable()."` AS `TP` WHERE `TP`.fleet_id=t1.id AND (`TP`.`from_location_id` = $from_location_id AND `TP`.`to_location_id` = $to_location_id) LIMIT 1 ) AS price_roundtrip")
						->find($_SESSION[$this->defaultStore]['fleet_id'])->getData();
					$this->set('fleet_arr', $fleet_arr);
					
					$country_arr = pjCountryModel::factory()
						->select('t1.id, t2.content AS country_title')
						->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->orderBy('`country_title` ASC')
						->findAll()
						->getData();
					
					$this->set('country_arr', $country_arr);
					
					$pjFleetExtraModel = pjFleetExtraModel::factory()
						->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.extra_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->join('pjExtra', "t1.extra_id=t3.id", 'left')
						->select("t1.*, t2.content as name, t3.price, t3.per")
						->where('t1.fleet_id', $_SESSION[$this->defaultStore]['fleet_id'])
						->orderBy("name ASC");
					$avail_extra_arr = $pjFleetExtraModel->findAll()->getData();
					$this->set('avail_extra_arr', $avail_extra_arr);
					
					$extra_id_arr = isset($_SESSION[$this->defaultForm]['extra_id']) && is_array($_SESSION[$this->defaultForm]['extra_id']) ? array_keys($_SESSION[$this->defaultForm]['extra_id']) : array();
					$price_arr = pjAppController::calPrice($_SESSION[$this->defaultStore]['fleet_id'], $from_location_id, $to_location_id, $passengers, $extra_id_arr, $this->option_arr, $booking_option);
					$this->set('price_arr', $price_arr);
					$this->set('passengers', $passengers);
					$this->set('extra_id_arr', $extra_id_arr);
				}
			}else{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
			}
		}
	}
	
	public function pjActionGetPrices()
	{
		if($this->isXHR())
		{
			if (isset($_SESSION[$this->defaultStore]) &&
					count($_SESSION[$this->defaultStore]) > 0 &&
					isset($_SESSION[$this->defaultStore]['fleet_id']))
			{
				$SEARCH = $this->_get('search');
				$passengers = !empty($SEARCH['passengers']) ? $SEARCH['passengers'] : 0;
				$luggage = !empty($SEARCH['luggage']) ? $SEARCH['luggage'] : 0;
				$distance = !empty($SEARCH['distance']) ? $SEARCH['distance'] : 0;
				$booking_option = !empty($SEARCH['booking_option']) ? $SEARCH['booking_option'] : 'oneway';
				$from_location_id  = $SEARCH['from_location_id'];
				$to_location_id  = $SEARCH['to_location_id'];
					
				$pjFleetExtraModel = pjFleetExtraModel::factory()
					->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.extra_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjExtra', "t1.extra_id=t3.id", 'left')
					->select("t1.*, t2.content as name, t3.price, t3.per")
					->where('t1.fleet_id', $_SESSION[$this->defaultStore]['fleet_id'])
					->orderBy("name ASC");
				$avail_extra_arr = $pjFleetExtraModel->findAll()->getData();
				$this->set('avail_extra_arr', $avail_extra_arr);
	
				$extra_id_arr = isset($_POST['extra_id']) && is_array($_POST['extra_id']) ? array_keys($_POST['extra_id']) : array();
				
				$price_arr = pjAppController::calPrice($_SESSION[$this->defaultStore]['fleet_id'], $from_location_id, $to_location_id, $passengers, $extra_id_arr, $this->option_arr, $booking_option);
	
				$this->set('price_arr', $price_arr);
				$this->set('passengers', $passengers);
				$this->set('extra_id_arr', $extra_id_arr);
			}else{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
			}
		}
	}
	
	public function pjActionPreview()
	{
		if($this->isXHR())
		{
			if (isset($_SESSION[$this->defaultStore]) &&
					count($_SESSION[$this->defaultStore]) > 0 &&
					isset($_SESSION[$this->defaultStore]['fleet_id']))
			{
				$SEARCH = $this->_get('search');
				$passengers = !empty($SEARCH['passengers']) ? $SEARCH['passengers'] : 0;
				$luggage = !empty($SEARCH['luggage']) ? $SEARCH['luggage'] : 0;
				$distance = !empty($SEARCH['distance']) ? $SEARCH['distance'] : 0;
				$booking_option = !empty($SEARCH['booking_option']) ? $SEARCH['booking_option'] : 'oneway';
				$from_location_id  = $SEARCH['from_location_id'];
				$to_location_id  = $SEARCH['to_location_id'];
				
				$fleet_arr = pjFleetModel::factory()
					->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->select("t1.*, t2.content as fleet, 
						(SELECT `TP`.price FROM `".pjPriceModel::factory()->getTable()."` AS `TP` WHERE `TP`.fleet_id=t1.id AND (`TP`.`from_location_id` = $from_location_id AND `TP`.`to_location_id` = $to_location_id) LIMIT 1 ) AS price,
						(SELECT `TP`.price_roundtrip FROM `".pjPriceModel::factory()->getTable()."` AS `TP` WHERE `TP`.fleet_id=t1.id AND (`TP`.`from_location_id` = $from_location_id AND `TP`.`to_location_id` = $to_location_id) LIMIT 1 ) AS price_roundtrip")
					->find($_SESSION[$this->defaultStore]['fleet_id'])->getData();
				$this->set('fleet_arr', $fleet_arr);
				
				$extra_id_arr = isset($_SESSION[$this->defaultForm]['extra_id']) && is_array($_SESSION[$this->defaultForm]['extra_id']) ? array_keys($_SESSION[$this->defaultForm]['extra_id']) : array();
				$price_arr = pjAppController::calPrice($_SESSION[$this->defaultStore]['fleet_id'], $from_location_id, $to_location_id, $passengers, $extra_id_arr, $this->option_arr, $booking_option);
					
				$this->set('price_arr', $price_arr);
				$this->set('passengers', $passengers);
				
				$country_arr = pjCountryModel::factory()
					->select('t1.id, t2.content AS country_title')
					->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->find($_SESSION[$this->defaultForm]['c_country'])
					->getData();
				$this->set('country_arr', $country_arr);
			}else{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
			}
		}
	}
	
	public function pjActionGetPaymentForm()
	{
		if ($this->isXHR())
		{
			$arr = pjBookingModel::factory()->find($_GET['booking_id'])->getData();
	
			if (!empty($arr))
			{
				switch ($arr['payment_method'])
				{
					case 'paypal':
						$this->set('params', array(
						'name' => 'tbsPaypal',
						'id' => 'tbsPaypal',
						'business' => $this->option_arr['o_paypal_address'],
						'item_name' => pjSanitize::html($arr['uuid']),
						'custom' => $arr['id'],
						'amount' => $arr['deposit'],
						'currency_code' => $this->option_arr['o_currency'],
						'return' => $this->option_arr['o_thankyou_page'],
						'notify_url' => PJ_INSTALL_URL . 'index.php?controller=pjFrontEnd&action=pjActionConfirmPaypal',
						'target' => '_self',
						'charset' => 'utf-8'
								));
					break;
					case 'authorize':
						$this->set('params', array(
						'name' => 'tbsAuthorize',
						'id' => 'tbsAuthorize',
						'target' => '_self',
						'timezone' => $this->option_arr['o_authorize_timezone'],
						'transkey' => $this->option_arr['o_authorize_transkey'],
						'x_login' => $this->option_arr['o_authorize_merchant_id'],
						'x_description' => pjSanitize::html($arr['uuid']),
						'x_amount' => $arr['deposit'],
						'x_invoice_num' => $arr['id'],
						'x_receipt_link_url' => $this->option_arr['o_thankyou_page'],
						'x_relay_url' => PJ_INSTALL_URL . 'index.php?controller=pjFrontEnd&action=pjActionConfirmAuthorize'
								));
					break;
					case 'stripe':
					    $secret_key = $this->option_arr['o_stripe_secret_key'];
					    $data = array(
					        'payment_method_types' => array('card'),
					        'line_items' => array(
					            array(
					                'name' => pjSanitize::html($arr['uuid']),
					                'description' => pjSanitize::html($arr['uuid']),
					                'amount' => $arr['deposit'] * 100,
					                'currency' => $this->option_arr['o_currency'],
					                'quantity' => 1,
					            )
					        ),
					        'locale' => 'auto',
					        'success_url' => PJ_INSTALL_URL . 'index.php?controller=pjFrontEnd&action=pjActionConfirmStripe&id=' .$arr['id'] .'&stripe_session_id={CHECKOUT_SESSION_ID}',
					        'cancel_url' => $this->option_arr['o_stripe_cancel_url'],
					    );
					    $http = new pjHttp();
					    $curl = "https://api.stripe.com/v1/checkout/sessions";
					    $http->setMethod("POST");
					    $http->setData($data);
					    $http
					    ->addHeader("Authorization: Basic " . base64_encode($secret_key . ":"))
					    ->curlRequest($curl);
					    $response = $http->getResponse();
					    $result = json_decode($response, true);
					    if (isset($result['id']))
					    {
					        $this->set('session_id', $result['id']);
					    }
					    break;
					
				}
			}
			$this->set('arr', $arr);
			$this->set('get', $_GET);
		}
	}
	
	public function pjActionSearchForm()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() || isset($_GET['_escaped_fragment_']))
		{
			if(isset($_POST['tbs_search']))
			{
				$date_time = pjUtil::formatDate($_POST['booking_date'], $this->option_arr['o_date_format']) . ' ' . date("H:i:s", strtotime($_POST['booking_time']));
				$date_time_ts = strtotime($date_time);
				if(time() + $this->option_arr['o_hour_earlier'] * 3600 > $date_time_ts)
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 120));
				}		
				if (isset($_POST['booking_option']) && $_POST['booking_option'] == 'roundtrip') {
					$return_date_time = pjUtil::formatDate($_POST['return_date'], $this->option_arr['o_date_format']) . ' ' . date("H:i:s", strtotime($_POST['return_time']));
					$return_date_time_ts = strtotime($return_date_time);
					if ($return_date_time_ts <= $date_time_ts) {
						pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 130));
					}
				}		
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200));
			}else{
			    $from_location_arr = pjLocationModel::factory()
			    ->select('t1.*, t2.content AS name')
			    ->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjLocation' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
			    ->where("t1.type", 'da')
			    ->findAll()
			    ->getData();
			    $this->set('from_location_arr', $from_location_arr);
			    
			    $SEARCH = @$_SESSION[$this->defaultStore]['search'];
			    if(isset($SEARCH['from_location_id']) && (int) $SEARCH['from_location_id']> 0)
			    {
    			    $to_location_arr = pjLocationModel::factory()
    			    ->select('t1.*, t2.content AS name')
    			    ->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjLocation' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
    			    ->where("t1.type", 'pd')
    			    ->where(sprintf("(t1.id IN(SELECT `TP`.to_location_id FROM `%s` AS `TP` WHERE `TP`.from_location_id=%u))", pjPriceModel::factory()->getTable(), $SEARCH['from_location_id']))
    			    ->findAll()
    			    ->getData();
    			    $this->set('to_location_arr', $to_location_arr);
			    }
			}
		}
	}
}
?>