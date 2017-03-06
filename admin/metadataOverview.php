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
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="../templates/bootstrap/css/bootstrap.min.css">
        <script src="../templates/bootstrap/js/bootstrap.min.js"></script>
        <script src="../js/jquery.min.js"></script>
        <script src="js/adminFunctions.js"></script>
    </head>
    <body style="background-color: grey;">

        <div class="container" style="margin-top:60px;">
            <ol class="breadcrumb">
                <li><a href="index.php">Admin Home</a></li>
            </ol>
            <div  class='panel panel-info'>
                <div class='panel-heading'>Metadata - Overview</div>
                <div class='panel-body'>
                    <?php
                    include('navigation.php');
                    include("metadataFunctions.php");
                    include("../config.php");
                    global $config;

                    $folders = json_decode(getFolderList());

                    echo "<table class=\"table table-striped\">\n";
                    echo "<thead><tr><th>Folder</th><th>Plain</th> <th>Dublin Core</th> <th>Collection</th> <th>Data Types</th> <th>Taco Properties</th></tr> </thead><tbody>";
                    foreach ($folders as $name => $value) {
                        echo "<tr><td>$value</td>";
                        if (file_exists("../$value/" . $config['nameMetadataText'])) {
                            echo "<td><font color='green'>Yes</font></td>";
                        } else {
                            echo "<td><font color='red'>No</font></td>";
                        }

                        if (file_exists("../$value/" . $config['nameDCFile'])) {
                            echo "<td><font color='green'>Yes</font></td>";
                        } else {
                            echo "<td><font color='red'>No</font></td>";
                        }

                        if (file_exists("../$value/" . $config['nameCollectionItems'])) {
                            echo "<td><font color='green'>Yes</font></td>";
                        } else {
                            echo "<td><font color='red'>No</font></td>";
                        }

                        if (file_exists("../$value/" . $config['namesDataTypes'])) {
                            echo "<td><font color='green'>Yes</font></td>";
                        } else {
                            echo "<td><font color='red'>No</font></td>";
                        }

                        if (file_exists("../$value/" . $config['nameTacoProperties'])) {
                            echo "<td><font color='green'>Yes</font></td>";
                        } else {
                            echo "<td><font color='red'>No</font></td>";
                        }

                        //echo "<td>" . $config['nameDCFile'] . "</td>";
                        // echo "<td>" . $config['nameCollectionItems'] . "</td>";
                        // echo "<td>" . $config['namesDataTypes'] . "</td>";
                        // echo "<td>" . $config['nameTacoProperties'] . "</td>";
                        echo "</tr>\n";
                    }
                    echo "</tbody></table>\n";
                    ?>
                </div>
            </div>
        </div>
    </body>
</html>
