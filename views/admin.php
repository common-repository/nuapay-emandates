<div class="wrap">
	<h2><?php echo NPUtils::i18('NUAPAY Options'); ?></h2>
	<form method="post" action="options.php">
		<?php settings_fields('np_option_group'); ?>
		<?php $options = get_option('np_form_options'); ?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php echo NPUtils::i18('REST URL'); ?></th>
				<td><input type="text" size="100" name="np_form_options[<?php echo NPSettings::REST_URL; ?>]" value="<?php echo $options[NPSettings::REST_URL]; ?>" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php echo NPUtils::i18('EMANDATE WEB URL'); ?></th>
				<td><input type="text" size="100" name="np_form_options[<?php echo NPSettings::EMANDATE_WEB_URL; ?>]" value="<?php echo $options[NPSettings::EMANDATE_WEB_URL]; ?>" /></td>
			</tr>
		</table>
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php echo NPUtils::i18('Save Changes'); ?>" />
		</p>
	</form>
</div>