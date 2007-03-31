#!/usr/bin/perl -w

##########################################################################
###      Begin CGI-BIN                                                 ###
##########################################################################
use strict;
use lib (".");
use CGI ":standard";
use settings;

my $logFile = "run.log";
my $localdir;
my $localurl;
my $jobname;


# Create job directory and copy some files
$jobname = $$;
  $localdir = "$settings::tmpDir/BlastAlign-$jobname";
  $localurl = "$settings::tmpUrl/BlastAlign-$jobname";


mkdir $localdir;
chdir $localdir;

system "cp $settings::blastAlignPath $localdir";
system "cp $settings::nuc_libPath $localdir";
system "cp $settings::logoVirusPath $localdir";

  # Get the  input sequence
my $sequence = "";
if (param()) {
	$sequence = param('blastseq');
}

$sequence =~ s/>\S+//; # strip out name line if FASTA
$sequence =~ s/\s+//g; # strip out linebreaks if FASTA
$sequence =~ s/[^acgtrymwskdhbvn]//ig; # strip out any non-nucleotide characters

open(SEQFILE, ">sequences.fasta") or die "can't create sequences.fasta\n";
print SEQFILE ">user_query\n$sequence\n"; # make into a fasta file
close(SEQFILE);

system "cat nuc_library.lib sequences.fasta > all_seqs"; # meld query file with library file (clumsy but works)
my $command =  "perl BlastAlign -i all_seqs -r user_query -m 0.5";

   print header,
    start_html('BlastAlign Progress'),
	hr;
    print "<img alt=\"\" src=\"$localurl/logoVirus.gif\" usemap=\"\#logoVirus.gif\" style=\"border: 0px solid ; width: 820px; height: 134px;\"> <map
 						name=\"logoVirus.gif\">
						<area shape=\"RECT\" alt=\"Evolutionary Biology Group, Oxford\"
 						coords=\"0,60,190,114\" href=\"http://evolve.zoo.ox.ac.uk\">
						<area shape=\"RECT\" alt=\"Bioinformatics Institute, Auckland\"
 						coords=\"300,60,480,114\" href=\"http://www.cebl.auckland.ac.nz\">
						</map>";
						
    print "<center> <font size \"+1\"> <b>BlastAlign Progress</b><br><hr> </font>";
    print "\<center\>"; 
    print "BlastAlign running, please be patient....."; 
   
    
    ## Do the analysis
 
 	$command = $command." >> $logFile 2>&1"; 
 	system "$command";

    
    print "<p><p><hr><p> The sequences had been aligned !<br>";
    print "<br> The Alignment can be extracted in Phylip or Nexus Format:<br>";
    print "<li><a href=\"$localurl/all_seqs.phy\">Alignment.phy</a>(Phylip format)<p>";
    print "<li><a href=\"$localurl/all_seqs.nxs\">Alignment.nxs</a>(Nexus format)<p><hr><p>";
    print "<br><br><center> Analysis complete <br><br>";

    
