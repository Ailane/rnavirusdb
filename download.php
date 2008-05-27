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
	// Send binary filetype HTTP header
	header ('Content-Type: text/plain' );
	// Send content-disposition with save file name HTTP header
	header ('Content-Disposition: attachment; filename="alignment"' ); 

	$db = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	mysql_select_db("$database",$db);

	$query = $_GET[query];
	$tree_query = $_GET[tree_query];
	$tree_pdf = $_GET[tree_pdf];
	$align_query = $_POST[align_query];
	$tree_figtree = $_GET[tree_figtree];
	

	
	if ($query) {
		$resource = mysql_query("SELECT * FROM genomealigns WHERE segment_id=\"$query\" AND divergence < 0.33",$db);
		if ($sequence = mysql_fetch_array($resource)) {
			do {
				$id = $sequence["id"];
				$seq = $sequence["sequence"];
				echo ">".$id."\n".$seq."\n";
			} while ($sequence = mysql_fetch_array($resource));
		}
		else {
			echo 'problem with recovering alignments';
		}
	}
	
	elseif ($tree_query) {
		$resource = mysql_query("SELECT tree FROM Segments WHERE id=\"$tree_query\"",$db);
		if ($sequence = mysql_fetch_array($resource)) {
			$tree = $sequence["tree"];
			echo "$tree";
		}
		else {
			echo 'problem with recovering tree';
		}
	}
	elseif ($tree_pdf) {
		echo $tree_pdf;
	}
	elseif ($align_query) {
		echo $align_query;
	}
	
	elseif ($tree_figtree) {
		$resource = mysql_query("SELECT tree FROM Segments WHERE id=\"$tree_query\"",$db);
		if ($sequence = mysql_fetch_array($resource)) {
			$tree = $sequence["tree"];
			echo "$tree";
			
  		echo '<applet'
    	 . ' code="figtree.applet.FigTreeApplet"'
   		 . ' archive="' . $FIGTREEURL . '"'
     	 . ' width="500" height="600">'
     	 . '<param name="tree" value="' . $treeurl .'" />'
     	 . '<param name="style" value="icarus_small" />'
     	 . 'Browser does not support Java</applet>';

			
			
			
		}
		else {
			echo 'problem with recovering tree';
		}
	}
	


?>
