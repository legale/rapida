<?php /* Smarty version Smarty-3.1.18, created on 2017-10-20 20:36:22
         compiled from "simpla\design\html\stats.tpl" */ ?>
<?php /*%%SmartyHeaderCode:23816366559ea34161da970-55656153%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5d5b58316cbc293f487cbf50244e2793024e0fb1' => 
    array (
      0 => 'simpla\\design\\html\\stats.tpl',
      1 => 1492708202,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '23816366559ea34161da970-55656153',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'currency' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.18',
  'unifunc' => 'content_59ea34162c4fb8_95113837',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_59ea34162c4fb8_95113837')) {function content_59ea34162c4fb8_95113837($_smarty_tpl) {?><?php $_smarty_tpl->_capture_stack[0][] = array('tabs', null, null); ob_start(); ?>
		<li class="active"><a href="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0][0]->url_modifier(array('module'=>'StatsAdmin'),$_smarty_tpl);?>
">Статистика</a></li>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
<?php $_smarty_tpl->tpl_vars['meta_title'] = new Smarty_variable('Статистика', null, 1);
if ($_smarty_tpl->parent != null) $_smarty_tpl->parent->tpl_vars['meta_title'] = clone $_smarty_tpl->tpl_vars['meta_title'];?>



<script src="design/js/highcharts/js/highcharts.js" type="text/javascript"></script>

<script>
var chart;

$(function() {


var options = {
	chart: {
		zoomType: 'x',
		renderTo: 'container',
		defaultSeriesType: 'area'
	},
	title: {
		text: 'Статистика заказов'
	},
	subtitle: {
		text: ''
	},
	xAxis: {
		type: 'datetime',
		minRange: 7 * 24 * 3600000,
		maxZoom: 7 * 24 * 3600000,
		gridLineWidth: 1,
		ordinal: true,
		showEmpty: false
	},
	yAxis: {
		title: {
			text: '<?php echo $_smarty_tpl->tpl_vars['currency']->value->name;?>
'
		}
	},

 
	plotOptions: {
		line: {
			dataLabels: {
				enabled: true
			},
			enableMouseTracking: true,
			connectNulls: false
		},
		area: {
			marker: {
                        enabled: false
            },		
		}
	},
	series: []

};

$.get('ajax/stat/stat.php', function(data){
	var series = {
		data: []
	};
	
	console.log(data);
	
var minDate = Date.UTC(data[0].year, data[0].month-1, data[0].day),
    maxDate = Date.UTC(data[data.length-1].year, data[data.length-1].month-1, data[data.length-1].day);

var newDates = [], currentDate = minDate, d;

while (currentDate <= maxDate) {
    d = new Date(currentDate);
    newDates.push((d.getMonth() + 1) + '/' + d.getDate() + '/' + d.getFullYear());
    currentDate += (24 * 60 * 60 * 1000); // add one day
}

console.log(newDates);	
	
	series.name = 'Сумма заказов, <?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
';

	// Iterate over the lines and add categories or series
	$.each(data, function(lineNo, line) {
		series.data.push([Date.UTC(line.year, line.month-1, line.day), parseInt(line.y)]);
	});
	//
	options.series.push(series);
	
	// Create the chart
	var chart = new Highcharts.Chart(options);
	});
	
 

});
 

 
Highcharts.theme = {
   colors: ["#DDDF0D", "#7798BF", "#55BF3B", "#DF5353", "#aaeeee", "#ff0066", "#eeaaee", 
      "#55BF3B", "#DF5353", "#7798BF", "#aaeeee"],
   chart: {
      backgroundColor: {
         linearGradient: [0, 0, 0, 400],
         stops: [
            [0, 'rgb(96, 96, 96)'],
            [1, 'rgb(16, 16, 16)']
         ]
      },
      borderWidth: 0,
      borderRadius: 15,
      plotBackgroundColor: null,
      plotShadow: false,
      plotBorderWidth: 0
   },
   title: {
      style: { 
         color: '#FFF',
         font: '16px Lucida Grande, Lucida Sans Unicode, Verdana, Arial, Helvetica, sans-serif'
      }
   },
   subtitle: {
      style: { 
         color: '#DDD',
         font: '12px Lucida Grande, Lucida Sans Unicode, Verdana, Arial, Helvetica, sans-serif'
      }
   },
   xAxis: {
      gridLineWidth: 0,
      lineColor: '#999',
      tickColor: '#999',
      labels: {
         style: {
            color: '#999',
            fontWeight: 'bold'
         }
      },
      title: {
         style: {
            color: '#AAA',
            font: 'bold 12px Lucida Grande, Lucida Sans Unicode, Verdana, Arial, Helvetica, sans-serif'
         }            
      }
   },
   yAxis: {
      alternateGridColor: null,
      minorTickInterval: null,
      gridLineColor: 'rgba(255, 255, 255, .1)',
      lineWidth: 0,
      tickWidth: 0,
      labels: {
         style: {
            color: '#999',
            fontWeight: 'bold'
         }
      },
      title: {
         style: {
            color: '#AAA',
            font: 'bold 12px Lucida Grande, Lucida Sans Unicode, Verdana, Arial, Helvetica, sans-serif'
         }            
      }
   },
   legend: {
      itemStyle: {
         color: '#CCC'
      },
      itemHoverStyle: {
         color: '#FFF'
      },
      itemHiddenStyle: {
         color: '#333'
      }
   },
   labels: {
      style: {
         color: '#CCC'
      }
   },
   tooltip: {
      backgroundColor: {
         linearGradient: [0, 0, 0, 50],
         stops: [
            [0, 'rgba(96, 96, 96, .8)'],
            [1, 'rgba(16, 16, 16, .8)']
         ]
      },
      borderWidth: 0,
      style: {
         color: '#FFF'
      }
   },
   
   
   plotOptions: {
      line: {
         dataLabels: {
            color: '#CCC'
         },
         marker: {
            lineColor: '#333'
         }
      },
      spline: {
         marker: {
            lineColor: '#333'
         }
      },
      scatter: {
         marker: {
            lineColor: '#333'
         }
      }
   },
   
   toolbar: {
      itemStyle: {
         color: '#CCC'
      }
   },
   
   navigation: {
      buttonOptions: {
         backgroundColor: {
            linearGradient: [0, 0, 0, 20],
            stops: [
               [0.4, '#606060'],
               [0.6, '#333333']
            ]
         },
         borderColor: '#000000',
         symbolStroke: '#C0C0C0',
         hoverSymbolStroke: '#FFFFFF'
      }
   },
   
   exporting: {
      buttons: {
         exportButton: {
            symbolFill: '#55BE3B'
         },
         printButton: {
            symbolFill: '#7797BE'
         }
      }
   },   
   
   // special colors for some of the demo examples
   legendBackgroundColor: 'rgba(48, 48, 48, 0.8)',
   legendBackgroundColorSolid: 'rgb(70, 70, 70)',
   dataLabelsColor: '#444',
   textColor: '#E0E0E0',
   maskColor: 'rgba(255,255,255,0.3)'
};

// Apply the theme
var highchartsOptions = Highcharts.setOptions(Highcharts.theme); 
 
</script>

 
 
<div>
<div id='container'>
</div>
 
</div>
<?php }} ?>
