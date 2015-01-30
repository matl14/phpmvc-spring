<div class="userQuestions">
<h3>Frågor:</h3>

<?php foreach($questions as $key => $value) : ?>
<?php $question = $value->getProperties() ?>
<a href="<?=$this->url->create('comments/question/' . $question['id'])?>" class="userQ">

<i><?=$question['timestamp']?></i><br>
<?=$question['title']?></p>
</a>
<?php endforeach; ?>

</div>