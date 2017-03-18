<?php

header("Content-Type: text/plain");
deploy("SadCRM");

function deploy($name)
{
    try {
        removeFromFolder($name);
        echo "Removed contents folder $name.\n";

        echo "Extracting \"$name.zip\"...\n";
        extractFromZip($name);

    } catch (Exception $exception) {
        echo "Could not deploy \"$name\". " . $exception->getMessage() . "\n";
    }
}

function removeFromFolder($directoryName)
{
    if (!is_dir($directoryName)) {
        return;
    }
    $directory = opendir($directoryName);
    while (false !== ($file = readdir($directory))) {
        if (!in_array($file, ['.', '..'])) {
            $full = $directoryName . '/' . $file;
            if (is_dir($full)) {
                removeFromFolder($full);
                rmdir($full);
            } else {
                if (is_file($full) || is_link($full)) {
                    if (!is_writable($full)) {
                        makeWritable($full);
                    }
                    unlink($full);
                }
            }
        }
    }
    closedir($directory);
}

function makeWritable($filename)
{
    echo "\"$filename\" is not writable. Changing permission...";
    if (chmod($filename, 0777)) {
        echo " Ok.\n";
    } else {
        echo "Failed to change permission\n";
    }
}

function extractFromZip($name)
{
    if (!is_file("$name.zip")) {
        throw new Exception("File \"$name.zip\" does not exists.");
    }
    $zip = new ZipArchive();
    try {
        if (!$zip->open("$name.zip")) {
            throw new Exception("Could not unzip \"$name.zip\". " . $zip->getStatusString());
        }
        if (!$zip->extractTo(getcwd() . DIRECTORY_SEPARATOR)) {
            throw new Exception("Could not unzip \"$name.zip\". " . $zip->getStatusString());
        }
        echo "Extracted.\n";
    } finally {
        $zip->close();
    }
}
