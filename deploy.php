<?php

header("Content-Type: text/plain");
deploy("SadCRM");

function deploy($name)
{
    try {
        removeFolder($name);
        echo "Removed folder $name.\n";

        mkdir($name);
        echo "Created empty folder $name\n";

        echo "Extracting \"$name.zip\"...\n";
        extractFromZip($name);

    } catch (Exception $exception) {
        echo "Could not deploy \"$name\". " . $exception->getMessage() . "\n";
    }
}

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
    rmdir($directoryName);
}

function makeWritable($filename)
{
    echo "\"$filename\" is not writable. Changing permission...\n";
    if (!chmod($filename, 0777)) {
        throw new Exception("Found read-only file. Failed to change permission");
    }
}

function extractFromZip($name)
{
    $zip = new ZipArchive();
    if ($zip->open("$name.zip")) {
        echo $zip->getStatusString() . "\n";
        $zip->extractTo(DIRECTORY_SEPARATOR . "$name" . DIRECTORY_SEPARATOR);
        $zip->close();
        echo "Extracted.\n";
    } else {
        throw new Exception("Could not unzup \"$name.zip\". " . $zip->getStatusString());
    }
}
