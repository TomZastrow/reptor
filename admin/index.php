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
include('../config.php');
?>
<html>
    <head>
        <link rel="stylesheet" href="../templates/bootstrap/css/bootstrap.min.css">
        <script src="../templates/bootstrap/js/bootstrap.min.js"></script>
        <script src="../js/jquery.min.js"></script>
        <title><?php echo "$repositoryTitle"; ?></title>
    </head>
    <body style="background-color: grey;">
        <?php include('navigation.php'); ?>

        <div class="container" style="margin-top:60px;">
            <div class="starter-template">

                <div class="myWrap">

                    <ol class="breadcrumb">
                        <li><a href="index.php">Admin Home</a></li>
                    </ol>


                    <div  class='panel panel-info'>
                        <div class='panel-heading'>Admin </div>
                        <div class='panel-body'>



                            <div  class='panel panel-danger'>
                                <div class='panel-heading'>Warning!</div>
                                <div class='panel-body'>Reptor does not contain a user management currently.
                                    <br /><br />
                                    <strong>This is the Admin panel.</strong>
                                    <br /><br />
                                    So please make sure that this folder is secured by the webserver!
                                    If you are using Apache Webserver, you will find here a <a href="https://www.digitalocean.com/community/tutorials/how-to-use-the-htaccess-file">tutorial</a> about securing a folder.
                                    If you don't need the Admin Panel for manual adding and editing data, you can delete the admin folder at all.
                                </div>
                            </div>

                            <h3>PID Management</h3>
                            <ul>
                                <li><a href="shortref.php">Shortref.org</a></li>
                            </ul>

                            <h3>Metadata</h3>
                            <ul>
                                <li><a href="metadataPlain.php">Plain Text</a></li>
                                <li><a href="metadataDC.php">Dublin Core</a></li>
                                <li><a href="metadataTypes.php">Data Type Registry</a></li>
                                <li><a href="collections.php">Collections</a></li>
                            </ul>

                            <h3>Object Data</h3>
                            <ul>
                                <li><a href="filesAndFolders.php">Files and Folders</a></li>
                            </ul>  
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>