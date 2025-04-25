<?php 

class bundleProcessor {
    protected $dirObj;
    protected $iterator;

    public function __construct($directory) {
        $this->dirObj = new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS);

        $this->iterator = new RecursiveIteratorIterator($this->dirObj, RecursiveIteratorIterator::SELF_FIRST);
	}

    public function getBundleArrayByID($id) { //returns array of bundle names
        $array = array();
        $this->iterator->rewind();
        foreach ($this->iterator as $file) {
            if ($file->isFile() && str_contains($file->getFilename(), $id)) {
                $array[] = $file->getFilename();
            }
        }
        return $array;
    }
 
    public function getBundlePathByNameStr($bundle_name) { //returns getrealPath Str
        $this->iterator->rewind();
        foreach ($this->iterator as $file) {
            if ($file->isFile() && $file->getFilename() == $bundle_name){
                return $file->getRealPath();
            }
        }
        echo "Bundle Processor Error: Could Not Find Path with given Str: ".$bundle_name.PHP_EOL;
        return null;
    }

    public function changeBundleName($oldBundleName, $newName, $folder= 'None') { //returns boolean | can be used to mv bundles to subfolder in current directory
        $this->iterator->rewind();
        foreach ($this->iterator as $file) {
            if ($file-> isFile() && $file->getFilename() == $oldBundleName) {
                $oldPathName = $file->getRealPath();
                if ($folder == 'None') {$newPathName = $file->getPath(). DIRECTORY_SEPARATOR.$newName; } 
                    else { $newPathName = $file->getPath().DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR.$newName;}
                
                return rename($oldPathName, $newPathName);
            }
        }
        echo "Bundle Processor Error: Could Not Find Old Bundle File with given Str:".$oldBundleName.PHP_EOL;
        return false;
    }

   


}



?>