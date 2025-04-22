<?php 

class bundleProcessor {
    protected $dirObj;
    protected $iterator;

    public function __construct($directory) {
        $this->iterator = new RecursiveIteratorIterator(
            $this->dirObj = new RecursiveDirectoryIterator($directory,
            RecursiveDirectoryIterator::SKIP_DOTS, RecursiveDirectoryIterator::SELF_FIRST )
        );
	}

    public function getBundleArrayByID($id) { //returns array of bundle names
        $array = array();
        foreach ($iterator as $file) {
            if ($file->isFile() && str_contains($file->getFilename(), $id)) {
                $array[] = $file->getFilename();
            }
        }
        return $array;
    }
 
    public function getBundlePathByNameStr($bundle_name) {
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getFilename() == $bundle_name){
                return $file->getPathname();
            }
        }
        echo "Error: Could Not Find Bundle Name".PHP_EOL;
        return null;
    }

    public function changeBundleName($oldBundleName, $newName) { //returns boolean
        foreach ($iterator as $file) {
            if ($file-> isFile() && $file.getFilename() == $oldBundleName) {
                $oldPathName = $file->getRealPath();
                $newPathName = $file->getRealPath() . DIRECTORY_SEPARATOR . $newName;
                return rename($oldPathName, $newPathName);
            }
        }
        echo "Could Not Find Old Bundle".PHP_EOL;
        return false;
    }

    public function moveBundleToDir($bundle) {
        
    }


}



?>