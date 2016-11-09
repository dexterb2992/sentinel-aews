<?php
if( !defined('__VIEWS') ) include "../config.php";

class ContentLoader{
	function __construct($filename, $title = null){
		
		$this->filename = $filename;
		$this->title = $title;

		$this->load();		
	}

	function load(){
		$this->path = __VIEWS.$this->filename.".php";
		
		if( !$this->exists() ){
			$this->path =  __VIEWS."404.php";
		}

		if( $this->title != null ){
			$_page_title = $this->title;
		}
		
		include __PARTIALS."_header.php";

		?>
		<!-- Content Wrapper. Contains page content -->
		<div class="content-wrapper">

			<!-- Main content -->
			<section class="content">

			<?php include $this->path; ?>

			</section><!-- /.content -->

		</div><!-- /.content-wrapper -->
		<?php
		

		include "views/_partials/_footer.php"; 
		
	}

	function exists(){
		if( file_exists($this->path) ){

			return true;
		}

		$this->title = "404 Page not found.";

		return false;
	}
}