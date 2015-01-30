<?php

namespace Anax\Users;
 
/**
 * A controller for users and admin related events.
 *
 */
class UsersController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;
	
		
		/**
		 * Initialize the controller.
		 *
		 * @return void
		 */
		public function initialize()
		{
			$this->users = new \Anax\Users\User();
			$this->users->setDI($this->di);
			
			$this->comments = new \Anax\Comments\Comments();
			$this->comments->setDI($this->di);
		}
	
		/**
		 * List all users.
		 *
		 * @return void
		 */
		public function listAction()
		{
			$this->initialize();
		 
			$all = $this->users->findAll();
			
			$this->session;
		 
			$this->theme->setTitle("Alla användare");
			$this->views->add('users/list-all', [
				'users' => $all,
				'title' => "Alla users",
			]);
		}
		
		/**
		 * list the three most active users
		 *
		 * @return view with list
		 *
		 */
		public function activeUsersAction()
		{
			$this->initialize();
			
			$three = $this->comments->query("user, COUNT(user) AS hits")
							->groupby("user")
							->orderby("hits desc")
							->limit("3")
							->execute();
							
			foreach($three as $value)
			{
				$one = $value->getProperties();
				$user = $this->users->findAcronym($one['user']);
				$user = $user->getProperties();
				$array[] = $user;
			}
			
			for($i = 0; $i <= 2; $i++)
			{
				$this->views->add('users/most-active', [
					'content'	=> $array[$i],
				],
				'triptych-' . ($i +1));
			}
		}
		
		/**
		 * Show user of certain acronym
		 *
		 * @param string $acronym
		 *
		 */
		public function userAction($acronym = null)
		{
			if(!isset($acronym))
			{
				die("Missing acronym");
			}
			
			$this->initialize();
			$this->session;
			$user = $this->users->findAcronym($acronym);
			
			if(!$user)
			{
				die("No user with chosen acronym");
			}
			
			$currentUser = $user->getProperties();
			
			// Get all questions, replies and comments belonging to the user
			$questions = $this->findComments('question', $currentUser['acronym']);
			$replies = $this->findComments('reply', $currentUser['acronym']);
			$comments = $this->findComments('comment', $currentUser['acronym']);
			
			if($this->users->loginStatus() == true)
			{
				$userSession = $this->session->get('UserSession');
				
				if($userSession['id'] == $currentUser['id'])
				{
				$this->theme->setTitle("Min profil");
				$this->views->add('users/viewEdit', [
					'user' => $user,
				]);
				}
				else {
					$this->theme->setTitle($currentUser['acronym']);
					$this->views->add('users/view', [
						'user' => $user,
					]);
				}
			}
			else {
			$this->theme->setTitle($currentUser['acronym']);
			$this->views->add('users/view', [
				'user' => $user,
			]);
			}
			
			if($questions)
			{
				foreach($questions as $key => $value)
				{
					$arrayQs[$key] = $value;
				}
			$this->views->add('users/userQs', [
						'questions'		=> $arrayQs,
					]);
			}
			
			if($replies)
			{
				foreach($replies as $key => $value)
				{
					$arrayRs[$key] = $value;
				}
			$this->views->add('users/userRs', [
						'replies'		=> $arrayRs,
					]);
			}
			
			if($comments)
			{
				foreach($comments as $key => $value)
				{
					$arrayCs[$key] = $value;
					$comment = $value->getProperties();
				}
			
			$this->views->add('users/userCs', [
						'comments'		=> $arrayCs,
					]);
			}
		}
		
		/**
		 * Add new user.
		 *
		 * @return void
		 */
		public function registerAction()
		{
			$this->session(); // Will load the session service which also starts the session
			
			$this->initialize();
			
			if($this->users->loginStatus() == false)
			{
			$this->CForm->create( array(), array(
				'acronym'	=> array(
				  'type'        => 'text',
				  'label'       => 'Användarnamn:',
				  'required'	=> true,
				  'validation'  => ['not_empty',
									'custom_test' =>	[
										'message'	=> 'Either the username is already in use or your username contains unallowed characters. It must be between 3 and 20 characters. Only characters a-z and 0-9 can be used.',
										'test'		=> array($this, 'checkAcronym')
										],
									],
				),
				'email'		=> array(
				  'type'        => 'text',
				  'label'       => 'E-mail:',
				  'required'	=> true,
				  'validation'  => ['not_empty', 'email_adress',
									'custom_test' => [
										'message'	=> 'A user with that email already exists',
										'test'		=> array($this, 'checkEmail')
										],
									],
				),
				'name'		=> array(
				  'type'        => 'text',
				  'label'       => 'Namn:',
				  'validation'  => ['not_empty'],
				  'required'	=> true,
				),
				'password'		=> array(
				  'type'        => 'password',
				  'label'       => 'Lösenord:',
				  'validation'  => ['not_empty'],
				  'required'	=> true,
				),
				'repeatpassword'		=> array(
				  'type'        => 'password',
				  'label'       => 'Upprepa lösenord:',
				  'validation'  => ['not_empty', 'match' => 'password',
									'custom_test'	=> [
											'message'	=> 'Lösenord måste vara mellan 5 och 20 tecken.',
											'test'		=> array($this, 'checkPasswordLength'),
										],
									],
				  'required'	=> true,
				),
				'submit'	=> array(
				  'type'	=> 'submit',
				  'value'	=> 'Skapa',
				  'callback'  => function($form) {
				  
					$now = date(DATE_RFC2822);
  
					$this->users->save(
						array(
							"acronym"   => $this->CForm->Value('acronym'),
							"name"      => $this->CForm->Value('name'),
							"email"     => $this->CForm->Value('email'),
							"password"	=> password_hash($this->CForm->Value('password'), PASSWORD_DEFAULT),
							"created"	=> $now
						)
					);
				return true;
				})
				));
				
				
			$status = $this->CForm->Check();
			$text = null;
			if($status === true)	{
				$this->theme->setTitle("Skapa en user");
				$this->views->add('default/page', [
				'title'		=> null,
				'text'		=> null,
				'content' 	=> "<p>Grattis, ditt konto har skapats! Klicka på \"logga in\" högst upp på sidan för att logga in med ditt nyskapade konto.</p>",
			]);
			}
			else if($status === false)	{
				$text = "<p>Användaren kunde inte skapas.</p>";
			}
				
			$this->theme->setTitle("Skapa en user");
			$this->views->add('default/page', [
				'title'		=> "Skapa en user",
				'text'		=> $text,
				'content' 	=> $this->CForm->getHTML(),
			]);
			}
			else {
			$this->theme->setTitle("Skapa en user");
			$this->views->add('default/page', [
				'title'		=> "Skapa en user",
				'text'		=> null,
				'content' 	=> "Du kan inte vara inloggad om du vill skapa ett konto.",
			]);
			}
		}
		
		/**
		 * Delete user.
		 *
		 * @param integer $id of user to delete.
		 *
		 * @return void
		 */
		public function deleteAction($id = null)
		{
			if (!isset($id)) {
				die("Missing id");
			}
			
			$this->initialize();
			$this->session;
			
			if($this->users->loginStatus() == false)
			{
				die("You can't delete an account without even being logged in..");
			}
			
			$user			= $this->users->find($id);
			$user			= $user->getProperties();
			$loggedInUser	= $this->session->get('UserSession');
			
			if($user['id'] == $loggedInUser['id'])
			{
			$this->users->logout();
			$res = $this->users->delete($id);
		 
			$url = $this->url->create('');
			$this->response->redirect($url);
			}
			else {
				die("You can't delete an account that isn't yours");
			}
		}
		
		/**
		 * Create form using CForm to edit an existing user
		 *
		 */
		public function editAction($id = null)
		{
			$this->session(); // Will load the session service which also starts the session
			
			if (!isset($id)) {
				die("Missing id");
			}
			
			$this->initialize();
			$urlStart = $this->url->create('');
			
			$user = $this->users->find($id);

			if($this->users->loginStatus() == true)
			{
				$userSession = $this->session->get('UserSession');
				$currentUser = $user->getProperties();
				
				if($userSession['id'] == $currentUser['id'])
				{
			
				$this->CForm->create( array(), array(
					'id'		=> array(
					  'type'        => 'hidden',
					  'value'       => $user->id,  
					),
					'origEmail'	=> array(
					  'type'        => 'hidden',
					  'value'       => $user->email,  
					),
					'name'		=> array(
					  'type'        => 'text',
					  'label'       => 'Namn:',
					  'validation'  => ['not_empty'],
					  'required'	=> true,
					  'value'       => $user->name,  
					),
					'email'		=> array(
					  'type'        => 'text',
					  'label'       => 'E-mail:',
					  'value'       => $user->email,
					  'required'	=> true,
					  'validation'  => ['not_empty', 'email_adress'],
					),
					'submit'	=> array(
					  'type'	=> 'submit',
					  'value'	=> 'Spara',
					  'callback'  => function($form) {
	  
						if($this->CForm->Value('email') == $this->CForm->Value('origEmail'))
						{
						$this->doEditAction(
							array(
								"id"        => $this->CForm->Value('id'),
								"name"      => $this->CForm->Value('name'),
								"email"     => $this->CForm->Value('email'),
							)
						);
						}
						elseif($this->checkEmail($this->CForm->Value('email')) == true) {
							
							$this->doEditAction(
							array(
								"id"        => $this->CForm->Value('id'),
								"name"      => $this->CForm->Value('name'),
								"email"     => $this->CForm->Value('email'),
							)
						);
						}
						elseif($this->checkEmail($this->CForm->Value('email')) == false) {
							return false;
						}
					return true;
					})
					));
					
					
				$status = $this->CForm->Check();
				$url = $this->url->create("users/user/" . $user->acronym);
				$link = "<a href='" . $url . "' class='margin-top'><i class='fa fa-long-arrow-left fa-2x'></i></a><br><span class='smaller'>Tillbaka</span>";
				$text = null;
				if($status === true)	{
					$text .= "<p><b>Användaren har blivit uppdaterad.</b></p>";
				}
				else if($status === false)	{
					$text .= "<p><b>Användaren kunde inte uppdateras.</b><br>Det kan vara så att den mejl du skrivit in används av en annan användare.</p>";
				}
					
				$this->theme->setTitle("Ändra en user");
				$this->views->add('default/page', [
					'title'		=> "Ändra en user",
					'text'		=> $text,
					'content' 	=> $this->CForm->getHTML(),
				]);	
				$this->views->addString($link);
				}
				else {
					$this->response->redirect($urlStart);
				}
			}
			else {
				$this->response->redirect($urlStart);
			}
		}
		
		/**
		 * Do edit action, update the user
		 *
		 */
		public function doEditAction($user)
		{
			$now = date(DATE_RFC2822);
			
			$this->users->save([
			'id'		=> $user['id'],
			'email'		=> $user['email'],
			'name'		=> $user['name'],
			'updated'	=> $now,
		]);
		
		}
		
		/**
		 * Create form using CForm to edit password of existing user
		 *
		 */
		public function changePwAction($id = null)
		{
			$this->session(); // Will load the session service which also starts the session
			
			if (!isset($id)) {
				die("Missing id");
			}
			
			$this->initialize();
			$homeUrl = $this->url->create('');
			
			$user = $this->users->find($id);
			
			if($this->users->loginStatus() == true)
			{
				$userSession = $this->session->get('UserSession');
				$currentUser = $user->getProperties();
				
				if($userSession['id'] == $currentUser['id'])
				{
			
				$this->CForm->create( array(), array(
					'id'			=> array(
					  'type'        	=> 'hidden',
					  'value'       	=> $user->id,  
					),
					'bajs'			=> array(
					  'type'			=> 'hidden',
					  'value'			=> 'bajs',
					),
					'oldPassword'	=> array(
						  'type'        => 'password',
						  'label'       => 'Nuvarande lösenord:',
						  'required'	=> true,
						  'validation'  => ['not_empty',
											'custom_test'	=> [
												'message'	=> 'Fel lösenord.',
												'test'		=> array($this, 'checkPassword'),
											],
											],
					),
					'password'		=> array(
					  'label'			=> 'Nytt lösenord:',
					  'type'			=> 'password',
					  'validation'		=> ['not_empty',
											'custom_test'	=> [
												'message'	=> 'Lösenord måste vara mellan 5 och 20 tecken.',
												'test'		=> array($this, 'checkPasswordLength'),
											],
											],
					),
					'repeatPassword'		=> array(
					  'label'			=> 'Upprepa nytt lösenord:',
					  'type'			=> 'password',
					  'validation'		=> ['not_empty', 'match' => 'password',
											'custom_test'	=> [
												'message'	=> 'Lösenord måste vara mellan 5 och 20 tecken.',
												'test'		=> array($this, 'checkPasswordLength'),
											],
											],
					),
					'submit'	=> array(
					  'type'	=> 'submit',
					  'value'	=> 'Spara',
					  'callback'  => function($form) {
					  
						$this->doChangePwAction(
							array(
								"id"        => $this->CForm->Value('id'),
								"password"	=> $this->CForm->Value('password'),
							)
						);
					return true;
					})
					));
					
					
				$status = $this->CForm->Check();
				$url = $this->url->create("users/user/" . $user->acronym);
				$link = "<a href='" . $url . "' class='margin-top'><i class='fa fa-long-arrow-left fa-2x'></i></a><br><span class='smaller'>Tillbaka</span>";
				$text = null;
				if($status === true)	{
					$text .= "<p><b>Lösenordet har ändrats.</b></p>";
				}
				else if($status === false)	{
					$text .= "<p><b>Lösenordet kunde inte ändras.</b></p>";
				}
					
				$this->theme->setTitle("Byt lösenord");
				$this->views->add('default/page', [
					'title'		=> "Byt lösenord",
					'text'		=> $text,
					'content' 	=> $this->CForm->getHTML(),
				]);	
				$this->views->addString($link);
				}
				else {
					$this->response->redirect($homeUrl);
				}
			}
			else {
				$this->response->redirect($homeUrl);
			}
		}
		
		/**
		 * Do changepw action, update the user
		 *
		 * @param array $user containg id and password from form
		 *
		 */
		public function doChangePwAction($user)
		{
			$now = date(DATE_RFC2822);
			
			$this->users->save([
			'id'		=> $user['id'],
			'password'	=> password_hash($user['password'], PASSWORD_DEFAULT),
			'updated'	=> $now,
		]);
		}
		
		/**
		 * --------------------------
		 * TESTS USED FOR FORMS ABOVE
		 * --------------------------
		 *
		 */
		
		/**
		 * Check if a user with certain acronym already exists
		 * Then if the acronym validates (a-z, A-Z, 0-9 and between 3 and 30 characters)
		 *
		 * @param string with username
		 *
		 */
		public function checkAcronym($acronym)
		{
			if($acronym != "")
			{
				$user = $this->users->findAcronym($acronym);
				if($user)
				{
					return false;
				}
				else {
					if(preg_match('/^[a-z0-9]{3,20}$/i', $acronym))
					{
						return true;
					}
					else {
						return false;
					}
				}
			}
		}
			
		/**
		 * Check if a user with certain email already exists
		 *
		 * @param string with email
		 *
		 */
		public function checkEmail($email)
		{
			if($email != "")
			{
				$user = $this->users->findEmail($email);
				if($user)
				{
					return false;
				}
				return true;
			}
		}
		
		/**
		 * Checks password of certain user to database, used on password-changing site
		 *
		 * @param string $password
		 *
		 */
		public function checkPassword($password)
		{
			$url	= $this->request->getCurrentUrl();
			$url	= explode("changepw/", $url);
			$id		= $url[1];
			
			$user	= $this->users->find($id);
			$user = $user->getProperties();
			
			if(password_verify($password, $user['password']) == true)
			{
				return true;
			}
			else {
				return false;
			}
		}
		
		/**
		 * Checks lengths of passwords input to forms
		 *
		 * @param string $value
		 *
		 */
		public function checkPasswordLength($value)
		{
			if(strlen(utf8_decode($value)) < 5)
			{
			return false;
			}
			if(strlen(utf8_decode($value)) > 20)
			{
			return false;
			}
			else {
			return true;
			}
		}
		
	
	/**
	 * -----------------------------------------------------------------
	 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 * SESSIONS, LOGGING IN AND OUT BELOW
	 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 * -----------------------------------------------------------------
	 *
	 */
	 
	/**
	 * Login with an account in the database
	 *
	 */
	public function loginAction()
	{
			$this->session; // Will load the session service which also starts the session
			
			$this->initialize();
			
			$this->CForm->create( array(), array(
				'acronym'	=> array(
				  'type'        => 'text',
				  'label'       => 'Användarnamn:',
				  'required'	=> true,
				  'validation'  => ['not_empty'],
				),
				'password'		=> array(
				  'type'        => 'password',
				  'label'       => 'Lösenord:',
				  'validation'  => ['not_empty'],
				  'required'	=> true,
				),
				'submit'	=> array(
				  'type'	=> 'submit',
				  'value'	=> 'Logga in',
				  'callback'  => function($form) {
				  
						if($this->users->login(
							array(
								"acronym"   => $this->CForm->Value('acronym'),
								"password"	=> $this->CForm->Value('password'),
							)
						) == false) {
						return false;
						}
				})
				));
				
				
			$status = $this->CForm->Check();
			$text = null;
			if($status === true)	{
			}
			else if($status === false)	{
				$text = "<p>Felaktigt användarnamn eller lösenord.</p>";
				unset($_SESSION['form-failed']);
			}
			
			if($this->users->loginStatus() == true)
			{
			$text = "<p>Du är inloggad.<br>Om du vill logga ut, <a href='" . $this->url->create('users/logout') . "'> klicka här</a>.</p>";
			$this->theme->setTitle("Logga in");
			$this->views->add('default/page', [
				'title'		=> "Inloggad",
				'text'		=> $text,
				'content' 	=> null,
			]);
			}
			else {
			$this->theme->setTitle("Logga in");
			$this->views->add('default/page', [
				'title'		=> "Logga in",
				'text'		=> $text,
				'content' 	=> $this->CForm->getHTML(),
			]);
			}
		}
		
		/**
		 * log out an already logged in account
		 *
		 */
		public function logoutAction()
		{
			$this->session;
			$this->initialize();
			
			$url = $this->url->create('');
			
			$this->CForm->create( array(), array(
				'submit'	=> array(
				    'type'	=> 'submit',
				    'value'	=> 'Logga ut',
				    'callback'  => function($form) {
						$this->users->logout();
						return true;
					}
			)));
			
			if($this->users->loginStatus() == true)
			{
			$this->theme->setTitle("Logga ut");
			$this->views->add('default/page', [
				'title'		=> "Logga ut",
				'content' 	=> $this->CForm->getHTML(),
			]);
			
			$status = $this->CForm->Check();
			if($status === true)
			{
				$this->response->redirect($url);
			}
			
			}
			else {
			$this->response->redirect($url);
			}
			
		}
	
	/**
	 * Get all comments made of certain type and from certain user/acronym
	 *
	 * @param string $type (type of comment), string $acronym (acronym of user)
	 *
	 */
	public function findComments($type, $acronym)
	{
		$this->initialize();

		$all = $this->comments->query()
			->where('type = ?')
			->andWhere('user = ?')
			->orderby('timestamp desc')
			->execute([$type, $acronym]);
		
		return $all;	
	}
	
	
}