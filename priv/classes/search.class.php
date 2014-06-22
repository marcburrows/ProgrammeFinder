<?php

/**
* A class that uses iPlayer's SearchExtended feed to retrieve  and display search results
* @author 	Marc Burrows (marcburrows@me.com)
*/

class search{

/**
* get_query
*
* Get query term from form and call a function to get results
*
*
* @param  	string	$query	the query entered
* @param  	int		$page	the page number requested
* @return 	string	the results of the search
* @access 	public
*
*/

  public function get_query($query, $page = 1){
		
		// Make sure there is a search query. if not, display an error
		if($query != ""){

			//Get Search Results
			echo $this->get_results($query, $page);
			
		}else{

			// Display Error
			echo "Please enter a search query";	
			
		}	  
  }

/**
* get_results
*
* get the results from the query sent
*
*
* @param  	string	$query	the query entered
* @param  	int		$page	the page number requested
* @param  	bool	$page	whether the search results will show below the search box or not
* @return 	string	the results of the search
* @access 	public
*
*/

  
  public function get_results($query, $page = 1, $inline = false){
	  
		$search_availability 	= "iplayer";
		$masterbrand 			= "";
		$service_type			= "radio";
		
		$search_url = $this->createSearchURL($search_availability, $masterbrand = "", $service_type, $page, $query);
		
		//Convert the search URL results to an object.
		$list 	= file_get_contents($search_url);
		$obj 	= json_decode($list);
		
		if($obj->count > 0){
			//Search string returns at least one match for the search term
			
			foreach($obj->blocklist as $item){
				
					//BRAND INFO
					//brand_title, my_series_url, masterbrand
					//EPISODE INFO
					//title, synopsis, my_url
					$results[] = array(
									"masterbrand_title" => $item->masterbrand_title,
									"brand_title" => $item->brand_title,
									"brand_id" => $item->brand_id,
									"series_url" => $item->my_series_url,
									"title" => $item->title,
									"episode_id" => $item->id,
									"episode_url" => $item->my_url,
									"synopsis" => $item->synopsis
									);
					
			}
			
			//Get the results where episodes are grouped by brand
			$results = $this->regroup_results($results);
			
			if($inline){
				
				$display = $this->display_inline_results($results);				
				
				return $display;
				
			}else{
				
				$display = $this->display_results($results);
				$display .= $this->display_pagination($query, $obj->pagination->page, $obj->pagination->total_count);
				
				return $display;
				
			}

		}else{
			
			//Search string returns no matches for the search term
			$display = "No search results";
			
			return $display;
			
		}
	  
  }
  
/**
* display_inline_results
*
* display the results when they are to be shown beneath search box
*
*
* @param  	array	$results	an array of results where episodes are grouped by brand
* @return 	string	the results of the search in an html format, only showing brand name and the amount of available episodes
* @access 	private
*
*/
  
  
  private function display_inline_results($results){

		$display = "";
		
		// Group all brands together
		foreach($results as $brand){
			
			$display .= '<div class="result">';
			$display .= '<a href="http://www.bbc.co.uk/'.$brand['series_url'].'" title="'.$brand['brand_title'].'" >'.$brand['brand_title'].'</a><br/>';
			$display .= $brand['masterbrand_title'].'<br/>';
			//Display the amount of episodes
			$display .= count($brand['episodes'])." episodes";
			$display .= '</div>';				

		}			
		
		return $display;

  }    
  
/**
* display_results
*
* display the results when on the results search page
*
*
* @param  	array	$results	an array of results where episodes are grouped by brand
* @return 	string	the results of the search in an html format, showing brand name and then all the episodes
* @access 	private
*
*/  
  
  private function display_results($results){
		
		$display = "";
		
		// Group the results by brand
		foreach($results as $brand){
			
			$display .= '<div class="result">';
			$display .= '<a href="http://www.bbc.co.uk/'.$brand['series_url'].'" title="'.$brand['brand_title'].'" >'.$brand['brand_title'].'</a><br/>';
			$display .= $brand['masterbrand_title'].'<br/>';
			$display .= "<div class='episodes'>";
				
				// Display all Brand episodes
				foreach($brand['episodes'] as $episode){
					
					$display .= '<a href="http://www.bbc.co.uk/'.$episode['episode_url'].'" title="'.$episode['title'].'" >'.$episode['title'].'</a><br/>';
					$display .= $episode['synopsis'].'<br/>';
				}

			$display .= "</div>";					
			$display .= '</div>';				
		}
		
		return $display;
  }  
  
/**
* display_pagination
*
* display the pagination options based on page and number of search results (10 items per page)
*
*
* @param  	string	$query	the query sent over
* @param  	int		$page_no	the current page number
* @param  	int		$total_results	the total number of results in the search
* @return 	string	the pagination in an html format
* @access 	private
*
*/  
  
