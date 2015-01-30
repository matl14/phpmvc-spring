<?php

namespace Anax\Comments;
 
/**
 * Model for Comments.
 *
 */
class Comments extends \Anax\MVC\CDatabaseModel
{
	/**
	 * Check if a question has an accepted answer
	 *
	 * @param integer $id, id of question to be checked
	 *
	 * @return boolean true or false, false if there is an answer
	 *
	 */
	public function checkAccept($id = null)
	{
	// Find all replies related to that question
	$allReplies = $this->query()
						->where('replyto = ?')
						->execute([$id]);
	// Look through all replies, die if any reply is already accepted
	foreach($allReplies as $one)
	{
		$one = $one->getProperties();
		if($one['accepted'] =='yes')
		{
			return false;
		}
	}
	return true;
	}
	
	/**
	 * Give a +1 score to a comment/reply/question
	 *
	 * @param integer $id and string $acronym
	 *
	 */
	public function upvote($id = null, $acronym = null)
	{
		if(!isset($id))
		{
			die("Missing id");
		}
		
		if(!isset($acronym))
		{
			die("Missing username");
		}
		$item = $this->find($id);
		
		if($item)
		{
			$item = $item->getProperties();
			
			$this->save([
				"id"			=> $item['id'],
				"score"			=> $item['score'] + 1,
				"scoreusers"	=> $item['scoreusers'] . $acronym . "up,",
			]);
			
			$this->db->select()
				 ->from('user')
				 ->where("acronym = ?");
			$this->db->execute([$acronym]);
			$user = $this->db->fetchInto($this);
			$user = $user->getProperties();
			
		
			$this->db->update('user', ["votes"], 'acronym = ?');
			$this->db->execute([$user['votes'] . $item['id'] . "up,", $acronym]);
							
		}
		else {
			die("No comment found with that id");
		}
	}
	
	/**
	 * Undo an already made upvote
	 *
	 * @param integer $id and string $acronym
	 *
	 */
	public function undoUpvote($id = null, $acronym = null)
	{
		if(!isset($id))
		{
			die("Missing id");
		}
		
		if(!isset($acronym))
		{
			die("Missing username");
		}
		
		$item = $this->find($id);
		
		if($item)
		{
			$item = $item->getProperties();
			$newScoreusers = str_replace($acronym . "up,", "", $item['scoreusers']);
			$newScore = $item['score'] - 1;
		}
		else {
			die("Invalid id");
		}
		
		$this->db->select()
					->from('user')
					->where('acronym = ?');
		$this->db->execute([$acronym]);
		$user = $this->db->fetchInto($this);
		
		if($user)
		{
		$user = $user->getProperties();
		$newVotes = str_replace($id . "up,", "", $user['votes']);
		}
		else {
			die("Invalid acronym");
		}
		
		$this->db->update('user', ["votes"], 'acronym = ?');
		$this->db->execute([$newVotes, $acronym]);
		
		$this->db->update('comments', ["scoreusers", "score"], 'id = ?');
		$this->db->execute([$newScoreusers, $newScore, $id]);
	}
	
	/**
	 * Give a -1 score to a comment/reply/question
	 *
	 * @param integer $id and string $acronym
	 *
	 */
	public function downvote($id = null, $acronym = null)
	{
		if(!isset($id))
		{
			die("Missing id");
		}
		
		if(!isset($acronym))
		{
			die("Missing username");
		}
		
		$item = $this->find($id);
		
		if($item)
		{
			$item = $item->getProperties();
			
			$this->save([
				"id"			=> $item['id'],
				"score"			=> $item['score'] - 1,
				"scoreusers"	=> $item['scoreusers'] . $acronym . "down,",
			]);
			
			$this->db->select()
				 ->from('user')
				 ->where("acronym = ?");
			$this->db->execute([$acronym]);
			$user = $this->db->fetchInto($this);
			$user = $user->getProperties();
			
		
			$this->db->update('user', ["votes"], 'acronym = ?');
			$this->db->execute([$user['votes'] . $item['id'] . "down,", $acronym]);
		}
		else {
			die("No comment found with that id");
		}
	}
	
	/**
	 * Undo an already made downvote
	 *
	 * @param integer $id and string $acronym
	 *
	 */
	public function undoDownvote($id = null, $acronym = null)
	{
		if(!isset($id))
		{
			die("Missing id");
		}
		
		if(!isset($acronym))
		{
			die("Missing username");
		}
		
		$item = $this->find($id);
		
		if($item)
		{
			$item = $item->getProperties();
			$newScoreusers = str_replace($acronym . "down,", "", $item['scoreusers']);
			$newScore = $item['score'] + 1;
		}
		else {
			die("Invalid id");
		}
		
		$this->db->select()
					->from('user')
					->where('acronym = ?');
		$this->db->execute([$acronym]);
		$user = $this->db->fetchInto($this);
		
		if($user)
		{
		$user = $user->getProperties();
		$newVotes = str_replace($id . "down,", "", $user['votes']);
		}
		else {
			die("Invalid acronym");
		}
		
		$this->db->update('user', ["votes"], 'acronym = ?');
		$this->db->execute([$newVotes, $acronym]);
		
		$this->db->update('comments', ["scoreusers", "score"], 'id = ?');
		$this->db->execute([$newScoreusers, $newScore, $id]);
	}
}