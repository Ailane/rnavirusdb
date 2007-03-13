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

	drawHeader();
	draw_toolbar();
	
	echo '<br><TABLE BORDER="0" BGCOLOR ="#CCCCCC">CAUTION: Pre-publication version. All suggestions and reports of problems will be gratefully received</table>';
	
	
	echo' <TABLE WIDTH="800px"><tbody><TR><TD><img alt="" src="images/logotree.png"
style="width: 450px; height: 450px;" usemap="#logotree.png" border="0">&nbsp;
                  <map name="logotree.png">
                  <area shape="RECT" alt="Align"
coords="66,106,130,145" href="align.php">
                  <area shape="RECT" alt="BLAST 701 Virus"
coords="169,18,234,68" href="blast.php">
                  <area shape="RECT" alt="Browse and Search"
coords="291,68,369,118" href="browse.php">
                  <area shape="RECT" alt="Links to other resources"
coords="340,231,402,278" href="viruslinks.php">
                  <area shape="RECT" alt="Proteins"
coords="135,237,213,380" href="proteins.php"></TD>';
	
	echo '<TD><br><br><h2>The RNA Virus Homepage</h2><br>
	
	<a href="blast.php"><h1>BLAST</h1></a>
	Identify your viral nucleotide or amino acid sequences<br><br>

	<a href="align.php"><h1>Align</h1></a>
	Get whole genome alignments for each virus species and align your sequence to them<br><br>
	
	<a href="browse.php"><h1>Browse</h1></a>
	To find information on all RNA viruses<br><br>
	
	<a href="proteins.php"><h1>Proteins</h1></a>
	Get amino acid sequences for viral genes and whole translated genomes for all viral species<br><br>
	
	<a href="viruslinks.php"><h1>Links</h1></a>
	Links to other virus web sites<br>
	</TD></TR></tbody></TABLE>
	</dl>
	';

	drawFooter("Robert Belshaw, Tulio de Oliveira & Andrew Rambaut"); 
	
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

?>
