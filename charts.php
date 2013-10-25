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
		<?php
		@include('./tinypass.js');
		?>

		<script src="Chart.min.js"></script>
	</head>
	<body>


<?php
require_once('settings.local.php');
require_once('functions.php');
include('db.php');
$tot_tweets_res = mysql_query('select count(tweets.id) as tweet_count, day(tweets.created_at) as  dag, month(tweets.created_at) as maand from tweets group by maand, dag');

$label = array();
$tweets = array();
$high = 0;
while ($row = mysql_fetch_array($tot_tweets_res))
{
	$label[] = $row['dag'];
	$tweets[] = $row['tweet_count'];
	$high = max($high, $row['tweet_count'] + 100);
}
$scaleWidth = ceil($high / 10);

$bar_label = '';
foreach($label as $lab)
{
	$bar_label .= $lab.',';
}
$bar_label = substr($bar_label, 0, strlen($bar_label) - 1);
$bar_tweet_data = '';
foreach($tweets as $tweet_data)
{
	$bar_tweet_data .= $tweet_data.',';
}
$bar_tweet_data = substr($bar_tweet_data, 0, strlen($bar_tweet_data) - 1);
?>

		<h1><a href="http://decorrespondent.nl/">de Correspondent</a> tweets in grafieken <a href="#footer" title="Klik en lees de verantwoording onderaan de pagina"> &#x15e3;</a><a href="https://twitter.com/dcrrspndnt" class="twitter-follow-button" data-show-count="false" data-lang="nl">Volg @dcrrspndnt</a></h1>
<?php include ('menu.php'); ?>
		<div class="center">
			<canvas id="tot_tweets" height="450" width="600"></canvas>
			<script>
				var barOptions = {
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

		</div>
<?php include('footer.php') ?>
	</body>
<?php @include('ga.inc.php') ?>

</html>