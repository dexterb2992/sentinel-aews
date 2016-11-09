<?php 
/**
 * A Simple LengthAwarePagination class
 * Requires PaginationPresenter class
 */
include "PaginationPresenter.php";

class LengthAwarePaginator{
	function __construct($currentPage, $lastPage){

		$this->currentPage = $currentPage;
		$this->lastPage = $lastPage;
	}

	function currentPage(){

		return $this->currentPage;
	}

	function lastPage(){

		return $this->lastPage;
	}

	function paginate($append=""){
		$presenter = new PaginationPresenter($append);

		$currentPage = $this->currentPage();
		$lastPage = $this->lastPage();
		$links = "";

		# identify the current page
		# if cur = 1
		# we need to if lastPage is greater than 10 && cur is < 8
		if( $lastPage < 10 ){
			# we directly show them all the links
			for ($x = 1; $x <= $lastPage; $x++) { 
				if( $x == 1 ){
					if( $currentPage-1 >= 1 ){
						$links.= $presenter->generatePrevPage($currentPage);
					}else{
						$links.= $presenter->generatePrevPage($currentPage, true);
					}
				}

				$links.= $x == $currentPage && $x == 1 ? $presenter->generateFirstPage() : 
						$x == $currentPage ? $presenter->generateCurrentPage($x) : $presenter->generateLink($x);
			
			}

			if( $currentPage+1 <= $lastPage ){
				$links.= $presenter->generateNextPage($currentPage);
			}else{
				$links.= $presenter->generateNextPage($currentPage, true);
			}
			
		}else if( $lastPage > 10 ){
			# we need to break the pages

			## first, we need to identify where the currentPage belongs if lower range or higher range
			#  we'll need to create now the ranges:
			$lower_range = array(
				"min" => 1, 
				"max" => 6
			);

			$higher_range = array(
				"min" => $lastPage-6,
				"max" => $lastPage
			);

			if( $currentPage <= $lower_range['max'] ){
				# here we take the 1st 6 pages then dots, then lastPage-1 and lastPage
				if( $currentPage  != $lower_range['min'] ){
					$links = $presenter->generatePrevPage($currentPage).$presenter->generateLink(1);
				}else{
					$links = $presenter->generateFirstPage();
				}

				for ($x = 2; $x <= $lower_range['max']+2; $x++) { 
					$links.= $x == $currentPage ? $presenter->generateCurrentPage($x) : $presenter->generateLink($x);
					
				}

				// $links.= "...".($lastPage-1)." ".$lastPage;
				$links.= $presenter->generateDisabledDottedItems()
							.$presenter->generateLink($lastPage-1)
							.$presenter->generateLink($lastPage)
							.$presenter->generateNextPage($currentPage);

			}else if( $currentPage > $lower_range['max'] && $currentPage < $higher_range['min'] ){
				# here we take 3 prev pages from currentPage and 3 next pages after currentPage, then take 2 pages from 
				#   lower_range and 2 pages from higher range
				for ($x = ($currentPage - 3 ); $x <= ($currentPage + 3); $x++) { 
				 	$links.= $x == $currentPage ? $presenter->generateCurrentPage($x) : $presenter->generateLink($x);
				} 

				$links = $presenter->generatePrevPage($currentPage)
							.$presenter->generateLink($lower_range['min'])
							.$presenter->generateLink($lower_range['min'] + 1)
							.$presenter->generateDisabledDottedItems()
							.$links
							.$presenter->generateDisabledDottedItems()
							.$presenter->generateLink($higher_range['max'] - 1)
							.$presenter->generateLink($higher_range['max'])
							.$presenter->generateNextPage($currentPage);

			}else if( $currentPage >= $higher_range['min'] ){
				# here we take the last 8 pages from higher range and add first 2 pages from lower range
				for ($x = $higher_range['min']-2; $x <= $higher_range['max']; $x++) { 
					if( $x == $currentPage && $x == $higher_range['max'] ){
						$links.= $presenter->generateLastPage($x);
					}else{
						$links.= ($x == $currentPage) ? $presenter->generateCurrentPage($x) : $presenter->generateLink($x);
					}
					
				}

				if( $currentPage != $higher_range['max'] ){
					$links.= $presenter->generateNextPage($currentPage);
				}

				$links= $presenter->generatePrevPage($currentPage)
					.$presenter->generateLink($lower_range['min'])
					.$presenter->generateLink($lower_range['min'] + 1)
					.$presenter->generateDisabledDottedItems()
					.$links;
			}
		}

		
		return "<div class='pagination'>$links</div>";

	}
}