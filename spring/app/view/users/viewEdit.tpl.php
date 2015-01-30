<?php $one = $user->getProperties() ?>

<?php $pic = get_gravatar($one['email'], 71) ?>

<h1><img src="<?=$pic?>"> <span class="userName">Min profil</span></h1>
<table class="oneUserOptions">
<tr>
<th><a href="<?=$this->url->create("users")?>"><i class='fa fa-long-arrow-left fa-2x'></i></a></th>
<th>
<a href="<?=$this->url->create("users/delete/" . $one['id'] . "")?>"><i class="fa fa-trash fa-2x"></i></a>
</th>
<th><a href="<?=$this->url->create("users/edit/" . $one['id'] . "")?>"><i class="fa fa-cog fa-2x"></i></a></th>
<th><a href="<?=$this->url->create("users/changepw/" . $one['id'] . "")?>"><i class="fa fa-lock fa-2x"></i></a></th>



</tr>

<tr>
<td>Tillbaka till användare</td>
<td>Ta bort konto</td>
<td>Inställningar</td>
<td>Ändra lösenord</td>
</tr>
</table>

<div class="oneUser">
<p><b>Id:</b> <?=$one['id']?></p>
<p><b>Användarnamn:</b> <?=$one['acronym']?></p>
<p><b>E-mail:</b> <?=$one['email']?></p>
<p><b>Namn:</b> <?=$one['name']?></p>
<p><b>Skapad:</b> <?=$one['created']?></p>
<p><b>Senast uppdaterad:</b> <?=$one['updated']?></p>
<p><b>Senast inloggad:</b> <?=$one['active']?></p>
</div>