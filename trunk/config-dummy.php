<?php
  /***********
  See the NOTICE file distributed with this work for additional
  information regarding copyright ownership.  Licensed under the Apache
  License, Version 2.0 (the "License"); you may not use this file except
  in compliance with the License. You may obtain a copy of the License
  at http://www.apache.org/licenses/LICENSE-2.0 Unless required by
  applicable law or agreed to in writing, software distributed under the
  License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR
  CONDITIONS OF ANY KIND, either express or implied. See the License for
  the specific language governing permissions and limitations under the
  License.
  ***************/
// **  You need to copy config-dummy.php to config.php and edit in proper values for the database and paths to the executables ** //

// ** Paths to Executables and your computer's URL ** //

$blastallpath = "";
$formatdbpath = "";
$clustalwpath = "";
$readseqpath = "";
$TGF = "";
$PS2PDF ="";
$PAUPpath  = "";
$serverURL = "http://............../"; # e.g. "http://MyComputerName.InstituteCode.ac.uk/";

// MySQL settings. You should not need to change these if you follow the instructions on the README//

define('DB_NAME', 'rnavirusdb');     // The name of the database
define('DB_USER', 'webuser');     // Your MySQL username
define('DB_PASSWORD', ''); // ...and password
define('DB_HOST', 'localhost');

// No more editing (other addresses will be adjusted here automatically) //
$myURL = $serverURL."rnavirusdb/";
$FIGTREEURL = $serverURL."rnavirusdb/lib/figtreeapplet.jar";
$BlastAlignURL = $serverURL."cgi-bin/BlastAlign.cgi"; # Only used in one option. You need to copy the version in the download to your cgi executables directory and make sure it is executable by all

$table_prefix  = 'rv_';   // example: 'rv_' or 'b2' or 'mylogin_'

// Google Analytics Tracker ID to use for this site
define('GOOGLE_TRACKER_ID', 'UA-EXAMPLE-NUM');  // Your Google Analytics tracker code
define('ABSPATH', dirname(__FILE__).'/');
require_once(ABSPATH.'rv-settings.php');
?>
