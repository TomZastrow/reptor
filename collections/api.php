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
$collection_file_name = $config['nameCollectionMetadata'];
$member_file_name = $config['nameCollectionItems'];

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

$MEMBER_FILE_TEMPLATE = <<<EOD
{ "contents" : [] }
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
  echo json_encode($spec,JSON_PRETTY_PRINT) . "\n";
}

// --- Get service features
if (sizeof($rec) == 1 && $rec[0] == "features") {
  echo $FEATURES_TEMPLATE . "\n";
}

// --- List of all collections: curl -X GET http://localhost:8000/collections/api.php/collections
if (sizeof($rec) == 1 && $rec[0] == "collections" && $method == "GET") {
    $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator("../data"), RecursiveIteratorIterator::SELF_FIRST);
    $coll = json_decode('{ "contents" : [] }');
    foreach ($objects as $name) {
        if (strpos($name->getPathname(), $collection_file_name)) {
            $file = json_decode(file_get_contents($name));
            if ($file) {
                array_push($coll->{'contents'},$file);
            }
        }
    }
    
    $result = json_encode($coll,JSON_PRETTY_PRINT);
    echo $result . "\n";
}

// --- Dealing with members:
if (sizeof($rec) > 1 && $rec[sizeof($rec) - 1] === "members") {
    // --- Adding a new member to a collecltion: curl -X POST -d '{"id" : "abc"}'  http://localhost:8000/collections/api.php/collections/hPotos/members
    if ($method == "POST") {
        $postdata = file_get_contents("php://input");
        $data = json_decode($postdata, true);
        $obj = json_decode($CODE_TEMPLATE);
    
        if (! $data{'id'}) {
            $obj->{'code'} = 'error';
            $obj->{'message'} = "Invalid input - unable to parse id from data";
        } else {
            $default = json_decode($MEMBER_TEMPLATE,true);
            $replaced = array_replace_recursive((array) $default, (array) $data);
        
            $json_str = file_get_contents($path . $member_file_name) or die("Unable to read file! " . $path . $member_file_name);
            $members = json_decode($json_str);
            array_push($members->{'contents'},(object) $replaced);


            // rewrite the members file
            $members_str = json_encode($members,JSON_PRETTY_PRINT);
            $collectionFileHandle = fopen($path . $member_file_name, "w");
            fwrite($collectionFileHandle, $members_str);
            fclose($collectionFileHandle);
            $obj->{'code'} = 'success';
            $obj->{'message'} = 'Added ' . $data["id"] . ' to ' . $request;
        }
        $result = json_encode($obj,JSON_PRETTY_PRINT);
        echo $result . "\n";
    }
    
    // --- Getting all members of a collection: curl -X GET http://localhost:8000/collections/api.php/collections/Photos/members
    if ($method == "GET") {
        $json_str  = file_get_contents($path . $member_file_name) or die("Invalid collection");
        $data = json_decode($json_str, true);
        $indexed = [];
        $index = 0;
        foreach ($data{'contents'} as $obj) {
            $obj{'mappings'}{'index'} = $index;
            $index = $index + 1;
            array_push($indexed,$obj);
        } 
        $data{'contents'} = $indexed;
        $result = json_encode($data,JSON_PRETTY_PRINT);
        echo $result . "\n";
    }
}

