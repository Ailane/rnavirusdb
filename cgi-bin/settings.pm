##########################################################################
### Edit and copy to settings.pm in the web server's cgi bin directory ###
##########################################################################

package settings;

use strict;

#use vars qw($tmpDir $subtypingDir $tmpUrl $clustalw $paup $drawgram $puzzle $ps2pdf $phyliptreefile $phylipdrawgramcmds $phylis $fastainfo $trimalignment $allowBatch $clean);
use vars qw($docRootDir $tmpDir $tmpUrl $clustalw $ps2pdf $phyliptreefile $phylipdrawgramcmds $allowBatch $clean);

# installation directories and options
$docRootDir = "/var/www";
$tmpUrl = "/virus/tmp";
$tmpDir = $docRootDir . $tmpUrl;
$allowBatch = 1;
$clean = 0;

# paths to programs
$clustalw = "/usr/bin/clustalw";
#$paup = "/usr/local/biotools/paup/paup4b10-ppc-macosx";
#$drawgram = "/usr/local/biotools/phylip/drawgram";
#$puzzle = "/usr/local/biotools/treepuzzle/src/puzzle-macosx-Xcode";
$ps2pdf = "/usr/bin/ps2pdf";

# these are highly dependent on your version of Phylip.
# for phylip-3.5:
$phyliptreefile = "intree";
$phylipdrawgramcmds = "(echo V; echo N;  echo Y)";
