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
    <h1>Something</h1>

    <!-- <form action="makeTable.php" method="post" enctype="multipart/form-data"> -->
    <form action="" method="post" enctype="multipart/form-data">
        CSV To Make Table From:<input type="file" name="filename" accept=".csv"> <br />
        <input type="submit" value="Upload File" name="submit">
    </form>








<?php
if (isset($_POST['submit'])){

?><br />_POST:<br /><?php
    var_dump($_POST);
?><br />_FILES:<br /><?php
    var_dump($_FILES);
?><br /><br /><?php
  $handle = fopen($_FILES['filename']['tmp_name'], "r");
  $headers = fgetcsv($handle, 1000, ",");
  $data = fgetcsv($handle, 1000, ",");
  ?><br />Handle:<br /><?php
  var_dump($handle);
  ?><br /><br />Headers:<br /><?php
  var_dump($headers);
  ?><br /><br />Data:<br /><?php
  var_dump($data);
  ?><br /><br /><?php
  fclose($handle);
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
</body>

</html>