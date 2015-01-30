<h2><?=$title?></h2>
<?php if (isset($text)) : ?>
<?=$text?>
<?php endif; ?>

<?=$content?>

<?php if (isset($links)) : ?>
<ul>
<?php foreach ($links as $link) : ?>
<li><a href="<?=$link['href']?>"><?=$link['text']?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
