<?php $one = $user->getProperties() ?>

<?php $pic = get_gravatar($one['email'], 71) ?>

<h1><img src="<?=$pic?>"> <span class="userName"><?=$one['acronym']?></span></h1>

<table class="oneUserOptions">
<tr>
<th><a href="<?=$this->url->create("users")?>"><i class='fa fa-long-arrow-left fa-2x'></i></a></th>
</tr>

<tr>
<td>Tillbaka till användare</td>
</tr>
</table>

<div class="oneUser">
<p><b>Användarnamn:</b> <?=$one['acronym']?></p>
<p><b>E-mail:</b> <?=$one['email']?></p>
<p><b>Namn:</b> <?=$one['name']?></p>
<p><b>Skapad:</b> <?=$one['created']?></p>
<p><b>Senaste inloggning:</b> <?=$one['active']?></p>
</div>