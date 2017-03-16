<?php

include("../generalFunctions.php");
include("../config.php");

$method = $_SERVER['REQUEST_METHOD'];
$request = $_SERVER['REQUEST_URI'];

$apipos = strpos($request, "api.php");
$base = substr($request,0,$apipos);
$request = substr($request, $apipos, strlen($request));
$request = str_replace("api.php/", "", $request);
$request = rtrim($request, "/");
$request = ltrim($request, "/");

$rec = explode("/", $request);
$path = "";
$datafile_name = $config['nameCollectionItems'];

if ($rec[sizeof($rec) - 1] == "members") {
    for ($i = 0; $i < sizeof($rec) - 1; $i++) {
        $path = $path . $rec[$i] . "/";
    }
} elseif (sizeof($rec) > 1 && $rec[sizeof($rec) - 2] == "members") {
    for ($i = 0; $i < sizeof($rec) - 2; $i++) {
        $path = $path . $rec[$i] . "/";
    }
} else {
    for ($i = 0; $i < sizeof($rec); $i++) {
        $path = $path . $rec[$i] . "/";
    }
}

$path = str_replace("collections", "../data", $path);

$FEATURES_TEMPLATE = <<<EOD
{
  "providesCollectionPids": false,
  "enforcesAccess": false,
  "supportsPagination": false,
  "asynchronousActions": false,
  "ruleBasedGeneration": false,
  "maxExpansionDepth": 0,
  "providesVersioning": false,
  "supportedCollectionOperations": [],
  "supportedModelTypes": ["https://github.com/RDACollectionsWG/Vocabulary/SimpleCollection"]
}
EOD;

$COLLECTION_TEMPLATE = <<<EOD
{
  "id": "",
  "capabilities": {
    "isOrdered": true,
    "appendsToEnd": true,
    "supportsRoles": false,
    "membershipIsMutable": true,
    "metadataIsMutable": true,
    "restrictedToType": "",
    "maxLength": -1
  },
  "properties": {
    "ownership": "ReptorDemoUser",
    "license": "https://creativecommons.org/licenses/by/4.0/",
    "modelType": "https://github.com/RDACollectionsWG/Vocabulary/SimpleCollection",
    "hasAccessRestrictions": false,
    "memberOf": [],
    "descriptionOntology": ""
  },
  "description": {}
}
EOD;

$MEMBER_TEMPLATE = <<<EOD
{
  "id": "",
  "location": "",
  "datatype": "",
  "ontology": "",
  "mappings": {
    "index": 0
   }
}
EOD;

$CODE_TEMPLATE = <<<EOD
{ 
  "code": "",
  "message": ""
}
EOD;

// --- Get the swagger spec 
if (sizeof($rec) == 1 && $rec[0] == "apidocs") {
  $spec = json_decode(file_get_contents("https://raw.githubusercontent.com/RDACollectionsWG/apidocs/master/swagger.json"));
  $base_uri = $base . "api.php";
  $spec->{'host'} = $_SERVER['SERVER_NAME'];
  $spec->{'basePath'} = $base_uri;
  $spec->{'schemes'} = [ "http" ];
  echo json_encode($spec) . "\n";
}

// --- List of all collections: curl -X GET http://localhost:8000/collections/api.php/collections
if (sizeof($rec) == 1 && $rec[0] == "collections" && $method == "GET") {
    $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator("../data"), RecursiveIteratorIterator::SELF_FIRST);
    $result = '{"contents" : [' . "\n";

    foreach ($objects as $name) {
        if (strpos($name->getPathname(), $datafile_name)) {
            $temp = str_replace("../data", "", $name);
            $temp = str_replace($datafile_name, "", $temp);
            $temp = str_replace("\\", "/", $temp);
            $temp = str_replace("/","",$temp);
            $obj = json_decode($COLLECTION_TEMPLATE);
            $obj->{'id'} = $temp;
            $result = $result . json_encode($obj,JSON_PRETTY_PRINT) . ",\n";
        }
    }
    
    $result = rtrim(trim($result), ',') . "\n]}\n";
    echo "$result";
}

