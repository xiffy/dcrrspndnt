<?php
require_once('settings.local.php');
include('db.php');

if (! isset($_GET['q']))
{
	header('Location: ./ ');
}
$search_string = mysql_real_escape_string($_GET['q']);
$display_string = htmlspecialchars($_GET['q']);
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>nrc.nl, zoekresultaten</title>
		<link rel="stylesheet" href="./style2.css" />
	</head>
	<body id="meta_art">
		<h1>Artikelen die "<?php echo $display_string;?>" bevatten</h1>
<?php include('menu.php')?>

		<div class="center">
		<table>
			<tr>
				<th>Gepubliceerd</th><th>Titel / Artikel</th><th>Auteur</th><th>Sectie</th><th>Tweets</th>
			</tr>
<?php
// teller-query toevoegen, pager voeden!

$res = mysql_query('select *, count(tweets.id) as tweet_count from meta left join meta_artikel on meta_artikel.meta_id = meta.id left join artikelen on artikelen.id = meta_artikel.art_id left outer join tweets on tweets.art_id = artikelen.id where waarde like "%'.$search_string.'%" and NOT artikelen.id IS NULL group by artikelen.id ');
$i = 0;
while($row = mysql_fetch_array($res) )
{
	$og = unserialize(stripslashes($row['og']));
	$titel = isset($og['title']) ? $og['title'] : substr($row['clean_url'],18,50);
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
				<td align="right"><?php echo $row['tweet_count'];?></td>
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
</html>