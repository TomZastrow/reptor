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
        <script>
            $(document).ready(function () {
                $.getJSON("metadataFunctions.php?verb=getFolderList", function (data) {
                    var mySelect = $('#combobox');
                    $.each(data, function (key, value) {
                        mySelect.append($('<option></option>').val(key).html(value));
                    });
                });

                $("#onExecute").click(function () {
                    theText = $("#metadataPlain").val();
                    if (theText == "") {
                        alert("Please enter metadata!");
                    } else {
                        // --- Calling the PHP script:  
                        thePath = $("#selectedPath").text();
                        $.ajax({
                            type: 'POST',
                            url: 'metadataFunctions.php?verb=saveMetadataPlain&path=' + thePath,
                            data: {'Text': theText},
                            dataType: 'text'
                        })
                                .done(function (data) {
                                    console.log('done');
                                    alert("Done: " + data);
                                    console.log(data);
                                })
                                .fail(function (data) {
                                    console.log('fail');
                                    console.log(data);
                                });

                    } // else: checking empty textfields
                });

                $("#combobox").change(function () {
                    var url = window.location.href;

                    baseUrl = url + $(this).find('option:selected').text();
                    baseUrl = baseUrl.replace("admin/metadataPlain.php", "index.php?path=");

                    $("#selectedFolder").text(baseUrl);
                    $("#selectedPath").text($(this).find('option:selected').text());

                    $.ajax({
                        type: 'GET',
                        url: 'metadataFunctions.php?verb=getMetadataPlain&path=' + $(this).find('option:selected').text(),
                        dataType: 'text'
                    })
                            .done(function (data) {
                                console.log('done');
                                $("#metadataPlain").val(data);
                                console.log(data);
                            })
                            .fail(function (data) {
                                console.log('fail');
                                console.log(data);
                            });
                });

            });
        </script>
    </head>
    <body style="background-color: grey;">
        <?php include('navigation.php'); ?>
        <div class="container" style="margin-top:60px;">

            <div  class='panel panel-info'>
                <div class='panel-heading'>Metadata - Plain Text</div>
                <div class='panel-body'>
                    <div class="starter-template">
                        <div class="form-group">
                            <label for="xxxx">Choose a folder:</label>
                            <select class="form-control" id="combobox" style="width:400px;">
                            </select>
                        </div>

                        <br />
                        <strong>Url: </strong><span id="selectedFolder"></span>
                        <br />
                        <strong>Path: </strong><span id="selectedPath"></span>
                        <br />
                        <br />

                        <form style="width:400px;">
                            <div class="form-group">
                                <label for="metadataPlain">Metadata:</label>
                                <textarea class="form-control" rows="5" id="metadataPlain"></textarea>
                            </div>
                        </form>
                        <br />
                        <button id="onExecute" class="btn btn-primary" >Save</button>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
