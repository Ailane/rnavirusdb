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
	$leftIndent = 20;
	$pageWidth = 800;
	$width = $pageWidth - $leftIndent;
	
	$database = DB_NAME;
	$googleAnalyticsTrackerID = GOOGLE_TRACKER_ID;
	$root = "/rnavirusdb/";
	$decoration = $root."Decoration";
	$empty = $root."Decoration/empty.gif";

	$researchIcon = $root."Decoration/Icons/Flask.png";
	$teachingIcon = $root."Decoration/Icons/Books.png";
	$opportunitiesIcon = $root."Decoration/Icons/Boffins.png";
	$generalIcon = $root."Decoration/Icons/Network.png";

	$peopleIcon = $root."Decoration/Icons/Boffins.png";
	$personIcon = $root."Decoration/Icons/Boffins.png";
	$publicationsIcon = $root."Decoration/Icons/Books.png";
	$softwareIcon = $root."Decoration/Icons/Cog.png";
	$dataIcon = $root."Decoration/Icons/Filing.png";
	$linksIcon = $root."Decoration/Icons/Network.png";
	$mapsIcon = $root."Decoration/Icons/Map.png";
	$jobsIcon = $root."Decoration/Icons/MonkeyTypewriter.png";
	

	function spacer($width = 1, $height = 1) {
		global $empty;

		return '<img src="'.$empty.'" alt="" width="'.$width.'" height="'.$height.'">';
	}
	
	function openDocument($title, $stylesheet="virus.css", $alt_stylesheet="virus.css", $keywords="", $description="") {
	
		// check whether we are using Windows versions of Internet
		// Explorer 5.5+
		$msie='/msie\s(5\.[5-9]|[6-9]\.[0-9]*).*(win)/i';
		$decentBrowser = ( 
			!isset($_SERVER['HTTP_USER_AGENT']) ||
			!preg_match($msie,$_SERVER['HTTP_USER_AGENT']) ||
			preg_match('/opera/i',$_SERVER['HTTP_USER_AGENT']));

		echo('
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">
<html>
	<head>
		<title>RNA Virus database - '.$title.'</title>
		');
		
		if (!$decentBrowser) {
			echo('
		<link href="'.$alt_stylesheet.'" rel="stylesheet" type="text/css">
			');
		} else {
			echo('
		<link href="'.$stylesheet.'" rel="stylesheet" type="text/css">
			');
		}
		
		echo('
		<meta name="keywords" content="'.$keywords.'">
		<meta name="description" content="'.$description.'">
	</head>
	<body>
		<table class="box" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td class="page-top">'.spacer(10,5).'</td>
			</tr>
			<tr>
				<td class="box-bottom">'.spacer(10,20).'</td>
			</tr>
		</table>
		');
	}

	function closeDocument() {
		global $googleAnalyticsTrackerID;
		echo('
					<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
					</script>
					<script type="text/javascript">
						_uacct = "' . $googleAnalyticsTrackerID . '";
						urchinTracker();
					</script>
				</body>
			</html> 
		');
	}
	
	function drawHeader() {
		global $headingIndent;
		global $pageWidth;
		global $leftIndent;

		echo('
			<table width="'.$pageWidth.'" cellspacing="0" cellpadding="0">
				<tr>
					<td>
						 <img alt="" src="images/logoVirus.gif"
						 usemap="#logoVirus.gif"
 						style="border: 0px solid ; width: 820px; height: 134px;"> <map
 						name="logoVirus.gif">
						<area shape="RECT" alt="Evolutionary Biology Group, Oxford"
 						coords="0,60,190,114" href="http://evolve.zoo.ox.ac.uk">
						<area shape="RECT" alt="Bioinformatics Institute, Auckland"
 						coords="300,60,480,114" href="http://www.cebl.auckland.ac.nz">
						<area shape="RECT" alt="Institute of Evolutionary Biology, Edinburgh"
 						coords="600,60,820,114" href="http://tree.bio.ed.ac.uk/">
						</map>

					</td>

				</tr>
		</table>
		');
	}

	function drawFooter($author) {
		echo('<br><br>
		<table class="box" width="100%" cellspacing="0" cellpadding="0">
			<tr><td class="box-top"></td></tr>
			<tr>
				<td class="page-bottom" valign="top">
					<table width="800" cellspacing="0" cellpadding="0">
						<tr>
							<td>
								<br><div class="box-extra" align="center">
									<table width="800" cellspacing="0" cellpadding="0" BGCOLOR="#FFFFFF" align="center"><tr><td><center>For questions, suggestions or problems please contact:<a href="http://evolve.zoo.ox.ac.uk/people.html?id=belshawr"> Robert Belshaw</a><br>Developed by ' . $author . '<br>Page layout last updated ' . date("d F Y ", getlastmod()) . 								
									'</center></td></tr><tr><td align="center">Funded by the Wellcome Trust, an EU Marie Curie Fellowship and the Royal Society</td></tr></table></div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		');
	}
	
	function navigationButton($label, $link, $disabled) {
		global $root;
		
		if (! $disabled) {
			return	'
				<div align="center">
					<table class="button" width="80%" cellspacing="0" cellpadding="0">
						<tr>
							<td class="button-left">
								<a href="'.$root.$link.'">'.spacer().'</a>
							</td>
							<td class="button-fill" align="center">
								<div class="button-title" align="center">
								<a href="'.$root.$link.'">'.$label.'</a>
								</div>
							</td>
							<td class="button-right">
								<a href="'.$root.$link.'">'.spacer().'</a>
							</td>
						</tr>
					</table>
				</div>';
		} else {
			return	'<div align="center">
						<table class="button" width="80%" cellspacing="0" cellpadding="0">
							<tr class="disabled">
								<td class="button-left">'.spacer().'</td>
								<td class="button-fill" align="center">
									<div class="button-title" align="center">'.$label.'</div>
								</td>
								<td class="button-right">'.spacer().'</td>
							</tr>
						</table>
					</div>';
		}
	}

	function drawNavigationBar($page="") {
		global $headingIndent;
		global $pageWidth;

		// if $page equals the button title then the button is disabled
		// as we are already on that page.
		echo('
			<table class="box" width="100%" cellspacing="0" cellpadding="0">
			<tr><td class="box-top"></td></tr>
			<tr>
				<td class="navigation-bar">
					<table width="800px" cellspacing="0" cellpadding="0">
						<tr;">
							<td>'.spacer(20,28).'</td>
							<td style="width: 152px;">' .
								navigationButton("Home", "", $page == "Home") .
							'</td>
							<td style="width: 152px;">' .
								navigationButton("Research", "research.php", $page == "Research Groups") .
							'</td>
							<td style="width: 152px;">' .
								navigationButton("Teaching", "teaching.php", $page == "Teaching") .
							'</td>
							<td style="width: 152px;">' .
								navigationButton("Seminars", "seminars.php", $page == "Seminars and Talks") .
							'</td>
							<td style="width: 152px;">' .
								navigationButton("Contacts", "contacts.php", $page == "Contact Information") .
							'</td>
							<td>'.spacer(20,28).'</td>
					</tr>
					</table>
				</td>
			</tr>
			<tr><td class="box-bottom"></td></tr>
		</table>');
	}
	
	function drawTitleBar($title, $icon, $subtitle = NULL, $class = "title-box") {
		global $leftIndent;
		global $pageWidth;
		
		echo('
			<table width="'.$pageWidth.'" cellspacing="0" cellpadding="0">
				<tr>
					<td>'.spacer($leftIndent,20).'</td>
					<td>
						<table class="'.$class.' box" width="100%" cellspacing="0" cellpadding="0">
							<tr>
								<td class="box-top-left">'.spacer().'</td>
								<td class="box-top-top-left">'.spacer().'</td>
								<td class="box-top">'.spacer().'</td>
								<td class="box-top-top-right">'.spacer().'</td>
								<td class="box-top-right">'.spacer().'</td>
							</tr>
							<tr>
								<td class="box-left-top-left">'.spacer().'</td>
								<td rowspan="2" colspan="3" class="title-bar">
									<table width="100%">
										<tr>
											<td>
												<img src="'.$icon.'" alt="" width="64" height="64">
											</td>
											<td halign="left">
												<div class="major-title">' . $title . '</div>');
			if ($subtitle) {
				echo('
												<div class="body">' . $subtitle . '</div>');
			}	
							
			echo('		
											</td>
										</tr>
									</table>
								</td>
								<td class="box-right-top-right">'.spacer().'</td>
							</tr>
							<tr>
								<td class="box-left">'.spacer().'</td>
								<td class="box-right">'.spacer().'</td>
							</tr>
							
							<tr>
								<td class="box-bottom-left">'.spacer().'</td>
								<td class="box-bottom-bottom-left">'.spacer().'</td>
								<td class="box-bottom">'.spacer().'</td>
								<td class="box-bottom-bottom-right">'.spacer().'</td>
								<td class="box-bottom-right">'.spacer().'</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>'
		);
	}
	
	function openPageBody() {
		global $leftIndent;
		global $pageWidth;
		
		return '
			<table width="'.$pageWidth.'" cellspacing="0" cellpadding="0">
				<tr>
					<td>'.spacer($leftIndent,20).'</td>
				';
	}
	
	function openColumn($width = "100%") {
		return '
				<td valign="top" width="'.$width.'">
				';
	}

	function closeColumn() {
		return '</td>';
	}

	function closePageBody() {
		return '
					</tr>
				</table>
				';
	}

	function button($label, $link, $class="button", $width="auto", $align="right", $valign="middle") {
		return	'<table width="100%" cellspacing="0" cellpadding="0">
					<tr valign="'. $valign .'">
						<td align="'. $align .'">
							<table class="'.$class.'" width="'.$width.'" cellspacing="0" cellpadding="0">
								<tr>
									<td class="button-left">
										<a href="'.$link.'">'.spacer().'</a>
									</td>
									<td class="button-fill">
										<div class="button-title" align="center">
											<a href="'.$link.'">'.$label.'</a>
										</div>
									</td>
									<td class="button-right">
										<a href="'.$link.'">'.spacer().'</a>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>';
	}

	function iconButton($label, $icon, $link, $class="button", $width="auto", $align="right") {
		return	'<table width="100%" cellspacing="0" cellpadding="0">
					<tr valign="'. $valign .'">
						<td align="'. $align .'">
							<table class="'.$class.'" width="'.$width.'" cellspacing="0" cellpadding="0">
								<tr>
									<td class="button-left">
										<a href="'.$link.'">'.spacer().'</a>
									</td>
									<td class="button-fill">
										<table><tr>
										<td>
										<a href="'.$link.'">
											<img src="'.$icon.'" height="14" width="14">
										</a></td>
										<td>
											<div class="button-title" align="center">
												<a href="'.$link.'">'.$label.'</a>
											</div>
										</td>
										</tr></table>
									</td>
									<td class="button-right">
										<a href="'.$link.'">'.spacer().'</a>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>';
	}

	function openBox($class="") {
		return '
					<table class="'.$class.' box" width="100%" cellspacing="0" cellpadding="0">
						<tr>
							<td class="box-top-left">'.spacer().'</td>
							<td class="box-top-top-left">'.spacer().'</td>
							<td class="box-top">'.spacer().'</td>
							<td class="box-top-top-right">'.spacer().'</td>
							<td class="box-top-right">'.spacer().'</td>
						</tr>
				';
	}
		
	function boxTop() {
		return '
						<tr>
							<td class="box-left-top-left">'.spacer().'</td>
							<td rowspan="2" colspan="3" class="box-body-top">'.spacer().'</td>
							<td class="box-right-top-right">'.spacer().'</td>
						</tr>
						<tr>
							<td class="box-left">'.spacer().'</td>
							<td class="box-right">'.spacer().'</td>
						</tr>
				';
	}
	function boxTitle($title, $link=NULL, $id=NULL) {
		$row = '
						<tr>
							<td class="box-left-top-left">'.spacer().'</td>
							<td rowspan="2" colspan="3" class="box-title">';
		
		if ($link) {
			$row .= '
								<a href="'.$link.'">';
		}
		
		$row .= '
									<div';
		if ($id) {
			$row .= ' id="'.$id.'"';
		}
		
		$row .= ' class="title">'.$title.'</div>';

		if ($link) {
			$row .= '
								</a>';
		}
				
		$row .= '
							</td>
							<td class="box-right-top-right">'.spacer().'</td>
						</tr>
						<tr>
							<td class="box-left">'.spacer().'</td>
							<td class="box-right">'.spacer().'</td>
						</tr>
				';
		return $row;
	}
	
	function boxSubtitle($subtitle, $link=NULL, $id=NULL) {
		$row = '
						<tr>
							<td class="box-left">'.spacer().'</td>
							<td colspan="3" class="box-subtitle">';
		
		if ($link) {
			$row .= '<a href="'.$link.'">';
		}
		
		$row .= '<div';
		if ($id) {
			$row .= ' id="'.$id.'"';
		}
		
		$row .= ' class="subtitle">'.$subtitle.'</div>';

		if ($link) {
			$row .= '</a>';
		}
				
		$row .= '</th><td class="box-right">'.spacer().'</td></tr>';
		return $row;
	}
	
	function openBoxRow() {
		return '
					<tr>
						<td class="box-left">'.spacer().'</td>
						<td colspan="3" class="box-body" valign="top">
			';
	}
	
	function closeBoxRow() {
		return '
							</td>
							<td class="box-right">'.spacer().'</td>
						</tr>
				';
	}
	
	function boxSubdivider() {
		return '
						<tr>
							<td class="box-left">'.spacer().'</td>
							<td colspan="3" class="box-subdivider" valign="top">'.spacer(0,0).'</td>
							<td class="box-right">'.spacer().'</td>
						</tr>
				';
	}
	
	function boxDivider() {
		return '
						<tr>
							<td class="box-left">'.spacer().'</td>
							<td colspan="3" class="box-divider" valign="top">'.spacer(0,0).'</td>
							<td class="box-right">'.spacer().'</td>
						</tr>
				';
	}
	
	function boxFooter($content=NULL) {
		if ($content) {
			$row = '
							<tr>
								<td class="box-left">'.spacer().'</td>
								<td colspan="3" class="box-body-bottom">'.$content.'</td>
								<td class="box-right">'.spacer().'</td>
							</tr>
					';
		} else {
			$row = '
							<tr>
								<td class="box-left-bottom-left">'.spacer().'</td>
								<td colspan="3" class="box-body-bottom">'.spacer().'</td>
								<td class="box-right-bottom-right">'.spacer().'</td>
							</tr>
					';
		}
		return $row;
	}
	
	function closeBox() {
/*		return '
						<tr>
							<td class="box-left-bottom-left">'.spacer().'</td>
							<td colspan="3" class="box-body-bottom">'.spacer().'</td>
							<td class="box-right-bottom-right">'.spacer().'</td>
						</tr>
				
						<tr>
							<td class="box-bottom-left">'.spacer().'</td>
							<td class="box-bottom-bottom-left">'.spacer().'</td>
							<td class="box-bottom">'.spacer().'</td>
							<td class="box-bottom-bottom-right">'.spacer().'</td>
							<td class="box-bottom-right">'.spacer().'</td>
						</tr>
					</table>
				';*/
		return '
						<tr>
							<td class="box-bottom-left">'.spacer().'</td>
							<td class="box-bottom-bottom-left">'.spacer().'</td>
							<td class="box-bottom">'.spacer().'</td>
							<td class="box-bottom-bottom-right">'.spacer().'</td>
							<td class="box-bottom-right">'.spacer().'</td>
						</tr>
					</table>
				';
	}

	function emailAddress($email) {
		if ($email == NULL) {
			return "Unknown email address";
		}
		$address = explode("@", $email);
		if (sizeof($address) != 2) {
			return "Bad email address";
		}
		return '<script type="text/javascript"><!--
					document.write( "'.$address[0].'" );
					document.write( "@" );
					document.write( "'.$address[1].'" );
				--></script>';
	}
	
	function errorPage($message, $email) {
		echo('<html>
			<head>
				<title>RNA virus database - Error</title>
				<link href="evolve.css" rel="stylesheet" media="screen">
			</head>
			<body>
				<h3>'.$message.'</h3>
				<h4>Please contact: '.emailAddress($email).'</h4>
			</body>
			</html>');
	}
	
?>
