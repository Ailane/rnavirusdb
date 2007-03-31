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
// ** MySQL settings ** //
// **  copy config-dummy.php to config.php and edit in proper values for the actual database ** //
define('DB_NAME', 'bioinf');     // The name of the database
define('DB_USER', 'bioinf');     // Your MySQL username
define('DB_PASSWORD', 'PASSWORD'); // ...and password
define('DB_HOST', 'cs-db.cs.auckland.ac.nz');

$table_prefix  = 'rv_';   // example: 'rv_' or 'b2' or 'mylogin_'

// Google Analytics Tracker ID to use for this site
define('GOOGLE_TRACKER_ID', 'UA-EXAMPLE-NUM');  // Your Google Analytics tracker code
/* Stop editing */

define('ABSPATH', dirname(__FILE__).'/');
require_once(ABSPATH.'rv-settings.php');
?>
