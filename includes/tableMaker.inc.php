<?php


// Need to be able to parse out currency symbols in input
// ^^^^^^^^^^ - $ - ^^^^^^^^^^^
// Look for positive / negative values in row functions
// Running total for column functions = get totalSum / totalAverage 
// Add back currency symbols - $ -

class TableMaker{
    private $inputData;
    private $workingData;
    private $columnFuncs = false;
    private $headers;
    private $otherVars;
    private $requestedSummaries = false;
    // private $requestedSummaries = [
    //     "Cost"=>[
    //         // "columnIndex"=>headerLabelStringToIndex("Cost"),
    //         "requested"=>"average",
    //         "runningTotalSum"=>0,
    //         "runningEntriesTotal"=>0
    //     ]
    // ];

    public function __construct($inputData){
        $this->inputData = $inputData;
        $this->workingData = $inputData;
    }


    public function generateTableHtml( $hasHeaders = true, $columnFuncsToUse=[], $summaryFuncsToUse=[]){
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
        $this->setRequestedSummaries($summaryFuncsToUse);
        $this->setRequestedSummariesColumnIndexes();
        $htmlForTable .= $this->generateTableBodyHtml($this->workingData) . '</table>';
        $this->getFinalSummariesRow();
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
            $this->headers = $headerRow;
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
        // RUN requestedSummaries FUNCTION HERE?
        if($this->requestedSummaries != false){
            $this->proccessRequestedSummariesForRow($rowData);
        }
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


    // --- calculate and add columns to row ---
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

    protected function setRequestedSummaries( $requestedSummaries){
        $formattedSummaries = array();
        // echo '<br />Set Requested Summaries:<br />';
        // var_dump($requestedSummaries);
        for($i=0; $i < count($requestedSummaries); $i++){
            // echo '<br />SetRequestedSummaries KEY:<br />';
            // var_dump( $requestedSummaries[$i] );
            $formattedSummaries[] = [
                    "columnName"=>$requestedSummaries[$i][0],
                    "columnIndex"=>0,
                    "requestedOperation"=>$requestedSummaries[$i][1],
                    "runningTotalSum"=>0,
                    "runningEntriesTotal"=>0
            ];
        }
        // echo '<br />Set Requested Summaries FINISHED:<br />';
        // var_dump($formattedSummaries);
        $this->requestedSummaries = $formattedSummaries;
    }
    protected function setRequestedSummariesColumnIndexes(){
        for($i=0; $i < count($this->requestedSummaries); $i++){
            $cIndex = $this->headerLabelStringToIndex( $this->headers, $this->requestedSummaries[$i]["columnName"] );
            $this->requestedSummaries[$i]["columnIndex"] = $cIndex;
        }
    }
    protected function proccessRequestedSummariesForRow(  $workingRow ){
        for($i=0; $i < count($this->requestedSummaries); $i++){
            $cIndex = $this->requestedSummaries[$i]["columnIndex"];
            $this->requestedSummaries[$i]["runningTotalSum"] += $workingRow[$cIndex];
            $this->requestedSummaries[$i]["runningEntriesTotal"]++;
            // echo '<br />Proccess Summaries:<br />';
            // var_dump($this->requestedSummaries);
            // echo '<br /><br />';
            // echo '<br /><br />';
            // echo '<br /><br />';
            // var_dump($this->requestedSummaries[$i]);
            // echo '<br /><br />';
            // var_dump($this->requestedSummaries[$i]["runningTotalSum"]);
            // echo '<br /><br />';
            // var_dump($this->requestedSummaries[$i]["runningEntriesTotal"]);
            // echo '<br />---------<br />';
            // var_dump($cIndex);
            // echo '<br /><br />';
            // var_dump($workingRow[$cIndex]);
            // echo '<br /><br />';
        }
    }
    protected function getFinalSummariesRow(){
        // $this->
        $emptyRow = [];
        echo '<br />FINAL SUMMARIES ROW : <br />';
        var_dump($this->requestedSummaries);
    }
    static function headerLabelStringToIndex( $headerRow, $columnLabelString){
        // echo '<br />labelStringToIndex :<br />';
        // print_r($headerRow);
        // print_r($columnLabelString);
        $label = array_search( $columnLabelString, $headerRow, false);
        if( $label == false){ //must not be a reference to a header label
            return $columnLabelString;
        } else{ return $label; }
    }
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
    static function addToRowTotalProfitConverted( $workingRow, $iOfBuyPrice, $iOfSellPrice, $iOfQuantitySold, $conversionRate){
        $rowWithNewColumn = $workingRow;
        // echo '<br />addToRowProfitMarginConverted :<br />';
        // print_r($workingRow);
        // echo '<br />';
        // print_r($iOfBuyPrice);
        // echo '<br />';
        // print_r($iOfSellPrice);
        // echo '<br />';
        // print_r($iOfQuantitySold);
        // echo '<br />';
        // var_dump($conversionRate);
        // echo '<br />';
        $rowWithNewColumn[] = (($workingRow[$iOfSellPrice] - $workingRow[$iOfBuyPrice]) * $workingRow[$iOfQuantitySold]) * $conversionRate ;
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

    public function getData(){
        return $this->workingData;
    }

}


