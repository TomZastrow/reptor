<?php
header('Content-type: text/xml');
header('Pragma: public');
header('Cache-control: private');
header('Expires: -1');
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";

include("oaipmh-functions.php");
include("config.php");
global $config;

$verb = $_GET['verb'];
$metadataPrefix = $_GET['metadataPrefix'];
$identifier = $_GET['identifier'];
$from = $_GET['from'];
$set = $_GET['set'];


$datadir = "data" . $identifier;
$dcFile = realpath($datadir . $config['nameDCFile']);
$dcFile = realpath(dirname(__FILE__)) . "/" . $datadir . $config['nameDCFile'];
$thisUrl = "http" . (!empty($_SERVER['HTTPS']) ? "s" : "") . "://" . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'];
?> 

<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/" 
         xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
         xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/ 
         http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd">

    <?php
    $date = date("c", time());
    echo "<responseDate>$date</responseDate>\n";

    if ($verb == "GetRecord") {
        getRecord($identifier, $metadataPrefix, $thisUrl, $dcFile);
    }

    if ($verb == "Identify") {
        identify($thisUrl);
    }

    if ($verb == "ListMetadataFormats") {
        getMetadataFormats($thisUrl);
    }

    if ($verb == "ListSets") {
        getListSets($thisUrl);
    }
    
    if ($verb == "ListRecords"){
        getListRecords($thisUrl, $from, $set, $metadataPrefix, false);
    }
    
        if ($verb == "ListIdentifiers"){
        getListRecords($thisUrl, $from, $set, $metadataPrefix, true);
    }
    
    ?>
</OAI-PMH> 

