<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminBookings extends pjAdmin
{
	public function pjActionCheckID()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (!isset($_GET['uuid']) || empty($_GET['uuid']))
			{
				echo 'false';
				exit;
			}
			$pjBookingModel = pjBookingModel::factory()->where('t1.uuid', $_GET['uuid']);
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				$pjBookingModel->where('t1.id !=', $_GET['id']);
			}
			echo $pjBookingModel->findCount()->getData() == 0 ? 'true' : 'false';
		}
		exit;
	}

	public function pjActionCheckEmail()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {
			if (empty($_GET['c_email'])) {
				echo 'true';
				exit;
			}

			$client = pjClientModel::factory()->where('email', pjObject::escapeString($_GET['c_email']))->findAll()->getDataIndex(0);
			echo (empty($client)) ? 'true' : 'false';
		}

		exit;
	}
	
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminBookings.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionGetBooking()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjBookingModel = pjBookingModel::factory()
				->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjClient', "t3.id=t1.client_id", 'left');
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjBookingModel->where("(t3.fname LIKE '%$q%' OR t3.lname LIKE '%$q%' OR t3.email LIKE '%$q%' OR t2.content LIKE '%$q%')");
			}
			
			if (isset($_GET['fleet_id']) && !empty($_GET['fleet_id']))
			{
				$fleet_id = pjObject::escapeString($_GET['fleet_id']);
				$pjBookingModel->where("(t1.fleet_id='".$fleet_id."')");
			}
			if (isset($_GET['client_id']) && (int) $_GET['client_id'] > 0)
			{
				$client_id = pjObject::escapeString($_GET['client_id']);
				$pjBookingModel->where("(t1.client_id='".$client_id."')");
			}
			if (isset($_GET['status']) && !empty($_GET['status']) && in_array($_GET['status'], array('confirmed','cancelled','pending')))
			{
				$pjBookingModel->where('t1.status', $_GET['status']);
			}

			if (isset($_GET['name']) && !empty($_GET['name']))
			{
				$q = pjObject::escapeString($_GET['name']);
				$pjBookingModel->where('t3.fname LIKE', "%$q%");
				$pjBookingModel->orWhere('t3.lname LIKE', "%$q%");
			}
			if (isset($_GET['email']) && !empty($_GET['email']))
			{
				$q = pjObject::escapeString($_GET['email']);
				$pjBookingModel->where('t3.email LIKE', "%$q%");
			}
			if (isset($_GET['phone']) && !empty($_GET['phone']))
			{
				$q = pjObject::escapeString($_GET['phone']);
				$pjBookingModel->where('t3.phone LIKE', "%$q%");
			}
			if (isset($_GET['date']) && !empty($_GET['date']))
			{
				$pjBookingModel->where("(DATE_FORMAT(t1.booking_date, '%Y-%m-%d')='".pjObject::escapeString($_GET['date'])."')");
			}
			$column = 'created';
			$direction = 'DESC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}
			
			$total = $pjBookingModel->findCount()->getData();
			
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = array();
			
			$data = $pjBookingModel
				->select("t1.*, t2.content as fleet, t3.fname, t3.lname, t3.email,t3.phone, AES_DECRYPT(t1.cc_type, '".PJ_SALT."') AS `cc_type`,
								AES_DECRYPT(t1.cc_num, '".PJ_SALT."') AS `cc_num`,
								AES_DECRYPT(t1.cc_exp_month, '".PJ_SALT."') AS `cc_exp_month`,
								AES_DECRYPT(t1.cc_exp_year, '".PJ_SALT."') AS `cc_exp_year`,
								AES_DECRYPT(t1.cc_code, '".PJ_SALT."') AS `cc_code`")
				->orderBy("$column $direction")
				->limit($rowCount, $offset)
				->findAll()
				->getData();
				
			foreach($data as $k => $v)
			{
				$client_arr = array();
				if(!empty($v['c_fname']) || !empty($v['fname']))
				{
					$client_arr[] = !empty($v['client_id']) ? pjSanitize::clean($v['fname']) : pjSanitize::clean($v['c_fname']) ;
				}
				if(!empty($v['c_lname']) || !empty($v['lname']))
				{
					$client_arr[] = !empty($v['client_id']) ? pjSanitize::clean($v['lname']) : pjSanitize::clean($v['c_lname']) ;
				}
				$v['client'] = join(" ", $client_arr) . "<br/>" . (!empty($v['client_id']) ? $v['email'] : $v['lname'] );
				$date_time = pjUtil::formatTime(date('H:i:s', strtotime($v['booking_date'])), 'H:i:s', $this->option_arr['o_time_format']) . ',<br/>' . pjUtil::formatDate(date('Y-m-d', strtotime($v['booking_date'])), 'Y-m-d', $this->option_arr['o_date_format']);
				if ($v['booking_option'] == 'roundtrip' && !empty($v['return_date'])) {
					$date_time .= '<br/>'.__('front_return_on', true).': '.pjUtil::formatTime(date('H:i:s', strtotime($v['return_date'])), 'H:i:s', $this->option_arr['o_time_format']) . ',<br/>' . pjUtil::formatDate(date('Y-m-d', strtotime($v['return_date'])), 'Y-m-d', $this->option_arr['o_date_format']);
				}
				$v['date_time']  = $date_time;
				$v['distance'] = (int) $v['distance'] . ' km';
				$data[$k] = $v;
			}
						
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionSaveBooking()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjBookingModel = pjBookingModel::factory();
			$pjBookingModel->reset()->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
		}
		exit;
	}
	
	public function pjActionExportBooking()
	{
		$this->checkLogin();
		
		if (isset($_POST['record']) && is_array($_POST['record']))
		{
			$arr = pjBookingModel::factory()->whereIn('id', $_POST['record'])->findAll()->getData();
			$csv = new pjCSV();
			$csv
				->setHeader(true)
				->setName("Bookings-".time().".csv")
				->process($arr)
				->download();
		}
		exit;
	}
	
	public function pjActionPrint()
	{
		$this->checkLogin();
		$this->setLayout('pjActionPrint');
		$transfer_arr = array();
		
		if ((isset($_GET['record']) && $_GET['record'] != '') || isset($_GET['today']) || isset($_GET['id']))
		{
			$pjBookingModel = pjBookingModel::factory()
				->select("t1.*, t2.content as fleet, t3.fname, t3.lname, t3.email, t3.phone, t3.company, t3.address, t3.city, t3.state, t3.zip, t4.content as country")
				->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjClient', "t3.id=t1.client_id", 'left outer')
				->join('pjMultiLang', "t4.model='pjCountry' AND t4.foreign_id=t3.country_id AND t4.field='name' AND t4.locale='".$this->getLocaleId()."'", 'left outer');
						
			if(!isset($_GET['id']))
			{
				if (isset($_GET['record']) && $_GET['record'] != '')
				{
					$pjBookingModel->whereIn("t1.id", explode(",", $_GET['record']));			
				}else{
					$pjBookingModel->where("(DATE_FORMAT(t1.booking_date, '%Y-%m-%d')=DATE_FORMAT(NOW(), '%Y-%m-%d'))")	;
					$pjBookingModel->where("t1.status <> 'cancelled'");
				}
			}else{
				$pjBookingModel->where("t1.id", $_GET['id']);
				
				$extras = NULL;
				$extra_arr = array();
				$avail_extra_arr = pjBookingExtraModel::factory()
					->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.extra_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjExtra', "t1.extra_id=t3.id", 'left')
					->select("t1.*, t2.content as name, t3.price, t3.per")
					->where('t1.booking_id', $_GET['id'])
					->orderBy("name ASC")
					->findAll()->getData();
				
				foreach($avail_extra_arr as $k => $v)
				{
					$extra_arr[] = pjSanitize::html($v['name']) . " (" . pjUtil::formatCurrencySign($v['price'], $this->option_arr['o_currency']) .  ($v['per'] == 'person' ? ' ' . __('lblPerPerson', true) : '') . ')';
				}
				
				$extras = join("<br/>", $extra_arr);
				$this->set('extras', $extras);
			}
			$transfer_arr = $pjBookingModel
				->orderBy("t1.created DESC")
				->findAll()
				->getData();
		}		
		$this->set('transfer_arr', $transfer_arr);
	}
	
	public function pjActionDeleteBooking()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			$pjBookingModel = pjBookingModel::factory();
			if ($pjBookingModel->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				pjBookingPaymentModel::factory()->where('booking_id', $_GET['id'])->eraseAll();
				pjBookingExtraModel::factory()->where('booking_id', $_GET['id'])->eraseAll();
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteBookingBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				$pjBookingModel = pjBookingModel::factory();
				$pjBookingModel->whereIn('id', $_POST['record'])->eraseAll();
				pjBookingPaymentModel::factory()->whereIn('booking_id', $_POST['record'])->eraseAll();
				pjBookingExtraModel::factory()->whereIn('booking_id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			if (isset($_POST['booking_create']))
			{
				$pjBookingModel = pjBookingModel::factory();
				
				$_date = $_POST['booking_date']; unset($_POST['booking_date']);
				if(count(explode(" ", $_date)) == 3)
				{
					list($date, $time, $period) = explode(" ", $_date);
					$time = pjUtil::formatTime($time . ' ' . $period, $this->option_arr['o_time_format']);
				}else{
					list($date, $time) = explode(" ", $_date);
					$time = pjUtil::formatTime($time, $this->option_arr['o_time_format']);
				}
				
				$data = array();
				$data['uuid'] = pjUtil::uuid();
				$data['ip'] = pjUtil::getClientIp();
				$data['booking_date'] = pjUtil::formatDate($date, $this->option_arr['o_date_format']) . ' ' . $time;
				$data['c_flight_time'] = isset($_POST['c_flight_time']) ? date("H:i:s", strtotime($_POST['c_flight_time'])) : ':NULL';
				if ($_POST['booking_option'] == 'roundtrip') {
					if(count(explode(" ", $_POST['return_date'])) == 3)
					{
						list($_date, $_time, $_period) = explode(" ", $_POST['return_date']);
						$_time = pjUtil::formatTime($_time . ' ' . $_period, $this->option_arr['o_time_format']);
					}else{
						list($_date, $_time) = explode(" ", $_POST['return_date']);
						$_time = pjUtil::formatTime($_time, $this->option_arr['o_time_format']);
					}
					$data['return_date'] = pjUtil::formatDate($_date, $this->option_arr['o_date_format']) . ' ' . $_time;
				} else {
					$data['return_date'] = ':NULL';
				}
				unset($_POST['return_date']);
				if(!isset($_POST['client_id']) || (isset($_POST['client_id']) && $_POST['client_id'] == ''))
				{
					$c_data = array();
					$c_data['title'] = isset($_POST['c_title']) ? $_POST['c_title'] : ':NULL';
					$c_data['fname'] = isset($_POST['c_fname']) ? $_POST['c_fname'] : ':NULL';
					$c_data['lname'] = isset($_POST['c_lname']) ? $_POST['c_lname'] : ':NULL';
					$c_data['email'] = isset($_POST['c_email']) ? $_POST['c_email'] : ':NULL';
					$c_data['password'] = pjUtil::getRandomPassword(6);
					$c_data['phone'] = isset($_POST['c_phone']) ? $_POST['c_phone'] : ':NULL';
					$c_data['status'] = 'T';
					if($c_data['email'] != ':NULL')
					{
						$pjClientModel = pjClientModel::factory();
						$client_arr = $pjClientModel->where('email', $c_data['email'])->limit(1)->findAll()->getData();
						if(count($client_arr) == 1)
						{
							$data['client_id'] = $client_arr[0]['id'];
						}else{
							$client_id = $pjClientModel->reset()->setAttributes($c_data)->insert()->getInsertId();
							if ($client_id !== false && (int) $client_id > 0)
							{
								$data['client_id'] = $client_id;
								pjAppController::pjActionAccountSend($this->option_arr, $client_id, PJ_SALT, $this->getLocaleId());
							}
						}
					}
				}
				
				$id = pjBookingModel::factory(array_merge($_POST, $data))->insert()->getInsertId();
				
				if ($id !== false && (int) $id > 0)
				{
					$pjBookingExtraModel = pjBookingExtraModel::factory();
					if (isset($_POST['extra_id']) && is_array($_POST['extra_id']) && count($_POST['extra_id']) > 0)
					{
						$pjBookingExtraModel->begin();
						foreach ($_POST['extra_id'] as $extra_id)
						{
							$pjBookingExtraModel
							->reset()
							->set('booking_id', $id)
							->set('extra_id', $extra_id)
							->insert();
						}
						$pjBookingExtraModel->commit();
					}
					$err = 'ABB03';
				}else{
					$err = 'ABB04';
				}
				
				pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminBookings&action=pjActionIndex&err=$err");
			}else{
				$country_arr = pjCountryModel::factory()
					->select('t1.id, t2.content AS country_title')
					->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->orderBy('`country_title` ASC')
					->findAll()
					->getData();
						
				$this->set('country_arr', $country_arr);
				
				$client_arr = pjClientModel::factory()->select('t1.*')->orderBy('`fname` ASC')->findAll()->getData();
				
				$this->set('client_arr', $client_arr);
				
				$fleet_arr = pjFleetModel::factory()
					->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->select("t1.*, t2.content as fleet")
					->where('t1.status', 'T')
					->orderBy("fleet ASC")
					->findAll()->getData();
				
				$this->set('fleet_arr', $fleet_arr);
				
				$from_location_arr = pjLocationModel::factory()
				->select('t1.*, t2.content AS name')
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjLocation' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
				->where("t1.type", 'da')
				->findAll()
				->getData();
				
				$this->set('from_location_arr', $from_location_arr);

				$api_key = isset($this->option_arr['o_google_api_key']) && !empty($this->option_arr['o_google_api_key']) ? '&key=' . $this->option_arr['o_google_api_key'] : '';
				$this->appendJs('https://maps.googleapis.com/maps/api/js?libraries=places' . $api_key, null, true);
				$this->appendJs('jquery-ui-sliderAccess.js', PJ_THIRD_PARTY_PATH . 'timepicker/');
				$this->appendJs('jquery-ui-timepicker-addon.js', PJ_THIRD_PARTY_PATH . 'timepicker/');
				$this->appendCss('jquery-ui-timepicker-addon.css', PJ_THIRD_PARTY_PATH . 'timepicker/');
				$this->appendJs('chosen.jquery.min.js', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('additional-methods.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminBookings.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			if (isset($_POST['booking_update']))
			{
				$pjBookingModel = pjBookingModel::factory();
				
				$data = array();
				$arr = $pjBookingModel->find($_POST['id'])->getData();
				if (empty($arr))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=ABB08");
				}

				if(count(explode(" ", $_POST['booking_date'])) == 3)
				{
					list($date, $time, $period) = explode(" ", $_POST['booking_date']);
					$time = pjUtil::formatTime($time . ' ' . $period, $this->option_arr['o_time_format']);
				}else{
					list($date, $time) = explode(" ", $_POST['booking_date']);
					$time = pjUtil::formatTime($time, $this->option_arr['o_time_format']);
				}
				unset($_POST['booking_date']);
				
				if ($_POST['booking_option'] == 'roundtrip') {
					if(count(explode(" ", $_POST['return_date'])) == 3)
					{
						list($_date, $_time, $_period) = explode(" ", $_POST['return_date']);
						$_time = pjUtil::formatTime($_time . ' ' . $_period, $this->option_arr['o_time_format']);
					}else{
						list($_date, $_time) = explode(" ", $_POST['return_date']);
						$_time = pjUtil::formatTime($_time, $this->option_arr['o_time_format']);
					}
					$data['return_date'] = pjUtil::formatDate($_date, $this->option_arr['o_date_format']) . ' ' . $_time;
				} else {
					$data['return_date'] = ':NULL';
				}
				unset($_POST['return_date']);
				
				$data['ip'] = pjUtil::getClientIp();
				$data['booking_date'] = pjUtil::formatDate($date, $this->option_arr['o_date_format']) . ' ' . $time;
				$data['c_flight_time'] = isset($_POST['c_flight_time']) ? date("H:i:s", strtotime($_POST['c_flight_time'])) : ':NULL';
				
				if(!isset($_POST['client_id']) || (isset($_POST['client_id']) && $_POST['client_id'] == ''))
				{
					$c_data = array();
					$c_data['title'] = isset($_POST['c_title']) ? $_POST['c_title'] : ':NULL';
					$c_data['fname'] = isset($_POST['c_fname']) ? $_POST['c_fname'] : ':NULL';
					$c_data['lname'] = isset($_POST['c_lname']) ? $_POST['c_lname'] : ':NULL';
					$c_data['email'] = isset($_POST['c_email']) ? $_POST['c_email'] : ':NULL';
					$c_data['password'] = pjUtil::getRandomPassword(6);
					$c_data['phone'] = isset($_POST['c_phone']) ? $_POST['c_phone'] : ':NULL';
					$c_data['status'] = 'T';
					if($c_data['email'] != ':NULL')
					{
						$pjClientModel = pjClientModel::factory();
						$client_arr = $pjClientModel->where('email', $c_data['email'])->limit(1)->findAll()->getData();
						if(count($client_arr) == 1)
						{
							$data['client_id'] = $client_arr[0]['id'];
						}else{
							$client_id = $pjClientModel->reset()->setAttributes($c_data)->insert()->getInsertId();
							if ($client_id !== false && (int) $client_id > 0)
							{
								$data['client_id'] = $client_id;
								pjAppController::pjActionAccountSend($this->option_arr, $client_id, PJ_SALT, $this->getLocaleId());
							}
						}
					}
				}
								
				$pjBookingModel->reset()->where('id', $_POST['id'])->limit(1)->modifyAll(array_merge($_POST, $data));
				
				$pjBookingExtraModel = pjBookingExtraModel::factory();
				$pjBookingExtraModel->where('booking_id', $_POST['id'])->eraseAll();
				if (isset($_POST['extra_id']) && is_array($_POST['extra_id']) && count($_POST['extra_id']) > 0)
				{
					$pjBookingExtraModel->begin();
					foreach ($_POST['extra_id'] as $extra_id)
					{
						$pjBookingExtraModel
						->reset()
						->set('booking_id', $_POST['id'])
						->set('extra_id', $extra_id)
						->insert();
					}
					$pjBookingExtraModel->commit();
				}
				
				$err = 'ABB01';
				pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminBookings&action=pjActionIndex&err=$err");
			}else{
				
				$arr = pjBookingModel::factory()->find($_GET['id'])->getData();
				if(count($arr) <= 0)
				{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminBookings&action=pjActionIndex&err=ABB08");
				}
				
				$country_arr = pjCountryModel::factory()
							->select('t1.id, t2.content AS country_title')
							->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
							->orderBy('`country_title` ASC')->findAll()->getData();
						
				$this->set('country_arr', $country_arr);
				$this->set('arr', $arr);
				
				$client_arr = pjClientModel::factory()->select('t1.*')->orderBy('`fname` ASC')->findAll()->getData();
				$this->set('client_arr', $client_arr);
				
				$this->set('client', pjClientModel::factory()->find($arr['client_id'])->getData());
				
				$fleet_arr = pjFleetModel::factory()
				->select('t1.*, t2.content AS fleet')
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjFleet' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'fleet'", 'left')
				->where("t1.status", 'T')
				->where(sprintf("(t1.id IN(SELECT `TP`.fleet_id FROM `%s` AS `TP` WHERE `TP`.from_location_id=%u AND `TP`.to_location_id=%u))", pjPriceModel::factory()->getTable(), $arr['from_location_id'], $arr['to_location_id']))
				->orderBy("fleet ASC")
				->findAll()
				->getData();
				$this->set('fleet_arr', $fleet_arr);
				
				$pjFleetExtraModel = pjFleetExtraModel::factory()
					->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.extra_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjExtra', "t1.extra_id=t3.id", 'left')
					->select("t1.*, t2.content as name, t3.price, t3.per")
					->where('t1.fleet_id', $arr['fleet_id'])
					->orderBy("name ASC");
				$avail_extra_arr = $pjFleetExtraModel->findAll()->getData();
				$this->set('avail_extra_arr', $avail_extra_arr);
				
				$extra_id_arr = pjBookingExtraModel::factory()->where('booking_id', $_GET['id'])->findAll()->getDataPair(null, 'extra_id');
				$this->set('extra_id_arr', $extra_id_arr);
				
				$from_location_arr = pjLocationModel::factory()
				->select('t1.*, t2.content AS name')
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjLocation' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
				->where("t1.type", 'da')
				->findAll()
				->getData();
				$this->set('from_location_arr', $from_location_arr);
				
				$to_location_arr = pjLocationModel::factory()
				->select('t1.*, t2.content AS name')
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjLocation' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
				->where("t1.type", 'pd')
				->where(sprintf("(t1.id IN(SELECT `TP`.to_location_id FROM `%s` AS `TP` WHERE `TP`.from_location_id=%u))", pjPriceModel::factory()->getTable(), $arr['from_location_id']))
				->findAll()
				->getData();
				$this->set('to_location_arr', $to_location_arr);
				
				$api_key = isset($this->option_arr['o_google_api_key']) && !empty($this->option_arr['o_google_api_key']) ? '&key=' . $this->option_arr['o_google_api_key'] : '';
				$this->appendJs('https://maps.googleapis.com/maps/api/js?libraries=places' . $api_key, null, true);
				$this->appendJs('chosen.jquery.min.js', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendJs('jquery-ui-sliderAccess.js', PJ_THIRD_PARTY_PATH . 'timepicker/');
				$this->appendJs('jquery-ui-timepicker-addon.js', PJ_THIRD_PARTY_PATH . 'timepicker/');
				$this->appendCss('jquery-ui-timepicker-addon.css', PJ_THIRD_PARTY_PATH . 'timepicker/');
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('additional-methods.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminBookings.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionResend()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			if (isset($_POST['reminder']))
			{
				$pjEmail = new pjEmail();
				if ($this->option_arr['o_send_email'] == 'smtp')
				{
					$pjEmail
						->setTransport('smtp')
						->setSmtpHost($this->option_arr['o_smtp_host'])
						->setSmtpPort($this->option_arr['o_smtp_port'])
						->setSmtpUser($this->option_arr['o_smtp_user'])
						->setSmtpPass($this->option_arr['o_smtp_pass'])
						->setSender($this->option_arr['o_smtp_user'])
					;
				}
				
				$pjEmail
					->setContentType('text/html')
					->setTo($_POST['to'])
					->setFrom($this->getAdminEmail())
					->setSubject($_POST['subject']);
				if ($pjEmail->send($_POST['message']))
				{
					$err = 'AB09';
				} else {
					$err = 'AB10';
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=$err");
			} else {
				
				$arr = pjBookingModel::factory()
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
					->find($_GET['id'])
					->getData();
					
				if (count($arr) === 0)
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=AB08");
				}
												 				
				$arr['data'] = pjAppController::getTokens($this->option_arr, $arr, PJ_SALT, $this->getLocaleId());
				
				$lang_message = pjMultiLangModel::factory()->select('t1.*')
											 ->where('t1.model','pjOption')
											 ->where('t1.locale', $this->getLocaleId())
											 ->where('t1.field', 'o_email_confirmation_message')
											 ->limit(0, 1)
											 ->findAll()->getData();
				$lang_subject = pjMultiLangModel::factory()->select('t1.*')
											 ->where('t1.model','pjOption')
											 ->where('t1.locale', $this->getLocaleId())
											 ->where('t1.field', 'o_email_confirmation_subject')
											 ->limit(0, 1)
											 ->findAll()->getData();
				
				$this->set('arr', $arr);
				if ($arr['booking_option'] == 'roundtrip') {
					$subject = str_replace(array('[Roundtrip]', '[/Roundtrip]'), array('', ''), $lang_subject[0]['content']);
					$message = str_replace(array('[Roundtrip]', '[/Roundtrip]'), array('', ''), $lang_message[0]['content']);
				} else {
					$subject = preg_replace('/\[Roundtrip\].*\[\/Roundtrip\]/s', '', $lang_subject[0]['content']);
					$message = preg_replace('/\[Roundtrip\].*\[\/Roundtrip\]/s', '', $lang_message[0]['content']);
				}
				$this->set('lang_subject', $subject);
				$this->set('lang_message', $message);
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
				$this->appendJs('pjAdminBookings.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	public function pjActionCancel()
	{
		$this->checkLogin();
	
		if ($this->isAdmin() || $this->isEditor())
		{
			if (isset($_POST['reminder']))
			{
				$pjEmail = new pjEmail();
				if ($this->option_arr['o_send_email'] == 'smtp')
				{
					$pjEmail
						->setTransport('smtp')
						->setSmtpHost($this->option_arr['o_smtp_host'])
						->setSmtpPort($this->option_arr['o_smtp_port'])
						->setSmtpUser($this->option_arr['o_smtp_user'])
						->setSmtpPass($this->option_arr['o_smtp_pass'])
						->setSender($this->option_arr['o_smtp_user'])
					;
				}
	
				$pjEmail
					->setContentType('text/html')
					->setTo($_POST['to'])
					->setFrom($this->getAdminEmail())
					->setSubject($_POST['subject']);
				if ($pjEmail->send($_POST['message']))
				{
					$err = 'AB13';
				} else {
					$err = 'AB14';
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=$err");
			} else {
	
				$arr = pjBookingModel::factory()
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
						t3.phone
					")
					->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjClient', "t3.id=t1.client_id", 'left outer')
					->find($_GET['id'])
					->getData();
					
				if (count($arr) === 0)
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=AB08");
				}
					
				$arr['data'] = pjAppController::getTokens($this->option_arr, $arr, PJ_SALT, $this->getLocaleId());
	
				$lang_message = pjMultiLangModel::factory()->select('t1.*')
					->where('t1.model','pjOption')
					->where('t1.locale', $this->getLocaleId())
					->where('t1.field', 'o_email_cancel_message')
					->limit(0, 1)
					->findAll()->getData();
				$lang_subject = pjMultiLangModel::factory()->select('t1.*')
					->where('t1.model','pjOption')
					->where('t1.locale', $this->getLocaleId())
					->where('t1.field', 'o_email_cancel_subject')
					->limit(0, 1)
					->findAll()->getData();
	
				$this->set('arr', $arr);
				
				if ($arr['booking_option'] == 'roundtrip') {
					$subject = str_replace(array('[Roundtrip]', '[/Roundtrip]'), array('', ''), $lang_subject[0]['content']);
					$message = str_replace(array('[Roundtrip]', '[/Roundtrip]'), array('', ''), $lang_message[0]['content']);
				} else {
					$subject = preg_replace('/\[Roundtrip\].*\[\/Roundtrip\]/s', '', $lang_subject[0]['content']);
					$message = preg_replace('/\[Roundtrip\].*\[\/Roundtrip\]/s', '', $lang_message[0]['content']);
				}
				
				$this->set('lang_subject', $subject);
				$this->set('lang_message', $message);
	
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
				$this->appendJs('pjAdminBookings.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	public function pjActionSendSms()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			if (isset($_POST['send_sms']) && isset($_POST['to']) && !empty($_POST['to']) && !empty($_POST['message']) && !empty($_POST['id']))
			{
				$params = array(
					'text' => $_POST['message'],
					'type' => 'unicode',						
					'key' => md5($this->option_arr['private_key'] . PJ_SALT)
				);
				
				$params['number'] = $_POST['to'];
				$result = $this->requestAction(array('controller' => 'pjSms', 'action' => 'pjActionSend', 'params' => $params), array('return'));
			
				if (isset($result) && (int) $result === 1)
				{
					$err = 'AB11';
				}else{
					$err = 'AB12';
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionUpdate&id=".$_POST['id']."&err=$err");
			} else {
				
				$arr = pjBookingModel::factory()
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
						t3.phone
					")
					->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjClient', "t3.id=t1.client_id", 'left outer')
					->find($_GET['id'])
					->getData();
					
				if (count($arr) === 0)
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=AB08");
				}
												 				
				$arr['data'] = pjAppController::getTokens($this->option_arr, $arr, PJ_SALT, $this->getLocaleId());
				
				$lang_message = pjMultiLangModel::factory()->select('t1.*')
											 ->where('t1.model','pjOption')
											 ->where('t1.locale', $this->getLocaleId())
											 ->where('t1.field', 'o_sms_confirmation_message')
											 ->limit(0, 1)
											 ->findAll()->getData();
					
				$this->set('arr', $arr);
				if ($arr['booking_option'] == 'roundtrip') {
					$message = str_replace(array('[Roundtrip]', '[/Roundtrip]'), array('', ''), $lang_message[0]['content']);
				} else {
					$message = preg_replace('/\[Roundtrip\].*\[\/Roundtrip\]/s', '', $lang_message[0]['content']);
				}
				$this->set('lang_message', $message);
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminBookings.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionGetExtras()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if(isset($_POST['fleet_id']) && (int)$_POST['fleet_id'] >0)
			{
				$pjFleetExtraModel = pjFleetExtraModel::factory()
					->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.extra_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjExtra', "t1.extra_id=t3.id", 'left')
					->select("t1.*, t2.content as name, t3.price, t3.per")
					->where('t1.fleet_id', $_POST['fleet_id'])
					->orderBy("name ASC");
				$avail_extra_arr = $pjFleetExtraModel->findAll()->getData();
				$this->set('avail_extra_arr', $avail_extra_arr);
			}
		}
	}
	
	public function pjActionCalPrice()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$extra_ids = isset($_POST['extra_id']) ? $_POST['extra_id'] : array();
			$booking_option = isset($_POST['booking_option']) ? $_POST['booking_option'] : 'oneway';
			$result = pjAppController::calPrice($_POST['fleet_id'], $_POST['from_location_id'], $_POST['to_location_id'], $_POST['passengers'], $extra_ids, $this->option_arr, $booking_option);	
			pjAppController::jsonResponse($result);
			exit;
		}
	}
	public function pjActionGetToLocations()
	{
	    $this->setAjax(true);
	    
	    if ($this->isXHR())
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
	public function pjActionGetFleets()
	{
	    $this->setAjax(true);
	    
	    if ($this->isXHR())
	    {
	        if(isset($_POST['from_location_id']) && (int) $_POST['from_location_id'] > 0 && isset($_POST['to_location_id']) && (int) $_POST['to_location_id'] > 0)
	        {
	            $fleet_arr = pjFleetModel::factory()
	            ->select('t1.*, t2.content AS fleet')
	            ->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjFleet' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'fleet'", 'left')
	            ->where("t1.status", 'T')
	            ->where(sprintf("(t1.id IN(SELECT `TP`.fleet_id FROM `%s` AS `TP` WHERE `TP`.from_location_id=%u AND `TP`.to_location_id=%u))", pjPriceModel::factory()->getTable(), $_POST['from_location_id'], $_POST['to_location_id']))
	            ->orderBy("fleet ASC")
	            ->findAll()
	            ->getData();
	            $this->set('fleet_arr', $fleet_arr);
	        }
	    }
	}
	
	public function pjActionCheckDateTime()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {
			if ($_POST['booking_option'] == 'roundtrip') {
				if(count(explode(" ", $_POST['booking_date'])) == 3)
				{
					list($date, $time, $period) = explode(" ", $_POST['booking_date']);
					$time = pjUtil::formatTime($time . ' ' . $period, $this->option_arr['o_time_format']);
				}else{
					list($date, $time) = explode(" ", $_POST['booking_date']);
					$time = pjUtil::formatTime($time, $this->option_arr['o_time_format']);
				}
				$booking_date = pjUtil::formatDate($date, $this->option_arr['o_date_format']) . ' ' . $time;
				
				if(count(explode(" ", $_POST['return_date'])) == 3)
				{
					list($_date, $_time, $_period) = explode(" ", $_POST['return_date']);
					$_time = pjUtil::formatTime($_time . ' ' . $_period, $this->option_arr['o_time_format']);
				}else{
					list($_date, $_time) = explode(" ", $_POST['return_date']);
					$_time = pjUtil::formatTime($_time, $this->option_arr['o_time_format']);
				}
				$return_date = pjUtil::formatDate($_date, $this->option_arr['o_date_format']) . ' ' . $_time;
				if (strtotime($return_date) > strtotime($booking_date)) {
					pjAppController::jsonResponse(array('status' => 'OK'));
				} else {
					pjAppController::jsonResponse(array('status' => 'ERR'));
				}
			} else {
				pjAppController::jsonResponse(array('status' => 'OK'));
			}
		}
	}
}
?>