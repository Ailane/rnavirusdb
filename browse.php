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

	$title = "Browse";

	$typeID = $_GET['type'];
	$selectedFamilyID = $_GET['family'];
	$selectedGenusID = $_GET['genus'];
	
	$byFamily = $_GET['byFamily'];
	$byGenus = $_GET['byGenus'];
	$byVirus = $_GET['byVirus'];
	
	openDocument($title);

	drawHeader();
	draw_toolbar();		
	$result1 = mysql_query("SELECT DISTINCT type FROM viruses ORDER BY type ASC",$db);	

	if ($type = mysql_fetch_array($result1)) {
	
		echo '<dl><br><br><h1>Find virus by clinking on its group</h1><br>';
		do {
			$type = $type["type"];
			if ($byFamily != "true" && $byGenus != "true" && $type == $typeID) {
				echo '
					<dt><b><a href="browse.php"><img src="images/disclosureOpen.png"> '.$type.'</a></b>
				';

				echo '<dd>';
				
				listFamilies($type);
				
				echo '</dd>';

		
			} else {
				echo '
					<dt><b><a href="browse.php?type='.$type.'"><img src="images/disclosureClosed.png"> '.$type.'</a></b> 
				';
			}
		} while ($type = mysql_fetch_array($result1));
		echo '<dl><br><br><h1>Or browse by family, genus or species</h1><br>';

//		echo '
//			<dt><br></dt><dd></dd>
//		';

		if ($byFamily != "true") {
			echo '
				<dt><b><a href="browse.php?byFamily=true"><img src="images/disclosureClosed.png"> Browse by family</a></b></dt><dd></dd>
			';
		} else {
			echo '
				<dt><b><a href="browse.php"><img src="images/disclosureOpen.png"> Browse by family</a></b></dt><dd></dd>
			';
			listFamilies(null);
		}

		if ($byGenus != "true") {
			echo '
				<dt><b><a href="browse.php?byGenus=true"><img src="images/disclosureClosed.png"> Browse by genus</a></b></dt><dd></dd>
			';
		} else {
			echo '
				<dt><b><a href="browse.php"><img src="images/disclosureOpen.png"> Browse by genus</a></b></dt><dd></dd>
			';
			listGenuses(null, null);
		}

		if ($byVirus != "true") {
			echo '
				<dt><b><a href="browse.php?byVirus=true"><img src="images/disclosureClosed.png"> Browse all viruses</a></b></dt><dd></dd>
			';
		} else {
			echo '
				<dt><b><a href="browse.php"><img src="images/disclosureOpen.png"> Browse all viruses</a></b></dt><dd></dd>
			';
			listViruses(null, null, null);
		}

		echo '</dl>';
	} else {
		echo "No viruses in the database";	
	}

	drawFooter("Robert Belshaw, Tulio de Oliveira & Andrew Rambaut"); 
	
	closeDocument();