// --- Dealing with members:
if (sizeof($rec) > 1 && $rec[sizeof($rec) - 1] === "members") {
    // --- Adding a new member to a collecltion: curl -X POST -d '{"id" : "abc"}'  http://localhost:8000/collections/api.php/collections/hPotos/members
    if ($method == "POST") {
        $postdata = file_get_contents("php://input");
        $data = json_decode($postdata, true);

        $collectionHandle = fopen($path . "/" . $datafile_name, "a") or die("Unable to open file!");
        fwrite($collectionHandle, $data["id"] . "\n");
        fclose($collectionHandle);
        $obj = json_decode($CODE_TEMPLATE);
        $obj->{'code'} = 'success';
        $obj->{'message'} = 'Added ' . $data["id"] . ' to ' . $request;
        $result = json_encode($obj,JSON_PRETTY_PRINT);
        echo $result . "\n";
    }
    
    // --- Getting all members of a collection: curl -X GET http://localhost:8000/collections/api.php/collections/Photos/members
    if ($method == "GET") {
        $temp = '{"contents" : [' . "\n";
        $index = 0;
        foreach (file($path . $datafile_name) as $line) {
            $line = trim($line);
            if(!$line == ""){
                $obj = json_decode($MEMBER_TEMPLATE);
                $obj->{'id'} = $line;
                $obj->{'mappings'}->{'index'} = $index++;
                $temp = $temp . json_encode($obj,JSON_PRETTY_PRINT) . ",\n";
            }
        }
        $temp = rtrim(trim($temp), ',') . "\n]}\n";
        echo $temp;
    }
}

// --- Delete a member: curl -X DELETE http://localhost:8000/collections/api.php/collections/Photos/members/123
if (sizeof($rec) > 1 && $rec[sizeof($rec) - 2] === "members" && $method == "DELETE") {
    $itemToDelete = urldecode($rec[sizeof($rec) - 1]);
    $collectionFile = $path . $datafie_name;
    $obj = json_decode($CODE_TEMPLATE);
    if (file_exists($collectionFile)) {
        $items = file($collectionFile, FILE_IGNORE_NEW_LINES);

        $collectionFileHandle = fopen($collectionFile, "w");
        foreach ($items as $item) {
            if ($item != $itemToDelete) {
                fwrite($collectionFileHandle, $item);
                fwrite($collectionFileHandle, "\n");
            }
        }

        fclose($collectionFileHandle);
        $obj->{'code'} = 'success';
        $obj->{'message'} = "All appearances of item $itemToDelete in collection $collectionFile were deleted";
    } else {
        $obj->{'code'} = 'error';
        $obj->{'message'} = "Collection $collectionFile does not exist";
        $result = json_encode($obj,JSON_PRETTY_PRINT);
    }
    $result = json_encode($obj,JSON_PRETTY_PRINT);
    echo $result . "\n";
}

// --- Deleting a collection: curl -X DELETE http://localhost:8000/collections/api.php/collections/Photos/Winter/
if (sizeof($rec) > 1 && $rec[sizeof($rec) - 2] !== "members" && $rec[sizeof($rec) - 1] !== "members" && $method == "DELETE") {
    $collectionFile = $path . $datafile_name;
    $obj = json_decode($CODE_TEMPLATE);
    if (file_exists($collectionFile)) {
        unlink($collectionFile);
        $obj->{'code'} = 'success';
        $obj->{'message'} = "Collection $collectionFile was successfully deleted";
    } else {
        $obj->{'code'} = 'error';
        $obj->{'message'} = "Collection $collectionFile does not exists and cant be deleted";
    }
    $result = json_encode($obj,JSON_PRETTY_PRINT);
    echo $result . "\n";
    $verb = "DeleteCollection";
}

// --- Creating a collection: curl -X POST http://localhost:8000/collections/api.php/collections/xxx
if (sizeof($rec) == 1 && $rec[0] == "collections" && $method == "POST") {
    $postdata = file_get_contents("php://input");

    $data = json_decode($postdata, true);
    $path = $path . "/" . $data["id"]; 
    $obj = json_decode($CODE_TEMPLATE);
    if (!file_exists($path)) {
        mkdir($path, 0777, true);
        touch($path . "/" . $datafile_name);
        $obj->{'code'} = 'success';
        $obj->{'message'} = "Collection $path created";
    } else if (!file_exists($path . "/" . $datafile_name)) {
        touch($path . "/" . $datafile_name);
        $obj->{'code'} = 'success';
        $obj->{'message'} = "Collection $path created";
    } else if (file_exists($path . "/" . $datafile_name)) {
        $obj->{'code'} = 'error';
        $obj->{'message'} = "Collection $path exists";
    }
    $result = json_encode($obj,JSON_PRETTY_PRINT);
    echo $result . "\n";
}
