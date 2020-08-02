<?php

define('FILE_REPO', __DIR__ . '\PlacesDetailJson\\'); 
define('BAD_PATHCONTENTS', '/^"([-\.\w]+|)$/');

class FileIO 
{
    private $file_name;
    private $file_path;

    function __construct($file_name) {
        $this->file_name = $this->filter_filename($file_name);
        $this->file_path = FILE_REPO . $this->file_name . '.json';
    }

    // Writes into an existing file, or creates new file
    public function WriteFile($data) {
        $file = fopen($this->file_path, 'w');
        fwrite($file, $data);
        fclose($file);
    }


//// OPEN-SOURCE REFERENCE:ONLY CLEANS GENERATED FILE NAME SO IT'S NOT INCOMPATIBLE W/ WINDOWS
    // REFERENCE FROM https://stackoverflow.com/questions/2021624/string-sanitizer-for-filename
    // Open-Source, just cleans file names
    private function filter_filename($filename, $beautify=true) {
        // sanitize filename
        $filename = preg_replace(
            '~
            [<>:"/\\|?*]|            # file system reserved https://en.wikipedia.org/wiki/Filename#Reserved_characters_and_words
            [\x00-\x1F]|             # control characters http://msdn.microsoft.com/en-us/library/windows/desktop/aa365247%28v=vs.85%29.aspx
            [\x7F\xA0\xAD]|          # non-printing characters DEL, NO-BREAK SPACE, SOFT HYPHEN
            [#\[\]@!$&\'()+,;=]|     # URI reserved https://tools.ietf.org/html/rfc3986#section-2.2
            [{}^\~`]                 # URL unsafe characters https://www.ietf.org/rfc/rfc1738.txt
            ~x',
            '-', $filename);
        // avoids ".", ".." or ".hiddenFiles"
        $filename = ltrim($filename, '.-');
        // optional beautification
        if ($beautify) $filename = $this->beautify_filename($filename);
        // maximize filename length to 255 bytes http://serverfault.com/a/9548/44086
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $filename = mb_strcut(pathinfo($filename, PATHINFO_FILENAME), 0, 255 - ($ext ? strlen($ext) + 1 : 0), mb_detect_encoding($filename)) . ($ext ? '.' . $ext : '');
        return $filename;
    }
    // REFERENCE FROM https://stackoverflow.com/questions/2021624/string-sanitizer-for-filename
    // Open-Source, just cleans file names
    function beautify_filename($filename) {
        // reduce consecutive characters
        $filename = preg_replace(array(
            // "file   name.zip" becomes "file-name.zip"
            '/ +/',
            // "file___name.zip" becomes "file-name.zip"
            '/_+/',
            // "file---name.zip" becomes "file-name.zip"
            '/-+/'
        ), '-', $filename);
        $filename = preg_replace(array(
            // "file--.--.-.--name.zip" becomes "file.name.zip"
            '/-*\.-*/',
            // "file...name..zip" becomes "file.name.zip"
            '/\.{2,}/'
        ), '.', $filename);
        // lowercase for windows/unix interoperability http://support.microsoft.com/kb/100625
        $filename = mb_strtolower($filename, mb_detect_encoding($filename));
        // ".file-name.-" becomes "file-name"
        $filename = trim($filename, '.-');
        return $filename;
    }

}

?>