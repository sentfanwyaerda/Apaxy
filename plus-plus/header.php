<?php
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'settings.php');
function url_navigator($path){
	$original = $path;
	$str = '<a href="'.$path.'">'.$path.'</a>';
		
	$steps = array();
	while(strlen($path) > strlen(dirname($path)) ){
		$steps[] = '/<a'.($path == $original ? ' class="current"' : NULL).' href="'.$path.(substr($path, -1)=='/' ? NULL : '/').'">'.basename($path).'</a>';
		$path = dirname($path);
	}
	#$str .= print_r($steps, TRUE);
	
	$str = NULL;
	for($i=count($steps)-1;$i>=0;$i--){
		$str .= $steps[$i];
	}
	$str = $str.(is_dir($_SERVER["DOCUMENT_ROOT"].$original) && substr($str, -1)=='/' ? NULL : '/');
	$str = '<strong><a href="http://'.$_SERVER["SERVER_NAME"].'/">/</a></strong>'.substr($str, 1);
	return $str;
}
function get_git_info($path){
	$config = NULL;
	$status = NULL;
	$config = `cd $path && git config -l`;
	#$status = `cd $path && git status`;
	#return '<pre>'.$path."\n\n".$config."\n\n".$status.'</pre>';
	if(strlen($config) > 10 ){
		if(!preg_match("#remote.origin.url=([^\s]+)#i", $config, $buffer)){ $buffer = array(1=>'#'); }
		return '<script> var history = true; </script><a class="header-logo-invertocat" href="'.str_replace('.git', '/', $buffer[1]).'"><span class="mega-octicon '.($buffer[1] == '#' ? 'mark-git' : 'octicon-mark-github').'"></span></a>';
		#return $buffer[1];
	}
	else {
		return '<script> var history = false; </script><a class="header-logo-invertocat" href="/"><span class="mega-oction raspberrypi-whitebox"></span></a>';
	}
	return NULL;
}
// PATH_INFO fix
if(!isset($_SERVER["PATH_INFO"])){ $_SERVER["PATH_INFO"] = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH); }

?>
<div class="wrapper">

<?php
if(file_exists(rawurldecode($_SERVER["DOCUMENT_ROOT"].$_SERVER["PATH_INFO"]).'README.md')){
	$name = basename(rawurldecode($_SERVER["PATH_INFO"]));
	print '<small class="navigator"><a href="#index">&darr; go to \'Index of <em>'.(strlen($name)>0 ? $name : '/').'</em>\' &darr;</a></small>';
	$text = file_get_contents(rawurldecode($_SERVER["DOCUMENT_ROOT"].$_SERVER["PATH_INFO"]).'README.md');
	require_once($plus_settings["php-markdown:dir"].DIRECTORY_SEPARATOR.'Michelf'.DIRECTORY_SEPARATOR.'Markdown.php');
	$text = Michelf\Markdown::defaultTransform($text);
	print '<div class="README">'.$text.'</div>';
}
/*git icon*/ print '<div style="float: right;">'.get_git_info($_SERVER["DOCUMENT_ROOT"].$_SERVER["PATH_INFO"]).'</div>';
?>
<!-- we open the `wrapper` element here, but close it in the `footer.html` file -->
	<a name="index"></a><h1>Index of <em><?php $name = basename(rawurldecode($_SERVER["PATH_INFO"])); print (strlen($name)>0 ? $name : '/'); ?></em></h1>
<?php
if(FALSE){
	print '<div class="block">Some TEXT!!</div>';
} else {
	?><small class="navigator"><strong>url:</strong> <em><?php print url_navigator(rawurldecode($_SERVER["PATH_INFO"])); ?></em></small><?php
} 
?>

<div id="index" class="Apache index">
