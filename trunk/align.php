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
	
	$title = "Alignment";
	$segmentID = $_GET[query]; # 1. Called from virus page
	if (!$segmentID) {
		$segmentID = $_POST[query2]; # 1. Called from within align page (along with sequence - hence need POST method)
	}
	$sequence = $_POST[sequence]; # 2. Sequence submitted (along with segmentID). Perform BLAST 
	$blast_results = $_POST[blast_results]; # 3. Display BLAST results including coordinates of match to reference sequence
	$coord = $_POST[coord]; # will now adjust coordinates to allow for gaps in the reference sequence caused by it being in a multiple alignment
	
	drawHeader();
	draw_toolbar();

	if ($coord) {
		//echo 'coord is '.$coord.'<br>';
		$array = preg_split("/_link_/", $coord);
		$query_start = $array[0];
		$query_stop = $array[1];
		$ref_start = $array[2];
		$ref_stop = $array[3];
		$segmentID = $array[4];
		if ($segmentID == "NC_001802") {
			$ref_start = $ref_start - 335; # correct for missing region at start of HIV-1 alignment
			$ref_stop = $ref_stop - 335;
		}
		$sequence = $array[5];
		$title = $title.' - '.get_name($segmentID); 
		cut_slice($query_start, $query_stop, $ref_start, $ref_stop, $segmentID, $sequence);
	}
	
	elseif ($blast_results) {
		//echo 'we have the blast results at '.$blast_results.'<br> query seq is '.$sequence.'<br>';
		$segmentID = read_results($blast_results, $sequence); # just need seg id here for page title
		$title = $title.' - '.get_name($segmentID); 
	}
	
	elseif ($sequence) {
		$title = $title.' - '.get_name($segmentID);
		$sequence = preg_replace('/-/', '', $sequence); # strip out gaps from query
		//echo 'test query is '.$sequence.' ref is '. $segmentID.'<br>';
		$seq = $sequence;
		$name = "UserQuery";
		$query_file = write_file($name,$seq);
		$ref_file = write_ref_genome($segmentID);
		exec("$formatdbpath -i $ref_file -o T -p F"); # need to format ref_file for BLAST
		blast($query_file, $ref_file, $sequence);
		
	}


	elseif ($segmentID) {
		$name = get_name($segmentID);
		$title = $title.' - '.$name;
		$segment_name = get_segment_name($segmentID);
		$number = get_num_aligns($segmentID);
		echo '<br><br>
		<p><h1>Attempt to place your sequence into our alignment of '.$number.' genome(s) for '.$name.' ('.$segment_name.')</h1></p>
		Paste your nucleotide sequence below and click Run<br>
		<form action="align.php" method="post">
		<textarea name="sequence" rows="6" cols="60"></textarea> 
		<input type="hidden" name="query2" value="'.$segmentID.'"><br>
		<input value="Run" type="submit"/>  <input type="reset" value="reset">
		</form>
		';
	}
	
	
	else {
		echo '<br><br><h1>Click here to select virus to which you wish to align your sequence</h1>
		<form action="browse.php" method="get"><input type="submit" value="Browse"/></form>
	
		<br><br><p><h1>Or, attempt to align your sequence to our library of 701 RNA viral genomes</h1></p>
		Paste a single nucleotide sequence below (in any format) and click Run<br>
		<form action="/cgi-bin/BlastAlign.cgi" method="get">
		<textarea name="blastseq" rows="6" cols="60"></textarea> <br>
		<input value="Run" type="submit">
		</form>
		<br>The above window uses the BlastAlign tool. Click icon for details: 
		<a href="http://www.bioafrica.net/blast/BlastAlign.html">
		<img border="0" src="images/blastalign.gif" width="55" height="55">
		</a>
		';
	}

	openDocument($title);

	drawFooter("Robert Belshaw, Tulio de Oliveira, Sidney Markowitz & Andrew Rambaut"); 
	
	closeDocument();
	

	// subroutines
	function blast($query_file, $ref_file, $sequence) {
		global $blastallpath;
		$outfile = tempnam("/tmp", "results");
		//echo "query file $query_file ref_file is $ref_file outfile is $outfile<br>";
		exec("$blastallpath -p blastn -i $query_file -d $ref_file -o $outfile -m 8");
		echo '<br><br><h1>Click here to get results of initial BLAST search</h1>
		<form action="align.php" method="POST">
		<input type="submit" value="Results"/>
		<input type="hidden" name="blast_results" value="'.$outfile.'"/>
		<input type="hidden" name="sequence" value="'.$sequence.'"/>
		</form>
		';
	}	

	function cut_slice($query_start, $query_stop, $ref_start, $ref_stop, $segmentID, $sequence) {
		global $db, $clustalwpath, $readseqpath;
		//echo ' old ref start is '.$ref_start.'<br>';
		//echo ' old ref stop is '.$ref_stop.'<br>';
		//echo ' seq is '.$sequence.'<br>';
		$resource = mysql_query("SELECT sequence FROM GenomeAligns WHERE id=\"$segmentID\" ",$db);
		if ($row = mysql_fetch_array($resource)) { # will only be one entry
			$seq_as_string = $row[0];
		}
		$seq_as_array = preg_split("//", $seq_as_string);
		$gaps = 0;
		$bases = 0;
		//print 'string is '.$seq_as_string.'<br>';
		foreach ($seq_as_array as $value) {
			//echo 'value is '.$value.' <br>';
			if ($temp = preg_match('/-/', $value)) {
				$gaps++;
			}
			elseif ($temp = preg_match('/[acgtrymwskdhbvn]/i', $value)) {
				$bases++;
				if ($bases == $ref_start) {
					$new_start = $ref_start + $gaps; # need new variable names or will repeatedly hit and update them as go through sequence
				}
				elseif ($bases == $ref_stop) {
					$new_stop = $ref_stop + $gaps;
				}
			}
		}
		$ref_start = $new_start; # revert to old names
		$ref_stop = $new_stop; 
		$ref_slice_length = $ref_stop - $ref_start + 1;
		$query_slice_length = $query_stop - $query_start + 1; # the ref and query slices will not be the same because the query has no gaps in it - we have not yet put it in the alignment
		//echo ' new ref start is '.$ref_start.'<br>';
		//echo ' new ref stop is '.$ref_stop.'<br>';
		$query_sequence_slice = substr($sequence, $query_start -1, $query_slice_length);
		//echo 'UserQuery<br>'.$query_sequence_slice.'<br><br>';
		$name = "UserQuery";
		$seq = $query_sequence_slice;
		//echo 'query slice is '.$seq.'<br>';
		$temp_file = write_file($name,$seq);
		//echo 'query slice is in'.$temp_file.'<br>';
		$ref_output = ""; # will use for all the ref alignments
		$resource = mysql_query("SELECT id, sequence FROM GenomeAligns WHERE segment_id=\"$segmentID\"",$db);
		if ($row = mysql_fetch_array($resource)) {
			do {
				$align_sequence = $row["sequence"];
				$id = $row["id"];
				$sequence_slice = substr($align_sequence, $ref_start - 1, $ref_slice_length);
				//echo $id.'<br>'. $sequence_slice.'<br>';
				$ref_output = $ref_output.">$id\n$sequence_slice\n";
			} while ($row = mysql_fetch_array($resource));
		}
		$temp_file2 = write_align_file($ref_output);
		//echo 'alignfile is '.$temp_file2.'<br>';
		$clustal_out = tempnam("/tmp", "temp_file"); # create file for clustalw output
		//echo 'clustalout is '.$clustal_out.'<br>';
		$clustal_out_nexus = tempnam("/tmp", "temp_file"); # create file for converting clustalw output into nexus (some bug in program)
		
		exec("$clustalwpath -profile1=$temp_file2 -profile2=$temp_file -output=FASTA -outfile=$clustal_out ");
		exec("$readseqpath -all -f17 $clustal_out > $clustal_out_nexus");

//		echo '<a href='.$clustal_out.'>file</a><br>';
		$filehandle = fopen("$clustal_out", "r");
		$text = "";
		while (!feof($filehandle)) {
			$text = $text.fgets($filehandle);
		}
		fclose($filehandle);
		echo '<br><br><h1>Click here to download alignment</h1>
		<form action="download.php" method="post"><input type="submit" value="Alignment"/><input type="hidden" name="align_query" value="'.$text.'"/></form>
		';
		
		$paup_out = tempnam("/tmp", "paup_out_file"); # create file for paup output
		$command_file = tempnam("/tmp", "command_file"); # create paup command file
		$handle = fopen($command_file, "w");
		if (fwrite($handle, "begin paup;\nset criterion=distance RootMethod=midpoint;\ndset distance=HKY ;\nNJ;\nsavetree brlens=yes format=NEXUS file=$paup_out replace=yes;\nquit;\nend;\n") == TRUE) {
			//echo "created paup command file<br>";
			}
		else {
			echo "<br>WebServer Error: cannot write command file for running paup<br>";
		}
		//echo "input is".$clustal_out_nexus." command file is ".$command_file."outfile is ".$paup_out."<br>";

		exec("/usr/bin/paup $clustal_out_nexus < $command_file");
		$pdfFile = getTreePDF($paup_out);

		echo '<br><br><h1>Click here to download phylogentic tree of alignment</h1>
		<a href="virus'.$pdfFile.'"><input type="submit" value="Tree"/></a>';

	}
	
	function draw_toolbar() {
		echo "<TABLE CLASS='toolbar' WIDTH='820px'>";
		echo "<tr><td><a href='index.php' >Home</a></td>";
		echo "<td><form action='search.php' method='get'><input type='text' name='query'/><input type='submit' value='Search'/></form></td>";
		echo "<td><a href='browse.php'>Browse</a></td>";
		echo "<td><a href='blast.php''>BLAST</a></td>";
		echo "<td><a href='proteins.php''>Proteins</a></td>";
		echo "</tr></table>";
	}

	function get_segment_name($segmentID) {
		global $db;
		$resource = mysql_query("SELECT name FROM Segments WHERE id=\"$segmentID\" ",$db);
		if ($sequence = mysql_fetch_array($resource)) { # will only be one entry
			$segment_name = $sequence[0];
		}
		else {
			echo "Failed to convert segment id, ".$segmentID." to segment name<br>";
		}
		if ($segment_name == "monopartite") {
			$segment_name = "unsegmented";
		}
		return $segment_name;
	}

	function get_name($segmentID) {
		global $db;
		$resource = mysql_query("SELECT Viruses.name FROM Segments,Viruses WHERE Segments.id=\"$segmentID\" AND Segments.virus_id = Viruses.id",$db);
		if ($sequence = mysql_fetch_array($resource)) { # will only be one entry
			$name = $sequence[0];
		}
		else {
			echo "Failed to convert segment id, ".$segmentID." to virus name<br>";
		}
		return $name;
	}
	
	function get_num_aligns($segmentID) {
		global $db;
		$resource = mysql_query("SELECT COUNT(*) FROM GenomeAligns WHERE segment_id=\"$segmentID\" AND divergence < 0.33 AND divergence IS NOT NULL",$db);
		if ($sequence = mysql_fetch_array($resource)) { # will only be one entry
			$number = $sequence[0];
		}
		else {
			echo "Failed to count number of alignments for ".$segmentID."<br>";
		}
		if ($number == 0) {
			$number = 1;
		}
		return $number;
	}
	
	function getTreePDF($paup_out) {
	
		$TGF = "/usr/local/biotools/tgf/tgf10rc1/tgf";
		$PS2PDF ="/usr/bin/pstopdf";
			
		$pdfFile = $paup_out.".pdf";
		$tgfFile = $paup_out.".tgf";
		$epsFile = $paup_out.".eps";

		exec ("($TGF -t $paup_out)");
 
 		$tgf = file_get_contents($tgfFile);
	  	$tgf = str_replace('\width{150}' ,'\width{180}', $tgf);
	 	$tgf = str_replace('\height{250}' ,'\height{270}', $tgf);
 		$tgf = str_replace('\margin{0}{0}{0}{0}' ,'\margin{10}{10}{10}{10}', $tgf);
		$tgf = preg_replace("/style{r}{plain}{[^}]+}/", "style{r}{plain}{10}", $tgf);
 		file_put_contents($tgfFile, $tgf);
		exec ("($TGF -p $tgfFile)");
  		exec ("($PS2PDF $epsFile)");
 	 	exec ("(cp $pdfFile /Library/WebServer/Documents/virus/tmp/)");
	 	return $pdfFile;
    	}

	function read_results($blast_results, $sequence) {
			//echo 'seq is '.$sequence.'<br>';
			echo '<br><br><br><h1>Results of BLAST against reference genomes showing matches</h1><br>';
			echo 'Click Select to build a multiple alignment for that region, including your sequence (there may be a delay)';
			echo "<DL><TABLE CLASS='data' WIDTH='600px'>";
			echo "<TD CLASS='heading'>Alignment</TD><TD CLASS='heading'>Coordinates in Query</TD><TD CLASS='heading'>Coordinates in Reference</TD><TD CLASS='heading'>E-value</TD>";
			echo "</TR>";
			$filehandle = fopen("$blast_results", "r");
			while (!feof($filehandle)) {
				$text = fgets($filehandle);
				if (trim($text) != "") {
					//echo 'results are '.$text.'<br>';
					$array = preg_split("/\t/", $text);
					$segmentID = $array[1];
					if ($array[10] < 0.01) {
						$coords = $array[6].'_link_'.$array[7].'_link_'.$array[8].'_link_'.$array[9].'_link_'.$segmentID.'_link_'.$sequence; # retain segment ID here and coords in both query and ref 
						echo '<TR><TD><form action="align.php" method="POST"><input type="submit" value="Select"/><input type="hidden" name="coord" value="'.$coords.'"/></TD><TD>'.$array[6].'-'.$array[7].'</TD><TD>'.$array[8].'-'.$array[9].'</TD><TD>'.$array[10].'</TD></form>';
					}
				}
			}
			fclose($filehandle);
 			echo '</dl>';
 			echo "</TABLE>";
 			return $segmentID;
 		}
	
	function write_align_file($ref_output) {
		$temp_file = tempnam("/tmp", "temp_file");
		$handle = fopen($temp_file, "w");
		if ($handle) {
			//echo "made handle $infile<br>";
		}
		else {
			echo "<br>WebServer Error: no handle to query_file created<br>";
		}
		if (fwrite($handle, $ref_output) == TRUE) {
				//echo "created file<br>";
			}
		else {
				echo "<br>WebServer Error: cannot write query_file<br>";
		}
		return $temp_file;
	}
	
	function write_file($name,$seq) {
		$temp_file = tempnam("/tmp", "temp_file");
		$handle = fopen($temp_file, "w");
		//echo "query file is ".$temp_file."seq is ".$seq;
		if ($handle) {
			//echo "made handle $infile<br>";
		}
		else {
			echo "<br>WebServer Error: no handle to query_file created<br>";
		}
		if (fwrite($handle, ">$name\n$seq\n") == TRUE) {
				//echo "created file<br>";
			}
		else {
				echo "<br>WebServer Error: cannot write query_file<br>";
		}
		return $temp_file;
	}
	
	function write_ref_genome($segmentID) {
		global $db;
		$resource = mysql_query("SELECT nuc_sequence FROM Segments WHERE id=\"$segmentID\" ",$db);
		if ($sequence = mysql_fetch_array($resource)) { # will only be one entry
			$sequence = $sequence[0];
		}
		$ref_file = tempnam("/tmp", "ref_file");
		$handle = fopen($ref_file, "w");
		if ($handle) {
			//echo "made handle $infile<br>";
		}
		else {
			echo "<br>WebServer Error: no handle to ref_file created<br>";
		}
		if (fwrite($handle, ">$segmentID\n$sequence\n") == TRUE) {
				//echo "created file<br>";
			}
		else {
				echo "<br>WebServer Error: cannot write ref_file<br>";
		}
		//echo "ref file is ".$ref_file;
		return $ref_file;
	}

?>
