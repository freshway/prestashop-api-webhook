<?php

require _PS_MODULE_DIR_.'apiwebhook/vendor/autoload.php';

use Carbon\Carbon;

class ApiWebHookCronjobModuleFrontController extends ModuleFrontController
{
	public function initContent()
	{
	  	if( Tools::getValue('token') !==  Configuration::get('WEBHOOK_TOKEN'))
	  		exit('Token error');

	  	$queue = Db::getInstance()->executeS(
            "SELECT * 
            FROM `"._DB_PREFIX_."webhook_queue` 
            WHERE status = '".ApiWebHook::STATUS_QUEUE."'
            ");
	  	
	  	foreach ($queue as $q) {
	  		$this->proccess((object)$q);
	  	}

		exit('OK');
	  	
	}



	public function proccess($queue){
		if($this->isAvailable($queue))
			$this->sendToEndPoint($queue);
	}

	public function isAvailable($queue){
		return empty($queue->reserved_at) || Carbon::now() >= Carbon::parse($queue->reserved_at);
	}

	public function sendToEndPoint($queue){
		$resp = $this->execute($queue->data);
		$status = $resp->httpCode == 200 ? ApiWebHook::STATUS_SENDED : ApiWebHook::STATUS_QUEUE;
		$this->updateStatus($queue, $status);
	}


	public function updateStatus($queue, $status){


		if($status == ApiWebHook::STATUS_SENDED){
			return Db::getInstance()->delete('webhook_queue', 'id = '.(int)$queue->id);
		}

		$data = [
			'status' => $status,
		];

		if($status == ApiWebHook::STATUS_QUEUE){

			$newAttempt = (int)$queue->attempt+1;
			if(  $newAttempt <=  (int)Configuration::get('WEBHOOK_ATTEMPTS')){
				$data['reserved_at'] = Carbon::now()->addMinutes((int)Configuration::get('WEBHOOK_INTERVAL'));
				$data['attempt'] = $newAttempt;
			}else{
				return $this->updateStatus($queue, ApiWebHook::STATUS_FAILED);
			}

		}


		return Db::getInstance()->update('webhook_queue', $data, 'id = '.(int)$queue->id);
	}



	public function execute($data){

		$return =[];

		$url = Configuration::get('WEBHOOK_URL');
		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $return["body"] = json_decode(curl_exec($ch));
        $return["httpCode"] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);


        return (object)$return;
	}
}