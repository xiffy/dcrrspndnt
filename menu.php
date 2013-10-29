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
$r = mysql_query('select count(artikelen.id) as aantal_art, meta.*
from meta
join meta_artikel on meta.id = meta_id
left join artikelen on artikelen.id = art_id
where meta.type = "article:author"
group by meta.id
order by count(artikelen.id) desc, waarde
limit 0,25');
while($row = mysql_fetch_array($r))
{
?>
				<li><a href="./meta_art.php?id=<?php echo $row['ID']?>"><div><?php echo $row['waarde'] ?> (<?php echo $row['aantal_art'] ?>)</div></a></li>
<?php
}
?>
		</ul>
	</li>
	<li><strong> Secties</strong> &#9660;
		<ul>
<?php
$r = mysql_query('select count(artikelen.id) as aantal_art, meta.*
from meta
join meta_artikel on meta.id = meta_id
left join artikelen on artikelen.id = art_id
where meta.type = "article:section"
group by meta.id
order by count(artikelen.id) desc, waarde
limit 0,25');
while($row = mysql_fetch_array($r))
{
?>
				<li><a href="./meta_art.php?id=<?php echo $row['ID']?>"><div><?php echo $row['waarde'] ?> (<?php echo $row['aantal_art'] ?>)</div></a></li>
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
