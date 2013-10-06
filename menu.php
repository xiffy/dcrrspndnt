<ul id="topmenu">
	<li>Auteurs
		<ul>
			<?php
$r = mysql_query('select * from meta where meta.type = "article:author" order by waarde');
while($row = mysql_fetch_array($r))
{
	?>
				<li><a href="./meta_art.php?id=<?php echo $row['ID']?>"><?php echo $row['waarde'] ?></a></li>
<?php
}
			?>
		</ul>
	</li>
	<li>Secties
		<ul>
			<?php
$r = mysql_query('select * from meta where meta.type = "article:section" order by waarde');
while($row = mysql_fetch_array($r))
{
	?>
				<li><a href="./meta_art.php?id=<?php echo $row['ID']?>"><?php echo $row['waarde'] ?></a></li>
<?php
}
			?>

		</ul>
	</li>
</ul>