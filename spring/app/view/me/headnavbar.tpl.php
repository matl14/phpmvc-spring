<?php
$this->session;
$user = $this->session->get('UserSession');
?>

<a href="http://www.student.bth.se/~matl14/phpmvc/kmom10/spring/webroot/about">Om sidan</a>

<?php if($user) : ?>
<span class="right">
<a href="<?=$this->url->create('users/user/' . $user['acronym'])?>"><?=$user['acronym']?></a>
 | <a href="<?=$this->url->create('users/logout')?>">Logga ut</a></span>
<?php else : ?>
<span class="right"><a href="<?=$this->url->create('users/register')?>">Registrera dig</a>
 | <a href="<?=$this->url->create('users/login')?>">Logga in</a></span>
<?php endif; ?>