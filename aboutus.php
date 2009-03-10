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
	
	echo'	<br><h1>Credits<br><br>The Zoology Department, University of Oxford, U.K.</h1>
	<a href="http://evolve.zoo.ox.ac.uk/Evolve/Robert_Belshaw.html">Robert Belshaw</a><br>
	<a href="http://evolve.zoo.ox.ac.uk/Evolve/Oliver_Pybus.html">Oliver Pybus</a><br><br>

	<h1>Institute of Evolutionary Biology, University of Edinburgh, U.K.</h1>
	<a href="http://tree.bio.ed.ac.uk/people/arambaut/">Andrew Rambaut</a><br><br>
	
	<h1>Africa Centre for Health and Population Studies, University of KwaZulu-Natal, South Africa. </h1>
	Tulio de Oliveira<br><br>
	
	<h1>Department of Computer Science, The University of Auckland, New Zealand. </h1>
	Sidney Markowitz<br>
	<a href="http://bioinf.cs.auckland.ac.nz/dr-alexei-drummond/">Alexei Drummond</a><br><br>
	';

	drawFooter("Robert Belshaw, Tulio de Oliveira, Sidney Markowitz & Andrew Rambaut"); 
	
	closeDocument();
	
	// subroutines
	
	function draw_toolbar() {
		echo "<TABLE CLASS='toolbar' WIDTH='820px'>";
		echo "<tr><td><a href='index.php' >Home</a></td>";
		echo "<td><form action='search.php' method='get'><input type='text' name='query'/><input type='submit' value='Search'/></form></td>";
		echo "<td><a href='browse.php'>Browse</a></td>";
		echo "<td><a href='blast.php''>BLAST</a></td>";
		echo "<td><a href='align.php''>Align</a></td>";
		echo "<td><a href='proteins.php''>Proteins</a></td>";
		echo "</tr></table>";
	}

?>

