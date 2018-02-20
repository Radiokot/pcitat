<?php

define("ERROR_BAD_REQUEST",     400);
define("ERROR_NOT_AUTHORIZED",  401);
define("ERROR_NOT_FOUND",       404);
define("ERROR_CONFLICT",        409);
define("ERROR_SERVER",          500);

define("EMAIL_HEADER", "HTTP_X_AUTH_EMAIL");
define("KEY_HEADER", "HTTP_X_AUTH_KEY");

include_once "../php/UserManager.php";

// CORS.
header("Access-Control-Allow-Origin: *");
if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {

    if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_METHOD"])) {
        header("Access-Control-Allow-Methods: GET, POST, DELETE, PATCH, OPTIONS");
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
    ], JSON_UNESCAPED_UNICODE));
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

function getUserOrError() {
    $user = null;
    if (isset($_SERVER[EMAIL_HEADER]) && isset($_SERVER[KEY_HEADER])) {
        $email = $_SERVER[EMAIL_HEADER];
        $key =  $_SERVER[KEY_HEADER];

        $user = UserManager::getByEmail($email);
        if ($key != hash("sha256", $user["email"].$user["password"])) {
            $user = null;
        }
    }

    if ($user != null) {
        return $user;
    } else {
        error(ERROR_NOT_AUTHORIZED);
        return null;
    }
}

?>
