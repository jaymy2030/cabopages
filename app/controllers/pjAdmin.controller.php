<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdmin extends pjAppController
{
	public $defaultUser = 'admin_user';
	
	public $requireLogin = true;
		
	public function __construct($requireLogin=null)
	{
		$this->setLayout('pjActionAdmin');
		
		if (!is_null($requireLogin) && is_bool($requireLogin))
		{
			$this->requireLogin = $requireLogin;
		}
		
		if ($this->requireLogin)
		{
			if (!$this->isLoged() && !in_array(@$_GET['action'], array('pjActionLogin', 'pjActionForgot')))
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin");
			}
		}
	}
	
	public function afterFilter()
	{
		parent::afterFilter();
		if ($this->isLoged() && !in_array(@$_GET['action'], array('pjActionLogin')))
		{
			$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
		}
	}
	
	public function beforeRender()
	{
		
	}
		
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			$pjBookingModel = pjBookingModel::factory();
			
			$enquiries_received_today = $pjBookingModel->where("(DATE_FORMAT(t1.created, '%Y-%m-%d')='".date('Y-m-d')."')")->findCount()->getData();
			$reservations_today = $pjBookingModel->reset()->where("(DATE_FORMAT(t1.booking_date, '%Y-%m-%d')='".date('Y-m-d')."')")->where("t1.status <>", 'cancelled')->findCount()->getData();
			$total_reservations = $pjBookingModel->reset()->findCount()->getData();
			
			$latest_enquiries = $pjBookingModel
				->reset()
				->select("t1.*, t2.content as fleet, t3.fname, t3.lname, t3.email, t3.phone")
				->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjClient', "t3.id=t1.client_id", 'left outer')
				->orderBy("t1.created DESC")
				->limit(4)
				->findAll()->getData();
			
			$reservations_today_arr = $pjBookingModel
				->reset()
				->select("t1.*, t2.content as fleet, t3.fname, t3.lname, t3.email, t3.phone")
				->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjClient', "t3.id=t1.client_id", 'left outer')
				->where("(DATE_FORMAT(t1.booking_date, '%Y-%m-%d')='".date('Y-m-d')."')")
				->where("t1.status <>", 'cancelled')
				->orderBy("t1.booking_date ASC")
				->limit(4)
				->findAll()->getData();
			
			$this->set('enquiries_received_today', $enquiries_received_today);
			$this->set('reservations_today', $reservations_today);
			$this->set('total_reservations', $total_reservations);
			
			$this->set('latest_enquiries', $latest_enquiries);
			$this->set('reservations_today_arr', $reservations_today_arr);
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionForgot()
	{
		$this->setLayout('pjActionAdminLogin');
		
		if (isset($_POST['forgot_user']))
		{
			if (!isset($_POST['forgot_email']) || !pjValidation::pjActionNotEmpty($_POST['forgot_email']) || !pjValidation::pjActionEmail($_POST['forgot_email']))
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionForgot&err=AA10");
			}
			$pjUserModel = pjUserModel::factory();
			$user = $pjUserModel
				->where('t1.email', $_POST['forgot_email'])
				->limit(1)
				->findAll()
				->getData();
				
			if (count($user) != 1)
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionForgot&err=AA10");
			} else {
				$user = $user[0];
				
				$Email = new pjEmail();
				$Email
					->setTo($user['email'])
					->setFrom($user['email'])
					->setSubject(__('emailForgotSubject', true));
				
				if ($this->option_arr['o_send_email'] == 'smtp')
				{
					$Email
						->setTransport('smtp')
						->setSmtpHost($this->option_arr['o_smtp_host'])
						->setSmtpPort($this->option_arr['o_smtp_port'])
						->setSmtpUser($this->option_arr['o_smtp_user'])
						->setSmtpPass($this->option_arr['o_smtp_pass'])
					;
				}
				
				$body = str_replace(
					array('{Name}', '{Password}'),
					array($user['name'], $user['password']),
					__('emailForgotBody', true)
				);

				if ($Email->send($body))
				{
					$err = "AA11";
				} else {
					$err = "AA12";
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionForgot&err=$err");
			}
		} else {
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('pjAdmin.js');
		}
	}
	
	public function pjActionMessages()
	{
		$this->setAjax(true);
		header("Content-Type: text/javascript; charset=utf-8");
	}
	
	public function pjActionLogin()
	{
		$this->setLayout('pjActionAdminLogin');
		
		if (isset($_POST['login_user']))
		{
			if (!isset($_POST['login_email']) || !isset($_POST['login_password']) ||
				!pjValidation::pjActionNotEmpty($_POST['login_email']) ||
				!pjValidation::pjActionNotEmpty($_POST['login_password']) ||
				!pjValidation::pjActionEmail($_POST['login_email']))
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin&err=4");
			}
			$pjUserModel = pjUserModel::factory();

			$user = $pjUserModel
				->where('t1.email', $_POST['login_email'])
				->where(sprintf("t1.password = AES_ENCRYPT('%s', '%s')", pjObject::escapeString($_POST['login_password']), PJ_SALT))
				->limit(1)
				->findAll()
				->getData();

			if (count($user) != 1)
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin&err=1");
			} else {
				$user = $user[0];
				unset($user['password']);
															
				if (!in_array($user['role_id'], array(1,2,3)))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin&err=2");
				}
				
				if ($user['role_id'] == 3 && $user['is_active'] == 'F')
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin&err=2");
				}
				
				if ($user['status'] != 'T')
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin&err=3");
				}
				
				# Login succeed
				$last_login = date("Y-m-d H:i:s");
				if($user['last_login'] == $user['created'])
				{
					$user['last_login'] = date("Y-m-d H:i:s");
				}
    			$_SESSION[$this->defaultUser] = $user;
    			
    			$data = array();
    			$data['last_login'] = $last_login;
    			$pjUserModel->reset()->setAttributes(array('id' => $user['id']))->modify($data);

    			if ($this->isAdmin() || $this->isEditor())
    			{
	    			pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionIndex");
    			}
			}
		} else {
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('pjAdmin.js');
		}
	}
	
	public function pjActionLogout()
	{
		if ($this->isLoged())
        {
        	unset($_SESSION[$this->defaultUser]);
        }
       	pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin");
	}
	
	public function pjActionProfile()
	{
		$this->checkLogin();
		
		if (!$this->isAdmin())
		{
			if (isset($_POST['profile_update']))
			{
				$pjUserModel = pjUserModel::factory();
				$arr = $pjUserModel->find($this->getUserId())->getData();
				$data = array();
				$data['role_id'] = $arr['role_id'];
				$data['status'] = $arr['status'];
				$post = array_merge($_POST, $data);
				if (!$pjUserModel->validates($post))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionProfile&err=AA14");
				}
				$pjUserModel->set('id', $this->getUserId())->modify($post);
				
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionProfile&err=AA13");
			} else {
				$this->set('arr', pjUserModel::factory()->find($this->getUserId())->getData());
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdmin.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
}
?>