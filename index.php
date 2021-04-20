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

<?php 
// Set values to back to what they were if was previously set
if ( isset($_POST['submit']) == false ){
    $ourBuyPriceLabel = "Cost";
    $ourSellPriceLabel = "Price";
    $ourQtySoldLabel = "Qty";
    $columnToAddLabel = "Profit Margin USD";
    $columnToAddFunction = "columnToAddFunction";
    // $columnToAddLabel2 = "These will need to be generated with JS";
    // $columnToAddFunction2 = "These will need to be generated with JS";
}else{
    $ourBuyPriceLabel = clean_input($_POST['ourBuyPriceLabel']);
    $ourSellPriceLabel = clean_input($_POST['ourSellPriceLabel']);
    $ourQtySoldLabel = clean_input($_POST['ourQtySoldLabel']);
    $columnToAddLabel = clean_input($_POST['columnToAddLabel']);
    $columnToAddFunction = clean_input($_POST['columnToAddFunction']);
    // if (isset($columnToAddLabel2)) $columnToAddLabel2= clean_input($_POST['columnToAddLabel2'];
    // if (isset($columnToAddFunction2)) $columnToAddFunction2= clean_input($_POST['columnToAddFunction2'];
}



function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>


<body>
    <h1>Create a Table</h1>

    <form action="" method="post" enctype="multipart/form-data" style="display:flex; flex-wrap:wrap;">

        <div>
            <h3>CSV To Make Table From:</h3>
            <input type="file" name="filename" accept=".csv" required>
        </div>

        <div>
            <h2>Columns needed for functions:</h2>

            <div class="referenceColumnLabelFormBlock" id="ourBuyPrice">
                <label for="ourBuyPriceLabel">Our Buy Price Column Label:</label>
                <input type="text" name="ourBuyPriceLabel" value="<?php echo $ourBuyPriceLabel;?>" required>
            </div>
            <div class="referenceColumnLabelFormBlock" id="ourSellPrice">
                <label for="ourSellPriceLabel">Our Sell Price Column Label:</label>
                <input type="text" name="ourSellPriceLabel" value="<?php echo $ourSellPriceLabel;?>" required>
            </div>
            <div class="referenceColumnLabelFormBlock" id="ourQtySold">
                <label for="ourQtySoldLabel">Our Quantity Sold Column Label:</label>
                <input type="text" name="ourQtySoldLabel" value="<?php echo $ourQtySoldLabel;?>" required>
            </div>
        </div>

        <div>
            <h2>Add Columns:</h2>
            
            <div class="desiredFunctionFormBlock" id="1">
                <label for="columnToAddLabel">Column To Add Label:</label>
                <input type="text" name="columnToAddLabel" value="<?php echo $columnToAddLabel;?>">
                <label for="columnToAddFunction">Column To Add Function:</label>
                <select name="columnToAddFunction">
                    <option value="addToRowProfitMargin" <?php if($columnToAddFunction =="addToRowProfitMargin"){echo 'selected="selected"';}?>>Profit Margin</option>
                    <option value="addToRowTotalProfit" <?php if($columnToAddFunction =="addToRowTotalProfit"){echo 'selected="selected"';}?>>Total Profit</option>
                </select>

            </div>
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

$fileHandler = new FileHandler($_FILES['filename']['tmp_name'], $_FILES['filename']['type']);
$explodeCsvStatus = $fileHandler->explodeCsv();
if( $explodeCsvStatus  != false){
    $tableMaker = new TableMaker($fileHandler->getData());

    $tableStatus = $tableMaker->generateTableHtml( true, array(
        // Columns we want to add on processing
        array("headerLabel"=>"Profit Margin", "functionName"=>"addToRowProfitMargin", "functionArgs"=>[$ourBuyPriceLabel, $ourSellPriceLabel], "functionArgsAreLabels"=>true),
        array("headerLabel"=>"Total Profit USD", "functionName"=>"addToRowTotalProfit", "functionArgs"=>[$ourBuyPriceLabel, $ourSellPriceLabel, $ourQtySoldLabel], "functionArgsAreLabels"=>true),
        array("headerLabel"=>"Total Profit CAD", "functionName"=>"addToRowTotalProfit", "functionArgs"=>[ $ourBuyPriceLabel, $ourSellPriceLabel, $ourQtySoldLabel, CurrencyConverter::getConverstionRate("USD", "CAD") ], "functionArgsAreLabels"=>true),
        array("headerLabel"=>"Total Profit EUR", "functionName"=>"addToRowTotalProfit", "functionArgs"=>[ $ourBuyPriceLabel, $ourSellPriceLabel, $ourQtySoldLabel, CurrencyConverter::getConverstionRate("USD", "EUR") ], "functionArgsAreLabels"=>true),
        array("headerLabel"=>$columnToAddLabel, "functionName"=>$columnToAddFunction, "functionArgs"=>[ $ourBuyPriceLabel, $ourSellPriceLabel, $ourQtySoldLabel ], "functionArgsAreLabels"=>true),

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
        .zeroSum{
            background-color: #cc8;
        }
    </style>
</body>

</html>