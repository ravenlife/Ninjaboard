<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<div class="ninjaboard-block category topics">
	<ul class="topiclist">
		<li class="header">
			<dl class="icon">
				<dt></dt>
				<dd class="topics"><?= @text('Replies') ?></dd>
				<dd class="posts"><?= @text('Views') ?></dd>
				<dd class="lastpost"><span><?= @text('Last post') ?></span></dd>
			</dl>
		</li>
	</ul>
	<ul class="topiclist forums stickies">
	<? foreach (@$stickies as $sticky) : ?>
		<?= @template('com://site/ninjaboard.view.topic.row_topic', array('topic' => $sticky)) ?>
	<? endforeach ?>
	</ul>
	<ul class="topiclist forums">
	<? foreach (@$topics as $topic) : ?>
		<?= @template('com://site/ninjaboard.view.topic.row_topic', array('topic' => $topic)) ?>
	<? endforeach ?>
	</ul>
</div>