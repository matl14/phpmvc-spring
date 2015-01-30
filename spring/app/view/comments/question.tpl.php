<div class="question">
<?php $question = $content->getProperties() ?>
<?php $pic = get_gravatar($question['email'], 45) ?>
<h1><?=$question['title']?></h1>

<div class="quesBox">
<img src="<?=$pic?>">  <a href="<?=$this->url->create('users/user/' . $question['user'])?>" class="userLink45"><?=$question['user']?></a>

<?php if(isset($user)) : ?>
<?php $array = array_filter(explode(",", $user['votes'])) ?>

<?php if(in_array($question['id'] . "up", $array)) : ?>
<div class="voteBoxQuestion">
<a href="<?=$this->url->create('comments/undo-upvote/' . $question['id'])?>"><i class="fa fa-arrow-up fa-2x upvoted" title="Du har redan röstat upp. Klicka för att ångra"></i></a>
<i class="fa fa-arrow-down fa-2x dead"></i>
<?php elseif(in_array($question['id'] . "down", $array)) : ?>
<div class="voteBoxQuestion">
<i class="fa fa-arrow-up fa-2x dead"></i>
<a href="<?=$this->url->create('comments/undo-downvote/' . $question['id'])?>"><i class="fa fa-arrow-down fa-2x downvoted" title="Du har redan röstat ner. Klicka för att ångra"></i></a>
<?php else : ?>
<div class="voteBoxQuestion">
<a href="<?=$this->url->create('comments/upvote/' . $question['id'])?>"><i class="fa fa-arrow-up fa-2x upReady" title="Click to upvote"></i></a>
<a href="<?=$this->url->create('comments/downvote/' . $question['id'])?>"><i class="fa fa-arrow-down fa-2x downReady" title="Click to downvote"></i></a>
<?php endif; ?>

<?php if($question['score'] > 0) : ?>
<span class="scorePos">+<?=$question['score']?></span>
</div>
<?php elseif($question['score'] < 0) : ?>
<span class="scoreNeg"><?=$question['score']?></span>
</div>
<?php else : ?>
<span class="scoreNeut"><?=$question['score']?></span>
</div>
<?php endif; ?>
<?php else : ?>
<div class="voteBoxQuestion">
<i class="fa fa-arrow-up fa-2x dead" title="You need to be logged in to vote"></i></a>
<i class="fa fa-arrow-down fa-2x dead" title="You need to be logged in to vote"></i></a>
<?php if($question['score'] > 0) : ?>
<span class="scorePos">+<?=$question['score']?></span>
</div>
<?php elseif($question['score'] < 0) : ?>
<span class="scoreNeg"><?=$question['score']?></span>
</div>
<?php else : ?>
<span class="scoreNeut"><?=$question['score']?></span>
</div>
<?php endif; ?>
<?php endif; ?>

<span class='right smaller'><?=$question['timestamp']?></span>
</div>

<?=$question['texthtml']?>

<p class="smaller"><b>Namn:</b> <?=$question['name']?><br>
<b>Email:</b> <?=$question['email']?></p>

<?php if(isset($buttons)) : ?>
<p><a href="<?=$this->url->create('comments/add-reply/' . $question['id'])?>" class="questionReplyButton">Svara</a>
<a href="<?=$this->url->create('comments/add-comment/' . $question['id'])?>" class="questionReplyButton">Kommentera</a></p>
<?php endif; ?>

<?php if(isset($tags)) : ?>
<p class="tagListQues">Taggar:
<?php foreach($tags as $key => $value) : ?>
<?php foreach($value as $key2 => $value2) : ?>
<?php $tag = get_object_vars($value2) ?>
<a href="<?=$this->url->create('tags/tag-by-slug/' . $tag['slug'])?>" class="tagQues"><?=$tag['name']?></a>

<?php endforeach; ?>
<?php endforeach; ?>
<?php endif; ?>

</div>

<?php if(isset($comments)) : ?>
<?php foreach($comments as $key => $value) : ?>
<?php $pic22 = get_gravatar($value->email, 22) ?>

<div class="comment">
<div class="commBox">
<img src="<?=$pic22?>"> <a href="<?=$this->url->create('users/user/' . $value->user)?>" class="userLink17"><?=$value->user?></a>

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
<?php if($order == true) : ?>
<div class="orderBox">
<p>Sortera svar efter:
<a href="<?=$question['id']?>?order=score">score</a>
 | <a href="<?=$question['id']?>?order=date">datum</a>
</p>
</div>
<?php endif; ?>