<?php

include('../generalFunctions.php');

function createSubfolder($path) {
    $subfolder = $_POST['Subfolder'];
    $path = ".." . $path . "/" . $subfolder;

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
        $destination = ".." . $path. '/' . $_FILES['file']['name'];
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