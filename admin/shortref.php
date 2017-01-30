<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="../templates/bootstrap/css/bootstrap.min.css">
        <script src="../templates/bootstrap/js/bootstrap.min.js"></script>
        <script src="../js/jquery.min.js"></script>
        <script>
            $(document).ready(function () {
                //pidFunctions.php?verb=getPidLackingFolders
                $.getJSON("metadataFunctions.php?verb=getFolderList", function (data) {
                    var mySelect = $('#combobox');
                    $.each(data, function (key, value) {
                        mySelect.append($('<option></option>').val(key).html(value));
                    });
                })
                
                var url = window.location.href;
                baseUrl = url + $(this).find('option:selected').text();
                baseUrl = baseUrl.replace("admin/shortref.php", "index.php?path=/data");
                $("#selectedFolder").text(baseUrl);
                $("#selectedPath").text("/data");

                ;

                $("#onExecute").click(function () {
                    //Creating the JSON object:
                    var shortrefData = new Object();
                    shortrefData.url = $("#selectedFolder").text();
                    shortrefData.title = $("#srTitle").val();
                    shortrefData.reportemail = $("#srReportEmail").val();
                    shortrefData.subprefix = $("#srSubprefix").val();
                    shortrefData.datasetname = $("#srDatasetName").val();
                    shortrefData.datasetversion = $("#srDatasetVersion").val();
                    shortrefData.path = $("#selectedPath").text();

                    if (shortrefData.url == "" || shortrefData.title == "" || shortrefData.reportemail == "") {
                        alert("Please choose a folder, a title and a report email at least!");
                    } else {
                        // --- Calling the PHP script:                    
                        $.ajax({
                            type: 'POST',
                            url: 'pidFunctions.php?verb=createShortrefPID',
                            data: {json: JSON.stringify(shortrefData)},
                            dataType: 'text'
                        })
                                .done(function (data) {
                                    console.log('done');
                                    alert(data);
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
                    baseUrl = baseUrl.replace("admin/shortref.php", "index.php?path=");
                    $("#selectedFolder").text(baseUrl);
                    $("#selectedPath").text($(this).find('option:selected').text());
                });


            });
        </script>
    </head>
    <body style="background-color: grey;">
        <?php include('navigation.php'); ?>
        <div class="container" style="margin-top:60px;">

            <div  class='panel panel-info'>
                <div class='panel-heading'>PID Management</div>
                <div class='panel-body'>
                    <div class="starter-template">
                        <div class="form-group">
                            <label for="xxxx">Choose a folder where the Shortref PID should point to:</label>
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
                                <label for="srTitle">Title</label>
                                <input type="text" class="form-control" id="srTitle" placeholder="Title">
                            </div>
                            <div class="form-group">
                                <label for="srReportEmail">Email</label>
                                <input type="text" class="form-control" id="srReportEmail" placeholder="Email">
                            </div>

                            <div class="form-group">
                                <label for="srSubprefix">Subprefix</label>
                                <input type="text" class="form-control" id="srSubprefix" placeholder="Subprefix">
                            </div>

                            <div class="form-group">
                                <label for="srDatasetName">Dataset Name</label>
                                <input type="text" class="form-control" id="srDatasetName" placeholder="Dataset Name">
                            </div>

                            <div class="form-group">
                                <label for="srDatasetVersion">Dataset Version</label>
                                <input type="text" class="form-control" id="srDatasetVersion" placeholder="Dataset Version">
                            </div>
                        </form>
                        <br />
                        <button id="onExecute" class="btn btn-primary" >Add Shortref PID</button>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
