<?php 

class bundleProcessor {
    protected $dirObj;
    protected $iterator;

    public function __construct($directory) {
        $this->dirObj = new RecursiveDirectoryIterator($directory);
        $this->iterator = new RecursiveIteratorIterator($this->iterator);
	}

    public function getBatchPath($batch_name) {
        foreach ($iterator as $bundle) {
            if ($file-> isFile() && $file.getFilename() == $batch_name){
                return $file->getPathname();
            }
        }
    }
}



?>