<?php
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'settings.php');
?>
</div><!--/.index[Apache]-->

</div><!--/.wrapper-->

<div class="footer">
	Powered by a <a href="<?php print $plus_settings["system-type:url"]; ?>"><?php print $plus_settings["system-type"]; ?></a> running <a href="<?php print $plus_settings["distribution:url"]; ?>"><?php print $plus_settings["distribution"]; ?></a>. <a href="http://apache.org/"><?php print preg_replace("#^([^\s]+)(.*)$#i", "\\1", $_SERVER["SERVER_SOFTWARE"]); ?></a> patched by <a href="http://adamwhitcroft.com/apaxy/">Apaxy</a><a href="https://github.com/sentfanwyaerda/Apaxy-plus-plus">++</a>
</div><!--/.footer-->
<script>
function inArray(needle, haystack) {
    var length = haystack.length;
    for(var i = 0; i < length; i++) {
        if(haystack[i] == needle) return true;
    }
    return false;
}
function mkButtons(href, history, i){
	if(href[href.length - 1] == '/' || href[0] == '?'){
		var node = document.createTextNode(" ");
	}
	else{
		var node = document.createElement("div");
		node.setAttribute("class","button actions");
		node.setAttribute("style","display: inline; white-space: nowrap; float: right;");
		node.setAttribute("align","right");

		//Edit
		//if(href.substring(href.length - 3) == '.md'){
		if(inArray(href.substring(href.length - 3), ['.md']) || inArray(href.substring(href.length - 4), ['.txt','.htm','.css']) || inArray(href.substring(href.length - 5), ['.html'])){
			node.appendChild( mkEditButton(href) );
		}
		else{
			node.appendChild( document.createTextNode(" ") );
		}

		//Signature
		var j = i + 1;
		//alert( index.getElementsByTagName('tr').length + " >= " + j);
		if( index.getElementsByTagName('tr').length > j ){
			var nextfilename = index.getElementsByTagName('tr')[j].getElementsByTagName('a')[0].getAttribute("href");
			if(nextfilename.substring(nextfilename.length - 4) == '.md5' && nextfilename == href + ".md5"){
				node.appendChild( mkSignatureButton(href) );
				index.getElementsByTagName('tr')[j].setAttribute("class", "hidden");
			}
		}

		//History
		if(history == true){
			node.appendChild( mkHistoryButton(href) );
		}
	}
	return node;
}
function mkEditButton(href){
	var button = document.createElement("a");
	button.setAttribute("href", href + "?edit");
        button.setAttribute("style","display: inline; white-space: nowrap;");
	var image = document.createElement("span");
	image.setAttribute("src","/.theme/icons/source.png");
	image.setAttribute("class","edit button");
	button.setAttribute("title","Edit " + decodeURIComponent( href ) );
	button.appendChild(image);
	return button;
}
function mkHistoryButton(href){
	var button = document.createElement("a");
	button.setAttribute("href", href + "?history");
	button.setAttribute("style","display: inline; white-space: nowrap;");
	var image = document.createElement("span");
	image.setAttribute("src","/.theme/icons/Git-Icon-Black-32px.png");
	image.setAttribute("class","history button");
	image.setAttribute("width","16");
	image.setAttribute("height","16");
	button.setAttribute("title","Inspect the history of " + decodeURIComponent( href ) );
	button.appendChild(image);
	return button;
}
function mkSignatureButton(href){
	var button = document.createElement("a");
	//button.setAttribute("href", href + "?signature=md5");
	button.setAttribute("href", href + ".md5");
	button.setAttribute("style","display: inline; white-space: nowrap;");
	var image = document.createElement("span");
	image.setAttribute("src","/.theme/icons/log.png");
	image.setAttribute("class","signature button");
	image.setAttribute("width","16");
	image.setAttribute("height","16");
	button.setAttribute("title","Inspect the signature of " + decodeURIComponent( href ) );
	button.appendChild(image);
	return button;
}

var index = document.getElementById('index');
//var content = [];
var field = [];
for(var i=0; i < index.getElementsByTagName('tr').length ; i++){
	var filename = index.getElementsByTagName('tr')[i].getElementsByTagName('a')[0].getAttribute("href");
	//content[i] = document.createTextNode(i + ":" + filename);
	//content[i] = mkEditButton(filename);
	field[i] = document.createElement((i == 0 ? "th" : "td"));
	field[i].setAttribute("align","right");
	field[i].appendChild((i == 0 ? document.createTextNode("Actions") : mkButtons(filename, history, i) ));
	index.getElementsByTagName('tr')[i].appendChild(field[i]);

	// fix the Last modified from wrapping
	//index.getElementsByTagName('tr')[i].getElementsByTagName('td')[2].setAttribute("style","white-space: nowrap;");
}



// grab the 2nd child and add the parent class. tr:nth-child(2)
if('<?php $name = basename(rawurldecode($_SERVER["REQUEST_URI"])); print (strlen($name)>0 ? $name : '/'); ?>' != '/'){
        index.getElementsByTagName('tr')[1].className = 'parent';
}
</script>
<style>
.index td { white-space: nowrap; }
</style>
