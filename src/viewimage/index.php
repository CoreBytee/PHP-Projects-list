<!DOCTYPE html>
<html lang="en">

<?php
$Hash = $_GET["hash"] ?? "";
$Image = glob(__DIR__ . "/../images/" . $Hash . ".png")[0] ?? "";
$imginfo = getimagesize($Image);
// header("Content-type: {$imginfo['mime']}");
// echo $Image;
header('Content-Length: ' . filesize($Image));
echo file_get_contents($Image);
?>