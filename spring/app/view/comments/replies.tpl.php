<div class="reply">
<?php $reply = $content->getProperties() ?>

<?php $pic = get_gravatar($reply['email'], 45) ?>

<div class="quesBox">
<img src="<?=$pic?>"> <a href="<?=$this->url->create('users/user/' . $reply['user'])?>" class="userLink45"><?=$reply['user']?></a>

<?php if(isset($user)) : ?>
<?php $array = array_filter(explode(",", $user['votes'])) ?>

<?php if(in_array($reply['id'] . "up", $array)) : ?>
<div class="voteBoxQuestion">
<a href="<?=$this->url->create('comments/undo-upvote/' . $reply['id'])?>"><i class="fa fa-arrow-up fa-2x upvoted" title="Du har redan röstat upp. Klicka för att ångra"></i></a>
<i class="fa fa-arrow-down fa-2x dead"></i>
<?php elseif(in_array($reply['id'] . "down", $array)) : ?>
<div class="voteBoxQuestion">
<i class="fa fa-arrow-up fa-2x dead"></i>
<a href="<?=$this->url->create('comments/undo-downvote/' . $reply['id'])?>"><i class="fa fa-arrow-down fa-2x downvoted" title="Du har redan röstat ner. Klicka för att ångra"></i></a>
<?php else : ?>
<div class="voteBoxQuestion">
<a href="<?=$this->url->create('comments/upvote/' . $reply['id'])?>"><i class="fa fa-arrow-up fa-2x upReady" title="Click to upvote"></i></a>
<a href="<?=$this->url->create('comments/downvote/' . $reply['id'])?>"><i class="fa fa-arrow-down fa-2x downReady" title="Click to downvote"></i></a>
<?php endif; ?>

<?php if($reply['score'] > 0) : ?>
<span class="scorePos">+<?=$reply['score']?></span>
</div>
<?php elseif($reply['score'] < 0) : ?>
<span class="scoreNeg"><?=$reply['score']?></span>
</div>
<?php else : ?>
<span class="scoreNeut"><?=$reply['score']?></span>
</div>
<?php endif; ?>
<?php else : ?>
<div class="voteBoxQuestion">
<i class="fa fa-arrow-up fa-2x dead" title="You need to be logged in to vote"></i></a>
<i class="fa fa-arrow-down fa-2x dead" title="You need to be logged in to vote"></i></a>
<?php if($reply['score'] > 0) : ?>
<span class="scorePos">+<?=$reply['score']?></span>
</div>
<?php elseif($reply['score'] < 0) : ?>
<span class="scoreNeg"><?=$reply['score']?></span>
</div>
<?php else : ?>
<span class="scoreNeut"><?=$reply['score']?></span>
</div>
<?php endif; ?>
<?php endif; ?>

<span class="smaller right"><?=$reply['timestamp']?></span>
</div>
<?=$reply['texthtml']?>

<?php if($reply['accepted'] == 'yes') : ?>
<p><i class="fa fa-check fa-2x" title="Accepterat svar" style="color:green;"></i></p>
<?php endif; ?>

<?php if(isset($buttons)) : ?>
<p><a href="<?=$this->url->create('comments/add-comment/' . $reply['id'])?>" class="questionReplyButton">Kommentera</a>
<?php if(isset($accept)) : ?>
<a href="<?=$this->url->create('comments/make-accepted/' . $reply['id'])?>" class="questionReplyButton">Gör till accepterat svar</a>
<?php endif; ?>
</p>
<?php endif; ?>

</div>

<?php if(isset($comments)) : ?>
<?php foreach($comments as $key => $value) : ?>

<?php $pic = get_gravatar($value->email, 22) ?>

<div class="replyComment">
<div class="commBox">
<img src="<?=$pic?>"> <a href="<?=$this->url->create('users/user/' . $value->user)?>" class="userLink17"><?=$value->user?></a>

<?php if(isset($user)) : ?>
<?php $array = array_filter(explode(",", $user['votes'])) ?>

<?php if(in_array($value->id . "up", $array)) : ?>
<div class="voteBoxComm">
<a href="<?=$this->url->create('comments/undo-upvote/' . $value->id)?>"><i class="fa fa-arrow-up fa-1x upvoted" title="Du har redan röstat upp. Klicka för att ångra"></i></a>
<i class="fa fa-arrow-down fa-1x dead"></i>
<?php elseif(in_array($value->id . "down", $array)) : ?>
<div class="voteBoxComm">
<i class="fa fa-arrow-up fa-1x dead"></i>
<a href="<?=$this->url->create('comments/undo-downvote/' . $value->id)?>"><i class="fa fa-arrow-down fa-1x downvoted" title="Du har redan röstat ner. Klicka för att ångra"></i></a>
<?php else : ?>
<div class="voteBoxComm">
<a href="<?=$this->url->create('comments/upvote/' . $value->id)?>"><i class="fa fa-arrow-up fa-1x upReady" title="Click to upvote"></i></a>
<a href="<?=$this->url->create('comments/downvote/' . $value->id)?>"><i class="fa fa-arrow-down fa-1x downReady" title="Click to downvote"></i></a>
<?php endif; ?>

<?php if($value->score > 0) : ?>
<span class="commScorePos">+<?=$value->score?></span>
</div>
<?php elseif($value->score < 0) : ?>
<span class="commScoreNeg"><?=$value->score?></span>
</div>
<?php else : ?>
<span class="commScoreNeut"><?=$value->score?></span>
</div>
<?php endif; ?>
<?php else : ?>
<div class="voteBoxComm">
<i class="fa fa-arrow-up fa-1x dead" title="You need to be logged in to vote"></i>
<i class="fa fa-arrow-down fa-1x dead" title="You need to be logged in to vote"></i>
<?php if($value->score > 0) : ?>
<span class="commScorePos">+<?=$value->score?></span>
</div>
<?php elseif($value->score < 0) : ?>
<span class="commScoreNeg"><?=$value->score?></span>
</div>
<?php else : ?>
<span class="commScoreNeut"><?=$value->score?></span>
</div>
<?php endif; ?>
<?php endif; ?>

</div>
<?=$value->texthtml?>
</div>

<?php endforeach; ?>
<?php endif; ?>