// --- Delete a member: curl -X DELETE http://localhost:8000/collections/api.php/collections/Photos/members/123
if (sizeof($rec) > 1 && $rec[sizeof($rec) - 2] === "members" && $method == "DELETE") {
    $itemToDelete = urldecode($rec[sizeof($rec) - 1]);
    $collectionFile = $path . $member_file_name;
    $obj = json_decode($CODE_TEMPLATE);
    $obj->{'code'} = 'error';
    $deleted = false;
    if (file_exists($collectionFile)) {
        $json_str  = file_get_contents($path . $member_file_name) or die("Invalid collection");
        $data = json_decode($json_str, true);
        foreach ($data{'contents'} as $index=>$item) {
            if ($item{'id'} == $itemToDelete) {
                array_splice($data{'contents'},$index,1);
                $deleted = true;
                break;
            }
        }
        if ($deleted) {
            // rewrite the members file
            $members_str = json_encode($data,JSON_PRETTY_PRINT);
            $collectionFileHandle = fopen($path . $member_file_name, "w");
            fwrite($collectionFileHandle, $members_str);
            fclose($collectionFileHandle);
            $obj->{'message'} = "All appearances of item $itemToDelete in collection $collectionFile were deleted";
            $obj->{'code'} = 'success';
        } else {
            $obj->{'message'} = "Member $itemToDelete is not found in $collectionFile";
        }
    } else {
        $obj->{'message'} = "Collection $collectionFile does not exist";
    }
    $result = json_encode($obj,JSON_PRETTY_PRINT);
    echo $result . "\n";
}

// --- Get a member of a collection : curl -X GET http://localhost:8000/collections/api.php/collections/{id}
if (sizeof($rec) > 1 && $rec[sizeof($rec) - 2] !== "members" && $rec[sizeof($rec) - 1] !== "members" && $method == "GET") {
    $collectionFile = $path . $collection_file_name;
    if (file_exists($collectionFile)) {
        $result =  file_get_contents($collectionFile);
    } else {
        $obj = json_decode($CODE_TEMPLATE);
        $obj->{'message'} = "Not found";
        $obj->{'code'} = "error";
        $result = json_encode($obj,JSON_PRETTY_PRINT);
    }
    echo $result . "\n";
}

// --- Deleting a collection: curl -X DELETE http://localhost:8000/collections/api.php/collections/{id}
if (sizeof($rec) > 1 && $rec[sizeof($rec) - 2] !== "members" && $rec[sizeof($rec) - 1] !== "members" && $method == "DELETE") {
    $collectionFile = $path . $collection_file_name;
    $memberFile = $path . $member_file_name;
    $obj = json_decode($CODE_TEMPLATE);
    if (file_exists($memberFile)) {
        unlink($memberFile);
    }
    if (file_exists($collectionFile)) {
        unlink($collectionFile);
        rmdir($path);
        $obj->{'code'} = 'success';
        $obj->{'message'} = "Collection $collectionFile was successfully deleted";
    } else {
        $obj->{'code'} = 'error';
        $obj->{'message'} = "Collection $collectionFile does not exists and cant be deleted";
    }
    $result = json_encode($obj,JSON_PRETTY_PRINT);
    echo $result . "\n";
}

// --- Creating a collection: curl -X POST http://localhost:8000/collections/api.php/collections
// with data adhering to $COLLECTION_TEMPLATE
//
if (sizeof($rec) == 1 && $rec[0] == "collections" && $method == "POST") {
    $postdata = file_get_contents("php://input");

    $data = json_decode($postdata, true);
    $obj = json_decode($CODE_TEMPLATE);

    if (! $data{'id'}) {
        $obj->{'code'} = 'error';
        $obj->{'message'} = "Invalid input - unable to parse id from data";
    } else {
        $path = $path . "/" . $data["id"]; 
        $default = json_decode($COLLECTION_TEMPLATE,true);
        $replaced = array_replace_recursive((array) $default, (array) $data);
        $coll_str = json_encode((object)$replaced,JSON_PRETTY_PRINT);

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        if (!file_exists($path . "/" . $collection_file_name)) {
            $collectionFileHandle = fopen($path . "/" . $collection_file_name, "w");
            fwrite($collectionFileHandle, $coll_str);
            fclose($collectionFileHandle);
            $memberFileHandle = fopen($path . "/" . $member_file_name, "w");
            fwrite($memberFileHandle, $MEMBER_FILE_TEMPLATE);
            fclose($memberFileHandle);
            $obj->{'code'} = 'success';
            $obj->{'message'} = "Collection $path created";
        } else {
            $obj->{'code'} = 'error';
            $obj->{'message'} = "Collection $path exists";
        }
    }

    $result = json_encode($obj,JSON_PRETTY_PRINT);
    echo $result . "\n";
}
