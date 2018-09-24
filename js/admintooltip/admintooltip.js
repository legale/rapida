"use strict";
$(function() {
	"use strict";
	$("<a href='simpla/' class='admin_bookmark'></a>").appendTo('body');
	let tooltip = $("<div class='tooltip'><div class='tooltipHeader'></div><div class='tooltipBody'></div><div class='tooltipFooter'></div></div>").appendTo($('body'));		
	$('.tooltip').live('mouseleave', function(){tooltipcanclose=true;setTimeout("close_tooltip();", 300);});
	$('.tooltip').live('mouseover', function(){tooltipcanclose=false;});
	
	$('[data-page]').live('mouseover', show_tooltip);	
	$('[data-category]').live('mouseover', show_tooltip);
	$('[data-brand]').live('mouseover', show_tooltip);
	$('[data-product]').live('mouseover', show_tooltip);
	$('[data-post]').live('mouseover', show_tooltip);
	$('[data-feature]').live('mouseover', show_tooltip);
});
 
