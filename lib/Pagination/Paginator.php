<?php 
/**
 * A Simple Pagination class
 * Requires LengthAwarePaginator class
 */
if( !defined('__VIEWS') ) include "../config.php";

class Paginator{
	function __construct($table_name = "", $table_id = 'id', $limit_per_page = 100, $offset = 0, $custom_sql = false)
	{
		$this->table_name = $table_name;
		$this->table_id = $table_id;
		$this->offset = $offset;
		$this->rec_limit = $limit_per_page;
		$this->custom_sql = $custom_sql;
		$this->process();
	}

	function items(){
		return $this->items;
	}

	function where_query($where_query){
		return $this->where_query = $where_query;
	}

	function process(){
		// $db = new DB();
		global $wpdb;


		/* Get total number of records */
		// $rec_count = $wpdb->num_rows($this->table_name, $this->table_id);
		$sql = "SELECT COUNT(*) FROM {$this->table_name} {$this->where_query}"; // for wordpress
		
		$rec_count =   $wpdb->get_var( $sql );


		$this->total_records = $rec_count;

		$rec_limit = $this->rec_limit;

		## limit: 100,
		## offset: 0 * 100

		if( isset($_GET{'page'} ) ) {
			// $page = $_GET{'page'};
			$page = $_GET{'page'} == "" || $_GET{'page'} == 1 ? 0 : $_GET{'page'} - 1;
			$offset = $rec_limit * $page ;
		}else {
			$page = 0;
			$offset = 0;
		}

		$left_rec = $rec_count - ($page * $rec_limit);
		
		
		if( $this->custom_sql != false ){
			$sql = $this->custom_sql." LIMIT $offset, $rec_limit";
		}else{
			$sql = "SELECT * FROM {$this->table_name} {$this->where_query} LIMIT $offset, $rec_limit";
		}

		// $this->items = $wpdb->query($sql);
		$this->items = $wpdb->get_results($sql);

		
		$records_remaining = $left_rec - $rec_limit;

		$records_remaining = $records_remaining > 0 ? $records_remaining : 0;

		$this->totalpages = floor($rec_count / $rec_limit) + 1;
		$this->lastpage = $this->totalpages;

		$this->records_remaining = $records_remaining;
		
		$this->current_offset = $offset;
		$this->left_rec = $left_rec;
	}

	function lastpage(){
		return $this->lastpage;
	}

	function total_records(){
		return $this->total_records;
	}

	function render($append = ""){
		if( !class_exists('LengthAwarePaginator') ){
			require_once "LengthAwarePaginator.php";
		}

		$current = !isset($_GET{'page'}) ? 1 : ($_GET{'page'} == "") ? 1 : $_GET{'page'};
		$lap = new LengthAwarePaginator( $current, $this->totalpages );

		return $lap->paginate($append);
	}

	
}