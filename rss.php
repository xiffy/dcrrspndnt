<?php
header('Content-Type: text/xml; charset=utf-8');
echo '<?xml';
echo ' version="1.0" encoding="utf-8"?>'; ?>

<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:dc="http://purl.org/dc/elements/1.1/">
	<channel>
<?php
require_once('settings.local.php');
include('db.php');
$extra_title = '';
$query = 'select artikelen.*,meta.waarde as pubdate from artikelen
left join meta_artikel on meta_artikel.art_id = artikelen.id
left join meta on meta.id = meta_artikel.meta_id where meta.`type` = "article:published_time" order by created_at desc limit 0,50';
if (isset($_GET['id']))
{
	$meta_id = (int) $_GET['id'];
	$meta_res = mysql_query('select * from meta where id = '.$meta_id );
	$meta_arr = mysql_fetch_array($meta_res);
	$extra_type = explode(':', $meta_arr['type']);
	$type = $extra_type[1];
	$value = $meta_arr['waarde'];
	$extra_title = $type.' : '.$value;
	$query = 'select artikelen.*, unixtime(artikelen.created_at) as tm from artikelen join meta_artikel on artikelen.ID = meta_artikel.art_id where meta_artikel.meta_id = '.$meta_id.' order by created_at desc limit 0,50';
}
$i = 0;
$res = mysql_query($query);
?>

		<title>de Correspondent - gedeelde artikelen. <?php echo $extra_title;?></title>
		<link>http://molecule.nl/decorrespondent/</link>
		<description>de Correspondent geeft de mogelijkheid betaalde artikelen te delen, dcrrspndnt zoekt deze links op twitter en slaat deze op, en geeft deze als overzicht terug op http://molecule.nl/decorrespondent/</description>
		<language>NL-nl</language>
		<pubDate><?php echo date('r');?></pubDate>
		<lastBuildDate><?php echo date('r');?></lastBuildDate>
		<docs>http://blogs.law.harvard.edu/tech/rss</docs>
		<generator>dcrrspndnt</generator>
		<atom:link href="http://molecule.nl/decorrespondent/rss.php" rel="self" type="application/rss+xml" />

<?php
while($row = mysql_fetch_array($res) )
{
	$og = unserialize(stripslashes($row['og']));
	$titel = isset($og['title']) ? $og['title'] : substr($row['clean_url'],26);
	$description = isset($og['description']) ? $og['description'] : 'Een mysterieus artikel';
	$auth_res = mysql_query('select * from meta_artikel left join meta on meta.ID = meta_artikel.meta_id where meta_artikel.art_id = ' .$row['ID']. ' and type = "article:author"');
	$author = mysql_fetch_array($auth_res);
	$section_res = mysql_query('select * from meta_artikel left join meta on meta.ID = meta_artikel.meta_id where meta_artikel.art_id = ' .$row['ID']. ' and type = "article:section"');
	$section = mysql_fetch_array($section_res);
	$section_value = isset($section['waarde']) ? $section['waarde'] : '';
?>
		<item>
			<title><?php echo $titel;?></title>
			<link><?php echo str_replace('&', '&amp;', $row['share_url'])?></link>
			<description><?php echo $description ?></description>
			<dc:creator><?php echo $author['waarde'];?></dc:creator>
			<guid><?php echo $row['clean_url'];?></guid>
			<category><?php echo $section_value;?></category>
			<pubDate><?php echo date('r', strtotime($row['created_at']));?></pubDate>
		</item>
<?php } ?>
	</channel>
</rss>