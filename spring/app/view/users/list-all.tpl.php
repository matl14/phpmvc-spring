<h1><?=$title?></h1>
 
<table id="userlist">
<th>Användarnamn</th>
<th>Gravatar</th>
<th>E-mail</th>
<th>Namn</th>
<th>Profil</th>
<?php foreach ($users as $user) : ?>
<?php $prop = $user->getProperties() ?>

<?php $pic = get_gravatar($prop['email'], 30) ?>

<tr>
<td><?=$prop['acronym']?></td>
<td><img src="<?=$pic?>"></td>
<td><?=$prop['email']?></td>
<td><?=$prop['name']?></td>


<td><a href="<?=$this->url->create("users/user/" . $prop['acronym'] . "")?>" title="Profil"><i class="fa fa-user fa-2x"></i></a></td>

</tr>

 
<?php endforeach; ?>
</table>