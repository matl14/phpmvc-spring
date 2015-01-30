<?php

namespace Anax\Comments;
 
/**
 * A controller for comments
 *
 */
class CommentsController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable,
	\Anax\MVC\TRedirectHelpers;
	
		
		/**
		 * Initialize the controller.
		 *
		 * @return void
		 */
		public function initialize()
		{
			$this->comments = new \Anax\Comments\Comments();
			$this->comments->setDI($this->di);
			
			$this->users = new \Anax\Users\User();
			$this->users->setDI($this->di);
			
			$this->VTags = new \Anax\Comments\VTags();
			$this->VTags->setDI($this->di);
			
			$this->tags = new \Anax\Tags\Tags();
			$this->tags->setDI($this->di);
		}
		
		/**
		 * List the three latest comments
		 *
		 * @return triptych view
		 *
		 */
		public function latestAction()
		{
			$this->initialize();
			$this->session;
			
			$all = $this->VTags->query()
				->where('type = ?')
				->orderby('timestamp desc')
				->limit('3')
				->execute(['question']);
			
			// Message says no questions exist if no questions are found
			if($all == null)
			{
				$message = "<p>Det finns inga frågor i databasen.</p>";
			}
			else {
				$message = null;
			}
			
			if($this->users->loginStatus() == true)
			{
				$replyButton = true;
			}
			else {
				$replyButton	= null;
			}
			
			foreach($all as $key => $value)
			{
				$array[] = $value->getProperties();
			}
			
			for($i = 0; $i <= 2; $i++)
			{
				$this->views->add('comments/latest', [
					'content'	=> $array[$i],
					'buttons'	=> $replyButton,
				],
				'featured-' . ($i + 1));
			}
		}
		
		/**
		 * List all questions
		 *
		 *
		 */
		public function questionListAction()
		{
			$this->initialize();
			$this->session;
			
			$all = $this->VTags->query()
				->where('type = ?')
				->orderby('id desc')
				->execute(['question']);
			
			// Message says no questions exist if no questions are found
			if($all == null)
			{
				$message = "<p>Det finns inga frågor i databasen.</p>";
			}
			else {
				$message = null;
			}
			
			if($this->users->loginStatus() == true)
			{
				$link = true;
				$replyButton = true;
			}
			else {
				$link			= null;
				$replyButton	= null;
			}
			
			// Creates array $replyCount with key[question id] => value[number of replies]
			foreach($all as $value)
			{
				$ques = $value->getProperties();
				$reply = $this->comments->query()
									->where('replyto = ?')
									->andWhere('type = "reply"')
									->execute([$ques['id']]);
				$replyCount[$ques['id']] = count($reply);
			}
			
			$this->views->add('comments/list', [
				'content'		=> $all,
				'link'			=> $link,
				'message'		=> $message,
				'replyButton'	=> $replyButton,
				'replyCount'	=> $replyCount,
			]);
		}
		
		/**
		 * View one question with replies underneath
		 *
		 * @param integer $id (id of comment to be displayed)
		 *
		 */
		public function questionAction($id = null)
		{
			// Only shown if id is set
			if(!isset($id))
			{
			die("Missing id");
			}
			
			$question = $this->VTags->find($id);
			
			// Only shown if there is an actual comment in the database under chosen id
			if(!$question) {
			die("Invalid id");
			}
			
			// Only shown if the comment in the database has the type 'question'
			if($question->type !== 'question')
			{
			die("Invalid type of comment");
			}
			
			$getTags	= $question->getProperties();
			$tags		= $getTags['tags'];
			
			// If tags column is not empty in database, otherwise null is given as tags to the view
			if($tags)
			{
				// Make tags in to an array
				$tags		= explode(",", $tags);
				
				// Query the tag/s, put in to array
				// Put all tags with id and name in array $allTs
				foreach($tags as $key => $value)
				{
					$allTs[$key] = $this->tags->query()
									->where("name = ?")
									->execute([$value]);
				}
			}
			else {
			$allTs = null;
			}
			
			// Check if user is logged in, tell template to use buttons if true
			$this->session;
			if($this->users->loginStatus() == true)
			{
				$currentUser = $this->session->get('UserSession');
				$userForVotes = $this->users->find($currentUser['id']);
				$userForVotes = $userForVotes->getProperties();
				$buttons = true;
			}
			else {
				$buttons = null;
				$currentUser = null;
				$userForVotes = null;
			}
			
			//Get the comments to the question, to be put into view
			$commentsToQ = $this->getComments($id);
			
			// If there are replies put in buttons that can change order of replies
			if($this->getReplies($id))
			{
				$orderButton = true;
			}
			else {
				$orderButton = null;
			}

			$this->theme->setTitle($question->title);
			$this->views->add('comments/question', [
				'content'	=> $question,
				'tags'		=> $allTs,
				'comments'	=> $commentsToQ,
				'buttons'	=> $buttons,
				'user'		=> $userForVotes,
				'order'		=> $orderButton,
			]);
			
			// check who current user is and if it matches the creator of the question
			// if true, give buttons to make an answer accepted
			if($this->users->loginStatus() == true)
			{
				$question = $question->getProperties();
				if($currentUser['acronym'] == $question['user'] && $this->comments->checkAccept($question['id']) !== false)
				{
					$accept = true;
				}
				else {
					$accept = null;
				}
			}
			else {
				$accept = null;
			}
			
			// All replies to current question
			$replies = $this->getReplies($id);
			
			if($replies)
			{
			
			// Get ids of all replies put into array $replyIds
			foreach($replies as $key => $arrays)
			{
				$replyIds[$key] = $arrays->id;
			}
			
			// Put comments in to array $comments, if nothing comes in $comments will be null
			foreach($replyIds as $key => $ids)
			{
				$comments[$key] = $this->getComments($ids);
				if(!$comments) {
				$comments = null;
				}
			}
			
			// Loop out all replies and their respective comments
			foreach($replies as $key => $value)
			{	
				$this->views->add('comments/replies', [
					'content'	=> $value,
					'comments'	=> $comments[$key],
					'buttons'	=> $buttons,
					'accept'	=> $accept,
					'user'		=> $userForVotes,
				]);
			}
			}
		}
		
		/**
		 * Find what question a reply is related to and show the question
		 * If what gotten is a question, questionAction is used straight away
		 * Otherwise questionAction is used when a "replyto" is found
		 *
		 * @param integer $id (id of reply)
		 *
		 */
		public function replyAction($id = null)
		{
			if(!isset($id))
			{
				die("Missing id");
			}
			
			$comment = $this->comments->find($id);
			$comment = $comment->getProperties();
			if($comment['type'] == 'question')
			{
				// If the comment is commenting on a question, redirect there
				$url = $this->url->create('comments/question/' . $id);
				$this->response->redirect($url);
			}
			elseif($comment['type'] == 'reply')
			{
				// If the comment is a reply, go to replyto of that reply
				$url = $this->url->create('comments/question/' . $comment['replyto']);
				$this->response->redirect($url);
			}
			elseif($comment['type'] == 'comment')
			{
				// Find what the comment is commenting on
				$commenton = $this->comments->find($comment['commenton']);
				$commenton = $commenton->getProperties();
				
				// if what the comment comments on is a reply, go to the question (the replyto)
				if($commenton['type'] == 'reply')
				{
					$url = $this->url->create('comments/question/' . $commenton['replyto']);
					$this->response->redirect($url);
				}
				elseif($commenton['type'] == 'question')
				{
					$url = $this->url->create('comments/question/' . $commenton['id']);
					$this->response->redirect($url);
				}
			}
		}
		
		/**
		 * Return all replies of a question
		 *
		 * @param integer $id (id of question to get replies to)
		 *
		 */
		private function getReplies($id = null)
		{
		if($this->request->getGet('order') == 'score')
		{
			$replies = $this->comments->query()
				 ->where("replyto = ?")
				 ->andWhere("type = 'reply'")
				 ->orderby("score desc")
				 ->execute([$id]);
		}
		elseif($this->request->getGet('order') == 'date')
		{
			$replies = $this->comments->query()
				 ->where("replyto = ?")
				 ->andWhere("type = 'reply'")
				 ->orderby("timestamp desc")
				 ->execute([$id]);
		}
		else {
		$replies = $this->comments->query()
				 ->where("replyto = ?")
				 ->andWhere("type = 'reply'")
				 ->execute([$id]);
		}
		
		return $replies;
		}
		
		/**
		 * Return all comments to a reply
		 *
		 * @param integer $id (id of reply to get comments to)
		 *
		 */
		private function getComments($id = null)
		{
		$comments = $this->comments->query()
				 ->where("commenton = ?")
				 ->andWhere("type = 'comment'")
				 ->execute([$id]);
		
		return $comments;
		}
		
		/**
		 * Delete comment.
		 *
		 * @param integer $id of comment to delete.
		 *
		 * @return void
		 */
		public function deleteAction($id = null)
		{
			if (!isset($id)) {
				die("Missing id");
			}
			
			$comment = $this->comments->find($id);
		 
			$res = $this->comments->delete($id);
			
			if($comment->page == 'report')
			{
			$this->redirectTo('report');
			}
			elseif($comment->page == 'home')
			{
			$this->redirectTo('');
			}
		}

		
		/**
		 * Action to add a question
		 *
		 */
		public function addQuestionAction()
		{
			$this->session;
			$this->initialize();
			
			
			if($this->users->loginStatus() == true)
			{
				$this->CForm->create( array(), array(
					'title'		=> array(
						'type'			=> 'text',
						'label'			=> 'Titel:',
						'required'		=> true,
						'validation'	=> ['not_empty',
											'custom_test' => [
												'message'	=> 'Titeln måste vara mellan 10-200 tecken. Tillåtna tecken: a-ö, 0-9, mellanrum och .?!',
												'test'		=> array($this, 'checkTitle'),
											],
											],
					),
					'text'		=> array(
						'type'			=> 'textarea',
						'label'			=> 'Kommentar:',
						'required'		=> true,
						'validation'	=> ['not_empty',
											'custom_test' => [
												'message'	=> 'Texten måste vara mellan 10-1000 tecken.',
												'test'		=> array($this, 'checkText'),
											],
											],
					),
					'tags'		=> array(
						'type'			=> 'text',
						'label'			=> 'Taggar:',
						'required'		=> true,
						'validation'	=> ['not_empty',
											'custom_test' => [
												'message'	=> 'De enda tecken som är tillåtna är A-Ö och mellanrum. Separera dina taggar med ett kommatecken. T.ex: "Cafe,kaffe,CBD" är godkänt. Om du skriver "bra biograf,sköna stolar" kommer det alltså att bli två taggar.',
												'test'		=> array($this, 'checkTags'),
											],
											],
					),
					'submit'	=> array(
						'type'			=> 'submit',
						'value'			=> 'Spara',
						'callback'		=> function($form) {
						
						
						
							$now		= date(DATE_RFC2822);
							$user		= $this->session->get('UserSession');
							$text 		= $this->CForm->Value('text');
							$htmltext	= $this->textFilter->doFilter($this->CForm->Value('text'), 'shortcode, markdown');
							
							// Make all tags from form into array
							$tags		= $this->CForm->Value('tags');
							$tags		= explode(",", $tags);
							$tagId = "";
							
							// Check if tag already exists
							foreach($tags as $keyTag => $valueTag)
							{
								$tag = trim($valueTag);
								$existing = $this->tags->query()
										->where("name = ?")
										->execute([$tag]);
								
								// Put tags that already exists in $notToImport
								$notToImport = "";
								foreach($existing as $obj)
								{
									$array = $obj->getProperties();
									$tagId .= $array['id'] . ",";
									$notToImport .= $array['name'];
								}
								
								// Insert tags that aren't in database already
								if(strcmp($tag, $notToImport) !==0)
								{
									$this->db->insert("tags", array("name"	=> $tag, "slug" => slugify($tag)));
									$this->db->execute();
									$tagId .= $this->db->lastInsertId() . ",";
								}
							// $tagId now contains id's of all tags, pre-existing and new ones
							// $tagId is a string where a comma (,) separates the id's of all tags
							}
							
							
							// Save the 
							$this->comments->save(
								array(
									"user"		=> $user['acronym'],
									"name"		=> $user['name'],
									"email"		=> $user['email'],
									"title"		=> $this->CForm->Value('title'),
									"text"		=> $this->CForm->Value('text'),
									"texthtml"	=> $htmltext,
									"type"		=> 'question',
									"score"		=> 0,
									"timestamp"	=> $now,
									)
							);
							// Get id of newly made question
							$id = $this->db->lastInsertId();
							
							// Make foreach on an array with no empty values and where all values are unique
							foreach(array_unique(array_filter(explode(",", $tagId))) as $value)
							{
								// Relate the tags to the question that have just been entered
								$this->db->insert("Ques2Tag", array("idQues" => $id, "idTag" => $value));
								$this->db->execute();
							}
							
						return true;
						}
					)
				));
			
			$status = $this->CForm->Check();
			if($status === true)
			{
			$url = $this->url->create('comments');
			$this->response->redirect($url);
			}

			$this->theme->setTitle("Ställ en fråga");
			$this->views->add('default/page', [
				'title'		=> 'Ställ en fråga',
				'content'	=> $this->CForm->getHTML(),
			]);
			$this->views->add('default/onlyContent', [
				'content'	=> '<h2>Textlängd</h2><p>Texten i textfältet måste vara mellan 10-1000 tecken.</p><h2>Markdown</h2><p>Alla frågor skrivs i markdown. Om du vill veta mer om markdown kan du läsa här: <a href="http://daringfireball.net/projects/markdown/">Markdown</a>.</p><h2>Taggar</h2>En fråga kan ha många taggar. Om du vill ha mer än en tagg så separera dem med kommatecken. T.ex: "Kaffe,café,gott kaffe" kommer att ge tre taggar.',
			],
			'sidebar');
			}
			else {
				$this->theme->setTitle("Ställ en fråga");
				$this->views->add('default/page', [
				'title'		=> 'Ställ en fråga',
				'content'	=> 'Man måste vara inloggad för att kunna ställa en fråga..',
			]);
			}
		}
		
		/**
		 * Action to reply to a question
		 *
		 * @param integer $id (id of comment to be replied to
		 *
		 */
		public function addReplyAction($id = null)
		{
			if(!isset($id))
			{
				die("Missing id");
			}
			
			$comment = $this->comments->find($id);
			
			if(!$comment) {
				die("Invalid id");
			}
			
			$comment = $comment->getProperties();
			
			if($comment['type'] !== 'question')
			{
				die("Replies can only be made to questions");
			}
			
			$this->session;
			$this->initialize();
			
			
			if($this->users->loginStatus() == true)
			{
				$this->CForm->create( array(), array(
					'replyToId'	=> array(
						'type'			=> 'hidden',
						'value'			=> $id,
					),
					'text'		=> array(
						'type'			=> 'textarea',
						'label'			=> 'Svar:',
						'required'		=> true,
						'validation'	=> ['not_empty'],
					),
					'submit'	=> array(
						'type'			=> 'submit',
						'value'			=> 'Spara',
						'callback'		=> function($form) {
						
							$now		= date(DATE_RFC2822);
							$user		= $this->session->get('UserSession');
							$text		= $this->CForm->Value('text');
							$id 		= $this->CForm->Value('replyToId');
							$htmltext	= $this->textFilter->doFilter($this->CForm->Value('text'), 'shortcode, markdown');
							
							$this->comments->save(
								array(
									"user"		=> $user['acronym'],
									"name"		=> $user['name'],
									"email"		=> $user['email'],
									"text"		=> $text,
									"texthtml"	=> $htmltext,
									"type"		=> 'reply',
									"replyto"	=> $id,
									"score"		=> 0,
									"timestamp"	=> $now,
									)
							);
						return true;
						}
					)
				));
			
			$status = $this->CForm->Check();
			if($status === true)
			{
			$url = $this->url->create('comments/question/' . $id);
			$this->response->redirect($url);
			}

			$this->theme->setTitle("Svara på en fråga");
			$this->views->add('default/page', [
				'title'		=> 'Svara på en fråga',
				'text'		=> '<p>Svara på frågan: <i>' . $comment['title'] . '</i> av <b>' . $comment['user'] . '</b>.</p>',
				'content'	=> $this->CForm->getHTML(),
			]);
			}
			else {
				$this->theme->setTitle("Svara på en fråga");
				$this->views->add('default/page', [
				'title'		=> 'Svara på en fråga',
				'content'	=> 'Man måste vara inloggad för att kunna svara på en fråga..',
			]);
			}
			
			
		}
		
		/**
		 * Action to comment on a reply or question
		 *
		 * @param integer $id (id of comment to be replied to
		 *
		 */
		public function addCommentAction($id = null)
		{
			if(!isset($id))
			{
				die("Missing id");
			}
			
			$comment = $this->comments->find($id);
			
			if(!$comment) {
				die("Invalid id");
			}
			
			$comment = $comment->getProperties();
			
			if($comment['type'] == 'comment')
			{
				die("You can't comment on a comment");
			}
			
			$this->session;
			$this->initialize();
			
			if($this->users->loginStatus() == true)
			{
				$this->CForm->create( array(), array(
					'commentonId'	=> array(
						'type'			=> 'hidden',
						'value'			=> $id,
					),
					'text'		=> array(
						'type'			=> 'textarea',
						'label'			=> 'Svar:',
						'required'		=> true,
						'validation'	=> ['not_empty'],
					),
					'submit'	=> array(
						'type'			=> 'submit',
						'value'			=> 'Spara',
						'callback'		=> function($form) {
						
							$now		= date(DATE_RFC2822);
							$user		= $this->session->get('UserSession');
							$text		= $this->CForm->Value('text');
							$id 		= $this->CForm->Value('commentonId');
							$htmltext	= $this->textFilter->doFilter($this->CForm->Value('text'), 'shortcode, markdown');
							
							$this->comments->save(
								array(
									"user"		=> $user['acronym'],
									"name"		=> $user['name'],
									"email"		=> $user['email'],
									"text"		=> $text,
									"texthtml"	=> $htmltext,
									"type"		=> 'comment',
									"commenton"	=> $id,
									"score"		=> 0,
									"timestamp"	=> $now,
									)
							);
						return true;
						}
					)
				));
			
			$status = $this->CForm->Check();
			if($status === true)
			{
			$url = $this->url->create('comments/reply/' . $id);
			$this->response->redirect($url);
			}

			$this->theme->setTitle("Kommentera");
			$this->views->add('default/page', [
				'title'		=> 'Kommentera',
				'text'		=> '<p>Kommentera:</p><i>' . $comment['texthtml'] . '</i>',
				'content'	=> $this->CForm->getHTML(),
			]);
			}
			else {
				$this->theme->setTitle("Kommentera");
				$this->views->add('default/page', [
				'title'		=> 'Kommentera',
				'content'	=> 'Man måste vara inloggad för att kunna kommentera..',
			]);
			}
			
			
		}
		
		/**
		 * Action to make a reply accepted
		 *
		 * @param integer $id of reply to make accepted
		 *
		 */
		public function makeAcceptedAction($id = null)
		{			
			// Die if no id param has been set
			if(!isset($id))
			{
				die("Missing id");
			}
			
			$this->initialize();
			$this->session;
			
			$reply = $this->comments->find($id);
			
			// Make sure there is a comment (of any kind) with param id, otherwise die
			if($reply)
			{
			$reply = $reply->getProperties();
			
				// Make sure the comment is a reply, otherwise die
				if($reply['type'] == 'reply')
				{
					// Find the question that the reply replies to
					$question = $this->comments->find($reply['replyto']);
					$question = $question->getProperties();
					
					if($this->comments->checkAccept($question['id']) == false)
					{
						die("There is a already an accepted reply");
					}
					
					// If user is logged in, get the logged in user, othwerwise die
					if($this->users->loginStatus() == true)
					{
						
						$user = $this->session->get('UserSession');
						
						// If the logged in user has made the question that the param id replies to,
						// make the change in the database, otherwise die
						if($user['acronym'] == $question['user'])
						{
							$this->comments->save(
								[
								"id"		=> $reply['id'],
								"user"		=> $reply['user'],
								"name"		=> $reply['name'],
								"email"		=> $reply['email'],
								"title"		=> null,
								"text"		=> $reply['text'],
								"texthtml"	=> $reply['texthtml'],
								"type"		=> $reply['type'],
								"replyto"	=> $reply['replyto'],
								"commenton"	=> null,
								"accepted"	=> "yes",
								"score"		=> $reply['score'],
								"timestamp"	=> $reply['timestamp'],
								]
							);
							$url = $this->url->create('comments/reply/' . $reply['replyto']);
							$this->response->redirect($url);
						}
						else {
							die("You can only accept answers to your own questions.");
						}
						
					}
					else {
						die("You have to be logged in to accept an answer");
					}
					
				}
				else {
					die("Only replies can be accepted");
				}
				
			}
			else {
				die("There is no reply in the database with that id.");
			}
			
		}
		
		/**
		 * Action to downvote a comment
		 *
		 * @param integer $id
		 *
		 */
		public function downvoteAction($id = null)
		{
			if(!isset($id))
			{
				die("Missing id");
			}
			
			$this->session;
			$user = $this->session->get('UserSession');
			
			if(!$user)
			{
				die("Login required");
			}
			
			$item = $this->comments->find($id);
			
			if(!$item)
			{
				die("Invalid id");
			}
			
			$item = $item->getProperties();
			
			if($item['scoreusers'] !== null)
			{
				foreach(explode(",", $item['scoreusers']) as $username)
				{
					if($username == $user['acronym'] . "up")
					{
						die("You have already voted on this, dont try...");
					}
					elseif($username == $user['acronym'] . "down")
					{
						die("You have already voted on this, dont try...");
					}
				}
			}
			
			if($user)
			{
				$this->comments->downvote($id, $user['acronym']);
				$url = $this->url->create('comments/reply/' . $id);
				$this->response->redirect($url);
			}
			else {
				die("You need to be logged in to vote");
			}
		}
		
		/**
		 * Undo downvote action
		 *
		 * @param integer $id of comment that has been voted on 
		 *
		 */
		public function undoDownvoteAction($id = null)
		{
			if(!isset($id))
			{
				die("Missing id");
			}
			
			$this->session;
			$user = $this->session->get('UserSession');
			
			if(!$user)
			{
				die("Login required");
			}
			
			$item = $this->comments->find($id);
			
			if(!$item)
			{
				die("Invalid id");
			}
			
			$item = $item->getProperties();
			
			if($item['scoreusers'] !== null)
			{
				$array = explode(",", $item['scoreusers']);
				if(in_array($user['acronym'] . "down", $array))
				{
					$this->comments->undoDownvote($id, $user['acronym']);
					$url = $this->url->create('comments/reply/' . $id);
					$this->response->redirect($url);
				}
				else {
					die("You havent made a downvote here");
				}
			}
		}
		
		/**
		 * Action to upvote a comment
		 *
		 * @param integer $id
		 *
		 */
		public function upvoteAction($id = null)
		{
			if(!isset($id))
			{
				die("Missing id");
			}
			
			$this->session;
			$user = $this->session->get('UserSession');
			
			if(!$user)
			{
				die("Login required");
			}
			
			$item = $this->comments->find($id);
			
			if(!$item)
			{
				die("Invalid id");
			}
			
			$item = $item->getProperties();
			
			if($item['scoreusers'] !== null)
			{
				foreach(explode(",", $item['scoreusers']) as $username)
				{
					if($username == $user['acronym'] . "up")
					{
						die("You've already voted on this, you can't do it again.");
					}
					elseif($username == $user['acronym'] . "down")
					{
						die("You've already voted on this, you can't do it again.");
					}
				}
			}
			
			if($user)
			{
				$this->comments->upvote($id, $user['acronym']);
				$url = $this->url->create('comments/reply/' . $id);
				$this->response->redirect($url);
			}
			else {
				die("You need to be logged in to vote");
			}
		}
		
		/**
		 * Undo upvote action
		 *
		 * @param integer $id of comment that has been voted on 
		 *
		 */
		public function undoUpvoteAction($id = null)
		{
			if(!isset($id))
			{
				die("Missing id");
			}
			
			$this->session;
			$user = $this->session->get('UserSession');
			
			if(!$user)
			{
				die("Login required");
			}
			
			$item = $this->comments->find($id);
			
			if(!$item)
			{
				die("Invalid id");
			}
			
			$item = $item->getProperties();
			
			if($item['scoreusers'] !== null)
			{
				$array = explode(",", $item['scoreusers']);
				if(in_array($user['acronym'] . "up", $array))
				{
					$this->comments->undoUpvote($id, $user['acronym']);
					$url = $this->url->create('comments/reply/' . $id);
					$this->response->redirect($url);
				}
				else {
					die("You havent made a downvote here");
				}
			}
		}
		
		
	/**
	 * ++++++++++++++++++++++++++++++++++++++++++
	 * ------------------------------------------
	 * CUSTOM TESTS FOR FORMS
	 * ++++++++++++++++++++++++++++++++++++++++++
	 * ------------------------------------------
	 */
	 
	/**
	 * Check length of a string and if characters are correct
	 *
	 * @param string $text
	 *
	 */
	public function checkTitle($text)
	{
		if(strlen(utf8_decode($text)) < 10)
		{
			return false;
		}
		if(strlen(utf8_decode($text)) > 200)
		{
			return false;
		}
		if(preg_match("/[^a-z '\.\?\!0-9\+åäö]/i", $text))
		{
			return false;
		}
		else {
			return true;
		}
	}
	
	/**
	 * Check length of text
	 *
	 * @param string $text
	 *
	 */
	public function checkText($text)
	{
		if(strlen(utf8_decode($text)) < 10)
		{
			return false;
		}
		if(strlen(utf8_decode($text)) > 1000)
		{
			return false;
		}
		else {
			return true;
		}
	}
	
	/**
	 * Check tag string that have been input to form
	 *
	 * @param string $string
	 *
	 */
	public function checkTags($string)
	{
		if(preg_match("/[^a-z ,\+åäöÅÄÖ]/i", $string))
		{
			return false;
		}
		else {
			return true;
		}
	}
			
}