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
// ** MySQL settings ** //

define('DB_NAME', '');     // The name of the database
define('DB_USER', '');     // Your MySQL username
define('DB_PASSWORD', ''); // ...and password
define('DB_HOST', '');

// ** Paths to Executables ** //

$blastallpath = "";
$formatdbpath = "";
$fastafilespath = "";
$clustalwpath = "";
$readseqpath = "";
$TGF = "";
$PS2PDF ="";

// http://sourceforge.net/projects/readseq
// http://bioinformatics.ubc.ca/resources/tools/readSeq
// http://iubio.bio.indiana.edu/soft/molbio/readseq/

$table_prefix  = 'rv_';   // example: 'rv_' or 'b2' or 'mylogin_'

// Google Analytics Tracker ID to use for this site
define('GOOGLE_TRACKER_ID', 'UA-EXAMPLE-NUM');  // Your Google Analytics tracker code
/* Stop editing */

define('ABSPATH', dirname(__FILE__).'/');
require_once(ABSPATH.'rv-settings.php');
?>
