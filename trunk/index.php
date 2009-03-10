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

	$title = "Home Page";

	openDocument($title);

	drawHeaderFirstPage();
	draw_toolbar();
	get_Total();
	

//$virustotal = mysql_query("SELECT COUNT(*) FROM viruses",$db);
//	return $virustotal;
    $number_viruses = get_Total();

	echo '<br>The database currently has '. $number_viruses.
	' viruses. Mirrors available at <a href="http://virus.zoo.ox.ac.uk/rnavirusdb/"><b>Oxford</b></a>, '.
	'<a href="http://newbioafrica.mrc.ac.za/rnavirusdb/"><b>Durban</b></a>, '.
	'<a href="http://bioinf.cs.auckland.ac.nz/rnavirusdb/"><b>Auckland</b></a>, and '.
	'<a href="http://tree.bio.ed.ac.uk/rnavirusdb/"><b>Edinburgh</b></a>';
	
	echo '<TABLE WIDTH="800px"><tbody><TR><TD><br><img src="images/logotree.png"
style="width: 450px; height: 450px;" usemap="#logotree.png" border="0">&nbsp;
                  <map name="logotree.png">
                  <area shape="RECT" coords="66,106,130,145" href="align.php">
                  <area shape="RECT" coords="169,18,234,68" href="blast.php">
                  <area shape="RECT" coords="291,68,369,118" href="browse.php">
                  <area shape="RECT" coords="340,231,402,278" href="viruslinks.php">
                  <area shape="RECT" coords="135,237,213,380" href="proteins.php"></TD>';
	
	echo '<TD><a href="blast.php"><h1>BLAST</h1></a>
	Identify your virus sequences<br><br>

	<a href="align.php"><h1>Align</h1></a>
	Get whole genome alignments for each virus species and align your sequence to them<br><br>
	
	<a href="browse.php"><h1>Browse</h1></a>
	Find information on all RNA viruses<br><br>
	
	<a href="proteins.php"><h1>Proteins</h1></a>
	Get amino acid sequences for genes and complete translated genomes<br><br>
	
	<a href="viruslinks.php"><h1>Links</h1></a>
	Links to other virus web sites<br><br>
	
	<a href="http://www.ncbi.nlm.nih.gov/pubmed/18948277"><h1>Citation</h1></a>
	Description of site in <i>Nucleic Acids Res. </i><b>37</b>:D431-5
	</TD></TR></tbody></TABLE>
	';

	drawFooterFirstPage("Robert Belshaw, Tulio de Oliveira, Sidney Markowitz & Andrew Rambaut"); 
	
	closeDocument();
	
	// subroutines
	
	function draw_toolbar() {
		echo "<TABLE CLASS='toolbar' WIDTH='820px'>";
		echo "<td><form action='search.php' method='get'><input type='text' name='query'/><input type='submit' value='Search'/></form></td>";
		echo "<td><a href='browse.php'>Browse</a></td>";
		echo "<td><a href='blast.php''>BLAST</a></td>";
		echo "<td><a href='align.php''>Align</a></td>";
		echo "<td><a href='proteins.php''>Proteins</a></td>";
		echo "<td><a href='aboutus.php''>About us</a></td>";
		echo "</tr></table>";
	}
	
	function get_Total() {
		global $db;
		$resource = mysql_query("SELECT COUNT(*) FROM viruses",$db);
		$number_viruses = mysql_result($resource, 0); // only one cell in field
		return $number_viruses;
	}

?>
