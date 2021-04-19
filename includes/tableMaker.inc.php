<?php

// need to get currency converter working

// Need to be able to parse out currency symbols in input
// ^^^^^^^^^^ - $ - ^^^^^^^^^^^
// Add back currency symbols - $ -

// add positive / negative styling classes with css child selectors for column index relevance

// Need to round averages summaries to 2 decimal points
// Highest / Lowest value summary options??
// Mark cells over a given value??

class TableMaker{
    private $inputData;
    private $workingData;
    private $columnFuncs = false;
    private $headers;
    private $otherVars;
    private $requestedSummaries = false;

    public function __construct($inputData){
        $this->inputData = $inputData;
        $this->workingData = $inputData;
    }


    public function generateTableHtml( $hasHeaders = true, $columnFuncsToUse=[], $summaryFuncsToUse=[]){
        $this->workingData = $this->inputData; // make sure we're starting fresh with the input 
        $htmlForTable = '<table>';
        
        // TABLE HEADER
        if($hasHeaders){
            $headerRow = array_shift($this->workingData); // remove header from working data
            $this->headers = $headerRow; // will be set again after new headers are added
            $this->setColumnFuncs($columnFuncsToUse);
            // echo '<br />HEADERS:<br />';
            // var_dump($this->headers);
            // echo '<br />COLUMN FUNCS:<br />';
            // var_dump($this->columnFuncs);
            $htmlForTable .= $this->generateTableHeadHtml( $headerRow);
        }
        // make sure columnFuncs property is formatted properly if there weren't headers
        if($this->columnFuncs == false){$this->setColumnFuncs($columnFuncsToUse);}

        // TABLE ROWS
        $this->setRequestedSummaries($summaryFuncsToUse);
        $htmlForTable .= $this->generateTableBodyHtml($this->workingData);

        //SUMMARIES BLOCK
        $summaryRows = $this->generateSummaryRows();
        $summariesHtml = $this->generateSummariesHtml($summaryRows);

        $htmlForTable .= $summariesHtml;
        $htmlForTable .= '</table>';
        $this->workingData = $htmlForTable;
        return "working data set to htmlForTable";
    }


    protected function generateStylesForColumnIndexes(){
        $styles = "";
    }

