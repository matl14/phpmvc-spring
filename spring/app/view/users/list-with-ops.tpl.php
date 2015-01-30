<h1>Users</h1>

<table class="oneUserOptions">
<tr>
<th><a href="<?=$this->url->create('users')?>"><i class="fa fa-list fa-2x"></i></a></th>
<th><a href="<?=$this->url->create('users/add')?>"><i class="fa fa-plus-square fa-2x"></i></a></th>
<th><a href="<?=$this->url->create('users/active-list')?>"><i class="fa fa-list-ul fa-2x"></i></a></th>
<th><a href="<?=$this->url->create('users/deleted-list')?>"><i class="fa fa-trash fa-2x"></i></a></th>
<th><a href="<?=$this->url->create('users/inactive-list')?>"><i class="fa fa-list-alt fa-2x"></i></a></th>
<th><a href="<?=$this->url->create('setup')?>"><i class="fa fa-file fa-2x"></i></a></th>
</tr>

<tr>
<td>Alla users</td>
<td>Skapa user</td>
<td>Aktiva users</td>
<td>Papperskorgen</td>
<td>Inaktiva users</td>
<td>Återställ databas</td>
</tr>
</table>

<h2><?=$title?></h2>
 
<table id="userlist">
<th>Id</th>
<th>Användarnamn</th>
<th>E-mail</th>
<th>Namn</th>
<th>Alternativ</th>
<th>Info</th>
<?php foreach ($users as $user) : ?>
<?php $prop = $user->getProperties() ?>

<tr>
<td>#<?=$prop['id']?></a></td>
<td><?=$prop['acronym']?></td>
<td><?=$prop['email']?></td>
<td><?=$prop['name']?></td>

<?php if($title == 'Papperskorgen') : ?>
<td><a href="<?=$this->url->create("users/id/" . $prop['id'] . "")?>" title="Inställningar"><i class="fa fa-user fa-2x"></i></a> <a href="<?=$this->url->create("users/delete/" . $prop['id'] . "")?>" title="Hård delete"><i class="fa fa-close fa-2x"></i></a></td>
<?php else : ?>
<td><a href="<?=$this->url->create("users/id/" . $prop['id'] . "")?>" title="Inställningar"><i class="fa fa-user fa-2x"></i></a></td>
<?php endif; ?>

<?php if($prop['deleted'] !== null) : ?>
<td>Raderad</td>
<?php elseif($prop['deleted'] == null && $prop['active'] == null) : ?>
<td>Inaktiv</td>
<?php elseif($prop['deleted'] == null && $prop['active'] !== null) : ?>
<td>Aktiv</td>
<?php else : ?>
<td>Ingen info</td>
<?php endif; ?>
</tr>

 
<?php endforeach; ?>
</table>