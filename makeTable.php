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
  <title>Table From CSV</title>
</head>

<body>
  <h1>Results</h1>
  <?php
    // $fileObj = new FileHandler($_POST["file"]);
    $fileObj = new FileHandler(  $_FILES['filename']['tmp_name']);
    $csvFile = $fileObj->getFile();
    if($csvFile != false){
      echo $csvFile;
    }else{
      echo "something went wrong" . $csvFile;
    }

  ?>
</body>

</html>