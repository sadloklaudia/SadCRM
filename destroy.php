<?php
$directory = "SadCRM";

array_map('unlink', glob("$directory/*.*"));
rmdir($directory);