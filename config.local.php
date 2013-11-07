<?php

$publicDir = $_SERVER['DOCUMENT_ROOT'];
$publicUrl = 'http://'.$_SERVER['HTTP_HOST'];

$_LOCALS = array(
    'theme' => 'dark',

    'uploadURL' => $publicUrl . '/RES/uploads',
    'uploadDir' => $publicDir . '/RES/uploads',
    'extraThumbnails' => array(
        array('thumbWidth' => 300, 'thumbHeight' => 250),
        array('thumbWidth' => 500, 'thumbHeight' => 400)
    )
);

