<?php



//   // Convert each line into the local $data variable
//   while (($data = fgetcsv($h, 1000, ",")) !== FALSE) 
//   {		
//     // Read the data from a single line
//   }

class FileHandler{
    private $fileVerified = false;
    private $fileHandle;
    private $openFileHandle;
    private $workingData;

    public function __construct($fileHandle){
        $this->verfiyNonMalicousFile($fileHandle);
        if( $this->fileVerified === false ) {return 'file potentially malicious!!!';}
        $this->fileHandle = $fileHandle;
        $this->openFileHandle = fopen($fileHandle, 'r');
    }

    private function verfiyNonMalicousFile($file){
        // Would be more important in a real project
        $this->fileVerified = true;
        return true;
    }


    public function explodeCsv(){
        if( $this->fileVerified === false ) {return 'File not verified';}
        if( empty($this->openFileHandle) ){return 'Working file not found';}
        if( $this->checkFileIsCsv($this->openFileHandle) === false){ return 'File is not CSV'; }
        $explodedCsv = [];

        $row = 1;
        while (($data = fgetcsv($this->openFileHandle, 1000, ",")) !== FALSE && $row < 20) {
            $num = count($data);
            echo "<p> $num fields in line $row: <br /></p>\n";
            $row++;
            for ($c=0; $c < $num; $c++) {
                echo $data[$c] . "<br />\n";
            }
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

    public function __destruct(){
        fclose($this->openFileHandle);
    }


}