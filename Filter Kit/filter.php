<?php

// IMPORTANT NOTE !
// A page may not work if at least on of the file/folder permission contained in the file path is not 755 !

/* Display errors if uncommented */

/*
error_reporting(E_ALL);
ini_set("display_errors", 1);
*/

// Set the timezone
//date_default_timezone_set('Europe/Vienna');


/* Main functions */

function sys_error($code)
{
	print("A major error occured!<br />Error code: " . $code);
	exit(0);
}


$domain = "DOMAIN_NAME.com";

$inter_website_path = "http://DOMAIN_NAME.up";
$website_path = "";

/* Import php files */

if (
	file_exists("assets/php/utils.php")
	&&
	file_exists("assets/php/functions.php")
)
{
	require_once("assets/php/utils.php");
	require_once("assets/php/functions.php");
} else {
	sys_error(0x616c7068612d31);
}

/* Headers */

// Disable the possibility to be integrated to iframes
header("X-Frame-Options: DENY");
header("Content-Security-Policy: frame-ancestors 'none'", false);


$page_loaded = false; // If page not loaded = Error 404


/* System variables */
$sys = array(
	"index" => "index.html",
	"max-width" => 1199, // Maximum width for mobile version
	"min-width" => 1200 // Minimum width for normal version
);

if (isset($_GET["absolute_path"])) {
	$filter = array(
		"absolute_path" => $_GET["absolute_path"],
		"file_extension" => null,
		"num" => 0, // For loops
		"multiple_files_redirection" => null,
		"multiple_files_redirection_path" => null,
		"path" => substr($_GET["absolute_path"], (strlen($_GET["absolute_path"]) - strlen($website_path)) * -1), // Path without the $website_path at the start
		"redirected" => false,
		"redirections" => json_decode(file_get_contents("assets/json/redirections.json"), true),
		"whitelist" => json_decode(file_get_contents("assets/json/whitelist.json"), true),
		"whitelist_status" => false
	);
	
	// See if page need a redirection
	for ($filter["num"] = 0; $filter["num"] < count($filter["redirections"]); $filter["num"]++)
	{
		if ($filter["absolute_path"] === $website_path . $filter["redirections"][$filter["num"]][0]) { // If page is like this -> "https://www.example.com/example/" & "https://www.example.com/example.txt"
			$filter["path"] = $filter["redirections"][$filter["num"]][1]; // Change the path to a redirection
			$filter["redirected"] = true;
			break;
		} else if ($filter["absolute_path"] . "/" === $website_path . $filter["redirections"][$filter["num"]][0]) { // If page is like this -> "https://www.example.com/example" (directory)
			$filter["absolute_path"] = $filter["redirections"][$filter["num"]][1]; // Change the path to a redirection
			$filter["redirected"] = true;
			break;
		} else if ($filter["absolute_path"] === $website_path . $filter["redirections"][$filter["num"]][0] . $sys["index"]) { // If page is like this -> "https://www.example.com/example/index.html"
			$filter["absolute_path"] = $filter["redirections"][$filter["num"]][1]; // Change the path to a redirection
			$filter["redirected"] = true;
			break;
		} else if (substr($filter["redirections"][$filter["num"]][0], -1) == "/") { // If has a "/" at the end
			$filter["multiple_files_redirection"] = substr(
				substr($filter["absolute_path"], (strlen($filter["absolute_path"]) - explode("/", $filter["absolute_path"])[count(explode("/", $filter["absolute_path"]))]) * -1), // This line has been changed the 10/08/2020 after the directory problem ("universal/WEBSITE" worked, but not "/WEBSITE")
				(
					strlen($filter["absolute_path"]) - strlen($website_path . $filter["redirections"][$filter["num"]][0])
				) * -1);
			// ^ Page like this -> "https://www.example.com/uploads/USER_ID.png" ^
			$filter["multiple_files_redirection_path"] = substr(
				$filter["path"],
				0,
				strlen($filter["multiple_files_redirection"]) * -1);
			// ^ Page like this -> "https://www.example.com/UPLOADS/user_id.png" (Get the path) ^

			if (
				$filter["redirections"][$filter["num"]][0] === $filter["multiple_files_redirection_path"]
				&&
				$filter["redirections"][$filter["num"]][0] !== "/"
				&&
				$filter["multiple_files_redirection_path"] !== "/"
			)
			{
				$filter["path"] = $filter["redirections"][$filter["num"]][1] . $filter["multiple_files_redirection"];
				$filter["redirected"] = true;
				break;
			}
		}
	}
	
	// If has not been redirected
	if ($filter["redirected"] === false) {
		if (file_exists("." . $filter["path"])) {
			if (is_file("." . $filter["path"])) {
				
			} else {
				$filter["path"] = $filter["path"] . "index.html";
				
				if (!is_file("." . $filter["path"])) {
					$filter["path"] = $filter["path"] . "/index.html";
				}
			}
		}
	}
	
	
	// See if page is in the whitelist
	for ($filter["num"] = 0; $filter["num"] < count($filter["whitelist"]); $filter["num"]++)
	{
		if ($filter["path"] === $filter["whitelist"][$filter["num"]]) { // If page is like this -> "https://www.example.com/example/index.html"
			$filter["whitelist_status"] = true;
			break;
		} else if ($filter["path"] . $sys["index"] === $filter["whitelist"][$filter["num"]]) { // If page is like this -> "https://www.example.com/example/"
			$filter["whitelist_status"] = true;
			break;
		} else if ($filter["multiple_files_redirection_path"] === $filter["whitelist"][$filter["num"]]) { // If page is like this -> "https://www.example.com/uploads/user_id.png"
			$filter["whitelist_status"] = true;
			break;
		}
	}
	
	
	$filter["path"] = "." . $filter["path"]; // Change "/page/index.html" to "./page/index.html" so we can include the file
	
	/* Include the file */
	
	if ($filter["whitelist_status"] === true) { // Check if file or directory exists
		if (file_exists($filter["path"]) && is_file($filter["path"])) { // Check if it's a file and not a directory
			/* Headers */
			$filter["file_extension"] = new SplFileInfo($filter["path"]);
			$filter["file_extension"] = $filter["file_extension"]->getExtension();
			
			// Website cache
			//header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
			//header("Expires: Sun, 11 Jun 2006 03:30:00 GMT"); // Website cache expiration data (If old date -> 'disable' the website cache)
			
			// Content type
			header("Content-Type: " . mime_type($filter["path"])); // mime_content_type() is not working, so here myme_type() is used (defined in "./assets/php/functions.php")
			
			if (in_array($filter["file_extension"], json_decode(file_get_contents("./assets/json/php_extensions.json")))) { // If file use PHP
				include_once($filter["path"]);
			} else {
				print(file_get_contents($filter["path"]));
			}
		} else {
			error(404);
		}
	} else {
		error(403);
	}
} else {
	error(0x6572726f7231);
}

?>