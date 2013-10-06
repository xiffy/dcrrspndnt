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
?>
<html>
	<head>
		<title>de Correspondent, artikelen <?php echo $title_by_in; ?>: <?php echo $meta_row['waarde'].' ('.$mode.')';?></title>
		<link rel="stylesheet" href="./style.css" />
	</head>
	<body>
		<a href="./">Alle artikelen</a>
		<h1>Artikelen geschreven door: <?php echo $meta_row['waarde']?></h1>
		<table class="meta-table">
			<tr>
				<th>Opgedoken</th>
				<th>Title / Artikel</th>
				<th><?php echo $th_extra; ?></th>
			</tr>
<?php
$i = 0;
$art_res = mysql_query ('select artikelen.* from artikelen join meta_artikel on artikelen.ID = meta_artikel.art_id where meta_artikel.meta_id = '.$meta_id.' order by created_at desc');
while($row = mysql_fetch_array($art_res))
{
	$og = unserialize(stripslashes($row['og']));
	$titel = isset($og['title']) ? $og['title'] : substr($row['clean_url'],26);

	if($mode == 'author')
	{
		// vind de sectie van dit artikel
		$r = mysql_query ('select * from meta_artikel left join meta on meta.ID = meta_artikel.meta_id where meta_artikel.art_id = '.$row['ID'].' and meta.type = "article:section"');
	}
	else
	{
		// vind de auteur
		$r = mysql_query ('select * from meta_artikel left join meta on meta.ID = meta_artikel.meta_id where meta_artikel.art_id = '.$row['ID'].' and meta.type = "article:author"');
	}
	$extra_arr = mysql_fetch_array($r);

	?>
	<tr <?php if($i % 2 == 1) echo 'class="odd"'?>>
		<td><?php echo substr($row['created_at'],8,2); echo '-'.substr($row['created_at'],5,2).' '.substr($row['created_at'],11,5) ?></td>
		<td><a href="<?php echo $row['share_url'];?>"><?php echo $titel ;?></a></td>
		<td><a href="./meta_art.php?id=<?php echo $extra_arr['ID'];?>"><?php echo $extra_arr['waarde'] ?></a></td>
	</tr>
<?php
	$i++;
}

?>
		</table>
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
		<a href="./" class="clear">Alle artikelen</a>
	</body>
<?php @include('ga.inc.php'); ?>
</html>