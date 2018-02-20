<?php

include_once dirname(__FILE__)."/ApiHelper.php";
error_reporting(E_ALL & ~E_DEPRECATED);

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    error(ERROR_BAD_REQUEST);
}

$user = getUserOrError();

response([
    "id" => $user["id"],
    "email" => $user["email"],
    "name" => $user["name"],
    "twitter" => [
        "username" => $user["twitter_name"],
        "book" => $user["twitter_book_id"]
    ]
]);

?>