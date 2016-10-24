<?php
// functions used in more than one file
function pager($tot_row, $qsa)
{
	$query = $_SERVER['PHP_SELF'];
	$path = pathinfo( $query );

	// how many pages?
	$pages = ceil($tot_row / ITEMS_PER_PAGE);
	if($pages > 35) {
		fragmented_pager($pages, $qsa);
		return true;
	}

	$i = 0;
	if ($pages > 1)
	{
?>
		<ul id="pager">
			<li class="text">pagina: </li>
<?php
		while ($i < $pages)
		{
			$page = $i + 1;
			echo '			<li><a href="./'.$path['basename'].'?page='.$page.$qsa.'">'.$page.'</a></li>';
			$i++;
		}
?>
			<li class="text">(tot: <?php echo $tot_row;?>)</li>
		</ul>
<?php
	}
}



function fragmented_pager($pages, $qsa) {
	$query = $_SERVER['PHP_SELF'];
	$path = pathinfo( $query );
	$adjacents = 8;
	$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

	$i = 0;
?>
		<ul id="pager">
			<li class="text">pagina: </li>
<?php
		while ($i < $pages)
		{
			$page = $i + 1;

			$is_current = $page == $current_page ? 'class="current"' : '';

			// at the front, hide tha back
			if($current_page < 1 + $adjacents * 2)
			{
				if ($page < 4 + $adjacents * 2) {
					echo "			<li $is_current><a href=\"./{$path['basename']}?page={$page}{$qsa}\">{$page}</a></li>\n";
				}
				else {
					// print the end and leave
					echo '			<li>...</li>';
					echo '			<li><a href="./'.$path['basename'].'?page='.$pages.$qsa.'">'.$pages.'</a></li>';
					break;
				}
			}
			// in the middle hide some-some
			elseif ($pages - $adjacents * 2 > $current_page && $current_page > $adjacents * 2) {
				if ($page == 1 || $page == 2) {
					echo '			<li><a href="./'.$path['basename'].'?page='.$page.$qsa.'">'.$page.'</a></li>';
				}
				elseif ($page == 3) {
					echo '			<li>...</li>';
				}
				elseif($page >= $current_page - $adjacents && $page <= $current_page + $adjacents) {
					echo "			<li $is_current><a href=\"./{$path['basename']}?page={$page}{$qsa}\">{$page}</a></li>\n";

				}
				elseif ($page > $current_page + $adjacents) {
					// print the end and leave
					echo '			<li>...</li>';
					echo '			<li><a href="./'.$path['basename'].'?page='.$pages.$qsa.'">'.$pages.'</a></li>';
					break;
				}
			}
			// in the end, hide the front
			elseif ($current_page >= $pages - 2 * $adjacents) {
				if ($page == 1 || $page == 2) {
					echo '			<li><a href="./'.$path['basename'].'?page='.$page.$qsa.'">'.$page.'</a></li>';
				}
				elseif ($page == 3) {
					echo '			<li>...</li>';
				}
				elseif($page >= $pages - 2 * ($adjacents + 1) ) {
					echo "			<li $is_current><a href=\"./{$path['basename']}?page={$page}{$qsa}\">{$page}</a></li>\n";
				}
			}
			$i++;
		}

?>

		</ul>
<?php

}