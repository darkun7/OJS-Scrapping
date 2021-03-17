<?php
	
class ScrappingOJS {
	public $current_issue;
	private $journal;
	private $xpath;
	
	function __construct($url) {
		$this->current_issue 	= array();
		$this->journal 			= $this->curlGet($url);
		$this->xpath 			= $this->returnXPathObject($this->journal);
	}
	
	function curlGet($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_URL, $url);
		$results = curl_exec($ch);
		curl_close($ch);
		return $results;
	}

	function returnXPathObject($item)
	{
		$xmlPageDom = new DomDocument();
		@$xmlPageDom->loadHTML($item);
		$xmlPageXPath = new DOMXPath($xmlPageDom);
		return $xmlPageXPath;
	}
	
	function push_value($query, $key, $removeCover=False, $type="value")
	{
		$selector = $this->xpath->query($query);
		if ($selector->length > 0){
			$elements = [];
			for ($i = 0; $i < $selector->length; $i++) {
				if($type == "value"){
					array_push($elements, $selector->item($i)->nodeValue);
					#print_r($this->current_issue);
				}else{
					array_push($elements, $selector->item($i)->getAttribute($type));
					
				}
			}
			if ($removeCover){
				$elements = array_slice($elements, 1, -1);
			}
			return $this->current_issue[$key] = $elements;
			#return true;
		}
	}
	
}
$scrapping = new ScrappingOJS('https://jurnal.unej.ac.id/index.php/JSEAHR/');
$issue_no  = $scrapping->push_value('//div[@class="current_issue_title"]','issue_no');						//Get recent issue number
$cover	   = $scrapping->push_value('//a[@class="cover"]/img','cover',false, 'src');						//Get recent issue cover
$date	   = $scrapping->push_value('//div[@class="published"]/span[@class="value"]','date'); 				//Get published date
$title	   = $scrapping->push_value('//div[@class="title"]','title', true);									//Get tittle, but remove its cover
$authors   = $scrapping->push_value('//div[@class="authors"]','authors');									//Get author
$pdf 	   = $scrapping->push_value('//a[@class="obj_galley_link pdf"]','pdf', true, 'href');				//Get article URL
print_r($scrapping->current_issue);
 ?>