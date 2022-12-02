<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjFrontEnd extends pjFront
{
	public function __construct()
	{
		parent::__construct();
		$this->setAjax(true);
		$this->setLayout('pjActionEmpty');
	}

	public function pjActionLoad()
	{
		$this->setAjax(false);
		$this->setLayout('pjActionFront');
		
		$_terms_conditions = pjMultiLangModel::factory()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $this->getLocaleId())
			->where('t1.field', 'o_terms')
			->limit(0, 1)
			->findAll()->getData();
		$terms_conditions = '';
		if(!empty($_terms_conditions))
		{
			$terms_conditions = $_terms_conditions[0]['content'];
		}
		$this->set('terms_conditions', $terms_conditions);
		$autoload_fleets = 0;
		if (isset($_GET['tbs_search']) && (int)$_GET['tbs_search'] == 1
			&& isset($_GET['booking_type']) && in_array($_GET['booking_type'], array('from','to'))
			&& isset($_GET['booking_option']) && in_array($_GET['booking_option'], array('oneway','roundtrip'))
			&& isset($_GET['from_location_id']) && (int)$_GET['from_location_id'] > 0
			&& isset($_GET['to_location_id']) && (int)$_GET['to_location_id'] > 0   
			&& isset($_GET['passengers']) && (int)$_GET['passengers'] > 0 
			&& isset($_GET['booking_date']) && !empty($_GET['booking_date']) 
			& isset($_GET['booking_time']) && !empty($_GET['booking_time']) 
		) {
			$date_time = pjUtil::formatDate($_GET['booking_date'], $this->option_arr['o_date_format']) . ' ' . date("H:i:s", strtotime($_GET['booking_time']));
			$date_time_ts = strtotime($date_time);
			$valid = true;
			if (isset($_GET['booking_option']) && $_GET['booking_option'] == 'roundtrip') {
				$valid = false;
				if (isset($_GET['return_date']) && !empty($_GET['return_date']) && isset($_GET['return_time']) && !empty($_GET['return_time'])) {
					$return_date_time = pjUtil::formatDate($_GET['return_date'], $this->option_arr['o_date_format']) . ' ' . date("H:i:s", strtotime($_GET['return_time']));
					$return_date_time_ts = strtotime($return_date_time);
					if ($return_date_time_ts > $date_time_ts) {
						$valid = true;
					}
				}
			}
			if($valid && time() + $this->option_arr['o_hour_earlier'] * 3600 <= $date_time_ts)
			{
				if($this->_is('search'))
				{
					$this->_unset('search');
				}
				$this->_set("search", $_GET);
				$autoload_fleets = 1;
			}
		}
		$this->set('autoload_fleets', $autoload_fleets);
		
		ob_start();
		header("Content-Type: text/javascript; charset=utf-8");
	}
	
	public function pjActionLoadCss()
	{
		$dm = new pjDependencyManager(PJ_INSTALL_PATH, PJ_THIRD_PARTY_PATH);
		$dm->load(PJ_CONFIG_PATH . 'dependencies.php')->resolve();
	
		$theme = $this->option_arr['o_theme'];
		$fonts = $this->option_arr['o_theme'];
		if(isset($_GET['theme']) && in_array($_GET['theme'], array('theme1', 'theme2', 'theme3', 'theme4', 'theme5', 'theme6', 'theme7', 'theme8', 'theme9', 'theme10')))
		{
			$theme = $_GET['theme'];
			$fonts = $_GET['theme'];
		}
		$arr = array(
				array('file' => 'bootstrap-datetimepicker.min.css', 'path' => $dm->getPath('pj_bootstrap_datetimepicker')),
				array('file' => "$fonts.css", 'path' => PJ_CSS_PATH . "fonts/"),
				array('file' => 'style.css', 'path' => PJ_CSS_PATH),
				array('file' => "$theme.css", 'path' => PJ_CSS_PATH . "themes/"),
				array('file' => 'transitions.css', 'path' => PJ_CSS_PATH)
		);
		header("Content-Type: text/css; charset=utf-8");
		foreach ($arr as $item)
		{
			ob_start();
			@readfile($item['path'] . $item['file']);
			$string = ob_get_contents();
			ob_end_clean();
				
			if ($string !== FALSE)
			{
				echo str_replace(
						array('../fonts/glyphicons', "pjWrapper"),
						array(
								PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/fonts/glyphicons',
								"pjWrapperTaxiBooking_" . $theme
						),
						$string
				) . "\n";
			}
		}
		exit;
	}
	
	public function pjActionCaptcha()
	{
		$Captcha = new pjCaptcha(PJ_INSTALL_PATH . 'app/web/obj/Anorexia.ttf', $this->defaultCaptcha, 6);
		$Captcha->setImage(PJ_INSTALL_PATH . 'app/web/img/button.png')->init(isset($_GET['rand']) ? $_GET['rand'] : null);
	}

	public function pjActionCheckCaptcha()
	{
		if (!isset($_GET['captcha']) || empty($_GET['captcha']) || strtoupper($_GET['captcha']) != $_SESSION[$this->defaultCaptcha]){
			echo 'false';
		}else{
			echo 'true';
		}
		exit;
	}
	
	public function pjActionSetTaxi()
	{
		if($this->isXHR())
		{
			if($this->_is('fleet_id'))
			{
				$this->_unset('fleet_id');
			}
			if(isset($_GET['fleet_id']) && (int) $_GET['fleet_id'] > 0)
			{
				$this->_set('fleet_id', $_GET['fleet_id']);
			}
			pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
		}
	}
	public function pjActionCheckLogin()
	{
		if($this->isXHR())
		{
			if(isset($_POST['lbs_login']))
			{
				$pjClientModel = pjClientModel::factory();
				
				$client = $pjClientModel
					->where('t1.email', $_POST['login_email'])
					->where(sprintf("t1.password = AES_ENCRYPT('%s', '%s')", pjObject::escapeString($_POST['login_password']), PJ_SALT))
					->limit(1)
					->findAll()
					->getData();
				
				if (count($client) != 1)
				{
					$client = $pjClientModel
						->reset()
						->where('t1.email', $_POST['login_email'])
						->limit(1)
						->findAll()
						->getData();
					if (count($client) != 1)
					{
						pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => __('front_email_does_not_exist', true)));
					}else{
						pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => __('front_incorrect_password', true)));
					}
				}else{
					if ($client[0]['status'] != 'T')
					{
						pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => __('front_your_account_disabled', true)));
					}else{
						$last_login = date("Y-m-d H:i:s");
							
						$client = $pjClientModel->reset()->find($client[0]['id'])->getData();
						
						$_SESSION[$this->defaultFrontClient] = $client;
				
						$_SESSION[$this->defaultForm]['c_email'] = $client['email'];
						$_SESSION[$this->defaultForm]['c_password'] = $client['password'];
						$_SESSION[$this->defaultForm]['c_fname'] = $client['fname'];
						$_SESSION[$this->defaultForm]['c_lname'] = $client['lname'];
						$_SESSION[$this->defaultForm]['c_phone'] = $client['phone'];
						
						$_SESSION[$this->defaultForm]['c_company'] = $client['company'];
						$_SESSION[$this->defaultForm]['c_lname'] = $client['lname'];
						$_SESSION[$this->defaultForm]['c_city'] = $client['city'];
						$_SESSION[$this->defaultForm]['c_state'] = $client['state'];
						$_SESSION[$this->defaultForm]['c_zip'] = $client['zip'];
						$_SESSION[$this->defaultForm]['c_country'] = $client['country_id'];
							
						$data = array();
						$data['last_login'] = $last_login;
						$pjClientModel->reset()->setAttributes(array('id' => $client[0]['id']))->modify($data);
						pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
					}
				}
				
			}else{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => ''));
			}
		}
	}
	public function pjActionSendPassword()
	{
		if($this->isXHR())
		{
			$forgot_err = __('forgot_err', true);
			if(isset($_POST['lbs_forgot']))
			{
				$pjClientModel = pjClientModel::factory();
				$client = $pjClientModel
					->select("t1.*, AES_DECRYPT(t1.password, '".PJ_SALT."') AS `password`")
					->where('t1.email', $_POST['email'])
					->limit(1)
					->findAll()
					->getData();
	
				if (count($client) != 1)
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => $forgot_err[100]));
				} else {
					$client = $client[0];
					if ($client['status'] != 'T')
					{
						pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => $forgot_err[101]));
					}
						
					pjAppController::pjActionForgotSend($this->option_arr, $client['id'], PJ_SALT, 'forgot');
						
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => $forgot_err[200]));
				}
			}else{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => $forgot_err[102]));
			}
		}
	}
	public function pjActionLogout()
	{
		if($this->isXHR())
		{
			if(isset($_SESSION[$this->defaultFrontClient]))
			{
				unset($_SESSION[$this->defaultFrontClient]);
			}
			pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
		}
	}
	public function pjActionGoogleLogin()
	{
	    if ($this->isXHR())
	    {
	        if(isset($_POST['email']) && !empty($_POST['email']))
	        {
	            $pjClientModel = pjClientModel::factory();
	            $email = pjObject::escapeString($_POST['email']);
	            
	            if($pjClientModel->where('email', $email)->findCount()->getData() > 0)
	            {
	                $client = $pjClientModel->where('email', $email)->limit(1)->findAll()->getDataIndex(0);
	                
	                $_SESSION[$this->defaultFrontClient] = $client;
	                
	                $_SESSION[$this->defaultForm]['c_email'] = $client['email'];
	                $_SESSION[$this->defaultForm]['c_password'] = $client['password'];
	                $_SESSION[$this->defaultForm]['c_fname'] = $client['fname'];
	                $_SESSION[$this->defaultForm]['c_lname'] = $client['lname'];
	                $_SESSION[$this->defaultForm]['c_phone'] = $client['phone'];
	                $_SESSION[$this->defaultForm]['c_company'] = $client['company'];
	                $_SESSION[$this->defaultForm]['c_lname'] = $client['lname'];
	                $_SESSION[$this->defaultForm]['c_city'] = $client['city'];
	                $_SESSION[$this->defaultForm]['c_state'] = $client['state'];
	                $_SESSION[$this->defaultForm]['c_zip'] = $client['zip'];
	                $_SESSION[$this->defaultForm]['c_country'] = $client['country_id'];
	                
	                $data = array();
	                $data['last_login'] = date("Y-m-d H:i:s");
	                $pjClientModel->reset()->setAttributes(array('id' => $client['id']))->modify($data);
	                pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
	            }else{
    	            
	                $client_data = array();
	                $client_data['email'] = $email;
	                $client_data['status'] = 'T';
	                $client_data['fname'] = isset($_POST['fname']) && !empty($_POST['fname']) ? pjObject::escapeString($_POST['fname']) : ':NULL';
	                $client_data['lname'] = isset($_POST['lname']) && !empty($_POST['lname']) ? pjObject::escapeString($_POST['lname']) : ':NULL';
    	            $client_data['password'] = isset($FORM['c_password']) ? $FORM['c_password'] : pjUtil::getRandomPassword(6);
    	            $client_id = $pjClientModel->reset()->setAttributes($client_data)->insert()->getInsertId();
    	            if ($client_id !== false && (int) $client_id > 0)
    	            {
    	                $client = $pjClientModel->reset()->find($client_id)->getData();
    	                
    	                $_SESSION[$this->defaultFrontClient] = $client;
    	                
    	                $_SESSION[$this->defaultForm]['c_email'] = $client['email'];
    	                $_SESSION[$this->defaultForm]['c_password'] = $client['password'];
    	                $_SESSION[$this->defaultForm]['c_fname'] = $client['fname'];
    	                $_SESSION[$this->defaultForm]['c_lname'] = $client['lname'];
    	                $_SESSION[$this->defaultForm]['c_phone'] = $client['phone'];
    	                $_SESSION[$this->defaultForm]['c_company'] = $client['company'];
    	                $_SESSION[$this->defaultForm]['c_lname'] = $client['lname'];
    	                $_SESSION[$this->defaultForm]['c_city'] = $client['city'];
    	                $_SESSION[$this->defaultForm]['c_state'] = $client['state'];
    	                $_SESSION[$this->defaultForm]['c_zip'] = $client['zip'];
    	                $_SESSION[$this->defaultForm]['c_country'] = $client['country_id'];
    	                
    	                $data = array();
    	                $data['last_login'] = date("Y-m-d H:i:s");
    	                $pjClientModel->reset()->setAttributes(array('id' => $client_id))->modify($data);
    	                
    	                pjAppController::pjActionAccountSend($this->option_arr, $client_id, PJ_SALT, $this->getLocaleId());
    	                
    	                pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
    	            }else{
    	                pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Client account could not be saved.'));
    	            }
	            }
	        }else{
	            pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Email is missing.'));
	        }
	    }
	}
	public function pjActionSaveBooking()
	{
		if ($this->isXHR())
		{
			if (!isset($_POST['tbs_preview']) || !isset($_SESSION[$this->defaultForm]) || empty($_SESSION[$this->defaultForm]) || !isset($_SESSION[$this->defaultStore]) || empty($_SESSION[$this->defaultStore]))
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 109));
			}
			if ((int) $this->option_arr['o_bf_include_captcha'] === 3 && (!isset($_SESSION[$this->defaultForm]['captcha']) ||
					!pjCaptcha::validate($_SESSION[$this->defaultForm]['captcha'], $_SESSION[$this->defaultCaptcha]) ))
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 110));
			}
	
			$STORE = @$_SESSION[$this->defaultStore];
			$SEARCH = @$_SESSION[$this->defaultStore]['search'];
			$FORM = @$_SESSION[$this->defaultForm];
	
			$passengers = !empty($SEARCH['passengers']) ? $SEARCH['passengers'] : 0;
			$luggage = !empty($SEARCH['luggage']) ? $SEARCH['luggage'] : 0;
			$distance = !empty($SEARCH['distance']) ? $SEARCH['distance'] : 0;
			
			$data = array();
			$client_data = array();
			$pjClientModel = pjClientModel::factory();
				
			$data['client_id'] = ':NULL';
				
			$client_data['email'] = isset($FORM['c_email']) ? $FORM['c_email'] : ':NULL';
			$client_data['email'] = isset($FORM['c_email']) ? $FORM['c_email'] : ':NULL';
			$client_data['title'] = isset($FORM['c_title']) ? $FORM['c_title'] : ':NULL';
			$client_data['fname'] = isset($FORM['c_fname']) ? $FORM['c_fname'] : ':NULL';
			$client_data['lname'] = isset($FORM['c_lname']) ? $FORM['c_lname'] : ':NULL';
			$client_data['phone'] = isset($FORM['c_phone']) ? $FORM['c_phone'] : ':NULL';
			$client_data['company'] = isset($FORM['c_company']) ? $FORM['c_company'] : ':NULL';
			$client_data['address'] = isset($FORM['c_address']) ? $FORM['c_address'] : ':NULL';
			$client_data['city'] = isset($FORM['c_city']) ? $FORM['c_city'] : ':NULL';
			$client_data['state'] = isset($FORM['c_state']) ? $FORM['c_state'] : ':NULL';
			$client_data['zip'] = isset($FORM['c_zip']) ? $FORM['c_zip'] : ':NULL';
			$client_data['country_id'] = isset($FORM['c_country']) ? $FORM['c_country'] : ':NULL';
				
			if($this->isFrontLogged())
			{
				$data['client_id'] = $_SESSION[$this->defaultFrontClient]['id'];
				$pjClientModel->reset()->where('id', $data['client_id'])->limit(1)->modifyAll($client_data);
			}else{
				if(isset($FORM['c_email']))
				{
					$client_arr = $pjClientModel->where('email', $FORM['c_email'])->limit(1)->findAll()->getData();
					if(count($client_arr) == 1)
					{
						$data['client_id'] = $client_arr[0]['id'];
						$pjClientModel->reset()->where('id', $client_arr[0]['id'])->limit(1)->modifyAll($client_data);
					}
				}
	
				if($data['client_id'] == ':NULL')
				{
					$client_data['status'] = 'T';
					$client_data['created'] = date('Y-m-d H:i:s');
					$client_data['password'] = isset($FORM['c_password']) ? $FORM['c_password'] : pjUtil::getRandomPassword(6);
					$client_id = $pjClientModel->reset()->setAttributes($client_data)->insert()->getInsertId();
					if ($client_id !== false && (int) $client_id > 0)
					{
						$data['client_id'] = $client_id;
						pjAppController::pjActionAccountSend($this->option_arr, $client_id, PJ_SALT, $this->getLocaleId());
					}
				}
			}
			$booking_option = isset($SEARCH['booking_option']) ? $SEARCH['booking_option'] : 'oneway';
			$data['uuid'] = pjUtil::uuid();
			$data['fleet_id'] = $STORE['fleet_id'];
			$data['booking_type'] = $SEARCH['booking_type'];
			$data['booking_option'] = $booking_option;
			$data['from_location_id'] = $SEARCH['from_location_id'];
			$data['to_location_id'] = $SEARCH['to_location_id'];
			$data['distance'] = $distance;
			$data['ip'] = pjUtil::getClientIp();
			$data['status'] = $this->option_arr['o_booking_status'];
			$data['passengers'] = $passengers;
			$data['luggage'] = $luggage;
			$data['created'] = date('Y-m-d H:i:s');
			$extra_id_arr = isset($FORM['extra_id']) && is_array($FORM['extra_id']) ? array_keys($FORM['extra_id']) : array();
			$price_arr = pjAppController::calPrice($STORE['fleet_id'], $SEARCH['from_location_id'], $SEARCH['to_location_id'], $passengers, $extra_id_arr, $this->option_arr, $booking_option);
			
			$data['price_id'] = $price_arr['price_id'];
			$data['sub_total'] = $price_arr['subtotal'];
			$data['tax'] = $price_arr['tax'];
			$data['total'] = $price_arr['total'];
			$data['deposit'] = $price_arr['deposit'];
			$data['booking_date'] = pjUtil::formatDate($SEARCH['booking_date'], $this->option_arr['o_date_format']) . ' ' . date("H:i:s", strtotime($SEARCH['booking_time']));
			if ($booking_option == 'roundtrip') {
				$data['return_date'] = pjUtil::formatDate($SEARCH['return_date'], $this->option_arr['o_date_format']) . ' ' . date("H:i:s", strtotime($SEARCH['return_time']));	
			}
			$data['c_flight_time'] = isset($FORM['c_flight_time']) ? date("H:i:s", strtotime($FORM['c_flight_time'])) : ':NULL';
			
			$payment = ':NULL';
			if(isset($FORM['payment_method']))
			{
				if (isset($FORM['payment_method'])){
					$payment = $FORM['payment_method'];
				}
			}
				
			$pjBookingModel = pjBookingModel::factory();
			$id = $pjBookingModel->setAttributes(array_merge($FORM, $data))->insert()->getInsertId();
			if ($id !== false && (int) $id > 0)
			{
				$arr = $pjBookingModel
					->reset()
					->select("
						t1.*, 
					 	AES_DECRYPT(t1.cc_type, '".PJ_SALT."') as cc_type,
					 	AES_DECRYPT(t1.cc_num, '".PJ_SALT."') as cc_num,
					 	AES_DECRYPT(t1.cc_exp_month, '".PJ_SALT."') as cc_exp_month,
					 	AES_DECRYPT(t1.cc_exp_year, '".PJ_SALT."') as cc_exp_year,
					 	AES_DECRYPT(t1.cc_code, '".PJ_SALT."') as cc_code,
						t2.content as fleet, 
						t3.fname, 
						t3.lname, 
						t3.email,
						AES_DECRYPT(t3.password, '".PJ_SALT."') as password,
						t3.phone
					")
					->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjClient', "t3.id=t1.client_id", 'left outer')
					->find($id)
					->getData();
	
				$pjBookingExtraModel = pjBookingExtraModel::factory();
				if (isset($FORM['extra_id']) && is_array($FORM['extra_id']) && count($FORM['extra_id']) > 0)
				{
					$pjBookingExtraModel->begin();
					foreach ($FORM['extra_id'] as $extra_id => $price)
					{
						$pjBookingExtraModel
						->reset()
						->set('booking_id', $id)
						->set('extra_id', $extra_id)
						->insert();
					}
					$pjBookingExtraModel->commit();
				}
				
				$pdata = array();
				$pdata['booking_id'] = $id;
				$pdata['payment_method'] = $payment;
				$pdata['payment_type'] = 'online';
				$pdata['amount'] = $arr['deposit'];
				$pdata['status'] = 'notpaid';
				pjBookingPaymentModel::factory()->setAttributes($pdata)->insert();
	
				pjAppController::pjActionConfirmSend($this->option_arr, $arr, PJ_SALT, 'confirm', $this->getLocaleId());
	
				unset($_SESSION[$this->defaultStore]);
				unset($_SESSION[$this->defaultForm]);
				unset($_SESSION[$this->defaultCaptcha]);
					
				$json = array('code' => 200, 'text' => '', 'booking_id' => $id, 'payment' => $payment);
				pjAppController::jsonResponse($json);
			}else {
				pjAppController::jsonResponse(array('code' => 'ERR', 'code' => 119));
			}
		}
	}
	
	public function pjActionConfirmAuthorize()
	{
		if (pjObject::getPlugin('pjAuthorize') === NULL)
		{
			$this->log('Authorize.NET plugin not installed');
			exit;
		}
		$pjBookingModel = pjBookingModel::factory();
	
		$booking_arr = $pjBookingModel
			->select("
				t1.*,
				AES_DECRYPT(t1.cc_type, '".PJ_SALT."') as cc_type,
				AES_DECRYPT(t1.cc_num, '".PJ_SALT."') as cc_num,
				AES_DECRYPT(t1.cc_exp_month, '".PJ_SALT."') as cc_exp_month,
				AES_DECRYPT(t1.cc_exp_year, '".PJ_SALT."') as cc_exp_year,
				AES_DECRYPT(t1.cc_code, '".PJ_SALT."') as cc_code,
				t2.content as fleet, 
				t3.fname, 
				t3.lname, 
				t3.email,
				AES_DECRYPT(t3.password, '".PJ_SALT."') as password,
				t3.phone
			")
			->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
			->join('pjClient', "t3.id=t1.client_id", 'left outer')
			->find($_POST['x_invoice_num'])
			->getData();
		if (count($booking_arr) == 0)
		{
			$this->log('No such booking');
			pjUtil::redirect($this->option_arr['o_thankyou_page']);
		}
	
		if (count($booking_arr) > 0)
		{
			$params = array(
					'transkey' => $this->option_arr['o_authorize_transkey'],
					'x_login' => $this->option_arr['o_authorize_merchant_id'],
					'md5_setting' => $this->option_arr['o_authorize_md5_hash'],
					'key' => md5($this->option_arr['private_key'] . PJ_SALT)
			);
	
			$response = $this->requestAction(array('controller' => 'pjAuthorize', 'action' => 'pjActionConfirm', 'params' => $params), array('return'));
			if ($response !== FALSE && $response['status'] === 'OK')
			{
				$pjBookingModel->reset()
					->setAttributes(array('id' => $response['transaction_id']))
					->modify(array('status' => $this->option_arr['o_payment_status'], 'processed_on' => ':NOW()'));
	
				pjBookingPaymentModel::factory()
					->setAttributes(array('booking_id' => $response['transaction_id'], 'payment_type' => 'online'))
					->modify(array('status' => 'paid'));

				pjAppController::pjActionConfirmSend($this->option_arr, $booking_arr, PJ_SALT, 'payment', $this->getLocaleId());
				
			} elseif (!$response) {
				$this->log('Authorization failed');
			} else {
				$this->log('Booking not confirmed. ' . $response['response_reason_text']);
			}
			?>
				<script type="text/javascript">window.location.href="<?php echo $this->option_arr['o_thankyou_page']; ?>";</script>
			<?php
			return;
		}
	}
		
	public function pjActionConfirmPaypal()
	{
		if (pjObject::getPlugin('pjPaypal') === NULL)
		{
			$this->log('Paypal plugin not installed');
			exit;
		}
		$pjBookingModel = pjBookingModel::factory();
	
		$booking_arr = $pjBookingModel
			->select("
				t1.*,
				AES_DECRYPT(t1.cc_type, '".PJ_SALT."') as cc_type,
				AES_DECRYPT(t1.cc_num, '".PJ_SALT."') as cc_num,
				AES_DECRYPT(t1.cc_exp_month, '".PJ_SALT."') as cc_exp_month,
				AES_DECRYPT(t1.cc_exp_year, '".PJ_SALT."') as cc_exp_year,
				AES_DECRYPT(t1.cc_code, '".PJ_SALT."') as cc_code, 
				t2.content as fleet, 
				t3.fname, 
				t3.lname, 
				t3.email,
				AES_DECRYPT(t3.password, '".PJ_SALT."') as password,
				t3.phone
			")
			->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
			->join('pjClient', "t3.id=t1.client_id", 'left outer')
			->find($_POST['custom'])
			->getData();
		if (count($booking_arr) == 0)
		{
			$this->log('No such booking');
			pjUtil::redirect($this->option_arr['o_thankyou_page']);
		}
	
		$params = array(
				'txn_id' => @$booking_arr['txn_id'],
				'paypal_address' => $this->option_arr['o_paypal_address'],
				'deposit' => @$booking_arr['deposit'],
				'currency' => $this->option_arr['o_currency'],
				'key' => md5($this->option_arr['private_key'] . PJ_SALT)
		);
		$response = $this->requestAction(array('controller' => 'pjPaypal', 'action' => 'pjActionConfirm', 'params' => $params), array('return'));
	
		if ($response !== FALSE && $response['status'] === 'OK')
		{
			$this->log('Booking confirmed');
			$pjBookingModel->reset()->setAttributes(array('id' => $booking_arr['id']))->modify(array(
					'status' => $this->option_arr['o_payment_status'],
					'txn_id' => $response['transaction_id'],
					'processed_on' => ':NOW()'
			));
			pjBookingPaymentModel::factory()
				->setAttributes(array('booking_id' => $booking_arr['id'], 'payment_type' => 'online'))
				->modify(array('status' => 'paid'));

			pjAppController::pjActionConfirmSend($this->option_arr, $booking_arr, PJ_SALT, 'payment', $this->getLocaleId());
				
		} elseif (!$response) {
			$this->log('Authorization failed');
		} else {
			$this->log('Booking not confirmed');
		}
		pjUtil::redirect($this->option_arr['o_thankyou_page']);
	}
		
	public function pjActionCancel()
	{
		$this->setAjax(false);
		$this->setLayout('pjActionCancel');
	
		$pjBookingModel = pjBookingModel::factory();
	
		if (isset($_POST['booking_cancel']))
		{
			$booking_arr = $pjBookingModel
				->select("
					t1.*,
					AES_DECRYPT(t1.cc_type, '".PJ_SALT."') as cc_type,
					AES_DECRYPT(t1.cc_num, '".PJ_SALT."') as cc_num,
					AES_DECRYPT(t1.cc_exp_month, '".PJ_SALT."') as cc_exp_month,
					AES_DECRYPT(t1.cc_exp_year, '".PJ_SALT."') as cc_exp_year,
					AES_DECRYPT(t1.cc_code, '".PJ_SALT."') as cc_code, 
					t2.content as fleet, 
					t3.fname, 
					t3.lname, 
					t3.email,
					AES_DECRYPT(t3.password, '".PJ_SALT."') as password,
					t3.phone
				")
				->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjClient', "t3.id=t1.client_id", 'left outer')
				->find($_POST['id'])
				->getData();
			if (count($booking_arr) > 0)
			{
				$sql = "UPDATE `".$pjBookingModel->getTable()."` SET status = 'cancelled' WHERE SHA1(CONCAT(`id`, `created`, '".PJ_SALT."')) = '" . $_POST['hash'] . "'";
	
				$pjBookingModel->reset()->execute($sql);
	
				$arr = $pjBookingModel->reset()->find($_POST['id'])->getData();
				pjAppController::pjActionConfirmSend($this->option_arr, $booking_arr, PJ_SALT, 'cancel', $this->getLocaleId());
				
				pjUtil::redirect($_SERVER['PHP_SELF'] . '?controller=pjFrontEnd&action=pjActionCancel&err=200');
			}
		}else{
			if (isset($_GET['hash']) && isset($_GET['id']))
			{
				$arr = $pjBookingModel
					->reset()
					->select("t1.*, t2.content as fleet, t3.fname, t3.lname, t3.email,AES_DECRYPT(t3.password, '".PJ_SALT."') as password, t3.phone,
					            AES_DECRYPT(t1.cc_type, '".PJ_SALT."') AS `cc_type`,
								AES_DECRYPT(t1.cc_num, '".PJ_SALT."') AS `cc_num`,
								AES_DECRYPT(t1.cc_exp_month, '".PJ_SALT."') AS `cc_exp_month`,
								AES_DECRYPT(t1.cc_exp_year, '".PJ_SALT."') AS `cc_exp_year`,
								AES_DECRYPT(t1.cc_code, '".PJ_SALT."') AS `cc_code`")
					->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjClient', "t3.id=t1.client_id", 'left outer')
					->find($_GET['id'])
					->getData();
				if (count($arr) == 0)
				{
					$this->set('status', 2);
				}else{
					if ($arr['status'] == 'cancelled')
					{
						$this->set('status', 4);
					}else{
						$hash = sha1($arr['id'] . $arr['created'] . PJ_SALT);
						if ($_GET['hash'] != $hash)
						{
							$this->set('status', 3);
						}else{
							$client = pjClientModel::factory()
								->select("t1.*, t2.content as country_title")
								->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.country_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
								->find($arr['client_id'])->getData();
							
							$this->set('arr', $arr);
							$this->set('client', $client);
						}
					}
				}
			}else if (!isset($_GET['err'])) {
				$this->set('status', 1);
			}
		}
	}
	public function pjActionConfirmStripe()
	{
	    header('HTTP/1.1 200 OK');
	    
	    $this->setAjax(true);
	    
	    if(isset($_REQUEST['stripe_session_id']) && !empty($_REQUEST['stripe_session_id']))
	    {
	        if(isset($_REQUEST['id']) && !empty($_REQUEST['id']))
	        {
	            $pjBookingModel = pjBookingModel::factory();
	            
	            $booking_arr = $pjBookingModel
	            ->select("
					t1.*,
					AES_DECRYPT(t1.cc_type, '".PJ_SALT."') as cc_type,
					AES_DECRYPT(t1.cc_num, '".PJ_SALT."') as cc_num,
					AES_DECRYPT(t1.cc_exp_month, '".PJ_SALT."') as cc_exp_month,
					AES_DECRYPT(t1.cc_exp_year, '".PJ_SALT."') as cc_exp_year,
					AES_DECRYPT(t1.cc_code, '".PJ_SALT."') as cc_code,
					t2.content as fleet,
					t3.fname,
					t3.lname,
					t3.email,
					AES_DECRYPT(t3.password, '".PJ_SALT."') as password,
					t3.phone
				")
				->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjClient', "t3.id=t1.client_id", 'left outer')
				->find($_REQUEST['id'])
				->getData();
	            
				
                if (!empty($booking_arr))
                {
                    $http = new pjHttp();
                    list($year, $month, $day) = explode("-", date("Y-n-j"));
                    $queryString = http_build_query(array(
                        'created' => array(
                            'gte' => mktime(0, 0, 0, $month, $day, $year),
                        ),
                    ), null, '&');
                    $curl = "https://api.stripe.com/v1/events?" . $queryString;
                    $secret_key = $this->option_arr['o_stripe_secret_key'];
                    $http->setMethod("GET");
                    $http
                    ->addHeader("Authorization: Basic " . base64_encode($secret_key . ":"))
                    ->curlRequest($curl);
                    
                    $response = $http->getResponse();
                    $events = json_decode($response, true);
                    
                    $found = false;
                    $completed = false;
                    foreach ($events['data'] as $item)
                    {
                        if ($item['data']['object']['id'] == $_REQUEST['stripe_session_id'] && $item['data']['object']['object'] == 'checkout.session')
                        {
                            $found = true;
                            if ($item['type'] == 'checkout.session.completed')
                            {
                                $completed = true;
                                
                                $this->log('Booking confirmed');
                                $pjBookingModel->reset()->setAttributes(array('id' => $booking_arr['id']))->modify(array(
                                    'status' => $this->option_arr['o_payment_status'],
                                    'processed_on' => ':NOW()'
                                ));
                                pjBookingPaymentModel::factory()
                                ->setAttributes(array('booking_id' => $booking_arr['id'], 'payment_type' => 'online'))
                                ->modify(array('status' => 'paid'));
                                
                                pjAppController::pjActionConfirmSend($this->option_arr, $booking_arr, PJ_SALT, 'payment', $this->getLocaleId());
                                
                                $this->log('Stripe: Booking is confirmed.');
                            }
                            break;
                        }
                    }
                    if (!$found)
                    {
                        $this->log("Stripe: Payment not found.");
                    }
                    if (!$completed)
                    {
                        $this->log("Stripe: Payment failed.");
                    }
                }else{
                    $this->log('Stripe: No such booking');
                }
	        }else{
	            $this->log('Stripe: Booking ID is missing.');
	        }
	    }else{
	        $this->log('Stripe: Session ID is missing');
	    }
	    
	    pjUtil::redirect($this->option_arr['o_thankyou_page']);
	}
}
?>