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
	include("config.php");
	
	$db = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);

	mysql_select_db($database,$db);

	$title = "RNA Viruses";
	$query = $_GET['id'];

	openDocument($title);

	drawHeader();
	
	if ($query) {
		echo "<br><br><br>Seeking entry in my database of ICTV links for $query<br>";
		get_link($query);
	}
	
	drawFooter("Robert Belshaw, Tulio de Oliveira, Sidney Markowitz& Andrew Rambaut"); 
	
	closeDocument();

	// sub-routine

	function get_link($query ) {
		$resource = mysql_query("SELECT link FROM ICTVlinks WHERE name=\"$query\"",$db);
		if ($row = mysql_fetch_array($resource)) {
			do {
				$link = $row["link"];
			} while ($row = mysql_fetch_array($resource));
		}
		if ($link) {
			echo "Found link: $link<br>";
			$link = "http://www.ncbi.nlm.nih.gov/ICTVdb/ICTVdB/".$link;
			echo "<a href='$link'>Click here for link to ICTVdb</a>";
		}
		else {
			echo "Sorry no link to ICTVdb available<br>";
		}
	}
?>



