<?php
$zip = new ZipArchive();
if ($zip->open('deploy.zip')) {
    $zip->extractTo('/public_html/SadCrmYay/');
    $zip->close();
    echo 'Deploy successful';
} else {
    echo 'Couldn\' open deploy.zip';
}