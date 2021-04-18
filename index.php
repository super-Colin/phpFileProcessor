<?php
    include 'includes/fileHandler.inc.php';
    include 'includes/tableMaker.inc.php';
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

    <!-- <form action="makeTable.php" method="post" enctype="multipart/form-data"> -->
    <form action="" method="post" enctype="multipart/form-data">
        CSV To Make Table From:<br /><input type="file" name="filename" accept=".csv">
        <br /><br />
        <input type="submit" value="Upload CSV File" name="submit">
    </form>


<?php
if (isset($_POST['submit'])){

$fileHandler = new FileHandler($_FILES['filename']['tmp_name'], $_FILES['filename']['type']);

$explodeCsvStatus = $fileHandler->explodeCsv();
if( $explodeCsvStatus  != false){
    $tableMaker = new TableMaker($fileHandler->getData());


    $tableStatus = $tableMaker->generateTableHtml( true, array(
        // array("headerLabel"=>"Profit Margin", "functionName"=>"addToRowProfitMargin", "functionArgs"=>[1, 2]),

        array("headerLabel"=>"Profit Margin", "functionName"=>"addToRowProfitMargin", "functionArgs"=>["Cost", "Price"], "functionArgsAreLabels"=>true),


        // array("headerLabel"=>"Total Profit", "functionName"=>"addToRowTotalProfit", "functionArgs"=>[1, 2,3], "functionArgsAreLabels"=>false),
        array("headerLabel"=>"Total Profit", "functionName"=>"addToRowTotalProfit", "functionArgs"=>["Cost", "Price", "Qty"], "functionArgsAreLabels"=>true)
    ));


    if( $tableStatus  != false){
        echo $tableMaker->getData();
    }
    else{echo "<h2>Something went wrong:<br />" . $tableMaker->getData() . "<br /><br /></h2>";}
} 
else{echo "<h2>Something went wrong:<br />" . $fileHandler->getData() . "<br /><br /></h2>";}







// echo "<br /> <br />";
// $echoData = $fileHandler->getData();
// echo "data is: <br />";var_dump($echoData);

echo "<!--";
?><br />_DATA:<br /><?php
//     var_dump($fileHandler->getData());
// ?><br />_POST:<br /><?php
//     var_dump($_POST);
// ?><br />_FILES:<br /><?php
//     var_dump($_FILES);
// echo "<!--";
?><br /><br /><?php
    // $handle = fopen($_FILES['filename']['tmp_name'], "r");
    // $headers = fgetcsv($handle, 1000, ",");
    // $data = fgetcsv($handle, 1000, ",");
    // ?><br />Handle:<br /><?php
    // var_dump($handle);
    // ?><br /><br />Headers:<br /><?php
    // var_dump($headers);
    // ?><br /><br />Data:<br /><?php
    // var_dump($data);
    // ?><br /><br /><?php
    // fclose($handle);
echo "-->";  
}
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
        th{
            background-color:#e3e3e3;
            margin: 2px;
        }
        tbody{
            background-color:#ccc;
        }
        td{
            background-color:#c3c3c3;
            margin: 2px;
        }
    </style>
</body>

</html>