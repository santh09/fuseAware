<?php
	$url = "https://www.black-ink.org/"; // url to do the web scrape
	$html = file_get_contents($url); // to get the html returned from the url
    
	
	libxml_use_internal_errors(true); // to disable the libxml errors
	$doc = new DOMDocument;   // declare the DOM doc used to convert the html string into DOM 

	if(!empty($html)) // to check if the html is actually returned
	{
		$doc->loadHTML($html); // to load the html
		$xpath = new DOMXpath($doc);  // to do queries with the DOM document
		$post_list = array();
		$combine = array();
		$final_result = array();
		$node = $xpath->query('//article[@itemprop="blogPost"]'); /* to get the article of the category - category-workblog but it did not work here so took the attribute itemprop */
		//$node = $html->find('article[class=" category-workblog"]',0);
		if($node->length > 0)
		{
			$headline = array();
			$title = $xpath->query('//h2[@class="entry-title"]'); // to get the Link Title
			foreach($title as $link_title){
			        $headline[] = $link_title->nodeValue; 
		    }
			
			$content = array();
			$cont_summary = $xpath->query('//div[@class="entry-summary"]'); // to get the Content
			foreach($cont_summary as $cont){
			        $content[] = $cont->nodeValue; 
		    }
			
			$link = array();
			$link_summary = $xpath->query('//h2[@class="entry-title"]//a'); // to get the link to the post
			foreach($link_summary as $lk){
			        $link[] = $lk->getAttribute('href'); 
		    }
			
			foreach($link as $key => $val)
			{
				$combine[] = [$val, $headline[$key], $content[$key]];  // to combine the individual array into one
				
			}
			
			
		}
		foreach ($combine as $k => $v )
        {
            $combine[$k]['URL'] = $combine[$k][0];
			$combine[$k]['Title'] = $combine[$k][1];
			$combine[$k]['Meta Description'] = $combine[$k][2];
            unset($combine[$k][0]);
			unset($combine[$k][1]);
			unset($combine[$k][2]);
        }
		/* for testing to print the array to check the output */
		/*echo "<pre>";
        print_r($combine);
        echo "</pre>";*/
		
		header('Content-Type: application/json');
		$jsondata = json_encode($combine, JSON_PRETTY_PRINT);
		$charc = file_put_contents('results.json', $jsondata); /* to get the file size */
		
		/* start to check if file exist */
		if(file_put_contents('results.json', $jsondata)) {
	        echo 'Data successfully written and saved';
	    }
	    else 
	        echo "error";
		/* end to check if file exist */	
	}





?>