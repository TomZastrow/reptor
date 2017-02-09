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
include('../generalFunctions.php');
include('../config.php');


function getPIDLackingFolders() {
    $fileSystemPath = realpath('../data');
    global $config;
    try {
        $thisUrl = "http" . (!empty($_SERVER['HTTPS']) ? "s" : "") . "://" . $_SERVER['SERVER_NAME'];
        $scriptPath = realpath(dirname(__FILE__));

        $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($fileSystemPath), RecursiveIteratorIterator::SELF_FIRST);
        $result = "{";
        $counter = 0;
        foreach ($objects as $name => $object) {
            if (is_dir($name)) {
                $counter = $counter + 1;
                if (!endsWithChar($name, ".")) {
                    //if (!file_exists($name . "/" . $config['namePIDFile'])) {
                        $name = str_replace($fileSystemPath, "", $name);

                        $name = str_replace("\\", "/", $name);
                        $result = $result . "\"" . $counter . "\" : \"" . $name . "\", ";
                    //}
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

function createShortrefPID() {
    global $config;
    $data = json_decode($_POST['json']);
    $url = $config['shortrefUrl'];

    $options = array(
        'http' => array(
            'header' => "Content-type: application/json\r\n",
            'method' => 'POST',
            'content' => $_POST['json']
        )
    );
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    
    $s = "";

    $logfile = fopen("pid.log", "a");
    fwrite($logfile, $result);
    fwrite($logfile, "\n");

    if ($result === FALSE) {
        $s = "Something went wrong: " . $result;
    } else {
        $results = json_decode($result);

        $pidfile = fopen(".." . $data->{'path'} . "/" . $config['namePIDFile'] , "a");
        fwrite($pidfile, $results->{'handle'});
        fwrite($pidfile, "\n");
        fclose($pidfile);

        fwrite($logfile, $results->{'handle'} . " written to: " . $data->{'path'} . "/" . $config['namePIDFile'] . "\n");

        $s = $results->{'handle'} . " was created. Remember the token: " . $results->{'token'};
    }
    fwrite($logfile, "--------------------------\n");
    fclose($logfile);
    return $s;
}

$verb = $_GET['verb'];

if ($verb == "getPidLackingFolders") {
    echo getPIDLackingFolders();
}

if ($verb == "createShortrefPID") {
    echo createShortrefPID();
}


