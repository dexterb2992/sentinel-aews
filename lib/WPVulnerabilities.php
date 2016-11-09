<?php
if( !function_exists('getPageData') ){
	include 'functions.php';
}

class WPVulnerabilities {
	function __construct(){
		$this->url = "http://wpvulndb.com/";
		$this->wp_versions_url = "{$this->url}wordpresses";
		$this->wp_plugins_url = "{$this->url}plugins";
		$this->wp_themes_url = "{$this->url}themes";
		$this->more_details = "{$this->url}vulnerabilities/";


		$this->plugins_api = 'https://wpvulndb.com/api/v2/plugins/';
		$this->themes_api = 'https://wpvulndb.com/api/v2/themes/';
	}

	public function get_wp_vulns(){
		$this->wpvulndb = getPageData($this->wp_versions_url);
		return $this->wpvulndb;
	}

	public function get_wp_plugins_vulns(){
		$this->wpvulndb_plugins = getPageData($this->wp_plugins_url);
		return $this->wpvulndb_plugins;
	}

	public function get_wp_themes_vulns(){
		$this->wpvulndb_plugins =  getPageData($this->wp_themes_url);
		return $this->wpvulndb_plugins;
	}

	public function check_for_slash($first, $last){
		if( substr($first, strlen($first), strlen($first)-1) != "/" && substr($last, 0, 1) != "/" ){
			return "$first/$last";
		}
		return $first.$last;
	}

	public function rel2abs($abs_url, $findme, $target_url){
		$pos = strpos($target_url, $findme);
		if( $findme == "*/" ){
			$parsed = parse_url($abs_url);
			return $parsed['scheme']."://".$parsed['host'].$target_url;
		}

		if( $pos !== false ){
			$res_path = dirname($abs_url);
			$target_url = substr_replace($target_url, '', $pos, strlen($findme));
			
		}else{
			return $this->check_for_slash($abs_url, $target_url);
		}


		return $this->rel2abs($res_path, $findme, $target_url);
		
	}

	public function get_table_elements($getWhat){
		switch ($getWhat) {
			case 'wordpresses':
				$source_html = $this->get_wp_vulns();
				break;
			
			case 'plugins':
				$source_html = $this->get_wp_plugins_vulns();
				break;

			case 'themes':
				$source_html = $this->get_wp_themes_vulns();
				break;
		}

		$dom = new DOMDocument();
		// ensuring we don't throw visible errors during html loading
		libxml_use_internal_errors( true );

		// DOMDocument doesn't handle encoding correctly and garbles the output.
		// mb_convert_encoding is an extension though, so we're checking if it's
		// available first.
		if ( function_exists( 'mb_convert_encoding' ) ) {
			$this->html = mb_convert_encoding( $source_html, 'HTML-ENTITIES', 'UTF-8' );
		}

		$dom->loadHTML( $this->html );  // suppress warnings

		libxml_use_internal_errors( false );
		// get all elements on the page
		$tableElements = $dom->getElementsByTagName( 'table' );

		$results = "";
		$tableDom = null;
		foreach ($tableElements as $key => $table) {
			$results .= '<table id="tbl_'.$getWhat.'" class="table table-info">'.DOMgetInnerHTML($table).'</table>';
			// $tableDom = $table;
		}

		// change relative paths to absolute paths
		$tableDom = new DOMDocument;
		libxml_use_internal_errors( true );
		$tableDom->loadHTML( $results ); 
		$anchors = $tableDom->getElementsByTagName('a');

		foreach ($anchors as $key => $anchor) {
			$url = $anchor->getAttribute('href');
			if( substr($url, 0, 4) != "http" &&
				$url != "" && $url != null
			){
				if( substr($url, 0, 2) == "//" ){
					$anchor->setAttribute('href', "http:$url");
				}else{
					if( substr($url, 0, 1) == "/" && substr($url, 0, 2) != "/" ){
						$re_abs_path = $this->rel2abs($this->url, "*/", $url);
					}else{
						$re_abs_path = $this->rel2abs($this->url, "../", $url);
					}

					$anchor->setAttribute('href', $re_abs_path);
					$anchor->setAttribute('target', '_blank');
				}
			}
		}

		return $this->extractDOMContent($tableDom)->saveHTML();
	}

	public function extractDOMContent($doc){
	    # remove <!DOCTYPE
	    $doc->removeChild($doc->doctype);

	    // lets get all children inside the body tag
	    foreach ($doc->firstChild->firstChild->childNodes as $k => $v) {
	        if($k !== 0){ // don't store the first element since that one will be used to replace the html tag
	            $doc->appendChild( clone($v) ); // appending element to the root so we can remove the first element and still have all the others
	        }
	    }
	    // replace the body tag with the first children
	    $doc->replaceChild($doc->firstChild->firstChild->firstChild, $doc->firstChild);
	    return $doc;
	}

	public function api($type, $slug){
		$src = $type == 'plugins' ? $this->plugins_api : $this->themes_api;
		return getPageData($src.$slug);
	}
}