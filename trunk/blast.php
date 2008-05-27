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
	
	$title = "BLAST Results";
	
	$db = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD); // Need to call within function
	mysql_select_db($database,$db);

	$query = $_POST[query];
	$results = $_POST[results];

	openDocument($title);

	drawHeader();
	draw_toolbar();
	 
	if ($query) {
		$infile = write_file($query);
		$outfile = tempnam("/tmp", "BLASTresults");
		$type = check_type($query);
		
		if ($type) {
			if ($type == "aa")	{
				exec("$blastallpath -p tblastn -i $infile -d cgi-bin/nuc_library_acc.lib -o $outfile -m 8");
			}
			elseif ($type == "nuc")	{
				exec("$blastallpath -p blastn -i $infile -d cgi-bin/nuc_library_acc.lib -o $outfile -m 8");
			}
			echo '<br><br><br>Click here to get results of blast search
			<form action="blast.php" method="post">
			<input type="hidden" name="results" value="'.$outfile.'"/>
 			<p><input type="submit" value="Results"/></p>
			</form>
			';
		}
	}
	
	elseif ($results) {
		if (file_exists("$results")) {
			$filehandle = fopen("$results", "r");
			$num_rows = 0;
			while (!feof($filehandle)) {
				$text = fgets($filehandle);
				if (trim($text) != "") {
					$array = preg_split("/\t/", $text);
					$sequence = get_name($array[1]);
					if ($array[10] < 0.05) {
						$queue = array($sequence[1], $sequence[0], $array[1], $array[8], $array[9], $array[8], $array[9], $array[1], $sequence[2], $array[10]);
						$stack[$num_rows] = $queue;
						$num_rows++;
					}
				}
			}
			fclose($filehandle);
			if ($num_rows > 0) {
				echo "<DL><TABLE CLASS='data' WIDTH='600px'>";
				echo "<TR>";
				echo "<TD CLASS='heading'>Match<TD CLASS='heading'>Coordinates</TD><TD CLASS='heading'>E-value</TD>";
				echo "</TR>";
				echo '<br><br><h1>Found matches to the following entries in our database</h1>';
				echo '<p>Click on coordinates to see genomic location of each match (or on the name of the match for more information)</p>';
				echo '<dl>';
				for ($i = 0; $i < $num_rows; $i++) {
					echo '<TR><TD> <a href="virus.php?id='.$stack[$i][0].'">'.$stack[$i][1].'</a></TD><TD><a href="match.php?id='.$stack[$i][2].'-'.$stack[$i][3].'-'.$stack[$i][4].'">'.$stack[$i][5].'-'.$stack[$i][6].' on '.$stack[$i][7].'</a>  ('.$stack[$i][8].')</TD><TD>'.$stack[$i][9].'</TD>';
					echo '</dl>';
				}
 				echo "</TABLE>";
			}
			else {
				echo "<p>Sorry, no matches found</p>";
			}
		}
		else {
  			exit('<br>WebServer Error: Failed to open blast results file');
		}
	}


	else {
		$res = mysql_query("SELECT COUNT(*) FROM viruses",$db);
		$num = mysql_result($res, 0);  # will only be one entry
		echo '
		<h1><br><br>BLAST your sequence against our library of '.$num.' RNA viral genomes </h1>
		<dt>Paste below a single sequence (amino acid or nucleotide):</dt>
		<dd>
		<form action="blast.php" method="post">
			<p><textarea name="query" rows="10" cols="60"></textarea> <br>
			<input type="submit" value="BLAST"/> <input type="reset" value="reset"></p>
		</form>
		</dd>
		<br>
	
		</dd>
		
		<br><br>
		</dl>
		';
	}
	
	drawFooter("Robert Belshaw, Tulio de Oliveira, Sidney Markowitz & Andrew Rambaut"); 
	
	// sub-routines
	
	function check_type($query) {
		$sequence = preg_replace('/>(\S+)/', '', $query); # strip out name if fasta
		$sequence = preg_replace('/\s+/', '', $sequence); # strip out linebreaks if fasta
		$non_nuc = "";
		$non_aa = "";
		$non_nuc = preg_replace('/[acgtrymwskdhbvn-]/i', '', $sequence); # see what non nucleotide characters you have
		if ($non_nuc == "") {
			$type = "nuc";
		}
		else {
			$non_aa = preg_replace('/[deqilfp\*]/i', '', $non_nuc);
			if ($non_aa == "") {
				echo "<br><br>Your sequence is amino acid<br>";
				$type = "aa";
			}
			else {
				echo "<br><br>INPUT ERROR: Check your input sequence for characters that are not nucleotides or amino acids<br>";
				$type = FALSE;
			}
		}
		return $type;
	}
	
	function draw_toolbar() {
		echo "<TABLE CLASS='toolbar' WIDTH='820px'>";
		echo "<tr><td><a href='index.php' >Home</a></td>";
		echo "<td><form action='search.php' method='get'><input type='text' name='query'/><input type='submit' value='Search'/></form></td>";
		echo "<td><a href='browse.php'>Browse</a></td>";
		echo "<td><a href='align.php''>Align</a></td>";
		echo "<td><a href='proteins.php''>Proteins</a></td>";
		echo "</tr></TABLE>";
	}
	
	function get_name($id) {
		global $db;
		$result = mysql_query("SELECT viruses.name, viruses.id, Segments.name FROM Segments,viruses WHERE Segments.id=\"$id\" AND Segments.virus_id = viruses.id",$db);
		if ($sequence = mysql_fetch_array($result)) { # will only be one entry
			//do {
				//$name = $sequence[0];
			//} while ($sequence = mysql_fetch_array($result));
		}
		else {
			echo "Failed to convert id, ".$id." to name<br>";
		}
		return $sequence;
	}
	
	function write_file($query) {	
		$infile = tempnam("/tmp", "BLASTquery");
		$handle = fopen($infile, "w");
		if ($handle) {
			//echo "made handle $infile<br>";
		}
		else {
			echo "<br>WebServer Error: no handle created<br>";
		}
		$fasta = preg_match('/>/', $query);
		if ($fasta) { # query is in fasta format
			if (fwrite($handle, "$query") == TRUE) {
				//echo "created file<br>";
			}
			else {
				echo "<br>WebServer Error: cannot write file<br>";
			}
		}
		else { # query is single sequence
			if (fwrite($handle, ">UserQuery\n$query\n") == TRUE) {
				//echo "created file<br>";
			}
			else {
				echo "<br>WebServer Error: cannot write file<br>";
			}
		}
		return $infile;
	}

	closeDocument();
		

?>
