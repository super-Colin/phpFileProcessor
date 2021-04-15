<?php


// if (isset($_POST['submit'])){
//   $handle = fopen($_FILES['filename']['tmp_name'], "r");
//   $headers = fgetcsv($handle, 1000, ",");
//   while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) 
//   {
//   $data[0];
//   $data[1];
//   }
//   fclose($handle);
// }


//   // Convert each line into the local $data variable
//   while (($data = fgetcsv($h, 1000, ",")) !== FALSE) 
//   {		
//     // Read the data from a single line
//   }

class FileHandler{
    private $fileVerified = false;
    private $fileHandle;
    private $workingData;

    public function __construct($fileHandle){
        $this->verfiyNonMalicousFile($fileHandle);
        if( $this->fileVerified === false ) {return 'file potentially malicious!!!';}
        $this->fileHandle = $fileHandle;
        
    }

    private function verfiyNonMalicousFile($file){
        // Would be more important in a real project
        $this->fileVerified = true;
        return true;
    }


    public function explodeCsv(){
        if( $this->fileVerified === false ) {return 'File not verified';}
        if( empty($this->fileHandle) ){return 'Working file not found';}
        if( $this->checkFileIsCsv($fileHandle) === false){ return 'File is not CSV'; }
        $explodedCsv = [];
        while(false){

        }
        $this->workingData = $explodedCsv;
        return 'workingData set to exploded csv';
    }

    private function checkFileIsCsv(){
        if($this->fileVerified === false) {return false;}
        return true;
    }

    public function getData(){
        if($this->fileVerified === false) {return false;}
        return $this->workingData;
    }


}