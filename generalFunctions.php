<?php

function startsWithChar($needle, $haystack) {
    return ($needle[0] === $haystack);
}

function endsWithChar($needle, $haystack) {
    return ($needle[strlen($needle) - 1] === $haystack);
}

function consoleOutput($thing) {
    $result = 'console.info( \'PHP Console output: \' );';
    $result = $result . 'console.log(' . json_encode($thing) . ');';
}

function parseIniFile($file) {
    if (!is_file($file))
        return null;
    $iniFileContent = file_get_contents($file);
    return parseIniString($iniFileContent);
}

function parseIniString($iniFileContent) {
    $iniArray = array();
    $iniFileContentArray = explode("\n", $iniFileContent);
    foreach ($iniFileContentArray as $iniFileContentArrayRow) {
        $iniArrayKey = substr($iniFileContentArrayRow, 0, strpos($iniFileContentArrayRow, '='));
        $iniArrayValue = substr($iniFileContentArrayRow, (strpos($iniFileContentArrayRow, '=') + 1));
        if($iniArrayKey != ""){
        $iniArray[$iniArrayKey] = $iniArrayValue;
        }
    }
    return $iniArray;
}
