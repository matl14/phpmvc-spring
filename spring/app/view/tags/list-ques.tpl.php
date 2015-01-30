<div class="allComments">
<h1>Frågor kopplade till taggen: <?=$tag?></h1>

<?php if(isset($message)) : ?>
<?=$message?>
<?php endif; ?>


<?php foreach($content as $comments) : ?>

<?php if (is_array($comments)) : ?>

<div class="oneQinList">
<div class='questionTitle'>
<h3><a href="<?=$this->url->create('comments/question/' . $comments['id'])?>"><?=$comments['title']?></a></h3>
</div>

<?php if(isset($comments['tags'])) : ?>
<div class="tagBox">
<p>Taggar: 

<?php $tagArray = explode(",", $comments['tags']) ?>
<?php foreach($tagArray as $value) : ?>
<a href="<?=$this->url->create('tags/tag-by-slug/' . slugify($value))?>" class="tagQues"><?=$value?></a>
<?php endforeach; ?>

</p>
</div>
<?php endif; ?>

<div class="userBox">
<?php $pic = get_gravatar($comments['email'], 16) ?>
<p><a href="<?=$this->url->create('users/user/' . $comments['user'])?>" class="questionReplyButtonDark"><img src="<?=$pic?>"> <?=$comments['user']?></a></p>
</div>


<?php if(isset($buttons)) : ?>
<div class="replyButtons">
<p>
<a href="<?=$this->url->create('comments/add-reply/' . $comments['id'])?>" class="questionReplyButton">Svara</a>
<a href="<?=$this->url->create('comments/add-comment/' . $comments['id'])?>" class="questionReplyButton">Kommentera</a>
</p>
</div>
<?php endif; ?>

</div>
<?php endif; ?>
<?php endforeach; ?>
</div>