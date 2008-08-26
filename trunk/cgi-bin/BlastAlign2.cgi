#! /usr/bin/perl -w

use strict;
use lib (".");
use CGI ":standard";

# EDIT this to be the absolute path of the web site directory no trailing /
my $website_dir = "/Library/WebServer/Documents/rnavirusdb";   # typical value on Mac OS X
#my $website_dir = "/var/www/html/rnavirusdb";   # typical value on linux
my $website_URL="http://eholmes2.zoo.ox.ac.uk/rnavirusdb";
my $logFile = "run.log";

# Create job directory and copy some files
my $lib_loc = "";
if (param()) {
	$lib_loc = param('id');
}
my $dir_for_final_aligns = "$website_dir/tmp";

my $dir_for_analysis = $lib_loc."_dir";

mkdir $dir_for_analysis;

system "cp $website_dir/cgi-bin/BlastAlign $dir_for_analysis";
system "cp $website_dir/cgi-bin/BlastAlign.py $dir_for_analysis";
system "cp $website_dir/cgi-bin/blastall $dir_for_analysis";
system "cp $website_dir/cgi-bin/formatdb $dir_for_analysis";
system "mv $lib_loc $dir_for_analysis";

chdir $dir_for_analysis; # move into tmp directory

$lib_loc =~ s/\/tmp//; # convert loc to just the file name
my $file_location = $dir_for_analysis.$lib_loc;
my $command =  "perl BlastAlign -i $file_location -r UserQuery -m 0.5"; 

   print header,
    start_html('BlastAlign Progress'),
	hr;
    print "<center> <font size \"+1\"> <b>BlastAlign Progress</b><br><hr> </font>";
    print "\<center\>"; 
    print "BlastAlign running, please be patient....."; 

    
    ## Do the analysis
 
 	$command = $command." >> $logFile 2>&1"; 
 	system "$command";

	system "cp $file_location.* $dir_for_final_aligns"; # copy files into tmp of rnavirusdb file
	
	#print "<p>dir_for_analysis is:$dir_for_analysis, file_location is:$file_location, dir_for_final_aligns is:$dir_for_final_aligns</p>";
    print "<p><p><hr><p> The sequences have been aligned !<br>";
    print "<br> The Alignment can be extracted in Phylip or Nexus Format (you may wish to edit the names):<br>";
    print "<li><a href=\"$website_URL/tmp$lib_loc.phy\">Alignment.phy</a>(Phylip format)<p>";
    print "<li><a href=\"$website_URL/tmp$lib_loc.nxs\">Alignment.nxs</a>(Nexus format)<p><hr><p>";
    print "<br><br><center> Analysis complete <br><br>";
