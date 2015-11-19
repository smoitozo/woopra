<?php
/*
 * A quick and dirty PHP shell script for deleting a list of profiles from Woopra using their API.
 * I used it to get rid of a bunch of spurious profiles created by a bot that hit one of my sites.
 *
 * 1) Tag all the profiles you want to get rid of. Then export them from Woopra in CSV. 
 *    NOTE: Check the file to ensure that the first column is "~pid".
 *
 * 2) Adjust the CONFIGURATION section with your AppID, SecretKey, and the domain for your website.  
 * 
 * 3) Execute this script from the command line as follows:
 *
 *   php -q woopra_delete_profiles.php /path/to/woopra/export/file.csv
 *
 * If you would like to capture the return messages in a log file you can execute the script like this:
 *
 *   php -q woopra_delete_profiles.php  /path/to/woopra/export/file.csv > output.log
 *
 * Author: Steve Moitozo @SteveMoitozo2
 * Date: 19 Oct 2015
 * License: MIT
 */

/*
 * CONFIGURATION
 */
$woopra_api_url = "https://www.woopra.com/rest/2.2/profile/delete";
$woopra_appid   = "YOUR APP ID";
$woopra_key     = "YOUR KEY";
$woopra_website = "YOUR DOMAIN";
$woopra_search_key = "pid";




/*
 * EXECUTION (Don't touch)
 */

// Were we handed the path to the Woopra export file?
if(!(isset($argv[1]) && $argv[1])){
        die("Whoa, wait a minute. I need you to tell me where Woopra export file with the PIDs is.\n");
}

// Does the file exist?
if(!(file_exists($argv[1]) && is_file($argv[1]))){
        die("Hmmm. This file doesn't exist (" . $argv[1] . ")\n");
}

// Create an array of profile IDs
$arrProfileIDs = woopraExport2PidArray($argv[1]);

print("We're ready to go, deleting " . count($arrProfileIDs) . " PIDs from Woopra.\n");

// Loop over the IDs and delete each one from Woopra
foreach($arrProfileIDs as $strPid){

        print(deleteWoopraProfile($strPid));

}


/*
 * USEFUL FUNCTIONS
 */

// A function for deleting a profile from Woopra
function deleteWoopraProfile($pid){
	GLOBAL $woopra_api_url, $woopra_appid, $woopra_key, $woopra_website, $woopra_search_key;

	$fields = 'website=' . urlencode($woopra_website) . '&pid=' . urlencode($pid);

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $woopra_api_url);
	curl_setopt($ch, CURLOPT_USERPWD, $woopra_appid . ':' . $woopra_key);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_POST, 2);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

        // execute the post
	$result = curl_exec($ch);

	curl_close($ch);

	return $result . "(" . $pid . ")\n";
}

// A function to read in a Woopra export file and return an array of PIDs
function woopraExport2PidArray($woopraExportFilePath){

        $arrContents = file($woopraExportFilePath);
    
        $arrPids = array();
    
        foreach($arrContents as $line){
                $arrLine = explode('","', $line);
                $strPid = str_replace('"','',$arrLine[0]);
    	
                // Skip the first line
                if('"~pid' != $strPid){
                        $arrPids[] = $strPid;
                }
        }
    
        return $arrPids;
}

?>
