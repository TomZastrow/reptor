<!--
Licensed to the Apache Software Foundation (ASF) under one
or more contributor license agreements.  See the NOTICE file
distributed with this work for additional information
regarding copyright ownership.  The ASF licenses this file
to you under the Apache License, Version 2.0 (the
"License"); you may not use this file except in compliance
with the License.  You may obtain a copy of the License at

  http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing,
software distributed under the License is distributed on an
"AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
KIND, either express or implied.  See the License for the
specific language governing permissions and limitations
under the License.
-->

<?php
header('Content-type: text/xml');
header('Pragma: public');
header('Cache-control: private');
header('Expires: -1');
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";

include("oaipmh-functions.php");

$verb = $_GET['verb'];
$metadataPrefix = $_GET['metadataPrefix'];
$identifier = $_GET['identifier'];
$from = $_GET['from'];
$set = $_GET['set'];


$datadir = "data" . $identifier;
$dcFile = realpath($datadir . "metadata.dc");
$dcFile = realpath(dirname(__FILE__)) . "/" . $datadir . "/metadata.dc";
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

