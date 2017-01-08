<?php

session_start();

if (isset($_SESSION["user"])) {
	header("Location: ./books");
} else {
	header("Location: ./login");
}

?>