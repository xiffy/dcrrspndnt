<!DOCTYPE html>
<?php
require_once('settings.local.php');
require_once('functions.php');
include('db.php');

$meta_id = (int)$_GET['id'];
$meta_res = mysql_query('select * from meta where ID = '.$meta_id);
$meta_row = mysql_fetch_array($meta_res);
// determine in what mode we are running; Author or Section?
$mode = explode(':', $meta_row['type']);
$mode = isset($mode[1]) ? $mode[1] : $mode;
$title_by_in = $mode == 'author' ? 'door' : 'in de sectie';
$th_extra = $mode == 'author' ? 'sectie' : 'auteur';
$th_related = $mode == 'author' ? 'auteurs' : 'secties';
$extra_query_var = $mode == 'author' ? 'article:section' : 'article:author';

// paging dr. beat:
$count_res = mysql_query('select count(artikelen.*) as amount from artikelen join meta_artikel on artikelen.ID = meta_artikel.art_id where meta_artikel.meta_id = '.$meta_id);
$count_arr = mysql_fetch_array($count_res);
$tot_row = $count_arr['amount'];
$start = 0;
if(isset($_GET['page']))
{
	$page = (int)$_GET['page'];
	$start = ($page - 1) * ITEMS_PER_PAGE;
	if($start < 0) $start = 0;
}

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

?>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="keywords" content="de correspondent, gedeelde artikelen, twitter, overzicht, gratis">
		<meta name="description" content="dcrrspndnt, indexer van gedeelde artikelen van De Correspondent, http://decorrespondent.nl", lees alle gedeelde artikelen op twitter gratis via http://molecule.nl/decorrespondent/>
		<meta name="author" content="xiffy">

		<title>de Correspondent, artikelen <?php echo $title_by_in; ?>: <?php echo $meta_row['waarde'].' ('.$mode.')';?></title>
		<link rel="stylesheet" href="./style2.css" />
		<link rel="alternate" type="application/rss+xml" title="Artikelen van De Correspondent - crrspndnt" href="./rss.php">
	</head>
	<body id="meta_art">
		<h1>Artikelen geschreven <?php echo $title_by_in; ?>: <?php echo $meta_row['waarde']?> <a href="#footer" title="Klik en lees de verantwoording onderaan de pagina"> &#x15e3;</a><a href="https://twitter.com/dcrrspndnt" class="twitter-follow-button" data-show-count="false" data-lang="nl">Volg @dcrrspndnt</a></h1>
<?php include('menu.php')?>
		<div class="clear"></div>

		<table class="meta-table">
			<tr>
				<?php echo $th_pubdate;?>
				<th>Title / Artikel</th>
				<th><?php echo $th_extra; ?></th>
				<?php echo $th_tweets;?>
			</tr>
<?php
$i = 0;
$art_res = mysql_query ('select artikelen.*, count(tweets.id) as tweet_count from artikelen join meta_artikel on artikelen.ID = meta_artikel.art_id left outer join tweets on tweets.art_id = artikelen.ID where meta_artikel.meta_id = '.$meta_id.' group by artikelen.ID '.$order_by.' limit '.$start.','.ITEMS_PER_PAGE);

while($row = mysql_fetch_array($art_res))
{
	$og = unserialize(stripslashes($row['og']));
	$titel = isset($og['title']) ? $og['title'] : substr($row['clean_url'],26);
	$description = isset($og['description']) ? $og['description'] : 'Een mysterieus artikel';
	$display_time = isset($og['article:published_time']) ? strftime('%e %b %H:%M', $og['article:published_time']) : substr($row['created_at'],8,2).'-'.substr($row['created_at'],5,2).' '.substr($row['created_at'],11,5);
	$found_at = substr($row['created_at'],8,2).'-'.substr($row['created_at'],5,2).' '.substr($row['created_at'],11,5);

	$r = mysql_query ('select * from meta_artikel left join meta on meta.ID = meta_artikel.meta_id where meta_artikel.art_id = '.$row['ID'].' and meta.type = "'.$extra_query_var.'"');
	$extra_arr = mysql_fetch_array($r);
	?>
	<tr <?php if($i % 2 == 1) echo 'class="odd"'?>>
		<td><abbr title="gevonden op: <?php echo $found_at;?>"><?php echo $display_time ?></abbr></td>
		<td><a href="<?php echo $row['share_url'];?>" title="<?php echo $description ?>"><?php echo $titel ;?></a></td>
		<td><a href="./meta_art.php?id=<?php echo $extra_arr['ID'];?>"><?php echo $extra_arr['waarde'] ?></a></td>
		<td><?php echo $row['tweet_count'];?></td>
	</tr>
<?php
	$i++;
}

?>
		</table>
<?php
	pager($tot_row, $qsa);
?>

		<table class="related">
			<tr><th>Alle <?php echo $th_related;?></th></tr>
<?php
			$i = 0;
			$metatype_res = mysql_query('select * from meta where meta.type = "'.$meta_row['type'].'" order by waarde');
			while($rel_row = mysql_fetch_array($metatype_res))
			{
				?>
				<tr <?php if($i % 2 == 1) echo 'class="odd"'?>>
					<td><a href="./meta_art.php?id=<?php echo $rel_row['ID']?>"><?php echo $rel_row['waarde'] ?></a></td>
				</tr>
				<?php
				$i++;
			}
?>
		</table>
<div class="center">
<?php include('search_box.php'); ?>
</div>
<?php include('footer.php') ?>
	</body>
<?php @include('ga.inc.php'); ?>
</html>