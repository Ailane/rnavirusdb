##########################################################################
### Edit and copy to settings.pm in the web server's cgi bin directory ###
##########################################################################

package settings;

use strict;

use vars qw($docRootDir $blastAlignPath $nuc_libPath $logoVirusPath $tmpDir $tmpUrl $clustalw $ps2pdf $phyliptreefile $phylipdrawgramcmds $allowBatch $clean);

# installation directories and options

# root directory of the web server
$docRootDir = "/var/www";
# absolute path of programs and data used by BlastAlign.cgi
$blastAlignPath = "/usr/lib/cgi-bin/BlastAlign";
$logoVirusPath = $docRootDir . "/virus/images/logoVirus.gif";
$nuc_libPath = "/usr/bin/fasta_files/nuc_library.lib";

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

1;
