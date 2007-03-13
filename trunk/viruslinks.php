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

echo'
	<table width="800">
<tbody>
<tr>
<td width="50%"><h1>Virus
Taxonomy Resources:</h1>
<a href="http://www.virustaxonomyonline.com/"> Virus Taxonomy
Online</a><br>
<a href="http://www.ncbi.nlm.nih.gov/ICTVdb"> The Universal Virus
Database of the International Committee on Taxonomy of Viruses</a><br>
<a href="http://gib-v.genes.nig.ac.jp/"> Genome Information
Broker for Virus genomes</a><br>
<a href="http://www.virology.net/Big_Virology/BVHomePage.html">The
Big Picture Book of Viruses </a><br>
<br style="font-weight: bold; color: rgb(0, 0, 102);">
<h1>GenBank
Resources:</h1>
<a href="http://www.ncbi.nlm.nih.gov/Entrez/">Entrez</a>
(GeneBank+PIR+Medline) at NCBI <br>
<a href="http://srs.sanger.ac.uk/srs6/">SRS7&nbsp; (Sequence
Retrieval System)&nbsp;</a> at Sanger Centre - EMBL <br>
<a href="http://www.ddbj.nig.ac.jp/">DDJB </a>database from
japan Patheways Search. <br>
<br>
<h1>Protein Databases:</h1>
<a href="http://www.expasy.ch/sprot/sprot-top.html">SWISS-PROT</a>
(amino acid sequences and others) <br>
<a href="http://www-nbrf.georgetown.edu/pir/">PIR (Protein
Identification Resource)</a> (amino acid sequences and others) <br>
<a href="http://www.rcsb.org/pdb/">PDB (Protein Data Bank)</a>
(three-dimensional structures of proteins)<br>
<br>
<h1>Virus 
Databases:</h1>
<a href="http://www.ncbi.nlm.nih.gov/retroviruses/">Retrovirus Resources</a> at NCBI<br>
<a href="http://www.biovirus.org/index.html">Viral bioinformatics resource</a><br>
<a href="http://www.dpvweb.net/">Plant Virus Database</a>
<br>
</td>
<td width="50%"><h1>HIV
Databases:</h1>
<a href="http://hiv-web.lanl.gov/">Los Alamos HIV
Sequence Database</a>&nbsp; Los Alamos National Laboratory, USA
<br><a href="http://hivdb.stanford.edu/">Stanford Drug Resistance
Database&nbsp; - </a>Curated database containing RT and Protease
sequences for evolutionary and drug resistance studies. 
<br><a href="http://srdata.nist.gov/hivdb/">HIV Protease Database</a>
-&nbsp; Structural database maintained at the NCI devoted to HIV and
SIV protease 

<h1>HCV
Databases:</h1>
<a href="http://hepatitis.ibcp.fr/">HCV Sequence database,
EBI/France.</a><br>
<a href="http://s2as02.genes.nig.ac.jp/">HCV sequence server </a>-
great resource for HCV sequence data (Japan)&nbsp; <br>
<br>
<h1>HBV
Databases:</h1>
<a href="http://s2as02.genes.nig.ac.jp/">Hepatitis B Virus
Database</a>
at DNA database of Japan <br>
<br>
<h1>Influenza
Databases:</h1>
<a href="http://www.flu.lanl.gov/">Flu Database</a> at Los Alamos<br>
<a href="http://www.ncbi.nlm.nih.gov/genomes/FLU/FLU.html">Influenza
Virus Resource</a> at NCBI<br>
<a href="http://www.eswi.org/" target="_top">European
Scientific Working Group on Influenza</a> <a
href="http://rhone.b3e.jussieu.fr/flunet/www/" target="_top">FluNet
Global Influenza Surveillance Network</a> <br>
</td>
</tr>
</tbody>
</table>
';
	
	
	
	
	drawFooter("Robert Belshaw, Tulio de Oliveira & Andrew Rambaut"); 
	
	closeDocument();
	

	// subroutines 
	function draw_toolbar() {
		echo "<TABLE CLASS='toolbar' WIDTH='820px'>";
		echo "<tbody><tr><td><a href='index.php' >Home</a></td>";
		echo "<td><form action='search.php' method='get'><input type='text' name='query'/><input type='submit' value='Search'/></form></td>";
		echo "<td><a href='browse.php' style='color: rgb(255,255,255)'>Browse</a></td>";
		echo "<td><a href='blast.php' style='color: rgb(255,255,255)'>BLAST</a></td>";
		echo "<td><a href='proteins.php' style='color: rgb(255,255,255)'>Proteins</a></td>";
		echo "<td><a href='align.php' style='color: rgb(255,255,255)'>Align</a></td>";
		echo "<td><a href='http://evolve.zoo.ox.ac.uk' style='color: rgb(255,255,255)'>About us</a></td>";
		echo "</tr></table>";
	}
?>
