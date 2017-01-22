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
                $("#dialog").hide();
                
                $.getJSON("metadataFunctions.php?verb=getFolderList", function (data) {
                    var mySelect = $('#combobox');
                    $.each(data, function (key, value) {
                      mySelect.append($('<option></option>').val(key).html(value));
                    });
                });

                $("#onExecute").click(function () {
                    theText = {};
                    
                    $('input').each(function () {
                        var id = $(this).attr("id");
                        id = id.substring(2, id.length).toLowerCase();
                        var data = $(this).val();
                        theText[id] = data;
                    });

                    alert(JSON.stringify(theText));
                    if (false) {
                        alert("Length:: " + Object.keys(theText).length);
                    } else {
                        // --- Calling the PHP script:  
                        thePath = $("#selectedPath").text();
                        $.ajax({
                            type: 'POST',
                            url: 'metadataFunctions.php?verb=saveMetadataDC&path=' + thePath,
                            data: {json: JSON.stringify(theText)},
                            dataType: "text"
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
                    baseUrl = baseUrl.replace("admin/metadataDC.php", "index.php?path=");

                    $("#selectedFolder").text(baseUrl);
                    $("#selectedPath").text($(this).find('option:selected').text());

                    $("#dcTitle").val("");
                    $("#dcDescription").val("");
                    $("#dcCreator").val("");
                    $("#dcSubject").val("");
                    $("#dcPublisher").val("");
                    $("#dcDate").val("");
                    $("#dcFormat").val("");
                    $("#dcType").val("");
                    $("#dcIdentifier").val("");
                    $("#dcLanguage").val("");
                    $("#dcCoverage").val("");
                    $("#dcRights").val("");
                    $("#dcContributor").val("");
                    $("#dcSource").val("");
                    $("#dcRelation").val("");

                    $.ajax({
                        type: 'GET',
                        url: 'metadataFunctions.php?verb=getMetadataDC&path=' + $(this).find('option:selected').text(),
                        dataType: 'json'
                    })
                            .done(function (data) {
                                console.log('done');
                                $("#dcTitle").val(data["title"]);
                                $("#dcDescription").val(data["description"]);
                                $("#dcCreator").val(data["creator"]);
                                $("#dcSubject").val(data["subject"]);
                                $("#dcPublisher").val(data["publisher"]);
                                $("#dcDate").val(data["date"]);
                                $("#dcFormat").val(data["format"]);
                                $("#dcType").val(data["type"]);
                                $("#dcIdentifier").val(data["identifier"]);
                                $("#dcLanguage").val(data["language"]);
                                $("#dcCoverage").val(data["coverage"]);
                                $("#dcRights").val(data["rights"]);
                                $("#dcContributor").val(data["contributor"]);
                                $("#dcSource").val(data["source"]);
                                $("#dcRelation").val(data["relation"]);
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
                <div class='panel-heading'>Metadata - Dublin Core</div>
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

                        <form style="width:400px;" id="dcForm">
                            <div class="form-group">
                                <table>
                                    <tr>
                                        <td><label>Title</label></td>
                                        <td><input type="text" class="form-control" id="dcTitle" placeholder="Title" ></td>
                                    </tr>
                                    <tr>
                                        <td><label>Description</label></td>
                                        <td><input type="text" class="form-control" id="dcDescription" placeholder="Description"></td>
                                    </tr>
                                    <tr>
                                        <td><label>Creator</label></td>
                                        <td><input type="text" class="form-control" id="dcCreator" placeholder="Creator"></td>
                                    </tr>
                                    <tr>
                                        <td><label>Subject</label></td>
                                        <td><input type="text" class="form-control" id="dcSubject" placeholder="Subject"></td>
                                    </tr>
                                    <tr>
                                        <td><label>Publisher</label></td>
                                        <td><input type="text" class="form-control" id="dcPublisher" placeholder="Publisher"></td>
                                    </tr>
                                    <tr>
                                        <td><label>Date</label></td>
                                        <td><input type="text" class="form-control" id="dcDate" placeholder="Date"></td>
                                    </tr>
                                    <tr>
                                        <td><label>Format</label></td>
                                        <td><input type="text" class="form-control" id="dcFormat" placeholder="Format"></td>
                                    </tr>
                                    <tr>
                                        <td><label>Type</label></td>
                                        <td><input type="text" class="form-control" id="dcType" placeholder="Type"></td>
                                    </tr>
                                    <tr>
                                        <td><label>Identifier</label></td>
                                        <td><input type="text" class="form-control" id="dcIdentifier" placeholder="Identifier"></td>
                                    </tr>
                                    <tr>
                                        <td><label>Language</label></td>
                                        <td><input type="text" class="form-control" id="dcLanguage" placeholder="Language"></td>
                                    </tr>
                                    <tr>
                                        <td><label>Coverage</label></td>
                                        <td><input type="text" class="form-control" id="dcCoverage" placeholder="Coverage"></td>
                                    </tr>
                                    <tr>
                                        <td><label>Rights</label></td>
                                        <td><input type="text" class="form-control" id="dcRights" placeholder="Rights"></td>
                                    </tr>
                                    <tr>
                                        <td><label>Contributor</label></td>
                                        <td><input type="text" class="form-control" id="dcContributor" placeholder="Contributor"></td>
                                    </tr>
                                    <tr>
                                        <td><label>Source</label></td>
                                        <td><input type="text" class="form-control" id="dcSource" placeholder="Source"></td>
                                    </tr>
                                    <tr>
                                        <td><label>Relation</label></td>
                                        <td><input type="text" class="form-control" id="dcRelation" placeholder="Relation"></td>
                                    </tr>
                                </table>
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
