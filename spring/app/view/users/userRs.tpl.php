<div class="userReplies">
<h3>Svar:</h3>

<?php foreach($replies as $key => $value) : ?>
<?php $reply = $value->getProperties() ?>
<a href="<?=$this->url->create('comments/reply/' . $reply['replyto'])?>" class="userR">

<i><?=$reply['timestamp']?></i><br>

<?php if(strlen($reply['text']) > 80) : ?>
<?php $text = preg_replace('/\s+?(\S+)?$/', '', substr($reply['text'], 0, 80)) ?>
<?=$text . ".. [Läs hela svaret]"?>
<?php else : ?>
<?=$reply['text']?>
<?php endif; ?>
</a>
<?php endforeach; ?>

</div>