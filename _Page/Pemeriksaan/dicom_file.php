<?php
$file = basename($_GET['file'] ?? '');
$path = realpath('../../_DCM/' . $file);

if (!$path || !file_exists($path)) {
    http_response_code(404);
    exit;
}

header('Content-Type: application/dicom');
header('Content-Transfer-Encoding: binary');
header('Accept-Ranges: bytes');
header('Cache-Control: no-cache');
header('Content-Length: ' . filesize($path));
readfile($path);
