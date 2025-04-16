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

    public function getBundlePath($bundle_name) {
        foreach ($iterator as $file) {
            if ($file-> isFile() && $file.getFilename() == $bundle_name){
                return $file->getPathname();
            }
        }
        echo "Could Not Find Bundle".PHP_EOL;
    }

    public function changeBundleName($oldBundleName, $newName) { //returns boolean
        foreach ($iterator as $file) {
            if ($file-> isFile() && $file.getFilename() == $oldbundleName) {
                $oldPathName = $file.getRealPath();
                $newPathName = $file->getPath() . DIRECTORY_SEPARATOR . $newName;
                return rename($oldPathName, $newPathName);
            }
        }
        echo "Could Not Find Old Bundle".PHP_EOL;
        return false;
    }


}



?>