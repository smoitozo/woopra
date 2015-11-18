<?php
/*
 * A quick and dirty PHP shell script for deleting a list of profiles from Woopra
 *
 * Author: Steve Moitozo
 * Date: 17 Oct 2015
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
 * DATA
 */

//Provide the comma delimited list of profile IDs to operate on
$woopra_profile_ids = "SOME_PID,SOME_OTHER_PID";

/*
 * EXECUTION (Don't touch)
 */

// Create an array of profile IDs
$arrProfileIDs = explode(',', $woopra_profile_ids);

// Loop over the IDs and delete each one
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

        return $result . "(" . $pid . ")
";
}
