<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="keywords" content="de correspondent, gedeelde artikelen, twitter, overzicht, gratis">
		<meta name="description" content="dcrrspndnt, indexer van gedeelde artikelen van De Correspondent, http://decorrespondent.nl", lees alle gedeelde artikelen op twitter gratis via http://molecule.nl/decorrespondent/>
		<meta name="author" content="xiffy">
		<title>de correspondent, de artikelen - populai op twitter</title>
		<link rel="stylesheet" href="./style2.css" />
		<link rel="alternate" type="application/rss+xml" title="Artikelen van De Correspondent - crrspndnt" href="./rss.php">
	</head>
	<body>


<?php
require_once('settings.local.php');
require_once('functions.php');
include('db.php');

$start = 0;


$qsa = '';
$th_pubdate = '<th>Gepubliceerd</th>';
$sep = strstr($_SERVER['REQUEST_URI'], '?') ? '&amp;' : '?';
$th_tweets = '<th>tweets</th>';
$order_by = ' order by tweet_count desc ';

$i = 0;
$res = mysql_query('select artikelen.*, count(tweets.id) as tweet_count from artikelen left outer join tweets on tweets.art_id = artikelen.id group by artikelen.id having tweet_count > 0 '.$order_by.' limit '.$start.',50');
?>
		<h1>Populaire artikelen van <a href="http://decorrespondent.nl/">de Correspondent</a> gevonden op Twitter <a href="#footer" title="Klik en lees de verantwoording onderaan de pagina"> &#x15e3;</a><a href="https://twitter.com/dcrrspndnt" class="twitter-follow-button" data-show-count="false" data-lang="nl">Volg @dcrrspndnt</a></h1>
<?php include ('menu.php'); ?>
		<div class="center">
		<table>
			<tr>
				<?php echo $th_pubdate;?><th>Titel / Artikel</th><th>Auteur</th><th>Sectie</th><?php echo $th_tweets;?>
			</tr>
<?php
while($row = mysql_fetch_array($res) )
{
	$og = unserialize(stripslashes($row['og']));
	$titel = isset($og['title']) ? $og['title'] : substr($row['clean_url'],26);
	$description = isset($og['description']) ? $og['description'] : 'Een mysterieus artikel';
	$auth_res = mysql_query('select * from meta where meta.waarde = "'.$og['article:author'].'"');
	$author = mysql_fetch_array($auth_res);
	$section_res = mysql_query('select * from meta where meta.waarde = "'.$og['article:section'].'"');
	$section = mysql_fetch_array($section_res);
	$display_time = isset($og['article:published_time']) ? strftime('%e %b %H:%M', $og['article:published_time']) : substr($row['created_at'],8,2).'-'.substr($row['created_at'],5,2).' '.substr($row['created_at'],11,5);
	$found_at = substr($row['created_at'],8,2).'-'.substr($row['created_at'],5,2).' '.substr($row['created_at'],11,5);
	?>

			<tr <?php if($i % 2 == 1) echo 'class="odd"'?>>
				<td><abbr title="gevonden op: <?php echo $found_at;?>"><?php echo $display_time ?></abbr></td>
				<td><strong><a href="<?php echo $row['share_url'];?>" title="<?php echo $description ?>"><?php echo $titel ;?></a></strong></td>
				<td><a href="./meta_art.php?id=<?php echo $author['ID'];?>" title="alle artikelen van deze auteur"><?php echo $author['waarde'];?></a></td>
				<td><a href="./meta_art.php?id=<?php echo $section['ID'];?>" title="alle artikelen in deze sectie"><?php echo $section['waarde'];?></a></td>
				<td><?php echo $row['tweet_count']?></td>
			</tr>
	<?php
	$i++;
}
?>
		</table>


<?php include('search_box.php') ?>
	</div>
<?php include('footer.php') ?>
</body>
<?php @include('ga.inc.php') ?>

</html>