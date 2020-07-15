<?php


$rootDirectory = $_SERVER['argv'][1] ?? '';

if (!$rootDirectory) {
    die('Specify root directory');
}


function p($data)
{
    return print_r($data);
}

function isEmpty($dir)
{

    if (in_array(basename($dir), ['__MACOSX'])) {
        return true;
    }


    try {
        $di = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
        // echo iterator_count($di) . PHP_EOL;
        if (iterator_count($di) === 0) {
            return true;
        }

        if (iterator_count($di) === 1) {
            // check that the only file in this folder is .desktop.ini
            return count(array_diff(scandir($dir), ['.', '..', 'desktop.ini', 'Thumbs.ini'])) === 0;
        }
    } catch (Exception $exception) {
    }
    return false;
}

 function delete_files($dir) { 
   if (is_dir($dir)) { 
     $objects = scandir($dir);
     foreach ($objects as $object) { 
       if ($object != "." && $object != "..") { 
         if (is_dir($dir. DIRECTORY_SEPARATOR .$object) && !is_link($dir."/".$object))
           delete_files($dir. DIRECTORY_SEPARATOR .$object);
         else
           unlink($dir. DIRECTORY_SEPARATOR .$object); 
       } 
     }
     rmdir($dir); 
   } 
 }

function getDirectoryList($path)
{
    $files = array_diff(scandir($path), ['.', '..']);
    $foldersOnly = array_filter($files, fn ($file) => is_dir($path . DIRECTORY_SEPARATOR . $file));

    // if empty remove
    static $contents = [];
    $contents = array_merge(
        $contents,
        array_filter($foldersOnly, fn ($item) => isEmpty($path . DIRECTORY_SEPARATOR . $item))
    );

    return $contents;
}

$toBeDeleted = getDirectoryList($rootDirectory);

foreach ($toBeDeleted as $directory) {
    echo ($rootDirectory . DIRECTORY_SEPARATOR . $directory);
    delete_files($rootDirectory . DIRECTORY_SEPARATOR . $directory);
}
