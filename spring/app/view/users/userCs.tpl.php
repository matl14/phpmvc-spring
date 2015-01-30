<div class="userComments">
<h3>Kommentarer:</h3>

<?php foreach($comments as $key => $value) : ?>
<?php $comment = $value->getProperties() ?>
<a href="<?=$this->url->create('comments/reply/' . $comment['commenton'])?>" class="userC">

<i><?=$comment['timestamp']?></i><br>

<?php if(strlen($comment['text']) > 80) : ?>
<?php $text = preg_replace('/\s+?(\S+)?$/', '', substr($comment['text'], 0, 80)) ?>
<?=$text . ".. [Läs hela kommentaren]"?>
<?php else : ?>
<?=$comment['text']?>
<?php endif; ?>

</a>
<?php endforeach; ?>


</div>