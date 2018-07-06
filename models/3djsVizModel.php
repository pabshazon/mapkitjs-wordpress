<?php

class ThreeDJsModel{

    private $rawData = array();
    private $visualizationData = array();

    public function __construct($data = array()){
        $this->rawData = $data;
    }

    public function getVisualizationData(){
        foreach($this->rawData as $pledgeData ){
            $this->generateAgregatedStats($pledgeData);
        }

        return $this->visualizationData;

    }

    private function generateAgregatedStats($pledgeData){
        //@todo refactor for readability.
        if(!isset($this->visualizationData['countries'][$pledgeData->country_code])){
            $this->visualizationData['countries'][$pledgeData->country_code]['total_pledges'] = 1;
            $this->visualizationData['countries'][$pledgeData->country_code]['country_name'] = $pledgeData->country;

        }else{
            $this->visualizationData['countries'][$pledgeData->country_code]['total_pledges']++;

        }

        if(!isset($this->visualizationData['countries'][$pledgeData->country_code]['hours'])){
            $this->visualizationData['countries'][$pledgeData->country_code]['hours'] = $pledgeData->hours;

        }else{
            $this->visualizationData['countries'][$pledgeData->country_code]['hours'] += $pledgeData->hours;

        }

        if(!isset($this->visualizationData['countries'][$pledgeData->country_code]['money'])){
            $this->visualizationData['countries'][$pledgeData->country_code]['money'] = $pledgeData->money;

        }else{
            $this->visualizationData['countries'][$pledgeData->country_code]['money'] += $pledgeData->money;

        }

    }

}
