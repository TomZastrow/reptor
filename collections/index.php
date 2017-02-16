<?php

$method = $_SERVER['REQUEST_METHOD'];

$request = $_SERVER['REQUEST_URI'];
#$request = str_replace("/collections/api.php/", "", $request);
$request = rtrim($request, "/");
echo "REQUEST: $request";
$rec = split("/", $request);
#echo "REC: " . sizeof($rec) . "\n";
$itemName = "";

if(sizeof($rec) == 2){
$rec[1] = "./store/" . $rec[1];
}


if(sizeof($rec) == 3){
$itemName = $rec[2];
$rec[1] = "./store/" . $rec[1];
}

#echo "REQUEST: $request\n";