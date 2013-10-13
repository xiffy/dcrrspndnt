<?php
// functions used in more than one file
function pager($tot_row)
{
	?>
		<ul id="pager">
			<li class="text">pagina:</li>
	<?php
	// how many pages?
	$pages = ceil($tot_row / ITEMS_PER_PAGE);
	$i = 0;
	while ($i < $pages)
	{
		$page = $i + 1;
		echo '			<li><a href="./?page='.$page.$qsa.'">'.$page.'</a></li>';
		$i++;
	}
	?>
		</ul>
<?php
}
?>