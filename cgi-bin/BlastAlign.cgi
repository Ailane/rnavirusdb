#! /usr/bin/perl -w

use strict;
use lib (".");
use CGI ":standard";

# EDIT this to be the absolute path of the web site directory no trailing /
my $website_dir = "/Library/WebServer/Documents/rnavirusdb";   # typical value on Mac OS X
#my $website_dir = "/var/www/html/rnavirusdb";   # typical value on linux
my $website_URL="http://eholmes2.zoo.ox.ac.uk/rnavirusdb";
my $logFile = "run.log";

my $dir_for_final_aligns = "$website_dir/tmp";
# Create job directory and copy some files
my $jobname = $$;
my $dir_for_analysis = "/tmp/BlastAlign_dir-$jobname";

mkdir $dir_for_analysis;

system "cp $website_dir/cgi-bin/BlastAlign $dir_for_analysis";
system "cp $website_dir/cgi-bin/BlastAlign.py $dir_for_analysis";
system "cp $website_dir/cgi-bin/blastall $dir_for_analysis";
system "cp $website_dir/cgi-bin/formatdb $dir_for_analysis";
system "cp $website_dir/cgi-bin/nuc_library.lib $dir_for_analysis";

chdir $dir_for_analysis; # move into tmp directory

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
    print "<center> <font size \"+1\"> <b>BlastAlign Progress</b><br><hr> </font>";
    print "\<center\>"; 
    print "BlastAlign running, please be patient....."; 

    
    ## Do the analysis
 
 	$command = $command." >> $logFile 2>&1"; 
 	system "$command";

	system "cp all_seqs.phy $dir_for_final_aligns/PhylipAlign$jobname"; # copy files into tmp with WebServer/Documents
	system "cp all_seqs.nxs $dir_for_final_aligns/NexusAlign$jobname";
	
    print "<p><p><hr><p> The sequences have been aligned !<br>";
    print "<br> The Alignment can be extracted in Phylip or Nexus Format (you may wish to edit the names):<br>";
    print "<li><a href=\"$website_URL/tmp/PhylipAlign$jobname\">Alignment.phy</a>(Phylip format)<p>";
    print "<li><a href=\"$website_URL/tmp/NexusAlign$jobname\">Alignment.nxs</a>(Nexus format)<p><hr><p>";
    print "<br><br><center> Analysis complete <br><br>";
