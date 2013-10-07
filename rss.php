<?php
header('Content-Type: text/xml');
echo '<?xml';
echo ' version="1.0"?>'; ?>

<rss version="2.0">
	<channel>
		<title>de Correspondent - gedeelde artikelen</title>
		<link>http://molecule.nl/decorrespondent/</link>
		<description>de Correspondent geeft de mogelijkheid betaalde artikelen te delen, dcrrspndnt zoekt deze links op twitter en slaat deze op, en geeft deze als overzicht terug op http://molecule.nl/decorrespondent/</description>
		<language>NL-nl</language>
		<pubDate></pubDate>
		<lastBuildDate></lastBuildDate>
		<docs>http://blogs.law.harvard.edu/tech/rss</docs>
		<generator>dcrrspndnt</generator>
<?php
require_once('settings.local.php');
include('db.php');
$query = 'select * from artikelen order by created_at desc limit 0,50';
if (isset($_GET['id']))
{
	$meta_id = (int) $_GET['id'];
	$query = 'select artikelen.* from artikelen join meta_artikel on artikelen.ID = meta_artikel.art_id where meta_artikel.meta_id = '.$meta_id.' order by created_at desc limit 0,50';
}
$i = 0;
$res = mysql_query($query);
while($row = mysql_fetch_array($res) )
{
	$og = unserialize(stripslashes($row['og']));
	$titel = isset($og['title']) ? $og['title'] : substr($row['clean_url'],26);
	$description = isset($og['description']) ? $og['description'] : 'Een mysterieus artikel';
?>
		<item>
			<title><?php echo $titel;?></title>
			<link><?php echo $row['share_url']?></link>
			<description><?php echo $description ?></description>
			<guid><?php echo $row['clean_url'];?></guid>
			<pubDate><?php $row['created_at'];?></pubDate>
		</item>
<?php } ?>
	</channel>
</rss>