    protected function classesToAddToCell($cellData){
        $classList = array();
        if( is_numeric($cellData) ){
            $positiveOrNegative = $this->addClassPositiveOrNegative($cellData);
            if($positiveOrNegative != false){$classList[] = $positiveOrNegative;}
        }
        return $classList;
    }
    protected function addClassPositiveOrNegative($cellData){
        if($cellData > 0){ return "positive";}
        if($cellData == 0){ return "zeroSum";}
        if($cellData < 0){ return "negative";}
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
            $htmlForTableBody .= $this->generateRowHtml($this->workingData[$i]);
        }
        $htmlForTableBody .= "</tbody>";
        return $htmlForTableBody;
    }

    protected function generateSummaryRows(){
        $summaryRows = [];
        // echo '<br />FINAL SUMMARIES : <br />';
        // var_dump($this->requestedSummaries);
        for($i=0; $i < count($this->requestedSummaries); $i++){
            $requestedOperation = $this->requestedSummaries[$i]["requestedOperation"];
            $currentSummaryTotal = $this->requestedSummaries[$i]["runningTotalSum"];
            $currentSummaryEntryTotal = $this->requestedSummaries[$i]["runningEntriesTotal"];
            $resultValueOfOperation;
            switch($requestedOperation){
                case "Average":
                    $resultValueOfOperation = $currentSummaryTotal / $currentSummaryEntryTotal;
                    break;
                case "Total":
                    $resultValueOfOperation = $currentSummaryTotal;
                    break;
            }
            if(array_key_exists( $requestedOperation, $summaryRows) == false){
                // echo "<br />making summary row for " . $requestedOperation;
                $summaryRows[$requestedOperation] = array();

                // fill array with empty values
                $numberOfColumns = $this->getNumberOfColumns();
                for($x=0; $x < $numberOfColumns; $x++){
                    $summaryRows[$requestedOperation][$x]='';
                }
            }
            // echo "<br />existing summary row for " . $requestedOperation;
            $columnHasCurrencySymbolTest = $this->workingData[1][$this->requestedSummaries[$i]["columnIndex"]];
            $currencyIndex = self::isCurrency($columnHasCurrencySymbolTest);
            if( $currencyIndex !== false){
                $resultValueOfOperation = substr($columnHasCurrencySymbolTest, $currencyIndex, 1) . $resultValueOfOperation;
            }
            // Insert value into column index
            $iOfOperationColumn = $this->requestedSummaries[$i]["columnIndex"];
            $summaryRows[$requestedOperation][$iOfOperationColumn] = $resultValueOfOperation;
        }
        // echo '<br />FINAL SUMMARY ROWS FIXED: <br />';
        // var_dump($summaryRows);
        return $summaryRows;
    }

    protected function generateSummariesHtml($summaryRows){
        $summariesHtmlBlock='<tbody>';
        foreach (array_keys($summaryRows) as $arrayKey) {
            $rowHtml = '<tr class="summariesRow">';
            for($i=0; $i < count($summaryRows[$arrayKey]); $i++){
                $rowHtml .= $this->generateCellHtml($summaryRows[$arrayKey][$i], 'td');
            }
            $rowHtml .= $this->generateCellHtml( $arrayKey, 'th');
            $rowHtml .= '</tr>';
            $summariesHtmlBlock .= $rowHtml;
        }
        $summariesHtmlBlock .= '</tbody>';
        return $summariesHtmlBlock;

    }

    protected function generateRowHtml($rowData, $cellElemement = 'td'){
        $rowHtml = '<tr>';
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
        $cleanedData = '';
        $currencyUsedIndex = false;
        $formattedForHtmlData='';
        // if(is_numeric($cellData)){ //middleware functions for ints
        if(filter_var($cellData, FILTER_SANITIZE_NUMBER_INT)){ //middleware functions for ints
            // echo "celldata is numeric: $cellData<br />";
            $currencyUsedIndex = self::isCurrency($cellData);
            // echo "currency used : $currencyUsedIndex";

            if($currencyUsedIndex !== false){
                // $cleanedData = self::stripCurrencySymbol($cellData, $currencyUsedIndex);
                // $cleanedData = number_format(self::stripCurrencySymbol($cellData, $currencyUsedIndex),2,".", ",");
                $cleanedData = self::getIntFromString($cellData);
            }else{$cleanedData = $cellData;}
            if( gettype($cleanedData) =="double"){
                $cleanedData = number_format($cleanedData,2,".", ",");
            }
        }else{
            // echo "celldata is NOT numeric: $cellData<br />";
            $cleanedData = $cellData;
        }
        // echo "<br />cleaned data is: $cleanedData <br />";

        $classList = $this->classesToAddToCell($cleanedData);
        if($currencyUsedIndex !== false){
            $formattedForHtmlData = substr( $cellData, $currencyUsedIndex, 1) . $cleanedData;
        }else{
            $formattedForHtmlData = $cleanedData;
        }
        $classesAsString = '';
        foreach($classList as $class){
            $classesAsString .= $class . ' ';
        }
        $cellHtml = "<$cellElemement class='$classesAsString'>$formattedForHtmlData</$cellElemement>";
        return $cellHtml;
    }


    protected function getNumberOfColumns(){
        return count($this->headers);
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
                }else{
                    $formattedFunc["functionArgs"] = $funcArgs;
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
        $this->setRequestedSummariesColumnIndexes();
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


            $this->requestedSummaries[$i]["runningTotalSum"] += self::getIntFromString($workingRow[$cIndex]);
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






    static function headerLabelStringToIndex( $headerRow, $columnLabelString){
        // echo '<br />labelStringToIndex :<br />';
        // print_r($headerRow);
        // print_r($columnLabelString);
        $label = array_search( $columnLabelString, $headerRow, false);
        // echo "label is : var_dump($label)";
        if( $label == false){ //must not be a reference to a header label
            return $columnLabelString;
        } else{ return $label; }
    }
    static function addToRowTotalProfit( $workingRow, $iOfBuyPrice, $iOfSellPrice, $iOfQuantitySold, $conversionRate=1){
        $rowWithNewColumn = $workingRow;
        // echo '<br />addToRowProfitMargin :<br />';
        // echo self::getIntFromString( $workingRow[$iOfBuyPrice] );
        // echo '<br />';
        // print_r($iOfBuyPrice);
        // echo '<br />';
        // print_r($iOfSellPrice);
        // echo '<br />';
        // print_r($iOfQuantitySold);
        // echo '<br />';
        $rowWithNewColumn[] = (
            self::getIntFromString( $workingRow[$iOfSellPrice] ) 
            - self::getIntFromString( $workingRow[$iOfBuyPrice]) 
        ) 
        * self::getIntFromString( $workingRow[$iOfQuantitySold] ) 
        * self::getIntFromString( $conversionRate );
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
        $rowWithNewColumn[] = self::getIntFromString($workingRow[$iOfSellPrice]) - self::getIntFromString($workingRow[$iOfBuyPrice]);
        return $rowWithNewColumn;
    }
    static function getIntFromString($string){
        $currencyUsedIndex = self::isCurrency( $string );
        if($currencyUsedIndex !== false){
            $int = self::stripCurrencySymbol($string, $currencyUsedIndex);
        }else{$int = (float) $string;}
        // echo "getIntFromString returning " . var_dump($int);
        return $int;
    }
    static function isCurrency($string){
        $currencySymbols = ["$", "¥", "€"];
        if(filter_var($string, FILTER_SANITIZE_NUMBER_INT) === false){echo "IS NOT CURRENCY <br />";return false;}
        for($i=0;$i < count($currencySymbols); $i++){
            // echo "looking for currency symbol : " . $currencySymbols[$i] . " in string $string";
            $symbolPos = strpos($string, $currencySymbols[$i]);
            // var_dump($symbolPos);
            if($symbolPos !== false){
                // echo "currency symbol found, returning : $symbolPos";
                return $symbolPos;
            }
        }
        // echo "currency symbol NOT found, returning : $symbolPos";
        return false;
    }
    static function stripCurrencySymbol($string, $currencyIndex){
        $cleanedString= substr($string , ($currencyIndex+1) );
        $intVal = (float) $cleanedString;
        // echo "<br />stripped currency is now: " . var_dump($intVal) . "<br />";
        return $intVal;
    }


    public function getData(){
        return $this->workingData;
    }

}


