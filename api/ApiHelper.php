<?php

define("ERROR_BAD_REQUEST",     400);
define("ERROR_NOT_AUTHORIZED",  401);
define("ERROR_NOT_FOUND",       404);
define("ERROR_CONFLICT",        409);
define("ERROR_SERVER",          500);

define("PC_USER", "pc_user");

// CORS.
header("Access-Control-Allow-Origin: *");
if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {

    if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_METHOD"])) {
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    }         

    if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"])) {
        header("Access-Control-Allow-Headers: {$_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"]}");
    }

    exit();
}

function getJsonBody() {
    return json_decode(file_get_contents("php://input"), true);
}

function response($data) {
    header("Content-Type: application/json");
    echo(json_encode([
        "response" => $data
    ]));
    exit();
}

function responseArray($array) {
    response([
        "count" => count($array),
        "items" => $array
    ]);
}

function error($code) {
    http_response_code($code);
    header("Content-Type: application/json");
    exit();
}

?>