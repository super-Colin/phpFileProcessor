<?php

class TableMaker{
    private $columnAddingFunctions = ["profitMargin"];
    private $inputData;
    private $workingData;

    public function __construct($inputData){
        $this->inputData = $inputData;
        $this->workingData = $inputData;
    }
    // calculate and add columns to row
    static function addToRowProfitMargin($workingRow, $iOfBuyPrice, $iOfSellPrice){
        $rowWithNewColumn = $workingRow;
        $rowWithNewColumn[] = $workingRow[$iOfSellPrice - $workingRow[$iOfBuyPrice]];
        return $rowWithNewColumn;
    }

    public function generateTableHtml( $hasHeaders = true, $columnFuncsToUse=[]){
        $this->workingData = $this->inputData;
        $htmlForTable = '<table>';
        
        // TABLE HEADER
        if($hasHeaders){
            $headerRow = array_shift($this->workingData); // remove header from working data
            $headerHtml = '<thead>';
            if(! empty( $columnFuncsToUse ) ){
                for($columnFuncI = 0; $columnFuncI < count($columnFuncsToUse); $columnFuncI++){
                    $headerRow[] = $columnFuncsToUse[$columnFuncI]["headerLabel"];
                }
            }
            $headerHtml .= $this->generateRowHtml($headerRow, 'th');
            $headerHtml .= '</thead>';
            $htmlForTable .= $headerHtml;
        }
        
        // TABLE ROWS
        for($i = 0; $i < count($this->workingData); $i++){
            // add columns to row before html is generated
            if(! empty( $columnFuncsToUse ) ){
                for($columnFuncI = 0; $columnFuncI < count($columnFuncsToUse); $columnFuncI++){
                    $funcToUse =$columnFuncsToUse[$columnFuncI]["functionName"];
                    self::$funcToUse();
                }
            }
            $htmlForTable .= $this->generateRowHtml($this->workingData[$i]);
        }
        $htmlForTable .= '</table>';

        $this->workingData = $htmlForTable;
        return "working data set to htmlForTable";
    }

    static function test(){echo "TEST";}


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


    // protected function getHeaders(){
    //     $headers = $this->inputData[0];
    //     return $headers;
    // }

    public function getData(){
        return $this->workingData;
    }

}


