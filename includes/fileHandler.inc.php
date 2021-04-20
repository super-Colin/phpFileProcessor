<?php

class FileHandler{
    private $fileVerified = false;
    private $fileType;
    private $fileHandle;
    private $openFileHandle;
    private $workingData;

    public function __construct($fileHandle, $fileType){
        $this->verfiyNonMalicousFile($fileHandle);
        if( $this->fileVerified == false ) {return 'file potentially malicious!!!';}
        ini_set('auto_detect_line_endings',TRUE); //detect line endings from mac file
        $this->fileType = $fileType;
        $this->fileHandle = $fileHandle;
        $this->openFileHandle = fopen($fileHandle, 'r');
    }

    private function verfiyNonMalicousFile(){
        // Would be more important in a real project
        $this->fileVerified = true;
        return true;
    }

    public function explodeCsv(){
        if( $this->fileVerified == false ) {$this->workingData = 'File not verified'; return false;}
        if( $this->checkFileIsCsv() == false){$this->workingData = 'File not CSV'; return false;}
        $explodedCsv = array();

        $rowNumber = 0;
        while ( ($data = fgetcsv($this->openFileHandle, 1000, ",")) !== false) {
            $num = count($data);
            $rowValues = array();
            for ($c=0; $c < $num; $c++) {
                $rowValues[] = $data[$c];
            }
            $explodedCsv[] = $rowValues;
            $rowNumber++;
            if($rowNumber > 5000){$this->workingData = "rowNumber failsafe of :$rowNumber triggered";return false;}
        }
        $this->workingData = array();
        $this->workingData = $explodedCsv;
        
        return 'workingData is now set to exploded csv';
    }


    private function checkFileIsCsv(){
        if($this->fileVerified == false) {$this->workingData = 'File not verified'; return false;}
        if( empty($this->openFileHandle) ){$this->workingData = 'Working file not found'; return false;}
        $csvMimeTypes = array('application/vnd.ms-excel','text/plain','text/csv','text/tsv');
        if( filetype($this->fileHandle) === "file" && in_array($this->fileType, $csvMimeTypes) ) {
            // echo "FILE IS CSV";
            return true;
        }
        $this->workingData = 'failed in checkFileIsCsv somehow';
        return false;
    }

    public function getData(){
        return $this->workingData;
    }

    public function __destruct(){
        fclose($this->openFileHandle);
    }


}