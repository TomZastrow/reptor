<?php
$method = $_SERVER['REQUEST_METHOD'];

$request = $_SERVER['REQUEST_URI'];
$request = str_replace("/collections/api.php/", "", $request);
$request = rtrim($request, "/");
echo "REQUEST: $request ---- \n";

$rec = split("/", $request);
echo "REC: " . sizeof($rec) . "\n";
echo var_dump($rec);

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
}

echo "<br><br>Verb: $verb";