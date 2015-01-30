<h1><?=$title?></h1>

<?php if(isset($tags)) : ?>
<p class="tagList">
<?php foreach($tags as $value) : ?>

<a href="<?=$this->url->create('tags/tag-by-slug/' . $value['slug'])?>" class="tagAll"><?=$value['name']?></a>

<?php endforeach; ?>
</p>
<?php endif; ?>