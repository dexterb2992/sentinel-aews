<?php 
/**
 * A Simple Pagination Presenter class based on Bootstrap 3 pagination
 */
class PaginationPresenter{
	function __construct($append = "")
	{
		$this->append = $append;
	}

	function generateFirstPage($page = 1){
		return "<li class='disabled'><span>«</span></li> 
				<li class='active'><span>$page</span></li>";
	}

	function generateCurrentPage($page){
		return "<li class='active'><span>$page</span></li>";
	}

	function generateLink($page){
		return "<li><a href='?page=$page&{$this->append}'>$page</a></li>";
	}

	function generateLastPage($page){
		return "<li class='active'><span>$page</span></li>
				<li class='disabled'><span rel='next'>»</a></li>";
	}

	function generateDisabledDottedItems(){
		return "<li class='disabled'><span>...</span></li>";
	}

	function generateNextPage($current_page, $is_disabled = false){
		if( $is_disabled )
			return "<li class='disabled'><span rel='next'>»</span></li>";

		$next = $current_page+1;
		return "<li><a href='?page=$next&{$this->append}' rel='next'>»</a></li>";
	}

	function generatePrevPage($current_page, $is_disabled = false){
		if( $is_disabled )
			return "<li class='disabled'><span rel='prev'>«</span></li>";

		if( $current_page < 1 )
			return "<li class='disabled'><span rel='prev'>«</span></li>";
		$prev = $current_page-1;
		return "<li><a href='?page=$prev&{$this->append}' rel='prev'>«</a></li>";
	}
}