// -------------- subroutines ------------

	function get_link($id) {
		global $db;
		$resource = mysql_query("SELECT link FROM ictvlinks WHERE name=\"$id\"",$db);
		if ($row = mysql_fetch_array($resource)) {
			do {
				$link = $row["link"];
			} while ($row = mysql_fetch_array($resource));
		}
		if ($link) {
			$link = "http://www.ncbi.nlm.nih.gov/ICTVdb/ICTVdB/".$link;
		}
		return $link;
	}

	function listFamilies($typeID) {
		global $db;
		global $selectedFamilyID;

		if ($typeID) {
			$search = "WHERE type=\"$typeID\" ";
		}
		
		$result = mysql_query("SELECT DISTINCT family FROM viruses $search ORDER BY family ASC", $db);	
		if ($family = mysql_fetch_array($result)) {
			echo '<dl>';
			do {
				$familyName = $family["family"];
				if (trim($familyName) == "") $familyName = "Unclassified";
				
				$familyID = $familyName;
				$id = $familyName;
				$link = get_link($id);
				$icon = "";
				if ($link) {
					$icon = "ICTVdb";
				}
				if ($familyID == $selectedFamilyID) {

					if ($typeID) {

						echo '
							<dt><i><a href="browse.php?type='.$typeID.'"><img src="images/disclosureOpen.png"> '.$familyName.'</a></i><a href='.$link.'> '.$icon.'</a><a name='.$familyName.'></a>
						';
					} else {
						if ($link) {
							echo '<dt><i><a href="browse.php?byFamily=true"><img src="images/disclosureOpen.png"> '.$familyName.'</a></i><a href='.$link.'> '.$icon.'</a><a name='.$familyName.'></a>
							';
						}
					}
					
					echo '<dd>';
					
					listGenuses($typeID, $familyID);

					echo '</dd>';
				} else {
					if ($typeID) {
						echo '
							<dt><i><a href="browse.php?type='.$typeID.'&amp;family='.$familyID.'#'.$familyID.'"><img src="images/disclosureClosed.png"> '.$familyName.'</a></i> <a href='.$link.'> '.$icon.'</a><a name='.$familyName.'></a>
						';
					} else {
						echo '
							<dt><i><a href="browse.php?byFamily=true&amp;family='.$familyID.'#'.$familyID.'"><img src="images/disclosureClosed.png"> '.$familyName.'</a></i> <a href='.$link.'>  '.$icon.'</a>
						';
					}
				}

			} while ($family = mysql_fetch_array($result));
			echo '</dl>';
		} else {
			echo "No viruses of that type in the database";	
		}
	}

	function listGenuses($typeID, $familyID) {
		global $db;
		global $selectedGenusID;
		
		if ($typeID) {
			$search = "WHERE type=\"$typeID\" ";
		}
		
		if ($familyID) {
			if ($search) {
				$search = $search." AND ";
			} else {
				$search = "WHERE ";
			}
			$search = $search."family=\"$familyID\" ";
		}
		
		$result = mysql_query("SELECT DISTINCT genus FROM viruses $search ORDER BY genus ASC", $db);	
				
		if ($genus = mysql_fetch_array($result)) {
			echo '<dl>';

			do {
				$genusName = $genus["genus"];
				if ($genusName == "") $genusName = "Unclassified";

				$genusID = $genusName;
				$id = $genusName;
				$link = get_link($id);
				$icon = "";
				if ($link) {
					$icon = "ICTVdb";
				}

				if ($genusID == $selectedGenusID) {
					if ($typeID) {
						echo '
							<dt><i><a href="browse.php?type='.$typeID.'&amp;family='.$familyID.'"><img src="images/disclosureOpen.png"> '.$genusName.'</a></i><a href='.$link.'> '.$icon.'</a><a name='.$genusName.'></a>
						';
					} else if ($familyID) {
						echo '
							<dt><i><a href="browse.php?byFamily=true&amp;family='.$familyID.'"><img src="images/disclosureOpen.png"> '.$genusName.'</a></i><a href='.$link.'> '.$icon.'</a><a name='.$genusName.'></a>
						';
					} else {
						echo '
							<dt><i><a href="browse.php?byGenus=true"><img src="images/disclosureOpen.png"> '.$genusName.'</a></i><a name='.$genusName.'></a>
						';
					}
		
					echo '<dd>';
					
					listViruses($typeID, $familyID, $genusID);

					echo '</dd>';
				} else {
					if ($typeID) {
						echo '
							<dt><i><a href="browse.php?type='.$typeID.'&amp;family='.$familyID.'&amp;genus='.$genusID.'#'.$genusID.'"><img src="images/disclosureClosed.png"> '.$genusName.'</a></i><a href='.$link.'> '.$icon.'</a><a name='.$genusName.'></a>
						';
					} else if ($familyID) {
						echo '
							<dt><i><a href="browse.php?byFamily=true&amp;family='.$familyID.'&amp;genus='.$genusID.'#'.$genusID.'"><img src="images/disclosureClosed.png"> '.$genusName.'</a></i><a href='.$link.'> '.$icon.'</a><a name='.$genusName.'></a>
						';
					} else {
						echo '
							<dt><i><a href="browse.php?byGenus=true&amp;genus='.$genusID.'#'.$genusID.'"><img src="images/disclosureClosed.png"> '.$genusName.'</a></i><a name='.$genusName.'></a>
						';
					}
				}

			} while ($genus = mysql_fetch_array($result));
			echo '</dl>';
		} else {
			echo "No viruses of that family in the database";	
		}
	}

	function listViruses($typeID, $familyID, $genusID) {
		global $db;
		
		if ($typeID) {
			$search = "WHERE type=\"$typeID\" ";
		}
		
		if ($familyID) {
			if ($search) {
				$search = $search." AND ";
			} else {
				$search = "WHERE ";
			}
			$search = $search."family=\"$familyID\" ";
		}
		
		if ($genusID) {
			if ($search) {
				$search = $search." AND ";
			} else {
				$search = "WHERE ";
			}
			$search = $search."genus=\"$genusID\" ";
		}
		
		$result = mysql_query("SELECT * FROM viruses $search ORDER BY name ASC",$db);	
	
		if ($virus = mysql_fetch_array($result)) {
			echo '<dl>';
			do {
				$virusID = $virus["id"];
				$virusName = $virus["name"];
				$virusName = preg_replace("/APOST/", "'", $virusName);
				echo '
					<dt><a href="virus.php?id='.$virusID.'">'.$virusName.'</a></dt>
				';

			} while ($virus = mysql_fetch_array($result));
			echo '</dl>';
		} else {
			echo "No viruses of that genus in the database";	
		}

	}
	
	function draw_toolbar() {
		echo "<TABLE CLASS='toolbar' WIDTH='820px'>";
		echo "<tr><td><a href='index.php' >Home</a></td>";
		echo "<td><form action='search.php' method='get'><input type='text' name='query'/><input type='submit' value='Search'/></form></td>";
		echo "<td><a href='blast.php''>BLAST</a></td>";
		echo "<td><a href='align.php''>Align</a></td>";
		echo "<td><a href='proteins.php''>Proteins</a></td>";
		echo "</tr></table>";
	}

?>
