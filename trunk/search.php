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
	
	$db = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ('Could not connect: ' . mysql_error());

	mysql_select_db($database,$db) or die ('Could not select db: ' . mysql_error());

	$title = "Search Results";
	
	$query = $_GET[query];

	openDocument($title);

	drawHeader();
	draw_toolbar();
	
	if ($query) {
		$result = mysql_query("SELECT id, name, family, genus FROM viruses WHERE (name LIKE \"%$query%\" OR  genus LIKE \"%$query%\" OR family LIKE \"%$query%\" OR abbreviations LIKE \"%$query%\") ORDER BY name",$db);
		if ($virus = mysql_fetch_array($result)) {
			echo "<br><br><h1>Select the virus name from the following matches to \"$query\":</h1>";
			echo "<DL><TABLE CLASS='data' WIDTH='600px'>";
			echo "<TR> <TD CLASS='heading'> Name</TD><TD CLASS='heading'>Genus</TD><TD CLASS='heading'>Family</TD>";
			echo "</TR>";


			do {
				$id = $virus["id"];
				$name = $virus["name"];
				$genus = $virus["genus"];
				$family = $virus["family"];
				$name = preg_replace("/APOST/", "'", $name);
				echo '<TR><TD><a href="virus.php?id='.$id.'">'.$name.'</a></TD><TD>'.$genus.'</TD><TD>'.$family.'</TD></TR>';
				//echo '<a href="virus.php?id='.$id.'">'.$name.' : '.$genus.' : '.$family.'</a><br>';
			} while ($virus = mysql_fetch_array($result));
			echo "</TABLE>";
		}
		else {
			echo "<p>Sorry, no matches found to \"$query\"</p>";
		}
	}
	
	
	else {
		echo '<br><h1>Type the virus name or abbreviation here and click Search</h1>';
		echo "<td><form action='search.php' method='get'><input type='text' name='query'/><input type='submit' value='Search'/></form></td>";
	}

	
	drawFooter("Robert Belshaw, Tulio de Oliveira, Sidney Markowitz & Andrew Rambaut"); 

	closeDocument();
	
	// subroutines
	
	function draw_toolbar() {
		echo "<TABLE CLASS='toolbar' WIDTH='820px'>";
		echo "<tr><td><a href='index.php' >Home</a></td>";
		echo "<td><form action='search.php' method='get'><input type='text' name='query'/><input type='submit' value='Search'/></form></td>";
		echo "<td><a href='browse.php'>Browse</a></td>";
		echo "<td><a href='align.php''>Align</a></td>";
		echo "<td><a href='proteins.php''>Proteins</a></td>";
		echo "</tr></TABLE>";
	}
	
?>
