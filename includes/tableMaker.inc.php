<?php

class TableMaker{
    private $inputData;
    private $workingData;

    public function __construct($inputData){
        $this->inputData = $inputData;
        $this->workingData = $inputData;
    }

    public function generateTableHtml($hasHeaders = true){
        $this->workingData = $this->inputData;
        $htmlForTable = '<table>';
        if($hasHeaders){
            // $headerRow = $this->getHeaders();
            $headerRow = array_shift($this->workingData); // remove header from working data
            $headerHtml = '<thead>' . $this->generateRowHtml($headerRow, 'th') . '</thead>';
            $htmlForTable .= $headerHtml;
            // array_shift($this->workingData); // remove header from working data
        }

        for($i = 0; $i < count($this->workingData); $i++){
            $htmlForTable .= $this->generateRowHtml($this->workingData[$i]);
        }


        $htmlForTable .= '</table>';
        $this->workingData = $htmlForTable;
        return "working data set to htmlForTable";
    }

    protected function getHeaders(){
        $headers = $this->inputData[0];
        return $headers;
    }

    protected function generateRowHtml($rowData, $cellElemement = 'td'){
        $rowHtml = '<tr>';
        for($i=0; $i < count($rowData); $i++){
            $rowHtml .= $this->generateCellHtml($rowData[$i], $cellElemement);
        }
        $rowHtml .= '</tr>';
        return $rowHtml;
    }
    protected function generateCellHtml($cellData, $cellElemement = 'td'){
        $cellHtml = '<' . $cellElemement . '>' . $cellData . '</' . $cellElemement . '>';
        return $cellHtml;
    }

    public function getData(){
        return $this->workingData;
    }

}