  private function display_pagination($query, $page_no, $total_results){

	    if($page_no == 1 && $total_results > 10){

			$next_url = "/results.php?q=".$query."&page=2";
			$pagination = "<a href='".$next_url."' class='pagination'>Next Page ></a>";
			
		}elseif($page_no > 1 & $total_results > ($page_no*10)){

			$prev_url = "/results.php?q=".$query."&page=".($page_no-1);
			$next_url = "/results.php?q=".$query."&page=".($page_no+1);
			$pagination =  "<a href='".$prev_url."' class='pagination'>< Prev Page</a>     <a href='".$next_url."' class='pagination'>Next Page ></a>";

		}elseif($page_no > 1 & $total_results <= ($page_no*10)){
		
			$prev_url = "/results.php?q=".$query."&page=".($page_no-1);
			$pagination = "<a href='".$prev_url."' class='pagination'>< Prev Page</a>";
			
		}else{
			$pagination = "";	
		}
		
		return $pagination;
  }
  
/**
* regroup_results
*
* original search results are a list of episodes with brand info, this rearranges them to be a list of brands with episode info
*
*
* @param  	array	$results	the search results in episode list format
* @return 	array	the search results in brand list format
* @access 	private
*
*/    
  
  private function regroup_results($results){
			$prev_brand = "";
			foreach($results as $item){
				if($prev_brand != $item['brand_id'] ){
					$brand_eps[$item['brand_id']] = array(
														"brand_title" => $item['brand_title'],
														"series_url" => $item['series_url'],
														"masterbrand_title" => $item['masterbrand_title'],
														"episodes" => array(
																		$item['episode_id'] => array(
																			"title" => $item['title'],
																			"episode_url" => $item['episode_url'],																
																			"synopsis" => $item['synopsis']																
																			)
																		)
													
															);
					
				}else{
					$brand_eps[$item['brand_id']]['episodes'][$item['episode_id']] = array(
																						"title" => $item['title'],
																						"episode_url" => $item['episode_url'],																
																						"synopsis" => $item['synopsis']																
																						);
				}
				$prev_brand = $item['brand_id'];
			}

	return $brand_eps;

  }
  
/**
* check_search_availability
*
* checks that the search availability term is a possible value. if no/a wrong value is given, "any" is returned
*
*
* @param  	string	$search_availability	the search availability term
* @return 	string	the accepted search availability term
* @access 	private
*
*/      


  private function check_search_availability($search_availability){
	  
	  $possibles = array("iplayer", "any", "discoverable", "ondemand", "simulcast", "comingup");
	  
	  if(in_array($search_availability, $possibles)){
		  
		  return $search_availability;
		  
	  }
	  else{
		  
		  return "any";
		  
	  }
  }
  



/**
* createSearchURL
*
* creates the SearchExtended feed URL that is to be used 
*
*
* @param  	string	$search_availability	the search availability term
* @param  	string	$masterbrand			the masterbrand used (N/A)
* @param  	string	$service_type			the service type (Radio)
* @param  	int		$page					the page number
* @param  	string	$query					the search query
* @return 	string	the SearchExtended feed URL 
* @access 	private
*
*/   


	public function createSearchURL($search_availability, $masterbrand = "", $service_type, $page, $query){
		$default_url 			= "http://www.bbc.co.uk/iplayer/ion/searchextended";
		
		$search_availability 	= "/search_availability/".$this->check_search_availability($search_availability);
		$masterbrand 			= ""; //Not used
		$service_type 			= "/service_type/".$service_type;
		$format 				= "/format/json";
		if($page > 1){
			$page	 				= "/page/".$page;
		}else{
			$page = "";
		}
		$query 					= "/q/".urlencode($query); 
		
		$search_url = $default_url.$search_availability.$masterbrand.$service_type.$format.$page.$query;
		return $search_url;
	}
	
}


?>
