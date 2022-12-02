<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminFleets extends pjAdmin
{
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			$post_max_size = pjUtil::getPostMaxSize();
			if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SERVER['CONTENT_LENGTH']) && (int) $_SERVER['CONTENT_LENGTH'] > $post_max_size)
			{
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminFleets&action=pjActionIndex&err=AF05");
			}
			if (isset($_POST['fleet_create']))
			{
				$data = array();
				
				$pjFleetModel = pjFleetModel::factory();
				$_POST['passengers'] = empty($_POST['passengers']) ? 0 : $_POST['passengers'];
				$_POST['luggage'] = empty($_POST['luggage']) ? 0 : $_POST['luggage'];
				$id = $pjFleetModel->setAttributes(array_merge($_POST, $data))->insert()->getInsertId();
				if ($id !== false && (int) $id > 0)
				{
					$pjFleetExtraModel = pjFleetExtraModel::factory();
					if (isset($_POST['extra_id']) && is_array($_POST['extra_id']) && count($_POST['extra_id']) > 0)
					{
						$pjFleetExtraModel->begin();
						foreach ($_POST['extra_id'] as $extra_id)
						{
							$pjFleetExtraModel
							->reset()
							->set('fleet_id', $id)
							->set('extra_id', $extra_id)
							->insert();
						}
						$pjFleetExtraModel->commit();
					}
					
					if (isset($_POST['i18n']))
					{
					    pjMultiLangModel::factory()->saveMultiLang($_POST['i18n'], $id, 'pjFleet', 'data');
					}
					
					if(isset($_POST['da_location_id']) && count($_POST['da_location_id']) > 0)
					{
					    $pjPriceModel = pjPriceModel::factory();
					    foreach($_POST['da_location_id'] as $index => $from_location_id)
					    {
					        if(isset($_POST['price'][$index]) && count($_POST['price'][$index]) > 0)
					        {
					            foreach($_POST['price'][$index] as $subindex => $price)
					            {
					                $to_location_id = $_POST['pd_location_id'][$index][$subindex];
					                if((float) $price > 0)
					                {
					                    $p_data = array();
					                    $p_data['fleet_id'] = $id;
					                    $p_data['from_location_id'] = $from_location_id;
					                    $p_data['to_location_id'] = $to_location_id;
					                    $p_data['price'] = $price;
					                    $p_data['price_roundtrip'] = $_POST['price_roundtrip'][$index][$subindex];
					                    $pjPriceModel->reset()->setAttributes($p_data)->insert();
					                }
					            }
					        }
					    }
					}
					
					if (isset($_FILES['image']))
					{
						if($_FILES['image']['error'] == 0)
						{
							$image_size = getimagesize($_FILES['image']['tmp_name']);
							if(!empty($image_size))
							{
								$Image = new pjImage();
								if ($Image->getErrorCode() !== 200)
								{
									$Image->setAllowedTypes(array('image/png', 'image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg'));
									if ($Image->load($_FILES['image']))
									{
										$resp = $Image->isConvertPossible();
										if ($resp['status'] === true)
										{
											$hash = md5(uniqid(rand(), true));
											$source_path = PJ_UPLOAD_PATH . 'fleets/source/' . $id . '_' . $hash . '.' . $Image->getExtension();
											$thumb_path = PJ_UPLOAD_PATH . 'fleets/thumb/' . $id . '_' . $hash . '.' . $Image->getExtension();
											if ($Image->save($source_path))
											{
												$Image->loadImage($source_path);
												$Image->resizeSmart(250, 130);
												$Image->saveImage($thumb_path);
												
												$data['source_path'] = $source_path;
												$data['thumb_path'] = $thumb_path;
												$data['image_name'] = $_FILES['image']['name'];
												$pjFleetModel->reset()->where('id', $id)->limit(1)->modifyAll($data);
											}
										}
									}
								}
							}else{
								pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminFleets&action=pjActionUpdate&id=$id&err=AF11");
							}
						}else if($_FILES['image']['error'] != 4){
							pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminFleets&action=pjActionUpdate&id=$id&err=AF09");
						}
					}
					
					$err = 'AF03';
				} else {
					$err = 'AF04';
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminFleets&action=pjActionIndex&err=$err");
			} else {
				
				$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
					->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
					->where('t2.file IS NOT NULL')
					->orderBy('t1.sort ASC')->findAll()->getData();
						
				$lp_arr = array();
				foreach ($locale_arr as $item)
				{
					$lp_arr[$item['id']."_"] = $item['file'];
				}
				
				$this->set('lp_arr', $locale_arr);
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
				
				$this->set('extra_arr', pjExtraModel::factory()
					->select('t1.*, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->orderBy('name ASC')
					->findAll()
					->getData());
				
				$this->set('da_arr', pjLocationModel::factory()
				    ->select('t1.*, t2.content AS name')
				    ->join('pjMultiLang', "t2.model='pjLocation' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				    ->where('`type`', 'da')
				    ->orderBy('name ASC')
				    ->findAll()
				    ->getData());
				
				$this->set('pd_arr', pjLocationModel::factory()
				    ->select('t1.*, t2.content AS name')
				    ->join('pjMultiLang', "t2.model='pjLocation' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				    ->where('`type`', 'pd')
				    ->orderBy('name ASC')
				    ->findAll()
				    ->getData());
				
				$this->appendJs('jquery.multiselect.min.js', PJ_THIRD_PARTY_PATH . 'multiselect/');
				$this->appendCss('jquery.multiselect.css', PJ_THIRD_PARTY_PATH . 'multiselect/');
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('additional-methods.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('pjAdminFleets.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionDeleteFleet()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			
			$pjFleetModel = pjFleetModel::factory();
			$arr = $pjFleetModel->find($_GET['id'])->getData();
			
			if (pjFleetModel::factory()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				if(file_exists(PJ_INSTALL_PATH . $arr['source_path']))
				{
					@unlink(PJ_INSTALL_PATH . $arr['source_path']);
				}
				if(file_exists(PJ_INSTALL_PATH . $arr['thumb_path']))
				{
					@unlink(PJ_INSTALL_PATH . $arr['thumb_path']);
				}

				pjMultiLangModel::factory()->where('model', 'pjFleet')->where('foreign_id', $_GET['id'])->eraseAll();
				pjBookingModel::factory()->where('fleet_id', $_GET['id'])->limit(1)->modifyAll(array('fleet_id' => ':NULL'));
				pjPriceModel::factory()->reset()->where('fleet_id', $_GET['id'])->eraseAll();
				pjFleetExtraModel::factory()->where('fleet_id', $_GET['id'])->eraseAll();
				
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteFleetBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				$pjFleetModel = pjFleetModel::factory();
				$arr = $pjFleetModel
					->reset()
					->whereIn('id', $_POST['record'])
					->findAll()
					->getData();
			
				foreach($arr as $v)
				{
					if(file_exists(PJ_INSTALL_PATH . $v['source_path']))
					{
						@unlink(PJ_INSTALL_PATH . $v['source_path']);
					}
					if(file_exists(PJ_INSTALL_PATH . $v['thumb_path']))
					{
						@unlink(PJ_INSTALL_PATH . $v['thumb_path']);
					}
				}
				
				$pjFleetModel->reset()->whereIn('id', $_POST['record'])->eraseAll();
				pjMultiLangModel::factory()->where('model', 'pjFleet')->whereIn('foreign_id', $_POST['record'])->eraseAll();
				pjBookingModel::factory()->whereIn('fleet_id', $_POST['record'])->modifyAll(array('fleet_id' => ':NULL'));
				pjPriceModel::factory()->reset()->whereIn('fleet_id', $_POST['record'])->eraseAll();
				pjFleetExtraModel::factory()->whereIn('fleet_id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionExportFleet()
	{
		$this->checkLogin();
		
		if (isset($_POST['record']) && is_array($_POST['record']))
		{
			$arr = pjFleetModel::factory()
						->select("t1.*, t2.content as fleet, t3.content as description")
						->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->join('pjMultiLang', "t3.model='pjFleet' AND t3.foreign_id=t1.id AND t3.field='description' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
						->whereIn('t1.id', $_POST['record'])
						->findAll()->getData();
			$csv = new pjCSV();
			$csv
				->setHeader(true)
				->setName("Fleets-".time().".csv")
				->process($arr)
				->download();
		}
		exit;
	}
	
	public function pjActionGetFleet()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjFleetModel = pjFleetModel::factory();
			
			$pjFleetModel->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						 ->join('pjMultiLang', "t3.model='pjFleet' AND t3.foreign_id=t1.id AND t3.field='description' AND t3.locale='".$this->getLocaleId()."'", 'left outer');
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjFleetModel->where("(t2.content LIKE '%$q%' OR t3.content LIKE '%$q%')");
			}

			if (isset($_GET['status']) && !empty($_GET['status']) && in_array($_GET['status'], array('T', 'F')))
			{
				$pjFleetModel->where('t1.status', $_GET['status']);
			}
				
			$column = 'fleet';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjFleetModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = array();
			
			$data = $pjFleetModel
						->select('t1.id, t1.thumb_path, t2.content as fleet, t1.passengers, t1.status, t1.luggage')
						->orderBy("$column $direction")
						->limit($rowCount, $offset)
						->findAll()->getData();
				
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminFleets.js');
			$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSaveFleet()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjFleetModel = pjFleetModel::factory();
			
			if (!in_array($_POST['column'], $pjFleetModel->i18n))
			{
				$value = $_POST['value'];
				
				$pjFleetModel->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $value));
			} else {
				pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), $_GET['id'], 'pjFleet', 'data');
			}
		}
		exit;
	}
	
	public function pjActionStatusFleet()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjFleetModel::factory()->whereIn('id', $_POST['record'])->modifyAll(array(
					'status' => ":IF(`status`='F','T','F')"
				));
			}
		}
		exit;
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			$post_max_size = pjUtil::getPostMaxSize();
			if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SERVER['CONTENT_LENGTH']) && (int) $_SERVER['CONTENT_LENGTH'] > $post_max_size)
			{
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminFleets&action=pjActionIndex&err=AF06");
			}	
			if (isset($_POST['fleet_update']))
			{
				$pjFleetModel = pjFleetModel::factory();
				
				$arr = $pjFleetModel->find($_POST['id'])->getData();
				if (empty($arr))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminFleets&action=pjActionIndex&err=AF08");
				}
				
				$data = array();
				if (isset($_FILES['image']))
				{
					if($_FILES['image']['error'] == 0)
					{
						$image_size = getimagesize($_FILES['image']['tmp_name']);
						if(!empty($image_size))
						{
							if(!empty($arr['source_path']))
							{
								$source_path = PJ_INSTALL_PATH . $arr['source_path'];
								$thumb_path = PJ_INSTALL_PATH . $arr['thumb_path'];
								@unlink($source_path);
								@unlink($thumb_path);
							}
								
							$Image = new pjImage();
							if ($Image->getErrorCode() !== 200)
							{
								$Image->setAllowedTypes(array('image/png', 'image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg'));
								if ($Image->load($_FILES['image']))
								{
									$resp = $Image->isConvertPossible();
									if ($resp['status'] === true)
									{
										$hash = md5(uniqid(rand(), true));
										$source_path = PJ_UPLOAD_PATH . 'fleets/source/' . $_POST['id'] . '_' . $hash . '.' . $Image->getExtension();
										$thumb_path = PJ_UPLOAD_PATH . 'fleets/thumb/' . $_POST['id'] . '_' . $hash . '.' . $Image->getExtension();
										if ($Image->save($source_path))
										{
											$Image->loadImage($source_path);
											$Image->resizeSmart(250, 130);
											$Image->saveImage($thumb_path);
											
											$data['source_path'] = $source_path;
											$data['thumb_path'] = $thumb_path;
											$data['image_name'] = $_FILES['image']['name'];
										}
									}
								}
							}
						}else{
							pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminFleets&action=pjActionUpdate&id=".$_POST['id']."&err=AF11");
						}
					}else if($_FILES['image']['error'] != 4){
						pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminFleets&action=pjActionUpdate&id=".$_POST['id']."&err=AF10");
					}	
				}
				$_POST['passengers'] = empty($_POST['passengers']) ? 0 : $_POST['passengers'];
				$_POST['luggage'] = empty($_POST['luggage']) ? 0 : $_POST['luggage'];
				$pjFleetModel->reset()->where('id', $_POST['id'])->limit(1)->modifyAll(array_merge($_POST, $data));
				
				$pjFleetExtraModel = pjFleetExtraModel::factory();
				$pjFleetExtraModel->where('fleet_id', $_POST['id'])->eraseAll();
				if (isset($_POST['extra_id']) && is_array($_POST['extra_id']) && count($_POST['extra_id']) > 0)
				{
					$pjFleetExtraModel->begin();
					foreach ($_POST['extra_id'] as $extra_id)
					{
						$pjFleetExtraModel
						->reset()
						->set('fleet_id', $_POST['id'])
						->set('extra_id', $extra_id)
						->insert();
					}
					$pjFleetExtraModel->commit();
				}
				
				$pjMultiLangModel = pjMultiLangModel::factory();
				
				if (isset($_POST['i18n']))
				{
				    pjMultiLangModel::factory()->updateMultiLang($_POST['i18n'], $_POST['id'], 'pjFleet', 'data');
				}
				
				$pjPriceModel = pjPriceModel::factory();
				$pjPriceModel->where('fleet_id', $_POST['id'])->eraseAll();
				if(isset($_POST['da_location_id']) && count($_POST['da_location_id']) > 0)
				{
				    foreach($_POST['da_location_id'] as $index => $from_location_id)
				    {
				        if(isset($_POST['price'][$index]) && count($_POST['price'][$index]) > 0)
				        {
				            foreach($_POST['price'][$index] as $subindex => $price)
				            {
				                $to_location_id = $_POST['pd_location_id'][$index][$subindex];
				                if((float) $price > 0)
				                {
				                    $p_data = array();
				                    $p_data['fleet_id'] = $_POST['id'];
				                    $p_data['from_location_id'] = $from_location_id;
				                    $p_data['to_location_id'] = $to_location_id;
				                    $p_data['price'] = $price;
				                    $p_data['price_roundtrip'] = $_POST['price_roundtrip'][$index][$subindex];
				                    $pjPriceModel->reset()->setAttributes($p_data)->insert();
				                }
				            }
				        }
				    }
				}
				
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminFleets&action=pjActionIndex&err=AF01");
				
			} else {
				$pjMultiLangModel = pjMultiLangModel::factory();
				
				$arr = pjFleetModel::factory()->find($_GET['id'])->getData();
				if (count($arr) === 0)
				{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminFleets&action=pjActionIndex&err=AF08");
				}
				$arr['i18n'] = $pjMultiLangModel->getMultiLang($arr['id'], 'pjFleet');
				
				$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
					->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
					->where('t2.file IS NOT NULL')
					->orderBy('t1.sort ASC')->findAll()->getData();
						
				$lp_arr = array();
				foreach ($locale_arr as $item)
				{
					$lp_arr[$item['id']."_"] = $item['file'];
				}
				
				$this->set('arr', $arr);
				$this->set('lp_arr', $locale_arr);
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
				
				$price_data = array();
				$price_arr = pjPriceModel::factory()->where('fleet_id', $_GET['id'])->findAll()->getData();
				foreach($price_arr as $k => $v)
				{
				    $price_data[$v['from_location_id']][] = $v;
				}
				$this->set('price_arr', $price_data);
				
				$this->set('extra_arr', pjExtraModel::factory()
					->select('t1.*, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->orderBy('name ASC')
					->findAll()
					->getData());
				$extra_id_arr = pjFleetExtraModel::factory()->where('fleet_id', $_GET['id'])->findAll()->getDataPair(null, 'extra_id');
				$this->set('extra_id_arr', $extra_id_arr);
				
				$this->set('da_arr', pjLocationModel::factory()
				    ->select('t1.*, t2.content AS name')
				    ->join('pjMultiLang', "t2.model='pjLocation' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				    ->where('`type`', 'da')
				    ->orderBy('name ASC')
				    ->findAll()
				    ->getData());
				
				$this->set('pd_arr', pjLocationModel::factory()
				    ->select('t1.*, t2.content AS name')
				    ->join('pjMultiLang', "t2.model='pjLocation' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				    ->where('`type`', 'pd')
				    ->orderBy('name ASC')
				    ->findAll()
				    ->getData());
				
				$this->appendJs('jquery.multiselect.min.js', PJ_THIRD_PARTY_PATH . 'multiselect/');
				$this->appendCss('jquery.multiselect.css', PJ_THIRD_PARTY_PATH . 'multiselect/');
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('additional-methods.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('pjAdminFleets.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionDeleteImage()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			
			$pjFleetModel = pjFleetModel::factory();
			$arr = $pjFleetModel->find($_GET['id'])->getData(); 
			
			if(!empty($arr))
			{
				if(!empty($arr['source_path']))
				{
					$source_path = PJ_INSTALL_PATH . $arr['source_path'];
					$thumb_path = PJ_INSTALL_PATH . $arr['thumb_path'];
					@unlink($source_path);
					@unlink($thumb_path);
				}
				
				$data = array();
				$data['source_path'] = ':NULL';
				$data['thumb_path'] = ':NULL';
				$data['image_name'] = ':NULL';
				$pjFleetModel->reset()->where(array('id' => $_GET['id']))->limit(1)->modifyAll($data);
				
				$response['code'] = 200;
			}else{
				$response['code'] = 100;
			}
			
			pjAppController::jsonResponse($response);
		}
	}
}
?>