<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? foreach ($settings as $i => $setting) : ?>
<tr class="sortable">
	<td class="handle"></td>
	<?= @ninja('grid.count', array('total' => $total)) ?>
	<td class="grid-check"><?= @helper('grid.checkbox', array('row' => $setting)) ?></td>
	<td width="50%"><?= @ninja('grid.edit', array('row' => $setting)) ?></td>
	<td><?= $setting->theme ?></td>
	<td class="grid-center">
	<? if ($setting->default) : ?>
		<img src="<?= @$img('/16/star.png') ?>" alt="<?= @text('COM_NINJABOARD_DEFAULT') ?>" />
	<? else : ?>
		<img src="<?= @$img('/16/star_off.png') ?>" alt="<?= @text('COM_NINJABOARD_DEFAULT') ?>" />
	<? endif ?>
	</td>
	<td class="grid-center"><?= @ninja('grid.toggle', array('enabled' => $setting->enabled)) ?></td>
</tr>
<? endforeach ?>
<?= @ninja('grid.placeholders', array('total' => $total, 'colspan' => 7)) ?>