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

	openDocument($title);

	drawHeader();
	draw_toolbar();
	
	$db = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);

	mysql_select_db($database,$db);

	$match = $_GET['id'];
	
	$title = "RNA viruses";

	if ($match) {
	
		$array = preg_split("/-/",$match, -1);
		$id = $array[0];
		$hit_start =  $array[1];
		$hit_end =  $array[2];
		$resource = mysql_query("SELECT Segments.id, Segments.length, Viruses.name, Segments.name FROM Segments, Viruses WHERE Segments.id=\"$id\" AND Segments.virus_id = Viruses.id",$db);
		$row = mysql_fetch_array($resource);
		if ($row[3] == "monopartite") {
			$row[3] = "";
		}
		echo '<br><h1>'.$row[2].' '.$row[3].'  -  Position of your BLAST query shown at bottom in black</h1>';
		
		# draw genome summary for this segment only (simplified from virus page subroutines)
		$width_segment_prop = 100;
		$width_segment_string = $width_segment_prop."%";
		$length = genome_overview($id,$width_segment_string);
		
		# now put in the BLAST hit
		$width_pre_hit = $width_segment_prop * ($hit_start/$length); # get width of cell before BLAST hit
		$width_pre_hit_string = $width_pre_hit."%";
		$width_hit = $width_segment_prop * ($hit_end - $hit_start)/$length; # get width of cell representing BLAST hit
		$width_hit_string = $width_hit."%";
		$width_post_hit = $width_segment_prop * (($length - $hit_end)/$length); # get width of cell representing BLAST hit
		$width_post_hit_string = $width_post_hit."%";
		
		//echo "width_pre_hit_string = ".$width_pre_hit_string." width_hit_string = ".$width_hit_string." width_post_hit_string = ".$width_post_hit_string;
		echo "<TABLE CLASS='overview'><TR><TD width='20%'></TD>";
		echo "<TD BGCOLOR='#FFFFFF' WIDTH='$width_pre_hit_string'</TD><TD BGCOLOR='#000000' WIDTH='$width_hit_string'><font color='#000000'>.</font></TD><TD BGCOLOR='#FFFFFF' WIDTH='$width_post_hit_string'</TD>";
		echo "</TR>";
		echo "<TABLE CLASS='overview'><TR><TD width='20%'></TD>";
		echo "<TD BGCOLOR='#FFFFFF' WIDTH='$width_pre_hit_string'</TD><TD BGCOLOR='#FFFFFF' WIDTH='$width_hit_string' ALIGN=CENTER>Query</TD><TD BGCOLOR='#FFFFFF' WIDTH='$width_post_hit_string'</TD>";
		echo "</TR>";
		echo "</TABLE>";
		
		# now put in legend
	
		echo "<br>Legend:<br><TABLE CLASS='genome'>";
		echo "<TR CLASS='segment'><TD CLASS='segment'>".spacer("12pt","12pt")."</TD><TD>&nbsp Segment</TD></TR>";
		echo "<TR CLASS='gene'><TD CLASS='gene'>".spacer("12pt","12pt")."</TD><TD>&nbsp Gene</TD></TR>";
		echo "</TABLE>";
 
	}
	drawFooter("Robert Belshaw, Tulio de Oliveira, Sidney Markowitz & Andrew Rambaut"); 
	
	closeDocument();

	// sub-routines
	function draw_toolbar() {
		echo "<TABLE CLASS='toolbar' WIDTH='820px'>";
		echo "<tr><td><a href='index.php' >Home</a></td>";
		echo "<td><form action='search.php' method='get'><input type='text' name='query'/><input type='submit' value='Search'/></form></td>";
		echo "<td><a href='browse.php'>Browse</a></td>";
		echo "<td><a href='blast.php''>BLAST</a></td>";
		echo "<td><a href='proteins.php''>Proteins</a></td>";
		echo "</tr></table>";
	}

	
	function genome_overview($id, $width_segment_string) { # just passing segment id (subroutine copied from main genome overview where virus_id is passed and it loops through all the segemnts of the segmented viruses
		global $db;
		$resource = mysql_query("SELECT length, name FROM Segments WHERE id=\"$id\"",$db);
		
		if ($row = mysql_fetch_array($resource)) {
			do {
			$name = $row["name"];
			$length = $row["length"];
			} while ($row = mysql_fetch_array($resource));
		}

		echo "<TABLE CLASS='overview'><TR><TD CLASS='names' width='20%'>";
		
		if ($name == "monopartite") {
			$name = "Unsegmented genome";
		}
		echo "<TABLE CLASS='genome'>";
		echo "<TR CLASS='scale'><TD>&nbsp</TD></TR></TABLE>";
		echo "<TABLE CLASS='genome'>"; 
		echo "<TR CLASS='segment'><TD CLASS='segment_name'><a href='http://www.ncbi.nlm.nih.gov/entrez/viewer.fcgi?db=nucleotide&val=$id'>".$name."</a></TD></TR>";
		echo "</TABLE>";
		get_gene_names($id); 
		echo "<BR>";

		echo "</TD><TD>";
		
		echo "<TABLE CLASS='genome' width='$width_segment_string'>";
		echo "<TR CLASS='scale'><TD>1</TD><TD align='right'>$length</TD></TR></TABLE>";
		echo "<TABLE CLASS='genome' width='$width_segment_string'>"; # each segment will make up < 70% of browser window
		echo "<TR CLASS='segment'>";
		echo "<TD WIDTH='1%'>&nbsp</TD>";	
		echo "<TD CLASS='segment'>
		<A HREF='http://www.ncbi.nlm.nih.gov/entrez/viewer.fcgi?db=nucleotide&val=$id'>".spacer("100%","12pt")."</A></TD>";
		echo "</TR>";
		echo "</TABLE>";
		get_gene($id, $length,$width_segment_string); # NOW PUT IN HERE THE GENES PROPORTIONAL TO SEGMENT LENGTH
		echo "<BR>";

		echo "</TD></TR></TABLE>";
		return $length;
	}

	function get_gene($id, $length,$width_segment_string) {
		global $db;
		$resource = mysql_query("SELECT coord, id, name FROM Proteins WHERE segment_id=\"$id\"",$db);
		$gene_counter = 1;
		if ($row = mysql_fetch_array($resource)) {
			do {
				$coord = $row["coord"];
				$gene = $row["id"];
				$name = $row["name"];
				if ($name == "") {
					$name = "none";
				}
				$master_array = preg_split("/,/",$coord, -1);
				echo "<TABLE CLASS='genome' width='$width_segment_string'>"; # each segment will make up < 70% of browser window
				echo "<TR CLASS='gene'>";
				echo "<TD WIDTH='1%'>&nbsp</TD>";	
				$previous_orf_length_bp = 0;
				$counter = 0;
				foreach ($master_array as $array) { # may be multiple ORFs in gene
					$array = preg_split("/-/",$array, -1);
					sort($array); # for rc genes
					$gene_length_bp = $array[1] - $array[0] +1;
					$width_gene_prop = 100 * ($gene_length_bp / $length);
					$width_gene_string = $width_gene_prop."%";
					$start_length_bp = $array[0] - $previous_orf_length_bp;
					$width_start_prop =  100 * ($start_length_bp / $length);
					if ($width_start_prop < 1) {
						$width_start_prop = 1;
					}
					$width_start_string = $width_start_prop."%";
	
					echo "<TD WIDTH='$width_start_string'>".spacer()."</TD> 
					<TD CLASS='gene' WIDTH='$width_gene_string'> 
					<a href='http://www.ncbi.nlm.nih.gov/entrez/viewer.fcgi?db=protein&val=$gene'>".spacer("100%","12pt")."</a></TD>";
					$previous_orf_length_bp = $previous_orf_length + $start_length_bp + $gene_length_bp; # keep track of progress
					$counter++;
				}
				
				# now just put in final end region
				$end_length_bp = $segment_length_bp - $array[1]; # this will be last value in last orf
				$width_end_prop = 1 + ($end_length_bp / $length);
				$width_end_string = $width_end_prop."%";
				echo "<TD WIDTH='$width_end_string'>".spacer()."</TD>";	
				echo "</TR>";
				echo "</TABLE>";
				$gene_counter++;
			} while ($row = mysql_fetch_array($resource));
		}
	}
	
	function get_gene_names($id) {
		global $db;
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

?>
