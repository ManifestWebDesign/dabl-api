<?php

/*
 * @param string $url_format The url to format paging links with, ie: /actu/index.html?page=page_num. page_num to be replaced with page number.
 * @param int $page_limit For limiting the number of page number links
 */

if (!isset($url_format)) {
	$args = (array) @$_GET;
	$args['page'] = 'page_num';
	$url_format = '?' . http_build_query($args);
}

if (!isset($page_limit)) {
	$page_limit = 9;
}

$page_limit = intval($page_limit);

$mid_page_limit = $page_limit >> 1;
$page = $pager->getPageNum();
$count = $pager->getPageCount();
$start = max(1, min($count - $page_limit, $page - $mid_page_limit));
$end = min($count, max($page_limit, $page + $mid_page_limit));
?>
<div class="pager ui-helper-clearfix">
	<span class="pager-text">
		<?php echo number_format($pager->getStart()) ?>-<?php echo number_format($pager->getEnd()) ?> / <?php echo number_format($pager->getTotal()) ?>
	</span>
<?php if ($count > 1): ?>
	<span class="pager-buttons button-group">
<?php
if ($page > 1):
	$link = str_replace('page_num', 1, $url_format);
?>
		<a title="First" href="<?php echo $link ?>">&laquo;</a>

<?php $link = str_replace('page_num', $page - 1, $url_format) ?>

		<a title="Previous" href="<?php echo $link ?>">&lsaquo;</a>

<?php endif; ?>

<?php for ($i = $start; $i <= $end; ++$i): ?>
	<?php if ($i == $page): ?>

		<span class="pager-current"><?php echo $i ?></span>

	<?php else: ?>
		<?php $link = str_replace('page_num', $i, $url_format); ?>

		<a href="<?php echo $link ?>"><?php echo $i ?></a>

	<?php endif ?>
<?php endfor; ?>

<?php if ($page < $count): ?>
	<?php $link = str_replace('page_num', $page + 1, $url_format); ?>

		<a title="Next" href="<?php echo $link ?>">&rsaquo;</a>

		<?php $link = str_replace('page_num', $count, $url_format); ?>
		<a title="Last" href="<?php echo $link ?>">&raquo;</a>

<?php endif ?>
	</span>
<?php endif?>
</div>