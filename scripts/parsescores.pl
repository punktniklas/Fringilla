#!/usr/bin/perl -w

use JSON::Parse ':all';

if($#ARGV > 0) {
    print "Usage: parsescores.pl [<date>]\n";
    exit;
}
$wantedDate = $#ARGV == 0 ? $ARGV[0] : "";


my $json = join('', <STDIN>);

$foo = parse_json($json);

$gameWeek = $foo->{'gameWeek'};
foreach $gameDay (@$gameWeek) {
    $date = $gameDay->{'date'};
    if($wantedDate && $wantedDate ne $date) {
        next;
    }
    $games = $gameDay->{'games'};
    foreach $game (@$games) {
        $nhlId = $game->{'id'};
	$homeTeam = $game->{'homeTeam'};
	$homeScore = $homeTeam->{'score'};
	$awayTeam = $game->{'awayTeam'};
	$awayScore = $awayTeam->{'score'};
	$gameOutcome = $game->{'gameOutcome'};
	$lastPeriodType = $gameOutcome->{'lastPeriodType'};
        if(!(defined $lastPeriodType)) {
            next;
        }
        $winner = $homeScore > $awayScore ? 1 : 2;

	print "UPDATE Games SET Winner = $winner, HomeGoals = $homeScore, AwayGoals = $awayScore, GameOutcome = '$lastPeriodType' WHERE NhlId = '$nhlId';\n";
    }
}
