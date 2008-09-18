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
	mysql_select_db("$database",$db);
	$title = "FigTree";
	openDocument($title);
	drawHeader();
	$virusID = $_GET['id'];
	$segID = $_GET['segmentID'];
	$tree_ID = $_GET['FigTree'];

	if ($tree_ID) {
		expand_names($tree_ID);
		getFigTree_query($tree_ID, $FIGTREEURL, $ABSPATH, $myURL);
	}
	else {
		draw_toolbar($virusID);
		$result = mysql_query("SELECT viruses.name, segments.name FROM segments, viruses WHERE (segments.id=\"$segID\" AND viruses.id=\"$virusID\" AND segments.virus_id = viruses.id)",$db);	
		$row = mysql_fetch_row($result); 
		$virus_name = $row[0]; 
		$segment_name = $row[1];  

		if ($segment_name == "monopartite") {
			echo '<br><h1>Tree of reference genome sequences for '.$virus_name.'</h1><br><br>';
		}
		else {
			echo '<br><h1>Tree of reference genome sequences for '.$segment_name.' of '.$virus_name.'</h1><br><br>';
		}
	
		getFigTree($segID, $FIGTREEURL, $ABSPATH, $myURL);
	}
	drawFooter("Robert Belshaw, Tulio de Oliveira, Sidney Markowitz & Andrew Rambaut"); 
	closeDocument();

# functions
	function expand_names($tree_ID) {
		global $db;
		$array = array();# this will hold full name description
		$resource = mysql_query("SELECT acc, isolate, strain FROM isolates",$db);
		if ($row = mysql_fetch_array($resource)) {
			do {
			$line = $row["isolate"]."-".$row["strain"]."-acc:".$row["acc"];
			array_push($array, $line);
			} while ($row = mysql_fetch_array($resource));
		}
		$contents = file_get_contents($tree_ID);
		#echo "<Br><br>starting contents are ".$contents."<br>";
		$pattern = "/\d+ \w+,/";
		preg_match_all($pattern, $contents, $matches); #remember, this creates a multidimensional array called matches, so always need to used $matches[0][entry number]
		foreach($matches[0] as $value) {
			$pattern = "/\d+ (\w+),/";
			$replacement = "$1";
			$value = preg_replace($pattern, $replacement, $value);
			$new_array = preg_grep("/$value/", $array);
			$new_array = array_values($new_array);
			#echo "old name is ".$value. " new taxon name is ".$new_array[0]."<br>";
			if ($new_array[0]) {
				$contents = preg_replace("/$value/", "\"$new_array[0]\"", $contents);  # replacing corresponding full description for accession
			}
		}
		#echo "<Br><br>final contents are ".$contents."<br>";
		#$contents = preg_replace("/UserQuery/", "UserQuery[&!color=\"red\"]", $contents); #'[&!color="red"] [&!color=#FF0000]
		file_put_contents($tree_ID, $contents);
	}

	function getFigTree($segID, $FIGTREEURL, $ABSPATH, $myURL) {
		global $db;
		$treeName = $segID . ".tre";
		$resource = mysql_query("SELECT tree FROM segments WHERE id=\"$segID\"",$db);	
		$tree = mysql_result($resource, 0); // only one cell in field
		if ($tree) { # tree will not be there if fewer than 3 aligned sequences
			$tempfile = tempnam("/tmp", "arvore"); # use relative path to tmp folder
			$tree_ID = $tempfile.".tre";
			$handle = fopen($tree_ID, "w");
			if ($handle) {
				if (fwrite($handle, "$tree") == TRUE) {
			}
			else {
				echo "<br>WebServer Error: cannot write treefile<br>";
			}
		}
		else {
			echo "<br>WebServer Error: no handle created for treefile<br>";
		}
		expand_names($tree_ID);
   	 	exec ("(cp $tree_ID ".ABSPATH."tmp/$treeName)"); # cannot point browser into /tmp so have to copy to a dir within rnavirusdb (ABSPATH gives path to it)
   	 	unlink($tree_ID);
		unlink($tempfile);
		$treeurl = $myURL."tmp/".$treeName;
		#echo '<br> tree name is:'.$treeurl.'<br>';
  		echo '<applet'
		. ' code="figtree.applet.FigTreeApplet"'
		. ' archive="' . $FIGTREEURL . '"'
		. ' width="800" height="600">'
		. '<param name="tree" value="' . $treeurl .'" />'
		. '<param name="style" value="default" />'
		. 'Browser does not support Java</applet>';
		echo '<p><a href="http://tree.bio.ed.ac.uk/software/figtree" target="_blank"><small><i>FigTree applet by Andrew Rambaut, Institute of Evolutionary Biology, University of Edinburgh</i></small></a></p>';

  	 	}
    }
    
    	function getFigTree_query($tree_ID, $FIGTREEURL, $ABSPATH, $myURL) {
		$tree_URL = $myURL.$tree_ID;
		#echo '<br> paup output is at:'.$tree_ID.' copying to '.$tree_URL.'<br>';
		exec ("(cp $tree_ID ".ABSPATH."tmp/)");
  		echo '<applet'
		. ' code="figtree.applet.FigTreeApplet"'
		. ' archive="' . $FIGTREEURL . '"'
		. ' width="800" height="600">'
		. '<param name="tree" value="' . $tree_URL .'" />'
		. '<param name="style" value="default" />'
		. 'Browser does not support Java</applet>';
		echo '<p><a href="http://tree.bio.ed.ac.uk/software/figtree" target="_blank"><small><i>FigTree applet by Andrew Rambaut, Institute of Evolutionary Biology, University of Edinburgh</i></small></a></p>';
    }

    
 	function draw_toolbar($virusID) {
		echo "<TABLE CLASS='toolbar' WIDTH='820px'>";
		echo "<tbody><tr><td><a href='index.php' >Home</a></td>";
		echo "<td><form action='search.php' method='get'><input type='text' name='query'/><input type='submit' value='Search'/></form></td>";
		echo "<td><a href='browse.php'>Browse</a></td>";
		echo "<td><a href='blast.php''>BLAST</a></td>";
		echo "<td><a href='align.php''>Align</a></td>";
		echo '<td><a href="proteins.php?id='.$virusID.'">Proteins</a></td>';
		echo "<td><a href='http://www.ncbi.nlm.nih.gov/Taxonomy/Browser/wwwtax.cgi?mode=Info&id=$virusID&lvl=3&lin=f&keep=1&srchmode=1&unlock'>GenBank</a></td>";
		echo "</tr></table>";
	}
