<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<?= @template('com://site/ninjaboard.view.default.head') ?>

<? $img = isset(@$topic->params['customization']['icon']) ? @$topic->params['customization']['icon'] : '32__default.png' ?>

<style type="text/css">
	.button.spinning .symbol {
		display: inline-block;
		width: 16px;
		height: 16px;
		text-align: center;
		background: transparent center no-repeat; 
	}
	.button.spinning .symbol {
		background-color: white;
		color: transparent;
		background-image: url(<?= @$img('/16/spinner.gif') ?>);
		border-radius: 2px;
		-webkit-border-radius: 2px;
		-moz-border-radius: 2px;
		-o-border-radius: 2px;
	}
	#ninjaboard .title a {
		display: inline-block;
		background-image: url(<?= @$img('/topic/' . $img) ?>); 
		background-repeat: no-repeat;
		padding-left: 37px;
		min-height: 32px;
		line-height: 1.3em;
	}
</style>

<script type="text/javascript">
	ninja(function($){
		$('.<?= @id('delete') ?>').click(function(event){
			event.preventDefault();

			if(!confirm(<?= json_encode(@text('COM_NINJABOARD_ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_TOPIC_THIS_ACTION_CANNOT_BE_UNDONE')) ?>)) return;

			$(this).closest('form').submit();
		});
	});
</script>

<div id="ninjaboard" class="ninjaboard topic <?= $topic->params['pageclass_sfx'] ?> <?= $forum->params['style']['type'] ?> <?= $forum->params['style']['border'] ?> <?= $forum->params['style']['separators'] ?>">
	<h1 class="title"><a href="<?= @route('id=' . $topic->id) ?>" class="topic"><?= @escape($topic->subject) ?></a></h1>

	<? if($topic->post_permissions > 0) : ?>
	<div class="header">
		<?/*= @render(@template('default_toolbar'), false, $forum->params['module'])*/ ?>
		<?= @template('default_toolbar') ?>
		<div class="clearfix"></div>
	</div>
	<div class="body">
		<?= $posts ?>
	</div>
	<? if($forum->params['view_settings']['topic_layout'] == 'classic') : ?>
		<div class="clearfix"></div>
		<div class="footer">
			<?/*= @render(@template('default_toolbar'), false, $forum->params['module'])*/ ?>
			<?= @template('default_toolbar') ?>
		</div>
	<? endif ?>
	<? endif ?>

	<? if(!JFactory::getUser()->guest && $topic->post_permissions > 1) : ?>
	<?= @service('com://site/ninjaboard.controller.post')->view('post')->layout('quick')->topic($topic->id)->display() ?>
	<? endif ?>
</div>