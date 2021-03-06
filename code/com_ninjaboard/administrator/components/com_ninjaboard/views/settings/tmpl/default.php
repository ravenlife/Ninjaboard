<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<?= @template('ninja:view.grid.head') ?>

<? if($length > 0) : ?>
	<?= @template('ninja:view.search.filter_search_enabled') ?>
<? endif ?>

<?= @ninja('behavior.sortable') ?>
<form action="<?= @route() ?>" method="post" id="<?= @id() ?>" class="placeholder-up-two-lines -koowa-grid">
	<?= @$placeholder('settings', null, 'Add %s', 'COM_NINJABOARD_YOU_HAVENT_ADDED_ANY_SETTINGS_YET') ?>
	<table class="adminlist ninja-list">
		<thead>
			<tr>
				<th class="hasHint" title="<?= @text('COM_NINJABOARD_DRAG_HERE_TO_REORDER') ?>"></th>
				<?= @ninja('grid.count', array('total' => @$total, 'title' => true)) ?>
				<th class="grid-check"><?= @helper('grid.checkall') ?></th>
				<th><?= @helper('grid.sort', array('column' => 'title')) ?></th>
				<th><?= @text('COM_NINJABOARD_THEME') ?></th>
				<th width="1px" class="grid-center">
					<?= @helper('grid.sort', array('column' => 'default')) ?>
				</th>																
				<th width="1px" class="grid-center"><?= @helper('grid.sort', array('title' => 'Enabled', 'column' => 'published')) ?></th>					
			</tr>
		</thead>
		<?= @ninja('paginator.tfoot', array('total' => $total, 'colspan' => 8)) ?>
		<tbody class="sortable">
			<?= @template('default_items') ?>
		</tbody>
	</table>
</form>