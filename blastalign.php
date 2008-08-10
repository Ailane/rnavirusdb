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
	$segmentID = $_POST[id];
	$sequence = $_POST[sequence];
	$title = "Using BlastAlign to align to ".get_name($segmentID); 
	openDocument($title);
	drawHeader();
	draw_toolbar();

	#echo 'query is '. $segmentID.' sequence is '.$sequence.'<br>';
	$lib = make_lib($segmentID, $sequence);
	#echo 'lib made at '.$lib.'<br>';
	echo '<br><br><h1>Have created your libraries. Just click Run to perform the analysis<br></h1>
	<form action='.$BlastAlignURL2.' method="get"><input type="hidden" name="id" 
	value="'.$lib.'"/><input type="submit" value="Run"/></form>';

	drawFooter("Robert Belshaw, Tulio de Oliveira, Sidney Markowitz & Andrew Rambaut"); 
	
	closeDocument();
	

	// subroutines

	function draw_toolbar() {
		echo "<TABLE CLASS='toolbar' WIDTH='820px'>";
		echo "<tr><td><a href='index.php' >Home</a></td>";
		echo "<td><form action='search.php' method='get'><input type='text' name='query'/><input type='submit' value='Search'/></form></td>";
		echo "<td><a href='browse.php'>Browse</a></td>";
		echo "<td><a href='blast.php''>BLAST</a></td>";
		echo "<td><a href='proteins.php''>Proteins</a></td>";
		echo "</tr></table>";
	}


	function get_name($segmentID) {
		global $db;
		$resource = mysql_query("SELECT viruses.name FROM segments,viruses WHERE segments.id=\"$segmentID\" AND segments.virus_id = viruses.id",$db);
		if ($sequence = mysql_fetch_array($resource)) { # will only be one entry
			$name = $sequence[0];
		}
		else {
			echo "Failed to convert segment id, ".$segmentID." to virus name<br>";
		}
		return $name;
		
	}
	
	function make_lib($segmentID, $sequence) {
		global $db;

		$lib = tempnam("/tmp", "BlastAlign-");
		$resource = mysql_query("SELECT id, sequence FROM genomealigns WHERE segment_id=\"$segmentID\"",$db);
		$output = '';
		if ($row = mysql_fetch_array($resource)) {
			do {
				$seq = $row["sequence"];
				$id = $row["id"];
				$output = $output.">$id\n$seq\n";
			} while ($row = mysql_fetch_array($resource));
		}
		$handle = fopen($lib, "w");
		if (fwrite($handle, ">UserQuery\n$sequence\n$output") == TRUE) {
				//echo "created file<br>";
		}
		else {
				echo "<br>WebServer Error: cannot write library file<br>";
		}
		return $lib;
	}