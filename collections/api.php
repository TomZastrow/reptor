<?php
$method = $_SERVER['REQUEST_METHOD'];

$request = $_SERVER['REQUEST_URI'];

$apipos = strpos($request, "api.php");
$request = substr($request, $apipos, strlen($request));
$request = str_replace("api.php/", "", $request);
$request = rtrim($request, "/");
$request = ltrim($request, "/");

echo "Request: $request \n";

$path = str_replace("collections", "../data", $request);

echo "Path: $path \n";


$rec = split("/", $request);
echo "REC: " . sizeof($rec) . "\n";
echo var_dump($rec) . "\n";

$verb = "??";

if(sizeof($rec) == 1 && $rec[0] == "collections"){
    $verb = "GetListOfCollections";
}

if(sizeof($rec) > 1 && $rec[sizeof($rec)-1] ===  "members"){
    if($method == "POST"){
        $verb = "AddMember";
    }
    if($method == "GET"){
        $verb = "GetMembers";
    }
}

if(sizeof($rec) > 1 && $rec[sizeof($rec)-2] ===  "members" && $method == "DELETE" ){
    $verb = "DeleteMember";
}

if(sizeof($rec) > 1 && $rec[sizeof($rec)-2] !==  "members" && $rec[sizeof($rec)-1] !==  "members" && $method == "DELETE" ){
    $verb = "DeleteCollection";
}

if(sizeof($rec) > 1 && $rec[sizeof($rec)-2] !==  "members" && $rec[sizeof($rec)-1] !==  "members" && $method == "POST" ){
    $verb = "CreateCollection";
	if (!file_exists($path)) {
    mkdir($path, 0777, true);
	touch($path . "/collection.txt");
	echo "{\"success\" : \"Collection created\"}\n";
}
}

echo "<br><br>Verb: $verb <br>\n";
