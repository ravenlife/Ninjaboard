<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<div class="ninjaboard-block category">
	<ul class="topiclist">
		<li class="header">
			<dl class="icon">
				<dt>
					<? if(KRequest::get('get.view', 'cmd') == 'forums' && $params['view_settings']['forums_title'] == 'permalink' && isset($forum)) : ?>
						<a href="<?= @route('view=forum&id='.$forum->id) ?>">[<?= @text('COM_NINJABOARD_PERMALINK') ?>]</a>
					<? endif ?>
				</dt>
				<dd class="topics"><?= @text('COM_NINJABOARD_TOPICS') ?></dd>
				<dd class="posts"><?= @text('COM_NINJABOARD_POSTS') ?></dd>
				<dd class="lastpost"><span><?= @text('COM_NINJABOARD_LAST_POST') ?></span></dd>
			</dl>
		</li>
	</ul>
	<ul class="topiclist forums">
		<?= @template('com://site/ninjaboard.view.forum.row_forum') ?>
	</ul>
</div>