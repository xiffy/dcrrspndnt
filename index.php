<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="author" content="xiffy">
		<title>nrc.nl, de artikelen volgens twitter</title>
		<link rel="stylesheet" href="./style2.css" />
	</head>
	<body>


<?php
require_once('settings.local.php');
require_once('functions.php');
include('db.php');
$count_res = mysql_query('select count(*) as amount from artikelen');
$count_arr = mysql_fetch_array($count_res);
$tot_row = $count_arr['amount'];
$start = 0;
if(isset($_GET['page']))
{
	$page = (int)$_GET['page'];
	$start = ($page - 1) * ITEMS_PER_PAGE;
	if($start < 0) $start = 0;
}
// sorteren kan op 'vinddatum' of aantal tweets
$order_by = ' order by created_at desc ';
$qsa = '';
$th_pubdate = '<th>Gepubliceerd</th>';
$sep = strstr($_SERVER['REQUEST_URI'], '?') ? '&amp;' : '?';
$th_tweets = '<th class="sortable"><a href="'.$_SERVER['REQUEST_URI'].$sep.'order=tweets" title="Sorteer op aantal maal gedeeld" >tweets</a>&#9660;</th>';
if(isset($_GET['order']) && $_GET['order'] == 'tweets')
{
	$order_by = ' order by tweet_count desc ';
	$qsa = '&amp;order=tweets'; // voor de pager
	$th_pubdate = '<th class="sortable"><a href="./?page='.$page.'" title="Sorteer op publicatiedatum">Gepubliceerd</a>&#9660;</th>';
	$th_tweets = '<th>tweets</th>';
}

$i = 0;
$res = mysql_query('select artikelen.*, count(tweets.id) as tweet_count from artikelen left outer join tweets on tweets.art_id = artikelen.id group by artikelen.id '.$order_by.' limit '.$start.','.ITEMS_PER_PAGE);
?>
		<h1>Artikelen van <a href="http://www.nrc.nl/">nrc.nl</a> gevonden op Twitter</h1>
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
				<td style="max-width:400px"><strong><a href="<?php echo $row['share_url'];?>" title="<?php echo $description ?>"><?php echo $titel ;?></a></strong></td>
				<td><a href="./meta_art.php?id=<?php echo $author['ID'];?>" title="alle artikelen van deze auteur"><?php echo $author['waarde'];?></a></td>
				<td><a href="./meta_art.php?id=<?php echo $section['ID'];?>" title="alle artikelen in deze sectie"><?php echo $section['waarde'];?></a></td>
				<td align="right"><?php echo $row['tweet_count']?></td>
			</tr>
	<?php
	$i++;
}
?>
		</table>
<?php
	pager($tot_row, $qsa);
?>

<?php include('search_box.php') ?>
	</div>
<?php include('footer.php') ?>
</body>
<?php @include('ga.inc.php') ?>

</html>
