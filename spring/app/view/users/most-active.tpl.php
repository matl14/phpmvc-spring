<a href="users/user/<?=$content['acronym']?>" class="frontUserBox">
<?php $pic = get_gravatar($content['email'], 100) ?>
<img src="<?=$pic?>">
<br>
<span class="insideFrontUserBox"><?=$content['acronym']?></span>
</a>