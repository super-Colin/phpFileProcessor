<?php

class TableMaker{
    private $inputData;
    private $workingData;

    public function __construct($inputData){
        $this->inputData = $inputData;
        $this->workingData = $inputData;
    }

    public function generateTable($hasHeaders = true){
        $htmlForTable = '';
        if($hasHeaders){
            $this->getHeaders();
        }
        return $this->workingData;
    }

    protected function getHeaders(){

    }

    protected function generateHtmlForRow($rowData){
        $rowHtml = '';
        
    }
    protected function generateHtmlForCell($cellData){
        $cellHtml = '<td>' . $cellData . '</td>';
        return $cellHtml;
    }

}


