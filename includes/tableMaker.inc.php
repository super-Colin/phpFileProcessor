<?php

class TableMaker{
    // private $columnsAdded = array();
    // private $columnsAdded = array(["addToRowProfitMargin"=>["columnKeys"=>["index1"=>"Cost", "index2"=>"Price"] ] ]);
    private $inputData;
    private $workingData;
    private $columnFuncs = false;

    public function __construct($inputData){
        $this->inputData = $inputData;
        $this->workingData = $inputData;
    }

    protected function headerLabelStringToIndex(){

    }
    // Could have used fewer args here but wanted it more independent
    static function addToRowTotalProfit( $workingRow, $iOfBuyPrice, $iOfSellPrice, $iOfQuantitySold){
        $rowWithNewColumn = $workingRow;
        $rowWithNewColumn[] = ($workingRow[$iOfSellPrice] - $workingRow[$iOfBuyPrice]) * $workingRow[$iOfQuantitySold];
        return $rowWithNewColumn;
    }
    static function addToRowProfitMargin( $workingRow, $iOfBuyPrice, $iOfSellPrice){
        $rowWithNewColumn = $workingRow;
        $rowWithNewColumn[] = $workingRow[$iOfSellPrice] - $workingRow[$iOfBuyPrice];
        return $rowWithNewColumn;
    }

    public function generateTableHtml( $hasHeaders = true, $columnFuncsToUse=[]){
        $this->workingData = $this->inputData;
        $this->columnFuncs = $columnFuncsToUse;
        $htmlForTable = '<table>';
        // TABLE HEADER
        if($hasHeaders){
            $headerRow = array_shift($this->workingData); // remove header from working data
            $htmlForTable .= $this->generateTableHeadHtml( $headerRow, $columnFuncsToUse);
        }
        // TABLE ROWS
        $htmlForTable .= $this->generateTableBodyHtml($this->workingData, $columnFuncsToUse) . '</table>';
        $this->workingData = $htmlForTable;
        return "working data set to htmlForTable";
    }

    protected function generateTableHeadHtml($headerRow, $columnFuncsToUse=[]){
            $headerHtml = '<thead>';
            if(! empty( $columnFuncsToUse ) ){
                for($columnFuncI = 0; $columnFuncI < count($columnFuncsToUse); $columnFuncI++){
                    // Check for and add header label
                    if(! empty( $columnFuncsToUse[$columnFuncI]["headerLabel"]) ){
                        $headerRow[] = $columnFuncsToUse[$columnFuncI]["headerLabel"];
                    }
                }
            }
            $headerHtml .= $this->generateRowHtml($headerRow, 'th') . '</thead>';
            return $headerHtml;        
    }

    protected function generateTableBodyHtml($workingData, $columnFuncsToUse=[]){
        $htmlForTableBody = "<tbody>";
        for($i = 0; $i < count($this->workingData); $i++){// for item in row

            if(! empty( $columnFuncsToUse ) ){ // if middleware functions apply them
                for($columnFuncI = 0; $columnFuncI < count($columnFuncsToUse); $columnFuncI++){
                    $funcToUse = $columnFuncsToUse[$columnFuncI]["functionName"];
                    if(method_exists(__CLASS__, $funcToUse)){
                        $funcArgs = $columnFuncsToUse[$columnFuncI]["functionArgs"];
                        $this->workingData[$i] = self::$funcToUse( $this->workingData[$i], ...$funcArgs);
                    }
                }
            }
            $htmlForTableBody .= $this->generateRowHtml($this->workingData[$i]) . "</tbody>";
        }
        return $htmlForTableBody;
    }

    // calculate and add columns to row


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


