<?php
// functions used in more than one file
function pager($tot_row)
{
	// how many pages?
	$pages = ceil($tot_row / ITEMS_PER_PAGE);
	$i = 0;
	if ($pages > 1)
	{
?>
		<ul id="pager">
			<li class="text">pagina:</li>
<?php
		while ($i < $pages)
		{
			$page = $i + 1;
			echo '			<li><a href="./?page='.$page.$qsa.'">'.$page.'</a></li>';
			$i++;
		}
?>
			<li class="text">(tot: <?php echo $tot_row;?>)</li>
		</ul>
<?php
	}
}
?>