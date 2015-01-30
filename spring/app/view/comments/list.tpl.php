<div class="allComments">
<h1>Frågor
<?php if(isset($link)) : ?>
<a href='<?=$this->url->create('comments/add-question')?>' class='questionBox right'>Ställ en fråga</a>
<?php endif; ?>
</h1>

<?php if(isset($message)) : ?>
<?=$message?>
<?php endif; ?>


<?php foreach($content as $comments) : ?>
<?php $comments = get_object_vars($comments) ?>

<div class="oneQinList">
<?php if (is_array($comments)) : ?>
<div class='questionTitle'>
<h3><a href="<?=$this->url->create('comments/question/' . $comments['id'])?>"><?=$comments['title']?></a>
</h3>
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


<div class="replyButtons">
<p>
<span class="replyCount" title="Frågan har <?=$replyCount[$comments['id']]?> svar"><?=$replyCount[$comments['id']]?> svar</span></span>
<?php if($comments['score'] > 0) : ?>
<span class="listScorePos" title="Frågan har positivt score">+<?=$comments['score']?></span>
<?php elseif($comments['score'] < 0) : ?>
<span class="listScoreNeg" title="Frågan har negativt score"><?=$comments['score']?></span>
<?php else : ?>
<span class="listScoreNeut" title="Frågan har neutralt score"><?=$comments['score']?></span>
<?php endif; ?>
</span>


<?php if(isset($replyButton)) : ?>
<a href="<?=$this->url->create('comments/add-reply/' . $comments['id'])?>" class="questionReplyButton">Svara</a>
<a href="<?=$this->url->create('comments/add-comment/' . $comments['id'])?>" class="questionReplyButton">Kommentera</a>
<?php endif; ?>
</p>
</div>

</div>
<?php endif; ?>
<?php endforeach; ?>
</div>