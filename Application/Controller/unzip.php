<?php
$zip = new ZipArchive();
if ($zip->open('dupa.zip')) {
    $zip->extractTo('/public_html/SadCrmYay/');
    $zip->close();
    echo 'jest ogz 2';
}
else {
    echo 'nie oks';
}