<ul id="topmenu">
	<li><strong> Artikelen</strong> &#9660;
		<ul>
			<li><a href="./"><div> Alle artikelen</div></a></li>
			<li><a href="./top.php"><div> Populiare artikelen</div></a></li>
			<li><a href="./top.php?mode=week"><div> Populiare artikelen (deze week)</div></a></li>
			<li><a href="./top.php?mode=day"><div> Populiare artikelen (vandaag)</div></a></li>
			<li><a href="./top.php?mode=hour"><div> Populiare artikelen (dit uur)</div></a></li>
			<li><a href="./charts.php"><div> Grafiekje </div></a></li>
		</ul>
	</li>
	<li><strong> Auteurs</strong> &#9660;
		<ul>
			<?php
$r = mysql_query('select * from meta where meta.type = "article:author" order by waarde');
while($row = mysql_fetch_array($r))
{
?>
				<li><a href="./meta_art.php?id=<?php echo $row['ID']?>"><div><?php echo $row['waarde'] ?></div></a></li>
<?php
}
?>
		</ul>
	</li>
	<li><strong> Secties</strong> &#9660;
		<ul>
<?php
$r = mysql_query('select * from meta where meta.type = "article:section" order by waarde');
while($row = mysql_fetch_array($r))
{
?>
				<li><a href="./meta_art.php?id=<?php echo $row['ID']?>"><div><?php echo $row['waarde'] ?></div></a></li>
<?php
}
?>
		</ul>
	</li>
	<li class="small">
		<?php
		if(isset($meta_id))
		{ ?>
		<a href="./rss.php?id=<?php echo $meta_id;?>" title="RSS Feed"><strong>RSS </strong><img src="img/ikoon.rss.png" /></a>
		<?php
		}
		else
		{ ?>
		<a href="./rss.php" title="RSS Feed"><strong>RSS </strong><img src="img/ikoon.rss.png" /></a>
		<?php
		}
		?>
	</li>
</ul>