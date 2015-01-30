<?php

namespace Anax\Tags;

/**
 * A controller for tags
 *
 */
class TagsController implements \Anax\DI\IInjectionAware
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
		$this->VTags = new \Anax\Comments\VTags();
		$this->VTags->setDI($this->di);
		
		$this->tags = new \Anax\Tags\Tags();
		$this->tags->setDI($this->di);
		
		$this->users = new \Anax\Users\User();
		$this->users->setDI($this->di);
		
		$this->q2t = new\Anax\Tags\Ques2Tag();
		$this->q2t->setDI($this->di);
	}
		
	/**
	 * List all tags
	 *
	 */
	public function listAction()
	{
		$this->session;
		$this->initialize();
		 
		$all = $this->tags->query()
					->orderby('id desc')
					->execute();
		
		foreach($all as $key => $value)
		{
			$all[$key] = $value->getProperties();
		}
		
		$this->theme->setTitle("Alla taggar");
			$this->views->add('tags/list-all', [
				'tags' => $all,
				'title' => "Alla taggar",
			]);	
	}
	
	/**
	 * List the five most popular tags (five tags with most questions related to them)
	 *
	 * @return sidebar view containing five most popular tags
	 */
	public function popularTagsAction()
	{
		$this->initialize();
		
		$objArray = $this->q2t->query("idTag, COUNT(idTag) as idHits")
								->groupby('idTag')
								->orderby('idHits DESC')
								->limit('5')
								->execute();
		
		foreach($objArray as $key => $value)
		{
			$tag = $value->getProperties();
			$three[] = $this->tags->query()
										->where('id = ?')
										->execute([$tag['idTag']]);
		}
		
		foreach($three as $key => $value)
		{
			foreach($value as $obj)
			{
				$array[] = $obj->getProperties();
			}
		}
		
		$this->views->add('tags/popular', [
			'tags'	=> $array,
			'title' => 'Populära taggar',
		],
		'sidebar');
	}
	
	
	/**
	 * list all questions related to a tag
	 * get tag based on slug
	 *
	 * @param string $slug
	 *
	 */
	public function tagBySlugAction($slug = null)
	{
		$this->session;
		
		if(!isset($slug))
		{
			die("Missing link");
		}
		
		$tag = $this->tags->query()
					->where("slug = ?")
					->execute([$slug]);
		
		if(!$tag)
		{
			die("No tag with that id");
		}
		
		$tag = $tag[0]->getProperties();
		
		$allQs = $this->q2t->query()
					->where("idTag = ?")
					->execute([$tag['id']]);
		
		if($allQs)
		{
			$array = [];
			// Get all questions related to current id, put into array $array
			foreach($allQs as $key => $value)
			{
				$question = $value->getProperties();
				$question = $this->VTags->find($question['idQues']);
				if($question)
				{
					$question = $question->getProperties();
					
					$array[$key] = $question;
				}
			}
			
			// Message says no questions exist if no questions are found
			if($array == null)
			{
				$message = "<p>Det finns inga frågor i databasen.</p>";
			}
			else {
				$message = null;
			}
			
			// If user is logged in reply and comment buttons will be shown
			if($this->users->loginStatus() == true)
			{
				$buttons = true;
			}
			else {
				$buttons = null;
			}
			
			$this->theme->setTitle("Taggen " . $tag['name']);
			$this->views->add('tags/list-ques', [
				'tag'		=> $tag['name'],
				'content'	=> $array,
				'message'	=> $message,
				'buttons'	=> $buttons,
			]);
		}
		
	}
}