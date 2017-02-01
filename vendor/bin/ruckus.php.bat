@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/../ruckusing/ruckusing-migrations/ruckus.php
php "%BIN_TARGET%" %*
