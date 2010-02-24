<?php
	require('../../example_dbconfig.php');    // Contains DB configuration
	$db = new DBConn;
	
	if (isset($_POST["PHPSESSID"])) {
		session_id($_POST["PHPSESSID"]);
	}
	session_start();

	if (!isset($_FILES["Filedata"]) || !is_uploaded_file($_FILES["Filedata"]["tmp_name"]) || $_FILES["Filedata"]["error"] != 0) {
		// Usually we'll only get an invalid upload if our PHP.INI upload sizes are smaller than the size of the file we allowed
		// to be uploaded.
		header("HTTP/1.1 500 File Upload Error");
		if (isset($_FILES["Filedata"])) {
			echo $_FILES["Filedata"]["error"];
		}
		exit(0);
	}
	

	if (!isset($_FILES["Filedata"])) {
		echo "Not recieved, probably exceeded POST_MAX_SIZE";
	}
	else if (!is_uploaded_file($_FILES["Filedata"]["tmp_name"])) {
		echo "Upload is not a file. PHP didn't like it.";
	} 
	else if ($_FILES["Filedata"]["error"] != 0) {
		echo "Upload error no. " + $_FILES["Filedata"]["error"];
	} else {
		$name = $_FILES['Filedata']['name'];
		echo "Complete";
		$db->getGrid();
		$upload = $db->grid->storeUpload('Filedata',$name);		
	}
	
?>