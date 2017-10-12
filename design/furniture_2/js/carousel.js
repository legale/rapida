jQuery.fn.prepare_slider = function(){	
		var x_pos = 0;
		var li_items_n = 0;	
		var right_clicks = 0;		
		var left_clicks = 0;					
		var li_col = jQuery("#slider_list > li");		
		var li_width = li_col.outerWidth(true);		
		var viewWindow = 4;
		
		li_col.each(function(index){			
			x_pos += jQuery(this).outerWidth(true);
			li_items_n++;								
		})	
		
		right_clicks = li_items_n - viewWindow;
		total_clicks = li_items_n - viewWindow;		
		
		jQuery('#slider_list').css('position','relative');
		jQuery('#slider_list').css('left','0px');
		jQuery('#slider_list').css('width', x_pos+'px');
		jQuery('#slider_list li:last').addClass ('last');   
		jQuery('#slider_list li:first').addClass ('first'); 
		
		var is_playing = false;
		var completed = function() { is_playing = false; }

		jQuery('#left_but').click( function(){													
			cur_offset = jQuery('#slider_list').position().left;
			if (!is_playing){						
				if (left_clicks > 0) {
						is_playing = true; jQuery('#slider_list').animate({'left': cur_offset + li_width + 'px'}, 700, "linear", completed); 
						right_clicks++; 
						left_clicks--;
					} 
					else {
						is_playing = true;
						jQuery('#slider_list').animate({'left':    -li_width*total_clicks	+ 'px'}, 700, "linear", completed); 
						right_clicks = 0;
						left_clicks = total_clicks;
					}
			}
		});		

		jQuery('#right_but').click( function(){
			if (!is_playing){			
				cur_offset = jQuery('#slider_list').position().left;			
			 	if (right_clicks > 0) {
						is_playing = true; 
						jQuery('#slider_list').animate({'left': cur_offset - li_width + 'px'},700, "linear", completed );
						right_clicks--; left_clicks++; 
				} 
				else { 
						is_playing = true; jQuery('#slider_list').animate({'left':    0	+ 'px'},700, "linear", completed ); 
						left_clicks = 0;
						right_clicks = total_clicks;
					}			 
			}
		});	
		
	}

jQuery.fn.over = function(){						
	jQuery(this).hover(
	   function () {
	 	 jQuery(this).addClass("over");
	   },
	   function () {
	 	 jQuery(this).removeClass("over");
	   }
	 );		
   }
jQuery.fn.intro = function(){						
	var slider_link = jQuery('.slider .box-right');
	var slider_links = jQuery('.slider .box-right, .slider .box-left');
	var slider_link_index = 1;
	var slider_count = jQuery('#slider_list > li').size();	
	var is_pressed = false;
	var focus_flag = true;
	function slider_intro(){			
		if(is_pressed == false && focus_flag == true){	
			if(slider_link_index <= slider_count){
				slider_link.trigger('click');
				slider_link_index++;
				setTimeout(function(){slider_intro()}, 7000); //select change time
			}	
		} else{
			setTimeout(function(){slider_intro()}, 7000); //select change time
			is_pressed = false;		
		}		
	}		
	setTimeout(function(){slider_intro()}, 7000)
	
	slider_links.each(function(){
		jQuery(this).bind('mouseup', function(){
			is_pressed = true;
			focus_flag = true;
		});
	});		
	jQuery(window).blur(function(){
	 focus_flag = false;	
	  }).focus(function(){
	 focus_flag = true;
  	});

} ;  
/****************** Height col, name ************/
document.observe('dom:loaded', function(){ 
	var home_blocks = $$('.box-top');
	home_blocks.each(function(p){	
		var grids = p.select('#slider_list');
		grids.each(function(n){
				var columns = n.select('li');					
				var max_height = 0;															
				columns.each(function(m){														
					if( m.getHeight() >  max_height ){
						max_height = m.getHeight();
					}						
				});		
				var boxes = n.select('li .product-name');
				boxes.each(function(b){			
					var this_column = b.up('li');
					var box_indent = this_column.getHeight() - b.getHeight();						
					b.setStyle({
						height: max_height - box_indent + 'px'
					});					
				 });
			});
	});
});

/****************** slider_list instal ************/
jQuery(window).bind('load', function(){
		jQuery().prepare_slider(); 
		jQuery().intro();
		jQuery('#slider_list > li').over();		
});	
