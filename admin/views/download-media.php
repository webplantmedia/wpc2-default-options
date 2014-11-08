<?php
if ( empty( $download ) ) {
	return;
}
?>
<div id="wpc2-download-media" class="postbox">
	<h3>Download Media Files</h3>
		<p>
		<?php foreach ( $download as $row ) : ?>
			<?php if ( 'at_font_face' == $row['key'] ) : ?>
				<?php foreach ( $row['url'] as $font ) : ?>
					<a target="_blank" href="<?php echo $font; ?>"><?php echo $font; ?></a><br />
				<?php endforeach; ?>
			<?php else : ?>
				<a target="_blank" title="<?php echo $row['newname']; ?>" alt="<?php echo $row['newname']; ?>" href="<?php echo $row['url']; ?>"><?php echo $row['newname']; ?></a><br />
			<?php endif; ?>
		<?php endforeach; ?>
		</p>
</div>
