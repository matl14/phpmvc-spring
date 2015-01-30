<?php

namespace Anax\Users;
 
/**
 * Model for Users.
 *
 */
class User extends \Anax\MVC\CDatabaseModel
{
	
	/**
	 * Find and return specific user based on acronym
	 *
	 * @return this
	 */
	public function findAcronym($acronym)
	{
		$this->db->select()
				 ->from($this->getSource())
				 ->where("acronym = ?");
	 
		$this->db->execute([$acronym]);
		return $this->db->fetchInto($this);
	}
	
	/**
	 * Find and return specific user based on email
	 *
	 * @return this
	 */
	public function findEmail($email)
	{
		$this->db->select()
				 ->from($this->getSource())
				 ->where("email = ?");
	 
		$this->db->execute([$email]);
		return $this->db->fetchInto($this);
	}
	
	/**
	 * Check if someone is logged in_array
	 *
	 */
	public function loginStatus()
	{
		if(isset($_SESSION['UserSession']))
		{
			return true;
		}
		else {
			return false;
		}
	}
	
	/**
	 * Login a user
	 *
	 * @param array $details containing username and password
	 *
	 */
	public function login($details)
	{
		$user = $this->findAcronym($details['acronym']);
		if($user) {
			$user = $user->getProperties();
			
			if(password_verify($details['password'], $user['password']) == true)
			{
				$this->session->set('UserSession', $user);
				
				$now = date(DATE_RFC2822);
				$this->update(
					array(
						"acronym"	=> $user['acronym'],
						"active"	=> $now
						)
				);
				$this->response->redirect('');
			}
			else {
			return false;
			}
		}
		else {
		return false;
		}
	}
	
	/**
	 * Logout a logged in user
	 *
	 */
	public function logout()
	{
		unset($_SESSION['UserSession']);
	}
	

}