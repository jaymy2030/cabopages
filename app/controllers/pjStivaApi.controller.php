<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjStivaApi extends pjAppController
{
    public function pjActionLogin()
    {
        $this->setLayout('pjActionEmpty');
        
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
            ->where('t1.email', pjObject::escapeString($_POST['login_email']))
            ->limit(1)
            ->findAll()
            ->getData();
            if (count($client) != 1)
            {
                self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => __('front_email_does_not_exist', true)));
            }else{
                self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => __('front_incorrect_password', true)));
            }
        }else{
            if ($client[0]['status'] != 'T')
            {
                self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => __('front_your_account_disabled', true)));
            }else{
                $last_login = date("Y-m-d H:i:s");
                
                $client = $pjClientModel->reset()->find($client[0]['id'])->getData();
                
                $data = array();
                $data['last_login'] = $last_login;
                $pjClientModel->reset()->setAttributes(array('id' => $client[0]['id']))->modify($data);
                self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => '', 'client' => $client));
            }
        }
        exit;
    }
    
    public function pjActionRegister()
    {
        $this->setLayout('pjActionEmpty');
        
        $pjClientModel = pjClientModel::factory();
        
        $client = $pjClientModel
        ->reset()
        ->where('t1.email', pjObject::escapeString($_POST['c_email']))
        ->limit(1)
        ->findAll()
        ->getData();
        
        if (count($client) != 1)
        {
            $client_data = array();
            $client_data['email'] = isset($_POST['c_email']) ? $_POST['c_email'] : ':NULL';
            $client_data['password'] = isset($_POST['c_password']) ? $_POST['c_password'] : pjUtil::getRandomPassword(6);
            $client_data['email'] = isset($_POST['c_email']) ? $_POST['c_email'] : ':NULL';
            $client_data['fname'] = isset($_POST['c_fname']) ? $_POST['c_fname'] : ':NULL';
            $client_data['lname'] = isset($_POST['c_lname']) ? $_POST['c_lname'] : ':NULL';
            $client_data['status'] = 'T';
            $client_data['created'] = date('Y-m-d H:i:s');
            
            $client_id = $pjClientModel->reset()->setAttributes($client_data)->insert()->getInsertId();
            if ($client_id !== false && (int) $client_id > 0)
            {
                $client = $pjClientModel->reset()->find($client_id)->getData();
                
                pjAppController::pjActionAccountSend($this->option_arr, $client_id, PJ_SALT, $this->getLocaleId());
                
                self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Client account has been created.', 'client' => $client));
            }else{
                self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Client account was not be created.'));
            }
        }else{
            self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Client with this email already exists.'));
        }
    }
}
?>