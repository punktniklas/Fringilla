<?php
function formatSeason($season) {
  preg_match("/(\d{4})(\d{4})-(\d)/", $season, $matches);
  $year1 = $matches[1];
  $year2 = $matches[2];
  $seasonType = $matches[3];
  $seasonTypes = [ 1 => "försäsong", 2 => "grundserien", 3 => "slutspelet" ];
  return "$seasonTypes[$seasonType] $year1-$year2";
}
?>
