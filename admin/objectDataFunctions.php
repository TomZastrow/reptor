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

function createSubfolder($path) {
    $subfolder = $_POST['Subfolder'];
    $path = "../data" . $path . "/" . $subfolder;

    $message = "";

    if (!file_exists($path)) {
        mkdir($path, 0777);
        $message = "Folder " . $path . " was created.";
    } else {
        $message = "Error. Folder or file with this name already exists: " . $path;
    }
    return $message;
}

function uploadFiles($path) {
    $result = "File Upload: ";
    if (0 < $_FILES['file']['error']) {
        $result = $result . ' - error: ' . $_FILES['file']['error'];
    } else {
        $destination = "../data/" . $path. '/' . $_FILES['file']['name'];
        move_uploaded_file($_FILES['file']['tmp_name'], $destination);
        $result = $result . " - success: " + $destination;
    }
    return $result;
}

// -------------------- The main calls:

$verb = $_GET['verb'];

if ($verb == "createSubfolder") {
    echo createSubfolder($_GET['path']);
}

if ($verb == "uploadFiles") {
    echo uploadFiles($_GET['path']);
}