<?php
    include 'includes/fileHandler.inc.php';
    include 'includes/tableMaker.inc.php';
    include 'includes/currencyConverter.inc.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP File Processor</title>
</head>

<body>
    <h1>Create a Table</h1>

    <form action="" method="post" enctype="multipart/form-data" style="display:flex; flex-wrap:wrap;">

        <div>
            <h3>CSV To Make Table From:</h3>
            <input type="file" name="filename" accept=".csv" required>
        </div>


        <div style="width:100%">
            <br /><br />
            Submit:
            <input type="submit" value="Upload CSV File" name="submit">
        </div>


    </form>



<?php

// FORM for more DYNAMICISM!
// Should make sure string to label index isn't sensitive to capital letters

// add css child selectors for relevent column index

// Highest / Lowest value summary options??
// Mark cells over a given value??


if (isset($_POST['submit'])){

// $currencyConverter = new CurrencyConverter();
// $rate = $currencyConverter->getConverstionRate("USD", "CAD");
// $rate = CurrencyConverter::getConverstionRate("USD", "CAD");
// echo "RATE IS SET AT : $rate";
// var_dump($rate);


$fileHandler = new FileHandler($_FILES['filename']['tmp_name'], $_FILES['filename']['type']);

$explodeCsvStatus = $fileHandler->explodeCsv();
if( $explodeCsvStatus  != false){
    $tableMaker = new TableMaker($fileHandler->getData());

    $tableStatus = $tableMaker->generateTableHtml( true, array(
        // Columns we want to add on processing
        array("headerLabel"=>"Profit Margin", "functionName"=>"addToRowProfitMargin", "functionArgs"=>["Cost", "Price"], "functionArgsAreLabels"=>true),
        array("headerLabel"=>"Total Profit USD", "functionName"=>"addToRowTotalProfit", "functionArgs"=>["Cost", "Price", "Qty"], "functionArgsAreLabels"=>true),
        array("headerLabel"=>"Total Profit CAD", "functionName"=>"addToRowTotalProfit", "functionArgs"=>[ "Cost", "Price", "Qty", CurrencyConverter::getConverstionRate("USD", "CAD") ], "functionArgsAreLabels"=>true),
        array("headerLabel"=>"Total Profit EUR", "functionName"=>"addToRowTotalProfit", "functionArgs"=>[ "Cost", "Price", "Qty", CurrencyConverter::getConverstionRate("USD", "EUR") ], "functionArgsAreLabels"=>true)

        // Our getConverstionRate method is very inefficient since it makes a new request for each
        // ...and parsing the entire page every time :/

    ),
    // Summaries to keep track of and output
    array(
        ["Cost", "Average"],
        ["Price", "Average"],
        ["Total Profit USD", "Average"],
        ["Total Profit USD", "Total"],
        ["Total Profit CAD", "Average"],
        ["Total Profit CAD", "Total"],
        ["Total Profit EUR", "Average"],
        ["Total Profit EUR", "Total"],
        ["Qty", "Total"]
    )
);


    if( $tableStatus  != false){
        echo $tableMaker->getData();
    }
    else{echo "<h2>Something went wrong:<br />" . $tableMaker->getData() . "<br /><br /></h2>";}
} 
else{echo "<h2>Something went wrong:<br />" . $fileHandler->getData() . "<br /><br /></h2>";}










} //if _POST


// echo '<br />------------<br />';
    // https://www.google.com/finance/quote/USD-CAD






?>

    <!-- <script>
        function csvToTable() {
            var objXMLHttpRequest = new XMLHttpRequest();
            objXMLHttpRequest.onreadystatechange = function () {
                if (objXMLHttpRequest.readyState === 4) {
                    if (objXMLHttpRequest.status === 200) {
                        alert(objXMLHttpRequest.responseText);
                    } else {
                        alert('Error Code: ' + objXMLHttpRequest.status);
                        alert('Error Message: ' + objXMLHttpRequest.statusText);
                    }
                }
            }
            objXMLHttpRequest.open('GET', 'request_ajax_data.php');
            objXMLHttpRequest.send();
        }
    </script> -->

    <style>
        thead{
            background-color:#eee;
        }
        td, th{
            padding: 0.3rem 0.5rem;
        }
        th{
            background-color:#e3e3e3;
        }
        tbody{
            background-color:#ccc;
        }
        td{
            background-color:#c3c3c3;
        }
        .other{
            background-color:orange;
        }
        .hidden{
            display:none;
        }
        .summariesRow{
            /* border:solid 2px #c3a3c3; */
        }
        .summariesRow th{
            /* background-color:#dbd; */
        }
        .summariesRow td{
            border: solid 2px #a373a3;
        }
        .summariesRow:nth-child(odd) td{
            border: solid 2px #c9c;
        }
        .positive{
            background-color:#8c8;
        }
        .negative{
            background-color: #c88;
        }
    </style>
</body>

</html>