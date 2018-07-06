<?php

defined ('ABSPATH') or die('Permission denied');

class MapKitJsModel {

    private $JWT = "yourMapkitjsJWToken";
    private $rawData = array();
    private $annotationData = array();


    public function __construct($data = array()){
        $this->rawData = $data;

    }

    public function setData($data){
        //@todo validate any data
        $this->rawData = $data;
    }

    public function getAnnotationData($format = 'array'){
        foreach($this->rawData as $pledgeData ){
            $this->generateAgregatedStats($pledgeData);

        }
        $this->prepareDataForJs();
        if($format == 'array'){
            return $this->annotationData;

        }elseif($format == 'json'){
            return json_encode($this->annotationData);

        }
        return $this->annotationData;//@todo handle unknown formats errors differently
    }

    private function prepareDataForJs(){
        $this->annotationData['jwt'] = $this->JWT;

    }

    private function generateAgregatedStats($pledgeData){
        //@todo refactor for readability, put some brains here please :)

        if(!isset($this->annotationData['data']['totals']['total_pledges'])){
            $this->annotationData['data']['totals']['total_pledges'] = 1;
        }else{
            $this->annotationData['data']['totals']['total_pledges']++;
        }

        if(!isset($this->annotationData['data']['totals']['hours'])){
            $this->annotationData['data']['totals']['hours'] = $pledgeData->hours;
        }else{
            $this->annotationData['data']['totals']['hours'] += $pledgeData->hours;
        }

        if(!isset($this->annotationData['data']['totals']['money'])){
            $this->annotationData['data']['totals']['money'] = $pledgeData->money;
        }else{
            $this->annotationData['data']['totals']['money'] += $pledgeData->money;
        }

        if(!isset($this->annotationData['data']['byCountry'][$pledgeData->country]['total_pledges'])){
            $this->annotationData['data']['byCountry'][$pledgeData->country]['total_pledges'] = 1;
            $this->annotationData['data']['byCountry'][$pledgeData->country]['country'] = $pledgeData->country;
        }else{
            $this->annotationData['data']['byCountry'][$pledgeData->country]['total_pledges']++;
        }

        if(!isset($this->annotationData['data']['byCountry'][$pledgeData->country]['hours'])){
            $this->annotationData['data']['byCountry'][$pledgeData->country]['hours'] = $pledgeData->hours;
        }else{
            $this->annotationData['data']['byCountry'][$pledgeData->country]['hours'] += $pledgeData->hours;
        }

        if(!isset($this->annotationData['data']['byCountry'][$pledgeData->country]['money'])){
            $this->annotationData['data']['byCountry'][$pledgeData->country]['money'] = $pledgeData->money;
        }else{
            $this->annotationData['data']['byCountry'][$pledgeData->country]['money'] += $pledgeData->money;
        }

        if(!isset($this->annotationData['data']['byZipCode'][$pledgeData->zip_code]['total_pledges'])){
            $this->annotationData['data']['byZipCode'][$pledgeData->zip_code]['total_pledges'] = 1;
        }else{
            $this->annotationData['data']['byZipCode'][$pledgeData->zip_code]['total_pledges']++;
        }

        if(!isset($this->annotationData['data']['byZipCode'][$pledgeData->zip_code]['country'])) {
            $this->annotationData['data']['byZipCode'][$pledgeData->zip_code]['country'] = $pledgeData->country;
        }

        if(!isset($this->annotationData['data']['byZipCode'][$pledgeData->country]['hours'])){
            $this->annotationData['data']['byZipCode'][$pledgeData->zip_code]['hours'] = $pledgeData->hours;
        }else{
            $this->annotationData['data']['byZipCode'][$pledgeData->zip_code]['hours'] += $pledgeData->hours;
        }

        if(!isset($this->annotationData['data']['byZipCode'][$pledgeData->country]['money'])){
            $this->annotationData['data']['byZipCode'][$pledgeData->zip_code]['money'] = $pledgeData->money;
        }else{
            $this->annotationData['data']['byZipCode'][$pledgeData->zip_code]['money'] += $pledgeData->money;
        }

        return true;

    }

}
