<?php

header("Content-Type: text/plain");
deploy("SadCRM.zip", "public_html");

function deploy($zipName, $name)
{
    try {
        removeContentsFromFolder($name);
        echo "Removed contents folder $name.\n";

        echo "Extracting \"$zipName\"...\n";
        extractFromZip($zipName);

    } catch (Exception $exception) {
        echo "Could not deploy \"$name\". " . $exception->getMessage() . "\n";
    }
}

function removeContentsFromFolder($directoryName)
{
    if (!is_dir($directoryName)) {
        return;
    }
    $directory = opendir($directoryName);
    while (false !== ($file = readdir($directory))) {
        if (!in_array($file, ['.', '..'])) {
            $full = $directoryName . '/' . $file;
            if (is_dir($full)) {
                removeContentsFromFolder($full);
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
    if (!is_file($name)) {
        throw new Exception("File \"$name\" does not exists.");
    }
    $zip = new ZipArchive();
    try {
        if (!$zip->open($name)) {
            throw new Exception("Could not unzip \"$name\". " . $zip->getStatusString());
        }
        if (!$zip->extractTo(getcwd() . DIRECTORY_SEPARATOR)) {
            throw new Exception("Could not unzip \"$name\". " . $zip->getStatusString());
        }
        echo "Extracted.\n";
    } finally {
        $zip->close();
    }
}
