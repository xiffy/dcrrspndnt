<!DOCTYPE html>
<?php
require_once('settings.local.php');
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
?>
<html>
	<head>
		<title>de Correspondent, artikelen <?php echo $title_by_in; ?>: <?php echo $meta_row['waarde'].' ('.$mode.')';?></title>
		<link rel="stylesheet" href="./style2.css" />
		<link rel="alternate" type="application/rss+xml" title="Artikelen van De Correspondent - crrspndnt" href="./rss.php">
	</head>
	<body id="meta_art">
		<h1>Artikelen geschreven <?php echo $title_by_in; ?>: <?php echo $meta_row['waarde']?></h1>
<?php include('menu.php')?>
		<div class="clear"></div>

		<table class="meta-table">
			<tr>
				<th>Opgedoken</th>
				<th>Title / Artikel</th>
				<th><?php echo $th_extra; ?></th>
			</tr>
<?php
$i = 0;
$art_res = mysql_query ('select artikelen.* from artikelen join meta_artikel on artikelen.ID = meta_artikel.art_id where meta_artikel.meta_id = '.$meta_id.' order by created_at desc limit '.$start.','.ITEMS_PER_PAGE);
while($row = mysql_fetch_array($art_res))
{
	$og = unserialize(stripslashes($row['og']));
	$titel = isset($og['title']) ? $og['title'] : substr($row['clean_url'],26);
	$description = isset($og['description']) ? $og['description'] : 'Een mysterieus artikel';

	$r = mysql_query ('select * from meta_artikel left join meta on meta.ID = meta_artikel.meta_id where meta_artikel.art_id = '.$row['ID'].' and meta.type = "'.$extra_query_var.'"');
	$extra_arr = mysql_fetch_array($r);
	?>
	<tr <?php if($i % 2 == 1) echo 'class="odd"'?>>
		<td><?php echo substr($row['created_at'],8,2); echo '-'.substr($row['created_at'],5,2).' '.substr($row['created_at'],11,5) ?></td>
		<td><a href="<?php echo $row['share_url'];?>" title="<?php echo $description ?>"><?php echo $titel ;?></a></td>
		<td><a href="./meta_art.php?id=<?php echo $extra_arr['ID'];?>"><?php echo $extra_arr['waarde'] ?></a></td>
	</tr>
<?php
	$i++;
}

?>
		</table>
		<ul id="pager">
	<?php
	// how many pages?
	$pages = ceil($tot_row / ITEMS_PER_PAGE);
	$i = 0;
	while ($i < $pages)
	{
		$page = $i + 1;
		echo '<li><a href="./meta_art.php?id='.$meta_id.'&amp;page='.$page.'">'.$page.'</a></li>';
		$i++;
	}
	?>
		</ul>

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

<?php include('footer.php') ?>
	</body>
<?php @include('ga.inc.php'); ?>
</html>