<?php

namespace Traits;

trait QueueTrait{

	public function sendProduct($event, $params){
        return $this->sendToQueue('product', $event, ['id' => $params['id_product']]);
    }
    public function hookActionProductAdd($params) {
        $this->sendProduct('create', $params);
    }

    public function hookActionProductDelete($params) {
        $this->sendProduct('delete',$params);
    }

    public function hookActionProductUpdate($params) {
        $this->sendProduct('update',$params);
    }


    /*public function sendCombination($event, $params){
        return $this->sendToQueue('combination', $event, ['id' => $params['id_product_attribute']]);
    }

    public function hookActionAttributeCombinationSave($params) {
        $this->sendCombination('delete',$params);
    }

    public function hookActionAttributeCombinationDelete($params) {
        $this->sendCombination('update',$params);
    }*/


    

    public function sendToQueue($resource, $event, $data){

        $payload = [
            'event' => $event,
            'resource' => $resource,
            'data' => $data
        ];


        if($this->isDuplicate($payload))
            return;


        return (\Db::getInstance()->insert('webhook_queue', 
            [
                'status' => self::STATUS_QUEUE,
                'data' => json_encode($payload)
            ]
        ));

        
    }

    public function isDuplicate($data){
        $result = \Db::getInstance()->executeS(
            "SELECT * 
            FROM `"._DB_PREFIX_."webhook_queue` 
            WHERE status = '".self::STATUS_QUEUE."'
            AND data = '".json_encode($data)."'
            AND reserved_at IS NULL
            ");

        return count($result) > 0;

       
    }


}