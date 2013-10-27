<?php

$publicDir = $_SERVER['DOCUMENT_ROOT'];
$publicUrl = 'http://'.$_SERVER['HTTP_HOST'];

$_LOCALS = array(
    'theme' => 'dark',

    'uploadURL' => $publicUrl . '/RES/uploads',
    'uploadDir' => $publicDir . '/RES/uploads'
);

