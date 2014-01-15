<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="keywords" content="de correspondent, gedeelde artikelen, twitter, overzicht, gratis">
		<meta name="description" content="dcrrspndnt, indexer van gedeelde artikelen van De Correspondent, http://decorrespondent.nl, lees alle gedeelde artikelen op twitter gratis via http://molecule.nl/decorrespondent/">
		<meta name="author" content="xiffy">
		<title>de correspondent, de tweets in grafieken</title>
		<link rel="stylesheet" href="./style2.css" />
		<link rel="alternate" type="application/rss+xml" title="Artikelen van De Correspondent - crrspndnt" href="./rss.php">

		<script src="Chart.min.js"></script>
	</head>
	<body>


<?php
require_once('settings.local.php');
require_once('functions.php');
include('db.php');

// Grafiek 1; tweets per dag

$tot_tweets_res = mysql_query('select count(tweets.id) as tweet_count, day(tweets.created_at) as  dag, month(tweets.created_at) as maand from tweets where created_at > "2013-10-13 21:00" group by maand, dag order by year(tweets.created_at) desc, month(tweets.created_at) desc, day(tweets.created_at) desc limit 0,30');

$label = array();
$tweets = array();
$high = 0;
$rows = array();
while ($row = mysql_fetch_array($tot_tweets_res))
{
	$rows[] = $row;
}
$rows = array_reverse($rows);

$cur_month = '';
foreach($rows as $row)
{
	$lab = $row['dag'];
	if ( (int)$row['maand'] != (int)$cur_month)
	{
		$lab .= '-'.$row['maand'];
		$cur_month = $row['maand'];
	}
	$label[] = $lab;
	$tweets[] = $row['tweet_count'];
	$high = max($high, $row['tweet_count'] + 10);
}
$scaleWidth = ceil($high / 10);

$bar_label = '';
foreach($label as $lab)
{
	$bar_label .= '"'.$lab.'",';
}
$bar_label = substr($bar_label, 0, strlen($bar_label) - 1);
$bar_tweet_data = '';
foreach($tweets as $tweet_data)
{
	$bar_tweet_data .= $tweet_data.',';
}
$bar_tweet_data = substr($bar_tweet_data, 0, strlen($bar_tweet_data) - 1);



// Grafiek 2;
// Tweets per uur en de dagtrend daar op afgezet
$dagen_res = mysql_query("select date(tweets.created_at) as dagen from tweets group by dagen");
$dagen = mysql_num_rows($dagen_res);

$graph_res = mysql_query("select count(tweets.id) as tweet_count, hour(tweets.created_at) as per_uur from tweets where created_at > \"2013-10-13 21:00\" group by per_uur ");

$high = 0;
$hour_label = '';
$hour_tweet_data = '';
$uur_nu = date('H');
$minuut_nu = date('i');

while ($row = mysql_fetch_array($graph_res))
{
	$hour_label .= $row['per_uur'].',';
	$deler = (int)$row['the_uur'] > (int)$uur_nu ? $dagen - 1 : $dagen;
	$tot = ceil($row['tweet_count'] / $deler);
	$hour_tweet_data .= $tot.',';
	$high = max($high, $tot + 10);
}

$hour_label = substr($hour_label, 0, strlen($hour_label) - 1);
$hour_tweet_data = substr($hour_tweet_data, 0, strlen($hour_tweet_data) - 1);

