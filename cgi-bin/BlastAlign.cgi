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

system "cp $docRootDir/virus/BlastAlign $localdir";
system "cp $docRootDir/virus/formatdb $localdir";
system "cp $docRootDir/virus/blastall $localdir";
system "cp $docRootDir/virus/fasta_files/nuc_library.lib $localdir";
system "cp $docRootDir/virus/logoVirus.gif $localdir";

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
    print "<img alt=\"\" src=\"http://evolve.zoo.ox.ac.uk/virus/logoVirus.gif\" usemap=\"\#logoVirus.gif\" style=\"border: 0px solid ; width: 820px; height: 62px;\"> <map
 						name=\"logoVirus.gif\">
						<area shape=\"RECT\" alt=\"Evolutionary Biology Group, Oxford\"
 						coords=\"12,10,202,56\" href=\"http://evolve.zoo.ox.ac.uk\">
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

    
