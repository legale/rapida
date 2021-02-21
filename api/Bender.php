<?php
/**
 * JS/CSS Minifier 
 * 
 * 
 */

class Bender
{
	// CSS minifier
	public $cssmin = "cssmin";
	// JS minifier
	public $jsmin = "jshrink";
	// Project's root dir
	public $root_dir;
	
	//source files names hash
	private $srchash = '';
	
	//JS files Array
	private $js_array = array();
	//CSS files Array
	private $css_array = array();
	
	//recombine
	private $recombine = false;


	public function __construct()
	{
		$this->root_dir = defined( 'ROOT_DIR' ) ? ROOT_DIR : $_SERVER['DOCUMENT_ROOT'];
	}
	// Enqueue CSS or Javascript
	public function enqueue( $src )
	{
		if ( !is_array( $src ) )
		{
			$src = array( $src );
		}
		foreach ( $src as $s )
		{
			$s = $this->root_dir . trim($s, '/');
			
			if(!file_exists($s)){
				print __METHOD__." $s not exists!\n";
			}
			switch ( $this->get_ext( $s ) )
			{
				case "css":
					$this->css_array[] = $s;
					break;
				case "js":
					$this->js_array[] = $s;
					break;
			}
		}
	}
	// Minify CSS / Javascripts and write output
	protected function minify( $output_array )
	{
		$ext = $output_array['extension'];
		$scripts = "{$ext}_array";
		$scripts = $this->$scripts;
		//Check src files modify time
		//if check_recombine returns false - skip recombine
		$srchash = $this->check_recombine( $output_array, $scripts ) ;
		
		$this->srchash = $srchash ;
		if( $this->recombine === false ){
			return true;
		}
		

		
		$root = $this->root_dir;
		
		$filename = $output_array['filename'];
		$ext = $output_array['extension'];
		$dirname = $output_array['dirname'];
		$outputfile = "$root$dirname/{$filename}_{$srchash}.$ext"; 

		$str = $this->join_files( $scripts );
			
		switch ( $ext )
		{
			case "css":
				switch ( $this->cssmin )
				{
					case "cssmin":
						require_once realpath( __DIR__ ) . "/Bender/cssmin.php" ;
						$packed = Cssmin::minify( $str );
						break;
					default:
						$packed = $str;
						break;
				}
				break;
			case "js":
				switch ( $this->jsmin )
				{
					case "jshrink":
						require_once realpath( __DIR__ ) . "/Bender/Minifier.php";
						$packed = \JShrink\Minifier::minify($str);
						break;
					default:
						$packed = $str;
						break;
				}
				break;
		}
		
		file_put_contents( $outputfile , $packed );
		return true;
	}

	// Get extension in lowercase
	protected function get_ext( $src )
	{
		return strtolower( pathinfo( $src, 4 ) );
	}

	// Print output for CSS or Javascript
	public function output( $output , $async = true)
	{

		$output_array = pathinfo($output);
		$output_array['extension'] = strtolower($output_array['extension']);

		$this->minify( $output_array );

		$srchash = $this->srchash;


		$filename = $output_array['filename'];
		$dirname = $output_array['dirname'];
		$ext = $output_array['extension'];
		
		$output_new = "$dirname/{$filename}_{$srchash}.$ext";
		

		switch ( $ext )
		{
			case "css":
				return '<link href="' . $output_new . '" rel="stylesheet" type="text/css"/>';
				break;
			case "js":
				$async = $async ? 'async ' : '';
				return '<script '. $async . 'type="text/javascript" src="' . $output_new . '"></script>';
				break;
		}
	}

	// Join array of files into a string
	protected function join_files( $files )
	{
		if ( !is_array( $files ) )
		{
			return "";
		}
		$c = "";
		foreach ( $files as $file )
		{
			$c .= file_get_contents( $file );
		}
		return $c;
	}

	/**
	 * Check if need to recombine output file
	 * @return bool true if recombine needed
	 */
	protected function check_recombine( $output_array, $files )
	{
		$root = $this->root_dir;
		$filename = $output_array['filename'];
		$dirname = $output_array['dirname'];
		$ext = $output_array['extension'];
		$srchash = md5(implode('', $files) );
		
		$outputfile = "$root$dirname/{$filename}_{$srchash}.$ext";
		//if outputfile is not exists set $outfile_mtime = 0
		if ( file_exists( $outputfile ) ){
			$outputfile_mtime = filemtime( $outputfile );
		} else {
			$outputfile_mtime = 0;
			$this->recombine = true;
		}

		
		//check modify time of each source file, if at least 1 file is newer than $outfile - set $this->recombine = true
		foreach ( $files as $file )
		{
			//~ print "mtime bundle " . $outputfile_mtime . " mtime src " . filemtime( $root.$file ) . $root.$file;
			if ( $outputfile_mtime < filemtime( $file ) ){
				$this->recombine = true;
				break;
			}
		}
		return $srchash; 
	}

}
?>
