<?php

function startsWithChar($needle, $haystack) {
    return ($needle[0] === $haystack);
}

function endsWithChar($needle, $haystack) {
    return ($needle[strlen($needle) - 1] === $haystack);
}

function consoleOutput($thing) {
    $result = 'console.info( \'PHP Console output: \' );';
    $result = $result .'console.log(' . json_encode($thing) . ');';    
}
