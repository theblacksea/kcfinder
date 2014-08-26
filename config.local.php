<?php

$origin = parse_url($_SERVER['HTTP_REFERER']);
$valid_domains = array("theblacksea.eu", "www.theblacksea.eu");

if(!in_array($origin['host'], $valid_domains)) {
    //echo sprintf("Origin domain is <b>%s</b>, with the HTTP referrer <b>%s</b><br>", $origin['host'], $_SERVER['HTTP_REFERER']);
    header('HTTP/1.0 403 Forbidden');
    die('Warning, access forbidden!');
}


#if(session_regenerate_id($_GET['SID']))) {
#    die('Warning, access forbidden!');
#}

$publicDir = $_SERVER['DOCUMENT_ROOT'];
$publicUrl = 'http://'.$_SERVER['HTTP_HOST'];

$_LOCALS = array(
    'theme' => 'dark',

    'read_exif' => true,

    'uploadURL' => $publicUrl . '/RES/uploads',
    'uploadDir' => $publicDir . '/RES/uploads',
    'extraThumbnails' => array(
        array('thumbWidth' => 300, 'thumbHeight' => 250),
        array('thumbWidth' => 500, 'thumbHeight' => 400)
    )
);

