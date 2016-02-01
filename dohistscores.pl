#! /usr/bin/perl

use DBD::mysql;

%Params = (p => 0.01, w => 100, d => 50, l => 0, f => 1, a => 0, j => 0.5, hd => 2, hr => 1, fz => 2);

$Database = DBI->connect("DBI:mysql:bgaleague;host=britgo.org", "jmc", "jmc\'s mysql p\@ssw0rd") or die "Cannot open DB";

# Reset params

$sfh = $Database->prepare("SELECT sc,val FROM params");
$sfh->execute;
while (my @row = $sfh->fetchrow_array)  {
	$Params{$row[0]} = $row[1] + 0;
}

# Build up table of matches by ind

$sfh = $Database->prepare("SELECT seasind,hteam,ateam,hscore,ascore,result FROM histmatch");
$sfh->execute;

$playedsc = $Params{p};
$wonsc = $Params{w};
$drawsc = $Params{d};
$lostsc = $Params{l};
$forsc = $Params{f};
$agsc = $Params{a};

while (my @row = $sfh->fetchrow_array)  {
	my ($seasind,$hteam,$ateam,$hscore,$ascore,$result) = @row;
	$seasind += 0;
	$hscore += 0.0;
	$ascore += 0.0;
	if ($result eq 'H')  {
		$totals{$hteam}->{$seasind} += $playedsc + $wonsc + $forsc * $hscore + $agsc * $ascore;
		$totals{$ateam}->{$seasind} += $playedsc + $lostsc + $forsc * $ascore + $agsc * $hscore;
	}
	elsif ($result eq 'A')  {
		$totals{$ateam}->{$seasind} += $playedsc + $wonsc + $forsc * $hscore + $agsc * $ascore;
		$totals{$hteam}->{$seasind} += $playedsc + $lostsc + $forsc * $ascore + $agsc * $hscore;
	}
	elsif ($result eq 'D') {
		$totals{$hteam}->{$seasind} += $playedsc + $drawsc + $forsc * $hscore + $agsc * $ascore;
		$totals{$ateam}->{$seasind} += $playedsc + $drawsc + $forsc * $ascore + $agsc * $hscore;
	}
	else {
		$totals{$hteam}->{$seasind} += $forsc * $hscore + $agsc * $ascore;
		$totals{$ateam}->{$seasind} += $forsc * $ascore + $agsc * $hscore;
	}
}

for my $team (keys %totals)  {
	my $qteam = $Database->quote($team);
	my $tres = $totals{$team};
	for my $season (keys %$tres) {
		my $sc = $tres->{$season};
		my $sfh = $Database->prepare("UPDATE histteam SET sortrank=$sc WHERE name=$qteam AND seasind=$season");
		$sfh->execute;
	} 
}
