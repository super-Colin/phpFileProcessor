<?php

class TableMaker{
    private $workingData;

    public function __construct($inputData){
        $this->workingData = $inputData;
    }

    public function generateTable(){
        return $this->workingData;
    }
    protected function calcProfitMargin($row){
        // get header columns
        // return 
    }
    protected function calcProfit($row){
        // get header columns
        // return 
    }
}


