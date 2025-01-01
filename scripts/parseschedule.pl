#!/usr/bin/perl -w

use JSON::Parse ':all';

if($#ARGV > 0) {
    print "Usage: parseschedule.pl [<date>]\n";
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
    $order = 0;
    foreach $game (@$games) {
        $season = $game->{'season'};
        $nhlId = $game->{'id'};
        $gameType = $game->{'gameType'};
	$homeTeam = $game->{'homeTeam'};
	$homeTeamAbbr = $homeTeam->{'abbrev'};
	$awayTeam = $game->{'awayTeam'};
	$awayTeamAbbr = $awayTeam->{'abbrev'};
	$startTimeApiStr = $game->{'startTimeUTC'};
	$startTimeApiStr =~ /(\d\d\d\d-\d\d-\d\d)T(\d\d:\d\d:\d\d)Z/ or die "Invalid start time: $startTimeApiStr";
	$startDateStr = $1;
	$startTimeStr = $2;
	$order++;
	print "INSERT INTO Games(SeasonId, NhlId, Home, Away, Date, StartTime, OrderInDay) VALUES('$season-$gameType', '$nhlId', (SELECT TeamId FROM Teams WHERE Code = '$homeTeamAbbr'), (SELECT TeamId FROM Teams WHERE Code = '$awayTeamAbbr'), '$date', '$startDateStr $startTimeStr+00:00', $order);\n";
    }
}
