<?php

header("Content-Type: text/plain");

function removeFolder($directoryName)
{
    if (!is_dir($directoryName)) {
        throw new Exception("Directory doesn't exists \"$directoryName\".");
    }
    $directory = opendir($directoryName);
    while (false !== ($file = readdir($directory))) {
        if (!in_array($file, ['.', '..'])) {
            $full = $directoryName . '/' . $file;
            if (is_dir($full)) {
                removeFolder($full);
            } else {
                if (is_file($full)) {
                    if (!is_writable($full)) {
                        makeWritable($full);
                    }
                    unlink($full);
                }
            }
        }
    }
    closedir($directory);
    rmdir($directoryName);
}

function makeWritable($filename)
{
    if (!chmod($filename, 0777)) {
        throw new Exception("Found read-only file. Failed to change permission");
    }
}

function deployFor($name)
{
    echo "Removing folder $name...\n";
    try {
        removeFolder($name);
        echo "Removed folder $name.\n";
        mkdir($name);
        echo "Created empty folder $name\n";
    } catch (Exception $exception) {
        echo "Could not remove \"$name\"! " . $exception->getMessage() . "\n";
    }
}

deployFor("SadCRM");