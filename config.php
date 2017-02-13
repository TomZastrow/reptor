<?php
global $config;


$config['dataTypeRegistry'] = "http://dtr.pidconsortium.eu:8081/objects/";
$config['namePIDFile'] = "pid.txt";
$config['nameMetadataText'] = "metadata.txt";
$config['namesDataTypes'] = "metadata.types";
$config['nameDCFile'] = "metadata.dc";
$config['nameTacoProperties'] = "metadata.properties";
$config['nameCollectionItems'] = "collection.txt";

$config['template'] = "default";
$config['showEvaluation']=true;
$config['showAdminLogin']=true;

$config['shortrefUrl'] = 'https://lindat.mff.cuni.cz/services/shortener/api/v1/handles';

$config['showColletionItemsAsLinks'] = true;

$config['showMissingParts']=false;

$config['repositoryTitle'] = "RDA Data Repository";
$config['repositoryEmail'] = "thomas.zastrow@mpcdf.mpg.de";
$config['repositoryIdentifier'] = "shortref.org";

$config['showFacebookLikeButton'] = true;
$config['showTwitterButton'] = true;