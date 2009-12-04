<?php
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';
include 'php/teammemb.php';
include 'php/match.php';
include 'php/matchdate.php';
include 'php/game.php';

class tcrems {

	public $Capt;
	public $Unalloc;
	public $Notplayed;
	public $Partplayed;
	
	public function __construct($c) {
		$this->Capt = $c;
		$this->Unalloc = array();
		$this->Notplayed = array();
		$this->Partplayed = array();
	}
	
	public function addmatch($m) {
		if ($m->Result == 'N')  {
			if ($m->is_allocated())
				array_push($this->Notplayed, $m);
			else
				array_push($this->Unalloc, $m);
		}
		else
			array_push($this->Partplayed, $m);
	}
	
	public function othercapt($m) {
		if ($this->Capt->is_same($m->Hteam->Captain))
			return $m->Ateam->Captain;
		return $m->Hteam->Captain;
	}
	
	public function mails() {
		$dest = $this->Capt->Email;
		$fh = popen("mail -s 'Go League match reminder' $dest", "w");
		$mess = <<<EOT

Dear {$this->Capt->display_name()},

This is an auto-reminder from the BGA online league software.
Please do not reply to this!

EOT;
		fwrite($fh, $mess);

		if (count($this->Unalloc) != 0)  {
			$mess = <<<EOT

Please can you complete the allocation of teams to the following matches.
The other team captains are given in ()s.


EOT;
			fwrite($fh, $mess);
			foreach ($this->Unalloc as $m) {
				$oth = $this->othercapt($m);
				$mess = <<<EOT
{$m->Date->display_month()} {$m->Hteam->display_name()} -v- {$m->Ateam->display_name()} ({$oth->display_name()} {$oth->Email})

EOT;
				fwrite($fh, $mess);
			}
		}

		if (count($this->Notplayed) != 0)  {
			$mess = <<<EOT

Please can you start playing the following matches.
The other team captains are given in ()s.


EOT;
			fwrite($fh, $mess);
			foreach ($this->Notplayed as $m) {
				$oth = $this->othercapt($m);
				$mess = <<<EOT
{$m->Date->display_month()} {$m->Hteam->display_name()} -v- {$m->Ateam->display_name()} ({$oth->display_name()} {$oth->Email})


EOT;
				fwrite($fh, $mess);
			}
		}
		
		if (count($this->Partplayed) != 0)  {
			$mess = <<<EOT

Please can you arrange to complete the following matches - outstanding games listed.
The other team captains are given in ()s.


EOT;
			fwrite($fh, $mess);
			foreach ($this->Partplayed as $m) {
				$oth = $this->othercapt($m);
				$mess = <<<EOT
{$m->Date->display_month()} {$m->Hteam->display_name()} -v- {$m->Ateam->display_name()} ({$oth->display_name()} {$oth->Email})

EOT;
				fwrite($fh, $mess);
				foreach ($m->Games as $g) {
					if ($g->Result != 'N')
						continue;
					$mess = <<<EOT
  {$g->Wplayer->display_name()} ({$g->Wplayer->display_online()}) of {$g->Wteam->display_name()} -v- {$g->Bplayer->display_name()} ({$g->Bplayer->display_online()}) of {$g->Bteam->display_name()}

EOT;
					fwrite($fh, $mess);
				}
			}
		}
		
		$mess = <<<EOT

If you have allocated teams/played matches listed above please enter them
on the website.

Thanks - league auto-reminder service.

EOT;
		fwrite($fh, $mess);
		pclose($fh);
	}
}

$Captains = array();

$ret = mysql_query("select ind from lgmatch where result!='H' and result!='A' and matchdate < date_add(current_date(),interval 45 day) order by divnum,matchdate,result");
if ($ret && mysql_num_rows($ret) > 0)  {
	while ($row = mysql_fetch_array($ret))  {
		$ind = $row[0];
		$mtch = new Match($ind);
		$mtch->fetchdets();
		try {
			$mtch->fetchteams();
			$mtch->fetchgames();
		}
		catch (MatchException $e) {
			continue;
		}
		$hc = $mtch->Hteam->Captain;
		$ac = $mtch->Ateam->Captain;
		$hcn = $hc->display_name();
		$acn = $ac->display_name();
		
		if  (!array_key_exists($hcn, $Captains))
			$Captains[$hcn] = new tcrems($hc);
		if  (!array_key_exists($acn, $Captains))
			$Captains[$acn] = new tcrems($ac);
		
		$Captains[$hcn]->addmatch($mtch);
		$Captains[$acn]->addmatch($mtch);
	}

	foreach (array_keys($Captains) as $cn)
		$Captains[$cn]->mails();
}
?>
