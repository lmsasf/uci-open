<?php
	//error_reporting(E_ERROR);

	//change define DBHOST to the DLC main site_config file. 4/2005

	define ("DBHOST","localhost");
	
	//set your CAT database username here
	define ("DBUSER","media");
	
	//set the salt for password encryption (used in first-time configuration and in BlueAuth.php
	define ("DBSALT","Stripes with Bill Murray");	

	//set your CAT database password here
	//define ("DBPASS","JieP3*ck2");
	define ("DBPASS","06-media-13");
	
	//set your MYSQL users database username here
	define("MYSQL_USER","media");
	
	//define("MYSQL_PASS","JieP3*ck2");
	define("MYSQL_PASS","06-media-13");
	
	//set your database name here or leave "dlc" as default. Changed to media for testing.
	define ("DB","ocw");
	
	//set your base URL here
	define ("BASE_URL","localhost");
	
	// don't change. Directory "oo" is where all basic code resides
	define ("CODEBASE","/cat/oo/");
	
	//Moodle install directory
	//define("CMS_DIR","/cms/");
	
	//CAT install directory
	define ("LOCAL_DIR","");
	
	// Media Directory
	define ("MEDIA_BASE_DIR","/cat/media/");

	
	/******************************Read Important Local Filesystem Notes*****************
	 The following directories are used by the local filesystem and the ones below are for a LINUX server. For Wnddows systems, change the backslashes and use the appropriate drive letter, as below:
	*/


	
	//Comment first line and uncomment second line for Windows
	define ("MEDIA_DIR","/Web/htdocs/cat/media/");
	//define ("MEDIA_DIR","c:\\documents and settings\\cooperml\\my documents\\cat\\media\\");
	
	//Comment first line and uncomment second line for Windows
	define ("CODE_DIR","/Web/htdocs/cat/oo/");
	//define ("CODE_DIR","c:\\documents and settings\\cooperml\\my documents\\cat\\oo\\");
	
	//Comment first line and uncomment second line for Windows
	define ("ADMIN_DIR","/Web/htdocs/cat/admin/");
	//define ("ADMIN_DIR","c:\\inetpub\\wwwroot\\cat\\admin\\");
	
	//Comment first line and uncomment second line for Windows
	define ("UI_DIR","/Web/htdocs/cat/UI/");
	//define ("UI_DIR","c:\\documents and settings\\cooperml\\my documents\\cat\\UI\\");

	//Comment first line and uncomment second line for Windows
	define ("LOGS_DIR","/Web/htdocs/cat/logs/");
	//define ("LOGS_DIR","c:\\documents and settings\\cooperml\\my documents\\cat\\logs\\");
	
	//comment firsts line and uncomment second line for Windows
	define ("DIRECTORY_SEPARATOR","/");
	//define ("DIRECTORY_SEPARATOR","\\");
?>