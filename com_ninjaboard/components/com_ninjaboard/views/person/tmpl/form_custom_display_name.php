<? /** $Id: form_custom_display_name.php 2446 2011-09-08 13:27:54Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<script type="text/javascript">
window.addEvent('domready', function(){
	document.id('use_alias').addEvent('click', function(){
	    //@TODO .select() might not work as it does in jQuery
		document.id('alias').select();
	});
	document.id('alias').addEvent('focus', function(){
		if(!document.id('use_alias').checked) document.id('use_alias').set('checked', true);
	});
});
</script>
<link rel="stylesheet" href="/site.person.form_custom_display_name.css" />

<? $which_name = $person->which_name ? $person->which_name : $params->view_settings->display_name ?>

<div class="element which_name-username">
	<div class="key">
		<input type="radio" name="which_name" <? if($which_name == 'username') echo 'checked' ?> id="username" value="username" />
	</div>
	<label for="username">
		<?= sprintf(@text('Replace my screen name with my username (%s)'), $person->username) ?>
	</label>
</div>
<div class="element which_name-name">
	<div class="key">
		<input type="radio" name="which_name" <? if($which_name == 'name') echo 'checked' ?> id="name" value="name" />
	</div>
	<label for="name">
		<?= sprintf(@text('Replace my screen name with my real name (%s)'), $person->name) ?>
	</label>
</div>
<div class="element which_name-alias">
	<div class="key">
		<input type="radio" name="which_name" <? if($which_name == 'alias') echo 'checked' ?> id="use_alias" value="alias" />
	</div>
	<label for="alias">
		<?= @text('Replace my screen name with') ?> 
	</label>
	<input type="text" name="alias" id="alias" placeholder="<?= @escape(@text('what I type in here…')) ?>" class="value" value="<?= @escape($person->alias) ?>" />
</div>