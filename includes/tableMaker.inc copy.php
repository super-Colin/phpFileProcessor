<?php

class TableMaker{
    // private $columnsAdded = array();
    private $columnsAdded = array([]);
    private $inputData;
    private $workingData;

    public function __construct($inputData){
        $this->inputData = $inputData;
        $this->workingData = $inputData;
    }
    // calculate and add columns to row
    static function addToRowProfitMargin( $workingRow, $iOfBuyPrice, $iOfSellPrice){
        $rowWithNewColumn = $workingRow;
        $rowWithNewColumn[] = $workingRow[$iOfSellPrice] - $workingRow[$iOfBuyPrice];
        return $rowWithNewColumn;
    }

    public function generateTableHtml( $hasHeaders = true, $columnFuncsToUse=[]){
        $this->workingData = $this->inputData;
        $htmlForTable = '<table>';
        
        // TABLE HEADER
        if($hasHeaders){
            $tableHeadHtml = $this->generateTableHeadHtml($columnFuncsToUse);
            $headerRow = array_shift($this->workingData); // remove header from working data
            $headerHtml = '<thead>';
            if(! empty( $columnFuncsToUse ) ){
                for($columnFuncI = 0; $columnFuncI < count($columnFuncsToUse); $columnFuncI++){
                    // Check for and add header label
                    if(! empty( $columnFuncsToUse[$columnFuncI]["headerLabel"]) ){
                        $headerRow[] = $columnFuncsToUse[$columnFuncI]["headerLabel"];
                    }
                }
            }
            $headerHtml .= $this->generateRowHtml($headerRow, 'th');
            $headerHtml .= '</thead>';
            $htmlForTable .= $headerHtml;
        }

        $tableBodyHtml = $this->generateTableBodyHtml($columnFuncsToUse);

        // TABLE ROWS
        for($i = 0; $i < count($this->workingData); $i++){
            // add columns to row before html is generated if needed
            if(! empty( $columnFuncsToUse ) ){

                // apply middleware functions on row
                for($columnFuncI = 0; $columnFuncI < count($columnFuncsToUse); $columnFuncI++){
                    $funcToUse = $columnFuncsToUse[$columnFuncI]["functionName"];
                    if(method_exists(__CLASS__, $funcToUse)){
                        $funcArgs = $columnFuncsToUse[$columnFuncI]["functionArgs"];
                        $this->workingData[$i] = self::$funcToUse( $this->workingData[$i], ...$funcArgs);
                    }
                }
            }
            $htmlForTable .= $this->generateRowHtml($this->workingData[$i]);
        }
        $htmlForTable .= '</table>';

        $this->workingData = $htmlForTable;
        return "working data set to htmlForTable";
    }

    static function test($args){echo "TEST recieving: <br />" . var_dump($args);}

    protected function generateTableHeadHtml($columnFuncsToUse=[]){
        if($hasHeaders){
            $headerRow = array_shift($this->workingData); // remove header from working data
            $headerHtml = '<thead>';
            if(! empty( $columnFuncsToUse ) ){
                for($columnFuncI = 0; $columnFuncI < count($columnFuncsToUse); $columnFuncI++){
                    // Check for and add header label
                    if(! empty( $columnFuncsToUse[$columnFuncI]["headerLabel"]) ){
                        $headerRow[] = $columnFuncsToUse[$columnFuncI]["headerLabel"];
                    }
                }
            }
            $headerHtml .= $this->generateRowHtml($headerRow, 'th');
            $headerHtml .= '</thead>';
            $htmlForTable .= $headerHtml;
        }
    }
    protected function generateTableBodyHtml($columnFuncsToUse=[]){
        for($i = 0; $i < count($this->workingData); $i++){
            // add columns to row before html is generated if needed
            if(! empty( $columnFuncsToUse ) ){

                // apply middleware functions on row
                for($columnFuncI = 0; $columnFuncI < count($columnFuncsToUse); $columnFuncI++){
                    $funcToUse = $columnFuncsToUse[$columnFuncI]["functionName"];
                    if(method_exists(__CLASS__, $funcToUse)){
                        $funcArgs = $columnFuncsToUse[$columnFuncI]["functionArgs"];
                        $this->workingData[$i] = self::$funcToUse( $this->workingData[$i], ...$funcArgs);
                    }
                }
            }
            $htmlForTable .= $this->generateRowHtml($this->workingData[$i]);
        }
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


    // protected function getHeaders(){
    //     $headers = $this->inputData[0];
    //     return $headers;
    // }

    public function getData(){
        return $this->workingData;
    }

}


