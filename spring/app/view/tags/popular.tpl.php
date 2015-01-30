<h3><?=$title?></h3>

<?php foreach($tags as $tag) : ?>

<a href="tags/tag-by-slug/<?=$tag['slug']?>" class="oneTag"><?=$tag['name']?></a>

<?php endforeach; ?>