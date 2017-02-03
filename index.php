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
include('config.php');
global $config;
include('socialMedia.php');
include('generalFunctions.php');
?>
<html>
    <head>
        <link rel="stylesheet" href="templates/bootstrap/css/bootstrap.min.css">
        <script src="templates/bootstrap/js/bootstrap.min.js"></script>
        <script src="js/jquery.min.js"></script>
        <title><?php echo $config['repositoryTitle']; ?></title>
    </head>
    <body style="background-color: grey;">
        <?php
        if ($config['showFacebookLikeButton']) {
            echo getFacebookSDK();
        }
        ?>

        <nav class="navbar navbar-inverse navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#"><?php echo $config['repositoryTitle']; ?></a>
                </div>
                <div id="navbar" class="collapse navbar-collapse">
                    <ul class="nav navbar-nav">
                        <?php
                        if ($config['showAdminLogin']) {
                            echo "<li><a href='admin/'>Admin</a></li>";
                        }
                        ?>

                    </ul>
                </div><!--Navigation -->
            </div> 
        </nav>

        <div class="container" style="margin-top:60px;">
            <div class="starter-template">
                <div class="myWrap">

                    <?php
                    /*
                     * Where are we, what should be displayed:
                     */
                    $verb = "";
                    if (isset($_GET['path'])) {
                        $verb = $_GET['path'];
                    }
                    $datadir = "data" . $verb;

                    if (!endsWithChar($datadir, "/")) {
                        $datadir = $datadir . "/";
                    }

                    $context = scandir($datadir);

                    /*
                     * Some more variables for directories, files, metadata and PID:
                     */
                    $dirs = array();
                    $files = array();
                    $datatypes = array();
                    $dublinCore = array();
                    $collectionItems = array();
                    $metadataText = "";
                    $pid = "";


                    /*
                     * Now iterate over the content of the current dir and - depending of 
                     * what we find - put the content in the variables:
                     */
                    foreach ($context as $entry) {
                        $toCheck = $datadir . $entry;
                        if (is_dir($toCheck)) {
                            if (!startsWithChar($entry, ".") and ( $entry != "lost+found")) {
                                array_push($dirs, $entry);
                            }
                        } else if (!startsWithChar($entry, ".")) {
                            if ($entry == $config['nameMetadataText']) {
                                $metadataText = file_get_contents($toCheck);
                            } else if ($entry == $config['namePIDFile']) {
                                $pid = file_get_contents($toCheck);
                            } else if ($entry == $config['namesDataTypes']) {
                                $datatypes = file($toCheck, FILE_IGNORE_NEW_LINES);
                            } else if ($entry == $config['nameDCFile']) {
                                $dublinCore = parse_ini_file($toCheck);
                            } else if ($entry == $config['nameCollectionItems']) {
                                $collectionItems = file($toCheck, FILE_IGNORE_NEW_LINES);
                            } else {
                                array_push($files, $entry);
                            }
                        }
                    }

                    /*
                     * Lets create a nice breadcrumb at the top of the side:
                     */
                    $bread = explode("/", $datadir);
                    $pathTemp = "";
                    echo '<ol class="breadcrumb">';
                    foreach ($bread as $entry) {
                        if ($entry == "data") {
                            echo "<li><a href='index.php'>Home</a></li>";
                        } else {
                            if (!$entry == "") {
                                $pathTemp = $pathTemp . "/" . $entry;
                                echo "<li><a href='index.php?path=$pathTemp'>" . $entry . "</a> </li> ";
                            }
                        } // if entry == data
                    }
                    echo "</ol>\n";

                    /*
                     * Do we have metadata in plain text:
                     */
                    if ($metadataText == "") {
                        if ($config['showMissingParts']) {
                            echo "<div  class='panel panel-danger'>";
                            echo "<div class='panel-heading'>Metadata - plain text</div>\n";
                            echo "<div class='panel-body'>No Metadata available here.</div></div>";
                        }
                    } else {
                        echo "<div  class='panel panel-success'>";
                        echo "<div class='panel-heading'>Metadata - plain text</div>\n";
                        echo "<div class='panel-body'>$metadataText</div>";
                        echo "</div>";
                    }

                    /*
                     * Do we have Dublin Core:
                     */
                    if (sizeof($dublinCore) == 0) {
                        if ($config['showMissingParts']) {
                            echo "<div  class='panel panel-danger'>";
                            echo "<div class='panel-heading'>Metadata - Dublin Core</div>\n";
                            echo "<div class='panel-body'>No Dublin Core entries.</div></div>";
                        }
                    } else {
                        echo "<div  class='panel panel-success'>";
                        echo "<div class='panel-heading'>Metadata - Dublin Core</div>\n";
                        echo "<div class='panel-body'>\n";
                        echo "<table class=\"table table-striped\">\n";
                        foreach ($dublinCore as $key => $value) {
                            echo "<tr><td>$key</td><td>$value</td></tr>\n";
                        }
                        echo "</table>\n";
                        echo "</div></div>\n";
                    }

                    /*
                     * Do we have Collection Items:
                     */
                    if (sizeof($collectionItems) == 0) {
                        if ($config['showMissingParts']) {
                            echo "<div  class='panel panel-danger'>";
                            echo "<div class='panel-heading'>Metadata - Collection items</div>\n";
                            echo "<div class='panel-body'>There are no collection items here.</div></div>";
                        }
                    } else {
                        echo "<div  class='panel panel-success'>";
                        echo "<div class='panel-heading'>Collection Items</div>\n";
                        echo "<div class='panel-body'>\n";
                        echo "<table class=\"table table-striped\">\n";
                        foreach ($collectionItems as $item) {
                            echo "<tr><td>$item</td></tr>\n";
                        }
                        echo "</table>\n";
                        echo "</div></div>\n";
                    }

                    /*
                     * Do we have data registry entries:
                     */
                    if (sizeof($datatypes) == 0) {
                        if ($config['showMissingParts']) {
                            echo "<div  class='panel panel-danger'>";
                            echo "<div class='panel-heading'>Metadata - Data Types</div>\n";
                            echo "<div class='panel-body'>No datatypes declared here.</div></div>";
                        }
                    } else {
                        echo "<div  class='panel panel-success'>";
                        echo "<div class='panel-heading'>Metadata - Data Types</div>\n";
                        echo "<div class='panel-body'>\n";
                        foreach ($datatypes as $type) {
                            $content = @file_get_contents($config['dataTypeRegistry'] . $type);
                            if ($content === false) {
                                echo "<strong>Can' resolve:</strong>" . $config['dataTypeRegistry'] . "$type <hr/>";
                            } else {
                                $content = json_decode($content);
                                echo "<strong>Name:</strong> " . $content->{'name'};
                                echo "<br>\n";
                                echo "<strong>Content:</strong> " . $content->{'description'};
                                echo "<br>\n";
                                echo "<strong>Identifier:</strong>" . $config['dataTypeRegistry'] . "$type</br></br>  \n";
                                echo "<a href='" . $config['dataTypeRegistry'] . "$type'><button class=\"btn btn-primary\">Go to definition</button></a><br/>\n";
                                echo "<hr>\n";
                            }
                        }
                        echo "</div></div>\n";
                    }


                    /*
                     * Do we have a PID:
                     */
                    if ($pid == "") {
                        if ($config['showMissingParts']) {
                            echo "<div  class='panel panel-danger'>";
                            echo "<div class='panel-heading'>Persistent Identifier</div>\n";
                            echo "<div class='panel-body'>No PID available on this level: add a file <i> $namePIDFile </i>with a PID!</div></div>";
                        }
                    } else {
                        echo "<div  class='panel panel-success'>";
                        echo "<div class='panel-heading'>Persistent Identifier</div>\n";
                        echo "<div class='panel-body'>\n";
                        echo "$pid </div></div>";
                    }

                    /*
                     * Are there subfolders in the current directory:
                     */
                    if (count($dirs) == 0) {
                        echo "<div  class='panel panel-info'>";
                        echo "<div class='panel-heading'>Subdirectories</div>\n";
                        echo "<div class='panel-body'>\n";
                        echo "<i> - No subdirs on this level -</i><br>";
                        echo "</div></div>\n";
                    } else {

                        echo "<div  class='panel panel-info'>";
                        echo "<div class='panel-heading'>Subdirectories</div>\n";
                        echo "<div class='panel-body'>\n";

                        echo "<table class=\"table table-striped table-bordered\">\n";
                        foreach ($dirs as $entry) {
                            echo "<tr><td><img src='images/folder.png' /> <a href='index.php?path=" . $verb . "/" . $entry . "'>$entry</a></td></tr>\n";
                        }
                        echo "</table>\n";
                        echo "</div></div>\n";
                    } //dirs

                    /*
                     * If we have files - show them:
                     */
                    if (count($files) == 0) {
                        echo "<div  class='panel panel-info'>";
                        echo "<div class='panel-heading'>Files</div>\n";
                        echo "<div class='panel-body'>\n";
                        echo "<i> - No files on this level -</i><br>";
                        echo "</div></div>\n";
                    } else {
                        echo "<div  class='panel panel-info'>";
                        echo "<div class='panel-heading'>Files</div>\n";

                        echo "<div class='panel-body'>\n";
                        echo "<table class=\"table table-striped\">\n";

                        foreach ($files as $entry) {
                            echo "<tr><td><img src='images/file.png' /> <a href='data" . $verb . "/" . $entry . "'>$entry</a></td></tr>";
                        }
                        echo "</table>\n";
                        echo "</div></div>\n";
                    } // if count

                    /*
                     * Exporting metadata (OAI-PMH, ResourceSync): 
                     */
                    echo "<div  class='panel panel-info'>";
                    echo "<div class='panel-heading'>Announcements</div>\n";
                    echo "<div class='panel-body'>\n";
                    echo "<a href='oai.php?verb=GetRecord&metadataPrefix=oai_dc&identifier=$verb'><button class=\"btn btn-primary\"><span class=\"glyphicon glyphicon-globe\" aria-hidden=\"true\"></span> OAI-PMH</button></a>\n";
                    echo "<a href='resourceSync.php'><button class=\"btn btn-primary\"><span class=\"glyphicon glyphicon-globe\" aria-hidden=\"true\"> ResourceSync</button></a>\n";
                    echo "<br /><br />\n";


                    /*
                     * Social Media:
                     */
                    echo '<div style="margin-bottom:20px;">';
                    if ($config['showFacebookLikeButton']) {
                        $thisUrl = "http" . (!empty($_SERVER['HTTPS']) ? "s" : "") . "://" . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'];
                        $thisUrl = $thisUrl . "?path=$verb";
                        echo getFacebookLikeButton($thisUrl);
                    }

                    if ($config['showTwitterButton']) {
                        echo getTwitterButton();
                    }

                    echo "</div></div></div>";

                    /*
                     * This is the validation for PID, metadata and bitstreams:
                     */
                    if ($config['showEvaluation']) {
                        echo "<div style='margin-bottom:5px;margin-top:10px;' class='greyBox'>";
                        echo '<div class="panel panel-default">';
                        echo '<div class="panel-body">';

                        if ($metadataText != "") {
                            echo "<img src='images/t_up.png' /> Metadata - ";
                        } else {
                            echo "<img src='images/t_down.png' /> Metadata - ";
                            $valid = false;
                        }

                        if ($pid != "") {
                            echo "<img src='images/t_up.png' /> PID - ";
                        } else {
                            echo "<img src='images/t_down.png' /> PID - ";
                            $valid = false;
                        }

                        if (count($files) == 0) {
                            echo "<img src='images/t_down.png' /> Bitstreams";
                        } else {
                            echo "<img src='images/t_up.png' /> Bitstreams";
                        }

                        /*
                         * Validation conclusion:
                         */
                        if ($metadataText != "" and $pid != "" and count($files) > 0) {
                            echo "<p>This folder can be interpreted as <a href='http://smw-rda.esc.rzg.mpg.de/index.php/Digital_Object'>Digital Object</a>.</p>";
                        }
                        if ($metadataText != "" and $pid != "" and count($files) == 0) {
                            echo "<p>This folder can be interpreted as <a href='http://smw-rda.esc.rzg.mpg.de/index.php/Registered_Data'>Registered Object</a>.</p>";
                        }
                        if ($metadataText == "" or $pid == "") {
                            echo "<p>This folder is just a folder. Put some metadata and/or a PID into it.</p>";
                        }
                        echo "</div></div>\n";
                    }
                    ?>
                </div>
            </div></div>
    </body>
</html>