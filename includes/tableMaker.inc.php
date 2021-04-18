<?php

class TableMaker{
    // private $columnsAdded = array();
    // private $columnsAdded = array(["addToRowProfitMargin"=>["columnKeys"=>["index1"=>"Cost", "index2"=>"Price"] ] ]);
    private $inputData;
    private $workingData;
    private $columnFuncs = false;
    private $headers;

    public function __construct($inputData){
        $this->inputData = $inputData;
        $this->workingData = $inputData;
    }


    // --- calculate and add columns to row ---
    static function headerLabelStringToIndex( $headerRow, $columnLabelString){
        // echo '<br />labelStringToIndex :<br />';
        // print_r($headerRow);
        // print_r($columnLabelString);
        return array_search( $columnLabelString, $headerRow, false);
    }
    // Could have used fewer args here but wanted it more independent
    static function addToRowTotalProfit( $workingRow, $iOfBuyPrice, $iOfSellPrice, $iOfQuantitySold){
        $rowWithNewColumn = $workingRow;
        // echo '<br />addToRowProfitMargin :<br />';
        // print_r($workingRow);
        // echo '<br />';
        // print_r($iOfBuyPrice);
        // echo '<br />';
        // print_r($iOfSellPrice);
        // echo '<br />';
        // print_r($iOfQuantitySold);
        // echo '<br />';
        $rowWithNewColumn[] = ($workingRow[$iOfSellPrice] - $workingRow[$iOfBuyPrice]) * $workingRow[$iOfQuantitySold];
        return $rowWithNewColumn;
    }
    static function addToRowProfitMargin( $workingRow, $iOfBuyPrice, $iOfSellPrice){
        $rowWithNewColumn = $workingRow;
        // echo '<br />addRowToProfitMargin :<br />';
        // print_r($workingRow);
        // echo '<br />';
        // print_r($iOfBuyPrice);
        // echo '<br />';
        // print_r($iOfSellPrice);
        // echo '<br />';
        $rowWithNewColumn[] = $workingRow[$iOfSellPrice] - $workingRow[$iOfBuyPrice];
        return $rowWithNewColumn;
    }

    protected function setColumnFuncs($columnFuncsToUse){
        $formattedFuncs = array();
        for($columnFuncI = 0; $columnFuncI < count($columnFuncsToUse); $columnFuncI++){
            $formattedFunc = $columnFuncsToUse[$columnFuncI];
            $funcToUse = $columnFuncsToUse[$columnFuncI]["functionName"];
            // echo '<br />setting column func :<br />';
            // print_r($funcToUse);
            if(method_exists(__CLASS__, $funcToUse)){
                $funcArgs = $columnFuncsToUse[$columnFuncI]["functionArgs"];        
                // echo '<br />args for func :<br />';
                // print_r($funcArgs);
                // Turn label key into a column index number
                if( $columnFuncsToUse[$columnFuncI]["functionArgsAreLabels"] == true){
                    // echo '<br />--- ARGS ARE STRINGS ---<br />';
                    for($columnFuncArgI = 0; $columnFuncArgI < count($funcArgs); $columnFuncArgI++){
                        // echo '<br />this one is :<br />';
                        // print_r($formattedFunc);
                        // print_r($formattedFunc["functionArgs"]);
                        $formattedFunc["functionArgs"][$columnFuncArgI] = self::headerLabelStringToIndex( $this->headers, $funcArgs[$columnFuncArgI]);
                        // echo '<br />BUT NOW IT IS :<br />';
                        // print_r($formattedFunc);
                        // print_r($formattedFunc["functionArgs"]);
                    }
                }
            }
            // echo '<br /><br />--- setColumnFuncs == Formatted Func is :  --- :<br />';
            // print_r($formattedFunc);
            // echo '<br /><br />';
            $formattedFuncs[] = $formattedFunc;
        }
        $this->columnFuncs = $formattedFuncs;
    }

    public function generateTableHtml( $hasHeaders = true, $columnFuncsToUse=[]){
        $this->workingData = $this->inputData;
        $htmlForTable = '<table>';
        // TABLE HEADER
        if($hasHeaders){
            $headerRow = array_shift($this->workingData); // remove header from working data
            $this->headers = $headerRow;
            $this->setColumnFuncs($columnFuncsToUse);
            // echo '<br />HEADERS:<br />';
            // var_dump($this->headers);
            // echo '<br />COLUMN FUNCS:<br />';
            // var_dump($this->columnFuncs);
            $htmlForTable .= $this->generateTableHeadHtml( $headerRow);
        }
        if($this->columnFuncs == false){$this->setColumnFuncs($columnFuncsToUse);}
        // TABLE ROWS
        $htmlForTable .= $this->generateTableBodyHtml($this->workingData) . '</table>';
        $this->workingData = $htmlForTable;
        return "working data set to htmlForTable";
    }

    protected function generateTableHeadHtml($headerRow){
            $headerHtml = '<thead>';
            if(! empty( $this->columnFuncs ) ){
                for($columnFuncI = 0; $columnFuncI < count($this->columnFuncs); $columnFuncI++){
                    // Check for and add header label
                    if(! empty( $this->columnFuncs[$columnFuncI]["headerLabel"]) ){
                        $headerRow[] = $this->columnFuncs[$columnFuncI]["headerLabel"];
                    }
                }
            }
            $headerHtml .= $this->generateRowHtml($headerRow, 'th') . '</thead>';
            return $headerHtml;        
    }

    protected function generateTableBodyHtml($workingData){
        $htmlForTableBody = "<tbody>";
        for($i = 0; $i < count($this->workingData); $i++){// for item in row

            if(! empty( $this->columnFuncs ) ){ // if middleware functions apply them
                for($columnFuncI = 0; $columnFuncI < count($this->columnFuncs); $columnFuncI++){
                    $funcToUse = $this->columnFuncs[$columnFuncI]["functionName"];
                    if(method_exists(__CLASS__, $funcToUse)){
                        $funcArgs = $this->columnFuncs[$columnFuncI]["functionArgs"];
                        $this->workingData[$i] = self::$funcToUse( $this->workingData[$i], ...$funcArgs);
                    }
                }
            }
            $htmlForTableBody .= $this->generateRowHtml($this->workingData[$i]) . "</tbody>";
        }
        return $htmlForTableBody;
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