// A la Chartbeat, de lijn wordt langer tijdens de dag
// verschijnt in de uur-trend-grafiek
// Eerst de tweets van vandaag
$res_today = mysql_query("select count(tweets.ID) per_hour, hour(tweets.created_at) as the_hour, tweets.created_at from tweets
where year(tweets.created_at) = year(now() )
  and month(tweets.created_at) = month(now())
  and day(tweets.created_at) = day(now() )
group by the_hour
order by created_at");
// verwerken in grafiek-data
$i=0;
while ($row = mysql_fetch_array($res_today))
{
	while ($i < (int)$row['the_hour'])
	{
		$hour_today_data .= '0,';
		$i++;
	}
	$high = max($high, $row['per_hour'] + 10);
	$hour_today_data .= $row['per_hour'].',';
	$i++;
	if( (int)$row['the_hour'] == (int)$uur_nu)
	{ // make projection; 12 times per hour, which time are we?

		$hour_part = floor($minuut_nu / 5) + 1;

		$projection = floor((12 / $hour_part) * (int)$row['per_hour']);

		$j = 0;
		while($j < (int)$uur_nu) // naar de juiste plek brengen ...
		{
			$projection_data .= ' ,';
			$j++;
		}
		$projection_data .= $projection;
	}
}

$hour_today_data = substr($hour_today_data, 0, strlen($hour_today_data) - 1);
$scaleWidth2 = ceil($high / 10);


?>

		<h1><a href="http://decorrespondent.nl/">de Correspondent</a> tweets in grafieken <a href="#footer" title="Klik en lees de verantwoording onderaan de pagina"> &#x15e3;</a><a href="https://twitter.com/dcrrspndnt" class="twitter-follow-button" data-show-count="false" data-lang="nl">Volg @dcrrspndnt</a></h1>
<?php include ('menu.php'); ?>
		<div class="center">
		<div class="meta_graph">
			<h2>Tweets per dag</h2>
			<canvas id="tot_tweets" height="450" width="800"></canvas>
			<script>
				var barOptions = {
					barValueSpacing : 1, // bar
					barDatasetSpacing : 1, // bar

					scaleOverride : 1,
					scaleSteps : 10,
					//Number - The value jump in the hard coded scale
					scaleStepWidth : <?php echo $scaleWidth; ?>,
					//Number - The scale starting value
					scaleStartValue : 0

				}
				var barChartData = {
					labels: [ <?php echo $bar_label;  ?>],
					datasets : [ {
												fillColor   : "rgba(77,83,97,0.5)",
												strokeColor : "rgba(77,83,97,1)",
												data : [<?php echo $bar_tweet_data;?>]
										 } ]
				}
				var tweetTot = new Chart(document.getElementById("tot_tweets").getContext("2d")).Bar(barChartData, barOptions);
			</script>
			<p>maximaal dertig dagen </p>

			<h2>Tweets per uur</h2>
			<canvas id="hour_tweets" height="450" width="800"></canvas>
			<script>
				var lineOptions = {
					pointDot : true, //line
					pointDotRadius : 3,
					pointDotStrokeWidth : 1,

					scaleOverride : 1,
					scaleSteps : 10,
					//Number - The value jump in the hard coded scale
					scaleStepWidth : <?php echo $scaleWidth2; ?>,
					//Number - The scale starting value
					scaleStartValue : 0

				}
				var lineChartData = {
					labels: [ <?php echo $hour_label;  ?>],
					datasets : [ {
												fillColor   : "rgba(77,83,97,0.5)",
												strokeColor : "rgba(77,83,97,1)",
												pointColor : "rgba(77,83,97,1)",
												pointStrokeColor : "#fff",
												data : [<?php echo $hour_tweet_data;?>]
										 },
										   {
										   	fillColor	  : "rgba(192,8,14,0.5)",
										   	strokeColor : "rgba(192,8,14,1)",
										   	pointColor : "rgba(192,8,14,1)",
										   	pointStrokeColor : "#000",
										   	data: [<?php echo $hour_today_data;?>]
										  },
										  	{
										   	fillColor	  : "rgba(61,186,0,0.0)",
										   	strokeColor : "#FFEB9D",
										   	pointColor : "#FFEB9D",
										   	pointStrokeColor : "rgba(192,8,14,1)",
										   	data: [<?php echo $projection_data;?>]
										  }
										 ]
				}
				var tweetHour = new Chart(document.getElementById("hour_tweets").getContext("2d")).Line(lineChartData, lineOptions);
			</script>
			<p>Grijs is de overall trend, rood geeft de tweets van vandaag weer</p>
		</div>
		</div>
<?php include('footer.php') ?>
	</body>
<?php @include('ga.inc.php') ?>

</html>