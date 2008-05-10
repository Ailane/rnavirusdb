#! /usr/bin/perl -w

use DBI;
open OUT, ">nuc_library.lib"; # for BlastAlign (full names)
close OUT;
open OUT, ">>nuc_library.lib";
open OUT2, ">nuc_library_acc.lib"; # for BLAST (accession numbers only)
close OUT2;
open OUT2, ">>nuc_library_acc.lib";

$dbh = DBI->connect('DBI:mysql:rnaviruses');
$statement = "select segments.id, viruses.name, segments.name, segments.nuc_sequence from viruses , segments where (segments.virus_id = viruses.id)"; 
$sth = $dbh->prepare($statement) or die "Can't prepare $statement: $dbh->errstr\n";	
$sth->execute or die "Can't execute the query: $sth->errstr\n";
while (@row = $sth->fetchrow_array) {
	print OUT ">",$row[0]," ", $row[1]," (",$row[2],") ","\n",$row[3],"\n";
	print OUT2 ">",$row[0],"\n",$row[3],"\n";
}

system "formatdb -i nuc_library_acc.lib -p F -o T "; # for use by BLAST
$sth->finish;
$dbh->disconnect;
close OUT;
close OUT2;
exit;