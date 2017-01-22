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

function getFacebookSDK() {
    $result = '<div id="fb-root"></div>';
    $result = $result . '<script>(function(d, s, id) {';
    $result = $result . '  var js, fjs = d.getElementsByTagName(s)[0];';
    $result = $result . '  if (d.getElementById(id)) return;';
    $result = $result . '  js = d.createElement(s); js.id = id;';
    $result = $result . '  js.src = "//connect.facebook.net/en_EN/sdk.js#xfbml=1&version=v2.8";';
    $result = $result . '  fjs.parentNode.insertBefore(js, fjs);';
    $result = $result . "}(document, 'script', 'facebook-jssdk'));</script>";

    return $result;
}

function getFacebookLikeButton($urlToShow) {
    $result = "<div class=\"fb-like\" data-href=\"$urlToShow\" data-width=\"30\"\n";
    $result = $result . 'data-layout="button" data-action="like"></div>';
    return $result;
}

function getTwitterButton() {
    $result = '<a href="https://twitter.com/share" class="twitter-share-button">Tweet</a>';
    $result = $result . '<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?\'http\':\'https\';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+\'://platform.twitter.com/widgets.js\';fjs.parentNode.insertBefore(js,fjs);}}(document, \'script\', \'twitter-wjs\');</script>';
    return $result;
}


