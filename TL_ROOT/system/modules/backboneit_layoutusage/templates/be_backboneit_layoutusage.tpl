<div id="tl_buttons">
  <a href="<?php echo $this->backLink; ?>" class="header_back" title="<?php echo specialchars($GLOBALS['TL_LANG']['MSC']['backBT']); ?>"><?php echo $GLOBALS['TL_LANG']['MSC']['backBT'] ?></a>
</div>

<div>
<h2 class="sub_headline"><?php echo $this->headline; ?></h2>
<?php echo $this->getMessages(); ?>

<div class="tl_listing_container list_view">
<div onmouseout="Theme.hoverDiv(this, 0);" onmouseover="Theme.hoverDiv(this, 1);" class="tl_header">
<table cellspacing="0" cellpadding="0" summary="Table lists summary of records" class="tl_header_table">
<tbody>
<tr>
	<td><span class="tl_label"><?php echo $GLOBALS['TL_LANG']['tl_layout']['backboneit_layoutusage_wholecount']; ?></span> </td>
	<td><?php echo $this->wholecount; ?></td>
</tr>
<tr>
	<td><span class="tl_label"><?php echo $GLOBALS['TL_LANG']['tl_layout']['backboneit_layoutusage_count']; ?></span> </td>
	<td><?php echo $this->count; ?></td>
</tr>
<?php if($this->fallback): ?>
<tr>
	<td><span class="tl_label"><?php echo $GLOBALS['TL_LANG']['tl_layout']['backboneit_layoutusage_fallback']; ?></span> </td>
	<td><?php echo $this->fallback; ?></td>
</tr>
<?php endif; ?>
</tbody>
</table>
</div>
<table cellspacing="0" cellpadding="0" summary="Table lists records" class="tl_listing" style="margin: 0 !important;">
<tbody>
<?php foreach($this->usages as $arrUsage): ?>
<tr onmouseout="Theme.hoverRow(this, 0);" onmouseover="Theme.hoverRow(this, 1);">
	<td class="tl_file"><a href="<?php echo $arrUsage['treeHref']; ?>" title="<?php echo $arrUsage['treeTitle']; ?>"><?php echo $arrUsage['icon'], ' ', $arrUsage['link']; ?></a></td>
	<td class="tl_file tl_right_nowrap"><?php echo $arrUsage['inheritedText']; ?> <a title="<?php echo $arrUsage['editTitle']; ?>" href="<?php echo $arrUsage['editHref']; ?>"><?php echo $arrUsage['editIcon']; ?></a></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>

</div>