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

function identify($thisUrl) {
    include('config.php');
    global $config;
    echo "<request verb='Identify'>$thisUrl</request>\n";
    echo "<Identify>\n";
    echo " <repositoryName>" . $config['repositoryTitle'] ."</repositoryName>\n";
    echo " <baseURL>$thisUrl</baseURL>\n";
    echo " <protocolVersion>2.0</protocolVersion>\n";
    echo " <adminEmail>" . $config['repositoryEmail'] . "</adminEmail>\n";
    echo " <earliestDatestamp>1970-01-01</earliestDatestamp>\n";
    echo " <deletedRecord>no</deletedRecord>\n";
    echo " <granularity>YYYY-MM-DD</granularity>\n";
    echo "  <description>\n";
    echo "   <rda xmlns='http://www.rd-alliance.org'\n";
    echo "        xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance'>\n";
    echo "  <dtr-type>21.T11148/81e3a5aaec9cae71240b</dtr-type>\n";
    echo "  </rda>\n";
    echo " </description>\n";
    echo " </Identify>\n";
}

function getRecord($identifier, $metadataPrefix, $thisUrl, $dcFile) {
    echo "<request verb='$verb' identifier='$identifier' metadataPrefix='$metadataPrefix'>$thisUrl</request>\n";
    echo "<GetRecord>\n";
    echo "<record>\n";
    echo "<header>\n";
    echo "<identifier>$identifier</identifier>\n";
    echo "<datestamp>$date</datestamp>\n";
    echo "<setSpec>gmd</setSpec>\n";
    echo "</header>\n";
    echo "<metadata>\n";
    echo "            <oai_dc:dc\n";
    echo "                xmlns:oai_dc='http://www.openarchives.org/OAI/2.0/oai_dc/'\n";
    echo "                xmlns:dc='http://purl.org/dc/elements/1.1/'\n";
    echo "                xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance'\n";
    echo "                xsi:schemaLocation='http://www.openarchives.org/OAI/2.0/oai_dc/ ";
    echo "                http://www.openarchives.org/OAI/2.0/oai_dc.xsd'>\n";

    try {
        if (file_exists($dcFile)) {
            $dublinCore = parse_ini_file($dcFile);
            foreach ($dublinCore as $key => $value) {
                echo "<dc:$key>$value</dc:$key>\n";
            }
        }
    } catch (Exception $e) {
        echo 'Exception: ', $e->getMessage(), "\n";
    }

    echo "</oai_dc:dc>\n";
    echo "</metadata>\n";
    echo "</record>\n";
    echo "</GetRecord>\n";
}

function getMetadataFormats($thisUrl) {
    echo "<request verb='ListMetadataFormats' identifier='$thisUrl'>$thisUrl</request>\n";
    echo "<ListMetadataFormats>\n";
    echo "<metadataFormat>\n";
    echo "<metadataPrefix>oai_dc</metadataPrefix>\n";
    echo "<schema>http://www.openarchives.org/OAI/2.0/oai_dc.xsd</schema>\n";
    echo "<metadataNamespace>http://www.openarchives.org/OAI/2.0/oai_dc/\n";
    echo "</metadataNamespace>\n";
    echo "</metadataFormat>\n";
    echo "</ListMetadataFormats>\n";
}

function getListSets($thisUrl) {
    echo "<request verb='ListSets'>$thisUrl</request>\n";
    echo "<ListSets>\n";
    echo "<set>\n";
    echo "<setSpec>RDA-DFT-Data</setSpec>\n";
    echo "<setName>RDA DFT compliant data</setName>\n";
    echo "<setDescription>\n";
    echo "  <oai_dc:dc \n";
    echo "      xmlns:oai_dc='http://www.openarchives.org/OAI/2.0/oai_dc/' \n";
    echo "      xmlns:dc='http://purl.org/dc/elements/1.1/' \n";
    echo "      xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' \n";
    echo "      xsi:schemaLocation='http://www.openarchives.org/OAI/2.0/oai_dc/ \n";
    echo "      http://www.openarchives.org/OAI/2.0/oai_dc.xsd'>\n";
    echo "      <dc:description>This repository contains data compatible with the RDA DFT definition.\n";
    echo "      </dc:description>\n";
    echo "   </oai_dc:dc>\n";
    echo "</setDescription>\n";

    echo "</set>\n";
    echo "</ListSets>\n";
}

function getListRecords($thisUrl, $from, $set, $metadataPrefix, $onlyHeaders) {
    $path = realpath('data');
    $scriptPath = realpath(dirname(__FILE__));

    $request = "<request verb='ListRecords' ";

    if (isset($from)) {
        $request = $request . " from='$from' ";
    }

    if (isset($set)) {
        $request = $request . " set='$set' ";
    }

    if (isset($metadataPrefix)) {
        $request = $request . " metadataPrefix='$metadataPrefix' ";
    }

    $request = $request . ">$thisUrl</request>\n";

    echo "$request \n";
    echo "<ListRecords>\n";
    try {
        $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($objects as $name => $object) {
            if (is_dir($name) && file_exists($name . "/metadata.dc")) {

                echo "<record>\n";
                $dcFile = $name . "/metadata.dc";

                $name = str_replace("/oai.php", '', $thisUrl) . str_replace($scriptPath, "", $name);

                echo "<header>\n";
                echo "<identifier>$name</identifier>\n";
                echo "<datestamp>2002-02-28</datestamp>\n";
                echo "<setSpec>RDA-DFT-Data</setSpec>\n";
                echo "</header>\n";

                if ($onlyHeaders == false) {

                    echo "<metadata>\n";
                    echo "<oai_dc:dc ";
                    echo "xmlns:oai_dc='http://www.openarchives.org/OAI/2.0/oai_dc/' ";
                    echo "xmlns:dc='http://purl.org/dc/elements/1.1/' ";
                    echo "xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' ";
                    echo "xsi:schemaLocation='http://www.openarchives.org/OAI/2.0/oai_dc/ ";
                    echo "http://www.openarchives.org/OAI/2.0/oai_dc.xsd'>\n";

                    $dublinCore = parse_ini_file($dcFile);
                    foreach ($dublinCore as $key => $value) {
                        echo "<dc:$key>$value</dc:$key>\n";
                    }
                    echo "</oai_dc:dc>\n";
                    echo "</metadata>\n";
                }
                echo "</record>\n";
            }
        }
    } catch (Exception $e) {
        echo 'Exception: ', $e->getMessage(), "\n";
    }

    echo "</ListRecords>\n";
}
?>


