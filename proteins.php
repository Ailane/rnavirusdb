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
	
	$title = "Proteins";

	drawHeader();
	draw_toolbar();
	
	$virusID = $_GET['id'];
	$showProteins = $_GET['showProteins'];
	$showLocations = $_GET['showLocations'];
	$choose_function = $_GET[choose_function];
	$choose_virus = $_GET[choose_virus];
	$choose_genus = $_GET[choose_genus];
	$choose_family = $_GET[choose_family];
	$choose_type = $_GET[choose_type];

	$trans_genome = $_GET[trans_genome];
	
	if ($virusID) {
		$result = mysql_query("SELECT * FROM viruses WHERE id=\"$virusID\"",$db);	
		$virus = mysql_fetch_array($result);
		$name = $virus["name"];
		$title = "Proteins - ".$name;
	}	
	openDocument($title);

	if ($trans_genome) {
		get_trans_genome($trans_genome, $virusID, $name);
		//echo 'will get '.$trans_genome.' for '.$virusID.'<br>';
	}
	
	elseif ($choose_function) {
		get_proteins($choose_function, $choose_virus, $choose_genus, $choose_family, $choose_type);
	}
	
	elseif ($virus) {
		echo '<br><h1>'.$virus["name"].'</h1>';
		echo '<br><h1> Click below to see details of the proteins in this virus</h1>';
					
		$result = mysql_query("SELECT * FROM segments WHERE virus_id=\"$virusID\" ORDER BY name ASC",$db);
		if ($segment = mysql_fetch_array($result)) {
			
			if ($virus["segments"] == 1) {
				echo '<dl><dt>(Note, unsegmented genome)</dt>';
				echo '<dd>';
				
				showProteins($segment["id"]);

				echo '</dd>';
			} else {
				echo '<dl><dt>Segments:</dt>';
				echo '<dd>';
	
				echo '<dl>';
				do {
					$segmentName = $segment["name"];
					$segmentID = $segment["id"];

					if ($showProteins) {
	
						echo '
							<dt><a href="proteins.php?id='.$virusID.'"><img src="images/disclosureOpen.png"> '.$segmentName.'</a></dt>
						';
						echo '<dd>';
						
						echo "<a href=http://www.ncbi.nlm.nih.gov/entrez/viewer.fcgi?db=protein&val=$segmentID > <input type='submit' value='GenBank'/><br><br>";
				
						showProteins($segmentID);
	
						echo '</dd>';
					} else {
						echo '
							<dt><a href="proteins.php?id='.$virusID.'&amp;showProteins=true"><img src="images/disclosureClosed.png"> '.$segmentName.'</a></dt>
						';

					}
	
				} while ($segment = mysql_fetch_array($result));
				echo '</dl>';
			}
		echo '<br><br><h1>Click below to download translated genome for this virus</h1>';
		echo 'Most RNA viruses have overlapping genes, so there are three possible translated genomes from which to choose<br>';
		echo "<br><br><TABLE CLASS='genome'>";
		//echo"<TR CLASS='heading'><TD CLASS='heading'>Genome picture</TD><TD>Type</TD><TD>Download proteomes</TD></TR>";
		echo '<TR CLASS="segment"><TD CLASS="segment"><img src=images/genemap.png width="290" height="94"></TD><TD></TD></TR>';
		echo '<TR CLASS="gene"><TD CLASS="gene"><img src=images/genemap1.png width="290" height="55"</TD><TD><a href="proteins.php?id='.$virusID.'&amp;trans_genome=proteome_std"><big>Simple</big></a></TD></TR>';
		echo '<TR CLASS="gene"><TD CLASS="gene"><img src=images/genemap2.png width="290" height="55"</TD><TD><a href="proteins.php?id='.$virusID.'&amp;trans_genome=proteome_no"><big>Non-overlapping</big></a></TD></TR>';
		echo '<TR CLASS="gene"><TD CLASS="gene"><img src=images/genemap3.png width="290" height="55"</TD><TD><a href="proteins.php?id='.$virusID.'&amp;trans_genome=proteome_nr"><big>Concatenated</big></a></TD></TR>';


		echo "</TABLE>";
			

			
		} else {
			echo '<dt>No segments found for this virus</dt><dd></dd>';
		}

		echo '</dd></dl>';

	} else { # entry to proteins page without being directed from virus page
		$virusID = "";
		select_by_function();
	}
	
	
	drawFooter("Robert Belshaw, Tulio de Oliviera, Sidney Markowitz & Andrew Rambaut"); 
	
	closeDocument();
	
	// subroutines 
	function draw_toolbar() {
		echo "<TABLE CLASS='toolbar' WIDTH='820px'>";
		echo "<tr><td><a href='index.php' >Home</a></td>";
		echo "<td><form action='search.php' method='get'><input type='text' name='query'/><input type='submit' value='Search'/></form></td>";
		echo "<td><a href='browse.php'>Browse</a></td>";
		echo "<td><a href='blast.php''>BLAST</a></td>";
		echo "<td><a href='align.php''>Align</a></td>";
		echo "</tr></table>";
	}

	function get_proteins($choose_function, $choose_virus, $choose_genus, $choose_family, $choose_type) {
		global $db;
		if ($choose_virus) {
			$resource = mysql_query("SELECT proteins.name, proteins.aa_seq, viruses.name FROM proteins, viruses WHERE viruses.id = \"$choose_virus\" AND proteins.function LIKE \"%$choose_function%\" AND proteins.virus_id = viruses.id",$db);
		}
		elseif ($choose_genus) {
			$resource = mysql_query("SELECT proteins.name, proteins.aa_seq, viruses.name FROM proteins, viruses WHERE viruses.genus = \"$choose_genus\" AND proteins.function LIKE \"%$choose_function%\" AND proteins.virus_id = viruses.id",$db);
		}
		elseif ($choose_family) {
			$resource = mysql_query("SELECT proteins.name, proteins.aa_seq, viruses.name FROM proteins, viruses WHERE viruses.family = \"$choose_family\" AND proteins.function LIKE \"%$choose_function%\" AND proteins.virus_id = viruses.id",$db);
		}
		elseif ($choose_type) {
			$resource = mysql_query("SELECT proteins.name, proteins.aa_seq, viruses.name FROM proteins, viruses WHERE viruses.type = \"$choose_type\" AND proteins.function LIKE \"%$choose_function%\" AND proteins.virus_id = viruses.id",$db);
		}
		else {
			echo 'No virus selected<br>';
		}
		if ($resource && ($sequence = mysql_fetch_array($resource))) {
			echo '<br><br>Amino acids sequence are:<br>';	
			do {
				$temp_array = preg_split("/;/", $sequence[0]);
				echo ">$sequence[2] - $temp_array[0]<br>$sequence[1]<br>";
			} while ($sequence = mysql_fetch_array($resource));
		}
		else {
			echo "<br><br>Sorry, no matches found\n";
		}

	}

	function get_trans_genome($trans_genome, $virusID, $name) {
		global $db;
		$trans_seq = "";
		$resource = mysql_query("SELECT $trans_genome FROM segments WHERE virus_id=\"$virusID\"",$db);
		if ($sequence = mysql_fetch_array($resource)) {
			do {
				$trans_seq = $trans_seq.$sequence[0];
			} while ($sequence = mysql_fetch_array($resource));
		}
		if ($trans_genome == "proteome_no") {
			$type = "non-overlapping";
		}
		elseif ($trans_genome == "proteome_nr") {
			$type = "concatenated";
		}
		elseif ($trans_genome == "proteome_std") {
			$type = "simple";
		}
		echo '<br><br>Translated '.$type.' genome of '.$name.' is: <br>'.$trans_seq.'<br>';	
	}
	
	function showProteins($segmentID) {
		global $db;
		global $virusID;
		global $showLocations;

		echo '<p>Proteins:</p>';

		$result2 = mysql_query("SELECT * FROM proteins WHERE segment_id=\"$segmentID\"",$db);
		if ($protein = mysql_fetch_array($result2)) {
			
			echo '<dl>';
			do {
				$proteinName = $protein["name"];
				if ($proteinName == "") {
					$proteinName = "unnamed protein";
				}
				$temp_array = preg_split("/;/", $proteinName);
				$proteinName = $temp_array[0];
				$proteinName = preg_replace("/APOST/", "'", $proteinName);
				$proteinID = $protein["id"];

				if ($showLocations) {
					echo '
						<dt><a href="proteins.php?id='.$virusID.'&amp;showProteins=true"><img src="images/disclosureOpen.png"> '.$proteinName.' ['.$protein["aa_length"].' aa]</a></dt>
					';
		
					echo '<dd><dl>';
									
					echo "<a href=http://www.ncbi.nlm.nih.gov/entrez/viewer.fcgi?db=protein&val=$proteinID > <input type='submit' value='GenBank'/>";
				
					echo '<dt><i>Coordinates:</i></dt><dd>';

					$result3 = mysql_query("SELECT * FROM coordinates WHERE protein_id=\"$proteinID\"",$db);
					if ($coordinate = mysql_fetch_array($result3)) {
					
						echo '<ul>';

						do {
							$start = $coordinate["start"];
							$length = $coordinate["length"];
							$end = $start + $length - 1;
							$phase = $coordinate["phase"];
							echo '<li>'.$start.' -> '.$end.' (frame: '.$phase.', length: '.$length.' bp)</li>';
						} while ($coordinate = mysql_fetch_array($result3));
						
						echo '</ul>';
						
					} else {
						echo '<p>No coordinates found for this protein</p>';
					}
					
					echo '</dd>';
					echo '<dt><i>Translation:</i></dt><dd><pre>'.wordWrap($protein["aa_seq"], 100, "\n", 1).'</pre></dd>';
					
					echo '</dl></dd>';
				} else {
					echo '
						<dt><a href="proteins.php?id='.$virusID.'&amp;showProteins=true&amp;showLocations=true"><img src="images/disclosureClosed.png"> '.$proteinName.'</a></dt>
					';
				}

			} while ($protein = mysql_fetch_array($result2));
			echo '</dl>';
		} else {
			echo '<dt>No proteins found for this segment</dt><dd></dd>';
		}
	}

	function select_by_function() {
		echo '<h1>Find and download proteins from the database</CENTER></h1>';
		echo '<br><TABLE BORDER="0" BGCOLOR ="#CCCCCC">CAUTION: This page relies currently on an automated word search of GenBank entries; it has not been verified manually</table>';

		global $db;
		$names = @mysql_query("SELECT id, name FROM viruses ORDER BY name ASC",$db);
		$genera = @mysql_query("SELECT DISTINCT genus FROM viruses ORDER BY genus ASC",$db);
		$families = @mysql_query("SELECT DISTINCT family FROM viruses ORDER BY family ASC",$db);
		$types = @mysql_query("SELECT DISTINCT type FROM viruses ORDER BY type ASC",$db);
		$functions = @mysql_query("SELECT id, name FROM proteinclassification ORDER BY name ASC",$db);
		if (!$names or !$functions or !$genera or !$types or !$families) {
			echo 'Unable to query database<br>';
		}
?>
		<form action="proteins.php" method="get">
		<label> <br><br>Select Proteins by Functional Group:
		<select size="1" name="choose_function">
<?php
		while ($function = mysql_fetch_array($functions)) {
			$this_function = $function['name'];
			$this_id = $function['id'];
			echo "<option value = '$this_id' >$this_function</option>\n";
		}

?>
		</select></label><br />
		<label><br><br>And by Virus:
		<select size="1" name="choose_virus">';
			<option selected value="">click and drag</option>
<?php
		while ($name = mysql_fetch_array($names)) {
			$this_virus = $name['name'];
			$this_virus_id = $name['id'];
			echo "<option value = '$this_virus_id' >$this_virus</option>\n";
		}
?>
		</select></label><br />
		<label><br><br> - or by Genus:
		<select size="1" name="choose_genus">';
			<option selected value="">click and drag</option>
<?php
		while ($genus = mysql_fetch_array($genera)) {
			$this_genus = $genus['genus'];
			echo "<option value = '$this_genus' >$this_genus</option>\n";
		}

?>
		</select></label><br />
		<label><br><br> - or by Family:
		<select size="1" name="choose_family">';
			<option selected value="">click and drag</option>
<?php
		while ($family = mysql_fetch_array($families)) {
			$this_family = $family['family'];
			echo "<option value = '$this_family' >$this_family</option>\n";
		}

?>
		</select></label><br />
		<label><br><br> - or by Type:
		<select size="1" name="choose_type">';
			<option selected value="">click and drag</option>
<?php
		while ($type = mysql_fetch_array($types)) {
			$this_type = $type['type'];
			echo "<option value = '$this_type' >$this_type</option>\n";
		}
?>
		</select></label><br />
		<br><br>Click here for results <input type="submit" value="Search" />
		</form>
<?php
	}

