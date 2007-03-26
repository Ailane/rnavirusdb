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

	$virusID = $_GET['id'];
	$query = $_GET['query'];

	if ($virusID) {
		$result = mysql_query("SELECT * FROM Viruses WHERE id=\"$virusID\"",$db);	
		$virus = mysql_fetch_array($result);
		$name = $virus["name"];
		$name = preg_replace("/APOST/", "'", $name);
		$type = $virus["type"];
		$number = $virus["segments"];
		if ($virus["family"]) {
			$family = $virus["family"];
		}
		else {
			$family = "Unclassified";
		}
		if ($virus["genus"]) {
			$genus = $virus["genus"];
		}
		else {
			$genus = "Unclassified";
		}

		$title = $name;
	}
	

	openDocument($title);
	drawHeader();
	if ($query) {
		show_alignments($query);
	}
	

	
	if ($virus) {
		draw_toolbar($virusID, $name);
		echo '<br><h1 ALIGN=left>'.$name.'</h1>';
		$genotype = see_if_genotype_tool($virusID);
		if ($genotype) {
			echo 'We have a genotyping tool available for this virus: click icon <a href="'.$genotype.'"><img border="0" src="images/logosubtypetools.jpg" width="175" height="30"></a><br><br>';
		}
		echo "<TABLE CLASS='data' WIDTH='400px'>";
		echo '<TR><TD CLASS="heading">Type:</TD><TD><a href="browse.php">'.$type.'</a></TD></TR>';
		echo '<TR><TD CLASS="heading">Family:</TD><TD><a href="browse.php?byFamily=true&family='.$family.'#'.$family.'">'.$family.'</a></TD></TR>';
		echo '<TR><TD CLASS="heading">Genus:</TD><TD><a href="browse.php?byFamily=true&family='.$family.'&genus='.$genus.'#'.$genus.'">'.$genus.'</a></TD></TR>';

		if ($number == 1) {
			echo '<TR><TD CLASS="heading">Genome Organisation:</TD><TD> Unsegmented </TD></TR>';
 		}
 		else {
 			echo '<TR><TD CLASS="heading">Genome Organisation:</TD><TD>'.$number.' Segments</TD></TR>';
 		}
 		echo "</TABLE>";
		
		$result = mysql_query("SELECT * FROM Segments WHERE virus_id=\"$virusID\" ORDER BY name ASC",$db);
		if ($segment = mysql_fetch_array($result)) {
			echo '<br><h1>Aligned complete genomes</h1>';
			echo "<TABLE CLASS='data' WIDTH='700px'>";
			echo '<TR CLASS="heading"><TD>Segment name</TD>
			<TD>Number of sequences</TD>
			<TD>Alignment to screen</TD>
			<TD>Download alignment</TD>
			<TD>Download tree file</TD>
			<TD>Download tree pdf</TD>
			<TD>Align your sequence</TD></TR>';

			do {
				$segmentName = $segment["name"];
				if ($segmentName == "monopartite") {
					$segmentName = "unsegmented";
				}
				$segmentID = $segment["id"];
				$number_aligns = getAlignments($segment["id"]);
				$pdfFile = getTreePDF($segmentID);
				if ($pdfFile) {
					$symbol = "Tree";
				}
				else {
					$symbol = "N/A";
				}
				echo '<TR><TD>'.$segmentName. '</TD>
				<TD><center>'. $number_aligns. '</center></TD>
				<TD><form action="virus.php" method="get"><input type="hidden" name="query" value="'.$segmentID.'"/><input type="submit" value="Screen"/></form></TD>
				<TD><form action="download.php" method="get"><input type="hidden" name="query" value="'.$segmentID.'"/><input type="submit" value="File"/></form></TD>
				<TD><form action="download.php" method="get"><input type="hidden" name="tree_query" value="'.$segmentID.'"/><input type="submit" value="'.$symbol.'"/></form></TD>
				<TD><a href="/virus'.$pdfFile.'"><input type="submit" value="'.$symbol.'"/></a></TD>
				<TD><form action="align.php" method="get"><input type="hidden" name="query" value="'.$segmentID.'"/><input type="submit" value="Align"/></form></TD></TR>';
			} while ($segment = mysql_fetch_array($result));
			echo "</TABLE>";

		} 
		else {
			echo '<dt>No segments found for this virus</dt><dd></dd>';
		}
		genome_overview($virusID);
	}
	drawFooter("Robert Belshaw, Tulio de Oliveira & Andrew Rambaut"); 
	closeDocument();
	

	// subroutines 
	function draw_toolbar($virusID, $name) {
		echo "<TABLE CLASS='toolbar' WIDTH='820px'>";
		echo "<tbody><tr><td><a href='index.php' >Home</a></td>";
		echo "<td><form action='search.php' method='get'><input type='text' name='query'/><input type='submit' value='Search'/></form></td>";
		echo "<td><a href='browse.php'>Browse</a></td>";
		echo "<td><a href='blast.php''>BLAST</a></td>";
		echo "<td><a href='align.php''>Align</a></td>";
		echo '<td><a href="proteins.php?id='.$virusID.'">Proteins</a></td>';
		//echo '<td><a href="virus.php?id='.$virusID.'">Alignments</a></td>';
		echo "<td><a href='http://www.ncbi.nlm.nih.gov/Taxonomy/Browser/wwwtax.cgi?mode=Info&id=$virusID&lvl=3&lin=f&keep=1&srchmode=1&unlock'>GenBank</a></td>";
		$link = get_link($name);
		if ($link) {
			echo "<td><a href='$link'>ICTVdb</a></td>";
		}
		echo "</tr></table>";
	}

	function genome_overview($virusID) {
		global $db;
		
		$resource = mysql_query("SELECT id, length, name FROM Segments WHERE virus_id=\"$virusID\"",$db);
		
		echo '<br><h1> Genome Overview</h1>
		Click on segment or component genes to go to GenBank entry';
		$counter = 0;
		$max_segment_length_bp = 0;
		if ($row = mysql_fetch_array($resource)) {
			do {
				$array_of_arrays["name"][$counter] = $row["name"];
				$array_of_arrays["id"][$counter] = $row["id"];
				$array_of_arrays["length"][$counter] = $row["length"];
				if ($row["length"] > $max_segment_length_bp) {
					$max_segment_length_bp = $row["length"];
				}
				$counter++;
			} while ($row = mysql_fetch_array($resource));
		}

		echo "<TABLE CLASS='overview'><TR><TD CLASS='names' width='20%'>";
		
		for ($i = 0; $i < $counter; $i++) {
			$id = $array_of_arrays["id"][$i];
			$name = $array_of_arrays["name"][$i];
			if ($name == "monopartite") {
				$name = "Unsegmented genome";
			}
			echo "<TABLE CLASS='genome'>";
			echo "<TR CLASS='scale'><TD>&nbsp</TD></TR></TABLE>";
			echo "<TABLE CLASS='genome'>"; 
			echo "<TR CLASS='segment'><TD CLASS='segment_name'><a href='http://www.ncbi.nlm.nih.gov/entrez/viewer.fcgi?db=nucleotide&val=$id'>".$name."</a></TD></TR>";
			echo "</TABLE>";
			get_gene_names($id, $segment_length_bp, $width_segment_prop,$width_segment_string); # NOW PUT IN HERE THE GENES PROPORTIONAL TO SEGMENT LENGTH
			echo "<BR>";
		} 

		echo "</TD><TD>";
		
		for ($i = 0; $i < $counter; $i++) {
			$id = $array_of_arrays["id"][$i];
			$name = $array_of_arrays["name"][$i];
			if ($name == "monopartite") {
				$name = "Unsegmented genome";
			}
			$segment_length_bp = $array_of_arrays["length"][$i];
			$width_segment_prop = 100 * $segment_length_bp/$max_segment_length_bp; 
			$width_segment_string = $width_segment_prop."%";
			echo "<TABLE CLASS='genome' width='$width_segment_string'>";
			echo "<TR CLASS='scale'><TD>1</TD><TD align='right'>$segment_length_bp</TD></TR></TABLE>";
			echo "<TABLE CLASS='genome' width='$width_segment_string'>"; # each segment will make up < 70% of browser window
			echo "<TR CLASS='segment'>";
			echo "<TD WIDTH='1%'>&nbsp</TD>";	
			echo "<TD CLASS='segment'>
			<A HREF='http://www.ncbi.nlm.nih.gov/entrez/viewer.fcgi?db=nucleotide&val=$id'>".spacer("100%","12pt")."</A></TD>";
//			<A HREF=\"http://www.ncbi.nlm.nih.gov/entrez/viewer.fcgi?db=nucleotide&val=$id\">".spacer("100%","100%")."</A></TD>";
			echo "</TR>";
			echo "</TABLE>";
			get_gene($id, $segment_length_bp, $width_segment_prop,$width_segment_string); # NOW PUT IN HERE THE GENES PROPORTIONAL TO SEGMENT LENGTH
			echo "<BR>";
		} 

		echo "</TD></TR></TABLE>";

		echo "<br><br>Legend:<br><TABLE CLASS='genome'>";
		echo "<TR CLASS='segment'><TD CLASS='segment'>".spacer("12pt","12pt")."</TD><TD>&nbsp Segment</TD></TR>";
		echo "<TR CLASS='gene'><TD CLASS='gene'>".spacer("12pt","12pt")."</TD><TD>&nbsp Gene</TD></TR>";
		echo "</TABLE>";

	}

	function getAlignments($segmentID) {
		global $db;
		$resource = mysql_query("SELECT COUNT(*) FROM GenomeAligns WHERE segment_id=\"$segmentID\" and divergence < 0.33",$db);
		$number_aligns = mysql_result($resource, 0); // only one cell in field
		return $number_aligns;
	}

	function get_link($name) {
		global $db;
		$resource = mysql_query("SELECT link FROM ICTVlinks WHERE name=\"$name\"",$db);
		if ($row = mysql_fetch_array($resource)) {
			do {
				$link = $row["link"];
			} while ($row = mysql_fetch_array($resource));
		}
		if ($link) {
			$link = "http://www.ncbi.nlm.nih.gov/ICTVdb/ICTVdB/".$link;
		}
		//else {
			//echo "<br>[No link to ICTV database available at present]<br>";
		//}
		return $link;
	}
	

	function get_gene_names($id, $segment_length_bp, $width_segment_prop,$width_segment_string) {
		$db = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD); // Need to call within function
		$resource = mysql_query("SELECT coord, id, name, function FROM Proteins WHERE segment_id=\"$id\"",$db);
		$gene_counter = 1;
		if ($row = mysql_fetch_array($resource)) {
			do {
				$gene = $row["id"];
				$name = $row["name"];
				$function = $row["function"];
				if ($function == "") {
					$function = "unknown";
				}
				if ($name == "") {
					$name = "unnamed";
				}
				$name = preg_replace("/APOST/", "'", $name);
				if (strlen($name) > 20) {
					$name = substr($name, 0, 20);
					$name = $name.'..';
				}
				echo "<TABLE CLASS='genome'>";
				echo "<TR CLASS='segment'><TD CLASS='gene_name'><a href='http://www.ncbi.nlm.nih.gov/entrez/viewer.fcgi?db=protein&val=$gene'>".$name."</a></TD></TR>";
				echo "</TABLE>";
				$gene_counter++;
			} while ($row = mysql_fetch_array($resource));
		}
	}

	function get_gene($id, $segment_length_bp, $width_segment_prop,$width_segment_string) {
		$db = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD); // Need to call within function
		$resource = mysql_query("SELECT coord, id, name, function FROM Proteins WHERE segment_id=\"$id\"",$db);
		$gene_counter = 1;
		if ($row = mysql_fetch_array($resource)) {
			do {
				$coord = $row["coord"];
				$gene = $row["id"];
				$name = $row["name"];
				$function = $row["function"];
				if ($function == "") {
					$function = "unknown";
				}
				if ($name == "") {
					$name = "none";
				}
				$master_array = preg_split("/,/",$coord, -1);
				#print_r($master_array);
				#echo "Gene $gene_counter [functional category = $function] - GenBank entry name = $name<br>";
				echo "<TABLE CLASS='genome' width='$width_segment_string'>"; # each segment will make up < 70% of browser window
				echo "<TR CLASS='gene'>";
				echo "<TD WIDTH='1%'>&nbsp</TD>";	
				$previous_orf_length_bp = 0;
				$counter = 0;
				foreach ($master_array as $array) { # may be multiple ORFs in gene
					$array = preg_split("/-/",$array, -1);
					#print_r($array);
					sort($array); # for rc genes
					$gene_length_bp = $array[1] - $array[0] +1;
					$width_gene_prop = ($width_segment_prop * ($gene_length_bp / $segment_length_bp));
					if ($width_gene_prop < 1) {
						$width_gene_prop = 1;
					}
					$width_gene_string = $width_gene_prop."%";
					$start_length_bp = $array[0] - $previous_orf_length_bp;
					$width_start_prop = $width_segment_prop * ($start_length_bp / $segment_length_bp);
					if ($width_start_prop < 1) {
						$width_start_prop = 1;
					}
					$width_start_string = $width_start_prop."%";
	
				
					#echo "gene $gene coords = $coord seg length = $segment_length_bp blank length = $blank_length_bp gene length = $gene_length_bp <br>";
					#echo "width first blank = $width_start_string width gene = $width_gene_prop width end = $width_end_string<br>";
					echo "<TD WIDTH='$width_start_string'>".spacer()."</TD> 
					<TD CLASS='gene' WIDTH='$width_gene_string'> 
					<a href='http://www.ncbi.nlm.nih.gov/entrez/viewer.fcgi?db=protein&val=$gene'>".spacer("100%","12pt")."</a></TD>";
					$previous_orf_length_bp = $previous_orf_length + $start_length_bp + $gene_length_bp; # keep track of progress
					$counter++;
				}
				
				# now just put in final end region
				$end_length_bp = $segment_length_bp - $array[1]; # this will be last value in last orf
				$width_end_prop = 1 + ($width_segment_prop * ($end_length_bp / $segment_length_bp));
				$width_end_string = $width_end_prop."%";
				echo "<TD WIDTH='$width_end_string'>".spacer()."</TD>";	
				echo "</TR>";
				echo "</TABLE>";
				$gene_counter++;
			} while ($row = mysql_fetch_array($resource));
		}
	}
	
	// Tree construction
	function getTreePDF($segmentID) {
		global $db;
		$resource = mysql_query("SELECT tree FROM Segments WHERE id=\"$segmentID\"",$db);	
		$tree = mysql_result($resource, 0); // only one cell in field
		if ($tree) { # tree will not be there if fewer than 3 aligned sequences
			//echo 'id is '.$segmentID. 'tree is '.$tree.'<br>';
			$treefile = tempnam("/tmp", "arvore");
			$treefile = $treefile.".tre";
			$handle = fopen($treefile, "w");
			if ($handle) {
				//echo "made handle $infile<br>";
				if (fwrite($handle, "$tree") == TRUE) {
					//echo "created file<br>";
				}
				else {
					echo "<br>WebServer Error: cannot write file<br>";
				}
			}
			else {
				echo "<br>WebServer Error: no handle created for treefile<br>";
			}
			$TGF = "/usr/local/bin/tgf";
			$PS2PDF ="/usr/bin/ps2pdf";
			
 			$pdfFile = str_replace('tre', 'pdf', $treefile);
   			$tgfFile = str_replace('tre', 'tgf', $treefile);
    			$epsFile = str_replace('tre', 'eps', $treefile);

    			exec ("($TGF -t $treefile)");
  
 			$tgf = file_get_contents($tgfFile);
  		  	$tgf = str_replace('\width{150}' ,'\width{180}', $tgf);
  		 	$tgf = str_replace('\height{250}' ,'\height{270}', $tgf);
  	 		$tgf = str_replace('\margin{0}{0}{0}{0}' ,'\margin{10}{10}{10}{10}', $tgf);
			$tgf = preg_replace("/style{r}{plain}{[^}]+}/", "style{r}{plain}{10}", $tgf);
 	 		file_put_contents($tgfFile, $tgf);
  			exec ("($TGF -p $tgfFile)");
  	  		exec ("($PS2PDF $epsFile " . ABSPATH . "${pdfFile})");
	 	}
	 	else {
	 		$pdfFile = FALSE; # There is no pdf file
	 	}
	 	return $pdfFile;
    	}


	function see_if_genotype_tool($virus) {
		global $db;
		$resource = mysql_query("SELECT genotype FROM Viruses WHERE id=\"$virus\"",$db);
		$genotype = mysql_result($resource, 0); // only one cell in field
		return $genotype;
	}
	
	function show_alignments($query) {
		global $db;
		if ($query) {
			$resource = mysql_query("SELECT * FROM GenomeAligns WHERE segment_id=\"$query\" AND divergence < 0.33",$db); # Exclude some highly divergent seqs that do not align
			if ($sequence = mysql_fetch_array($resource)) {
				do {
					$id = $sequence["id"];
					$seq = $sequence["sequence"];
					echo ">$id<br>$seq<br>";
				} while ($sequence = mysql_fetch_array($resource));
			}
			else {
				echo 'problem with recovering alignments';
			}
		}
	}


?>
