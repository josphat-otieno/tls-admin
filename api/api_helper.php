<?php
// api/api_helper.php

// Allow from any origin
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}

// Define your CMS Base URL here (no trailing slash)
// For local development, this might be http://localhost/tls-cms
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$base_dir = str_replace('/api', '', dirname($_SERVER['SCRIPT_NAME']));
$base_dir = rtrim($base_dir, '/\\');
define('BASE_URL', $protocol . "://" . $host . $base_dir);

function fix_paths(&$data) {
    if (is_array($data)) {
        foreach ($data as $key => &$value) {
            if (is_array($value)) {
                fix_paths($value);
            } else if (is_string($value) && !empty($value)) {
                // Check for common media keys or paths starting with 'uploads/'
                $media_keys = ['image_path', 'profile_image', 'media_url', 'image', 'thumbnail', 'placeholder_image'];
                if (in_array($key, $media_keys) || strpos($value, 'uploads/') === 0) {
                    // Only prepend if it doesn't already have http/https
                    if (strpos($value, 'http') !== 0 && !empty($value)) {
                        $value = BASE_URL . '/' . ltrim($value, '/');
                    }
                }
            }
        }
    }
}

function response($status, $message, $data = null) {
    if ($data !== null) {
        fix_paths($data);
    }
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

include_once '../dbConnect.php';

if (!$con) {
    response('error', 'Database connection failed');
}
?>
