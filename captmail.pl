#! /usr/bin/perl

use DBD::mysql;
$Database = DBI->connect("DBI:mysql:bgaleague", "www-data", "BGA league access") or die "Cannot open DB";
$sfh = $Database->prepare("select player.email from player,team where team.captfirst=player.first and team.captlast=player.last and length(player.email)>0");
$sfh->execute;

while (@row = $sfh->fetchrow_array) {
	$Emails{$row[0]} = 1;
}

$sfh = $Database->prepare("select email from player where admin!='N' and length(email) > 0");
while (@row = $sfh->fetchrow_array) {
	$Emails{$row[0]} = 1;
}

$tmpfile = "/tmp/splurge$$";

open(MOUT, ">$tmpfile");
while (<STDIN>) {
		$_ = "To: " . join(", ", keys %Emails) . "\n" if /^To:\s+/i;
	print MOUT "$_";
	last if /^\s*\r?\n?$/;
}

while (<STDIN>) {
	print MOUT $_;
}
close MOUT;
#system("/usr/sbin/exim -t -oi <$tmpfile");
#unlink $tmpfile;
