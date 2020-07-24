<?php

define('FILE_REPO', __DIR__ . '\PlacesDetailJson\\'); 

class FileIO 
{
    private $file_name;
    private $file_path;

    function __construct($file_name) {
        $this->file_name = $file_name;
        $this->file_path = FILE_REPO . $file_name . '.json';
    }

    // Writes into an existing file, or creates new file
    public function WriteFile($data) {
        $file = fopen($this->file_path, 'w');
        fwrite($file, $data);
        fclose($file);
    }

}

?>