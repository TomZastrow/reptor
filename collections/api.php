<?php
include("../generalFunctions.php");
$method = $_SERVER['REQUEST_METHOD'];

$request = $_SERVER['REQUEST_URI'];

$apipos = strpos($request, "api.php");
$request = substr($request, $apipos, strlen($request));
$request = str_replace("api.php/", "", $request);
$request = rtrim($request, "/");
$request = ltrim($request, "/");

//echo "Request: $request \n";

$rec = split("/", $request);
$path = "";

if ($rec[sizeof($rec) - 1] == "members") {
    echo "LAST MEMBER";
    for($i = 0; $i < sizeof($rec)-1; $i++){
        $path = $path . $rec[$i] . "/";
    }
} elseif ($rec[sizeof($rec) - 2] == "members") {
    echo "SECOND LAST MEMBER";
    for($i = 0; $i < sizeof($rec)-2; $i++){
        $path = $path . $rec[$i] . "/";
    }
} else {
    for($i = 0; $i < sizeof($rec); $i++){
        $path = $path . $rec[$i] . "/";
    }    
}

$path = str_replace("collections", "../data", $path);
//$path = str_replace("/members", "", $path);


//echo "Path: $path \n";
//echo "REC: " . sizeof($rec) . "\n";
//echo var_dump($rec) . "\n";

$verb = "??";

echo "PATH: " . $path . "\n";

if (sizeof($rec) == 1 && $rec[0] == "collections") {
            $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator("../data"), RecursiveIteratorIterator::SELF_FIRST);
        $result = "";
        
        foreach ($objects as $name) {
            
                
                //if (file_exists($name . "collection.txt")) {
            if( strpos(  $name->getPathname(), "collection.txt")){
                $temp = str_replace("../data", "", $name);
                $temp = str_replace("collection.txt", "", $temp);
                $temp = str_replace("\\", "/", $temp);
                 $result = $result . $temp . "\n";
                }
        
        }
        echo "$result";
    $verb = "GetListOfCollections";
}

if (sizeof($rec) > 1 && $rec[sizeof($rec) - 1] === "members") {
    if ($method == "POST") {
        $postdata = file_get_contents("php://input");
        $data = json_decode($postdata, true);

        $collectionHandle = fopen($path . "/collection.txt", "a") or die("Unable to open file!");
        fwrite($collectionHandle, $data["id"] . "\n");
        fclose($collectionHandle);
        $verb = "AddMember";
        echo '{"success" : "Added ' . $data["id"] . ' to ' . $request . '"}' . "\n";
    }
    if ($method == "GET") {
        $temp = '{"contents" : [' . "\n";
        foreach (file($path . "collection.txt") as $line) {
            $line = trim($line);
            $temp = $temp . '{"id" : "' . $line . "\"},\n";
        }
        $temp = rtrim(trim($temp), ',') . "]}";
        echo $temp;
        $verb = "GetMembers";
    }
}

if (sizeof($rec) > 1 && $rec[sizeof($rec) - 2] === "members" && $method == "DELETE") {
    $itemToDelete = $rec[sizeof($rec) - 1];
    $collectionFile = $path . "collection.txt";
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
        echo "{\"success\" : \" All appearances of item $itemToDelete in collection $collectionFile were deleted\"}\n";
    } else {
        echo "{\"error\" : \" Collection $collectionFile does not exists\"}\n";
    }
    $verb = "DeleteMember";
}

if (sizeof($rec) > 1 && $rec[sizeof($rec) - 2] !== "members" && $rec[sizeof($rec) - 1] !== "members" && $method == "DELETE") {
    $collectionFile = $path . "collection.txt";
    echo "COLLFILE: " . $collectionFile;
    if(file_exists($collectionFile)){
         unlink($collectionFile);
                echo "{\"success\" : \" Collection $collectionFile was successfully deleted\"}\n";

        } else {
                echo "{\"error\" : \" Collection $collectionFile does not exists and cant be deleted\"}\n";
        }
    $verb = "DeleteCollection";
}

if (sizeof($rec) > 1 && $rec[sizeof($rec) - 2] !== "members" && $rec[sizeof($rec) - 1] !== "members" && $method == "POST") {
    $verb = "CreateCollection";
    if (!file_exists($path)) {
        mkdir($path, 0777, true);
        touch($path . "/collection.txt");
        echo "{\"success\" : \"Collection created\"}\n";
    } else if (!file_exists($path . "/collection.txt")) {
        touch($path . "/collection.txt");
        echo "{\"success\" : \"Collection created\"}\n";
    } else if (file_exists($path . "/collection.txt")) {
        echo "{\"error\" : \"Collection exists\"}\n";
    }
}

//echo "<br><br>Verb: $verb <br>\n";
