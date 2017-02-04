function loadData(path, funcName, textField) {
    $.ajax({
        type: 'GET',
        url: 'metadataFunctions.php?verb=' + funcName + '&path=' + path,
        dataType: 'text'
    })
            .done(function (data) {
                console.log('done');
                $(textField).val(data);
                console.log(data);
            })
            .fail(function (data) {
                console.log('fail');
                console.log(data);
            });
}

function loadFolderlist() {
    $.getJSON("metadataFunctions.php?verb=getFolderList", function (data) {
        var mySelect = $('#combobox');
        $.each(data, function (key, value) {
            mySelect.append($('<option></option>').val(key).html(value));
        });
    })
}

function loadLables(filename, path) {
    var url = window.location.href;
    baseUrl = url + $(this).find('option:selected').text();
    baseUrl = baseUrl.replace("admin/" + filename, "index.php?path=" + path);
    $("#selectedFolder").text(baseUrl);
    $("#selectedPath").text(path);
   
}


