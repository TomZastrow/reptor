<?php
include('../generalFunctions.php');
include('../config.php');

// ------------- Generic function for getting the folders:
function getFolderList() {
    $fileSystemPath = realpath('../data');
    try {
        $thisUrl = "http" . (!empty($_SERVER['HTTPS']) ? "s" : "") . "://" . $_SERVER['SERVER_NAME'];
        $scriptPath = realpath(dirname(__FILE__));
        $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($fileSystemPath), RecursiveIteratorIterator::SELF_FIRST);
        $result = "{ \"0\" : \"/data\",";
        $counter = 0;
        foreach ($objects as $name => $object) {
            if (is_dir($name)) {
                $counter = $counter + 1;
                if (!endsWithChar($name, ".")) {
                    $name = str_replace($fileSystemPath, "", $name);
                    $name = str_replace("\\", "/", $name);
                    $result = $result . "\"" . $counter . "\" : \"/data" . $name . "\", ";
                }
            }
        }
    } catch (Exception $e) {
        echo 'Exception: ', $e->getMessage(), "\n";
    }
    $result = rtrim($result, ", ");
    $result = $result . "}";
    return $result;
}
// ------------- Metadata - Plain
function getMetadataPlain($path) {
    global $config;
    $theFile = ".." . $path . "/" . $config['nameMetadataText'];
    $result = "";
    if (file_exists($theFile)) {
        $result = file_get_contents($theFile);
    }
    return $result;
}
function saveMetadataPlain($path) {
    global $config;
    $path = ".." . $path . "/" . $config['nameMetadataText'];
    $theText = $_POST['Text'];
    $theFile = fopen($path, "w");
    fwrite($theFile, $theText);
    fclose($theFile);
    return "Plain metadata saved in:  " . $path . $theText;
}
// ------------- Metadata - Types
function getMetadataTypes($path) {
    global $config;
    $theFile = ".." . $path . "/" . $config['namesDataTypes'];
    $result = "";
    if (file_exists($theFile)) {
        $result = file_get_contents($theFile);
    }
    return $result;
}
function saveMetadataTypes($path) {
    global $config;
    $path = ".." . $path . "/" . $config['namesDataTypes'];
    $theText = $_POST['Text'];
    $theFile = fopen($path, "w");
    fwrite($theFile, $theText);
    fclose($theFile);
    return "Type metadata saved in:  " . $path . $theText;
}

// ------------- Metadata - DC


function getMetadataDC($path) {
    global $config;
    $theFile = ".." . $path . "/" . $config['nameDCFile'];
    $result = "RESULT: " . $theFile;
    if (file_exists($theFile)) {
        $dcFile = parse_ini_file($theFile);
        $result = json_encode($dcFile);
    }
    return $result;
}
function saveMetadataDC($path) {
    global $config;
    $path = ".." . $path . "/" . $config['nameDCFile'];
    $data = json_decode($_POST['json']);
    $theFile = fopen($path, "w");
    foreach ($data as $key => $value) {
        fwrite($theFile, $key . "=" . $value . "\n");
    }
    fclose($theFile);
    return "Dublin Core metadata saved in:  " . $path;
}

// ------------- Metadata - Collections

function getCollectionItems($path) {
    global $config;
    $theFile = ".." . $path .  "/" . $config['nameCollectionItems'];
    $result = "";
    if (file_exists($theFile)) {
        $result = file_get_contents($theFile);
    }
    return $result;
}

function saveCollectionItems($path) {
    global $config;
    $path = ".." . $path . "/" . $config['nameCollectionItems'];
    $theText = $_POST['Text'];
    $theFile = fopen($path, "w");
    fwrite($theFile, $theText);
    fclose($theFile);

    return "Collection items saved in:  " . $path . $theText;
}

// ---------------------------------
// ---------- Anylizing the verbs:
// ---------------------------------
$verb = $_GET['verb'];
if ($verb == "getFolderList") {
    echo getFolderList();
}

// --- Plain:
if ($verb == "getMetadataPlain") {
    echo getMetadataPlain($_GET['path']);
}
if ($verb == "saveMetadataPlain") {
    echo saveMetadataPlain($_GET['path']);
}

// --- Types:
if ($verb == "getMetadataTypes") {
    echo getMetadataTypes($_GET['path']);
}
if ($verb == "saveMetadataTypes") {
    echo saveMetadataTypes($_GET['path']);
}

// --- Dublin Core:
if ($verb == "getMetadataDC") {
    echo getMetadataDC($_GET['path']);
}
if ($verb == "saveMetadataDC") {
    echo saveMetadataDC($_GET['path']);
}

// --- Collection Items:
if ($verb == "getCollectionItems") {
    echo getCollectionItems($_GET['path']);
}

if ($verb == "saveCollectionItems") {
    echo saveCollectionItems($_GET['path']);
}
