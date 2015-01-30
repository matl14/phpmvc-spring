<div class="latestBox">
<div class="titleBlock"><h3><a href="comments/question/<?=$content['id']?>"><?=$content['title']?></a></h3></div>

<?php if($buttons) : ?>
<p class="textLeft">
<a href="comments/add-reply/<?=$content['id']?>" class="questionReplyButton">Svara</a>
<a href="comments/add-comment/<?=$content['id']?>" class="questionReplyButton">Kommentera</a>
</p>
<?php endif; ?>

<?php $pic = get_gravatar($content['email'], 15) ?>

<p class="textLeft">
<a href="users/user/<?=$content['user']?>" class="questionReplyButtonDark"><img src="<?=$pic?>"> <?=$content['user']?></a>
<span class="smaller"><?=substr($content['timestamp'], 0, -5)?></span>
</p>
</div>