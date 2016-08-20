<?php
/**
* @package plugin_niftybox
* @copyright Copyright (C) 2007 Prana. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

/*  Author: Prana - www.prana.bz  */
/*  Any questions? You can e-mail info@prana.bz */
/*  Author: Prana - www.prana.bz */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$mainframe->registerEvent('onPrepareContent', 'botniftyBox');

// Define local options
	$boxincrement = 0;
	$columnincrement = 0;
	$totalcolumncount = 0;
	$columnbreakincrement = 0;
	$currentcolumn_width = 0;
	$array_columnbreak_foreach_column = array();
	$parent_key_and_values = array();
   $is_gecko = false;


function botniftybox( &$row, &$params, $page=0 ) {
  global $mosConfig_session_type, $mainframe,
  $database,$boxincrement,$array_columnbreak_foreach_column,$columnincrement, $is_gecko;

$mosConfig_absolute_path = JPATH_SITE;
$mosConfig_live_site = JURI :: base();
		
  global $opt_bgcolor,$opt_padding,$opt_boxfloat,$opt_boxcornersize,$opt_boxclearspace, $opt_columncontainerwidth,$opt_columngap, $opt_bordercolor;

	/* Simple check for performance sake moved up here */
  	$is_there_a_box 	= strpos( $row->text, 'niftybox' );
  	$is_there_a_column 	= strpos( $row->text, 'niftycolumn' );
	if ( $is_there_a_box === false && $is_there_a_column === false){
		return true;
	}  /* 2. Check - if not published, just erase the niftybox bot tags */
             /*
  	if ( !$published ) {
		$row->text = preg_replace('/{niftybox\s*.*?}|{\/niftybox\s*.*?}/i', '', $row->text );
		$row->text = preg_replace('/{niftycolumn\s*.*?}|{\/niftycolumn\s*.*?}/i', '', $row->text );
		return true;
	}
             */
  $is_gecko = (strpos($_SERVER['HTTP_USER_AGENT'],"Geck") > 0);

  $mambot = & JPluginHelper::getPlugin('content', 'niftybox');

  if ($mambot)
  {
	  $botParams = new JParameter( $mambot->params );
	  $opt_bgcolor					= $botParams->def( "backgroundcolor", "navajowhite" );
	  $opt_padding					= $botParams->def( 'padding', "10px" );
	  $opt_bordercolor				= $botParams->def( 'bordercolor', "white" );
	  $opt_boxfloat					= $botParams->def( "boxfloat", "center" );
	  $opt_boxcornersize			= $botParams->def( "boxcornersize", "normal" );
	  $opt_boxclearspace			= $botParams->def( "boxclearspace", "none" );
	  $opt_columncontainerwidth	= $botParams->def( "columncontainerwidth", "100%" );
	  $opt_columngap				= $botParams->def( "columngap", "20px" );
	  if ($is_gecko)
	  {
	  	$is_gecko = 4;
	  }
  } else print("Niftybox is unregistered - perhaps your database is in error. prana.bz");
  /* 3. Add CSS header */
 static $niftybox_css_loaded = 0;
  if ($niftybox_css_loaded != 1)
  {
	$header = "<link href=\"$mosConfig_live_site/plugins/content/niftybox/niftybox.css\" rel=\"stylesheet\" type=\"text/css\" />";
	$niftybox_css_loaded = 1;
  	$mainframe->addCustomHeadTag($header);
  }
	if ( $is_there_a_box) 
	{
   //		$regex = '/{niftybox\s*([^\{]*)?}\s*(.+?)\s*\{\/niftybox\s*\}/i';
        $regex =  "#{niftybox\s*([^\{]*)?}(.*?){/niftybox}#is";

		$row->text = preg_replace_callback( $regex, 'botniftybox_createbox', $row->text );
    }
     ///////////////////////////////////////////////////////////////////
	if ($is_there_a_column)
	{
	  //  $regex = '/{niftycolumn\s*([^\{]*)?}\s*(.+?)\s*\{\/niftycolumn\s*\}/i';
        $regex =  "#{niftycolumn\s*([^\{]*)?}(.*?){/niftycolumn}#is";

		$row->text = preg_replace_callback( $regex, 'botniftybox_createcolumn', $row->text );
	} // endif ($is_there_a_column)
	/////////////////////////////////////////
	 return true;
}
 

function get_options_from_user ($nifty_options)
// Translate the user option string into array with keys and values
{
	$nifty_options_exploded = explode(',', $nifty_options, 10);
	for ($i = 0; $i <count($nifty_options_exploded);$i++)
	{
		list ($key,$value) = explode("=", $nifty_options_exploded[$i]);
		$key = ltrim($key);
		$key_and_values[$key] = substr($value, 0, 35); // maximum: 20 characters to prevent abuse? - is it necessary?
	};
	return $key_and_values;
}

function process_standard_options ($key_and_values)
// Process standard options - color, background, font, width, height, etc
{
	global $opt_padding,$opt_bgcolor,$opt_bordercolor;
	if (!isset($key_and_values['border'])) $key_and_values['border']=$opt_bordercolor;
	if (!isset($key_and_values['background'])) $key_and_values['background']=$opt_bgcolor;
	if (!isset($key_and_values['padding'])) $key_and_values['padding']=$opt_padding;
	if (!isset($key_and_values['width'])) $key_and_values['width']="100%";
	if (!isset($key_and_values['float']))  $key_and_values['float'] = 'left';
	return $key_and_values;
}

function roundtop($the_background, $the_edge)
{
 return "<b class=\"niftyroundbox_b\">
  <b class=\"niftyroundbox_1\" style=\"background:$the_edge; \"></b>
  <b class=\"niftyroundbox_2\" style=\"background:$the_background;  border-color: $the_edge;\"></b>
  <b class=\"niftyroundbox_3\" style=\"background:$the_background;  border-color: $the_edge;\"></b>
  <b class=\"niftyroundbox_4\" style=\"background:$the_background;  border-color: $the_edge;\"></b>
 </b>
";
}

function roundbottom($the_background, $the_edge)
{
 return "<b class=\"niftyroundbox_b\">
  <b class=\"niftyroundbox_4\" style=\"background:$the_background;border-color: $the_edge; \"></b>
  <b class=\"niftyroundbox_3\" style=\"background:$the_background;border-color: $the_edge; \"></b>
  <b class=\"niftyroundbox_2\" style=\"background:$the_background;border-color: $the_edge; \"></b>
  <b class=\"niftyroundbox_1\" style=\"background:$the_edge; \"></b>
 </b>";
}


function get_column_style ($key_and_values)
{
	$style2="";
	if (isset($key_and_values['textcolor']))  $style2 .= "color: ".$key_and_values['textcolor'].";";
	if (isset($key_and_values['font']))  $style2 .= "font-family:".$key_and_values['font'].";";
	if (isset($key_and_values['border']))  
	{
		$style2 .= "border-left:1px solid ".$key_and_values['border'].";";
		$style2 .= "border-right:1px solid ".$key_and_values['border'].";";
	}
	$style2.= (isset($key_and_values['fontsize']) ? "font-size: ".$key_and_values['fontsize'].";" : '');

	return $style2;
}

function get_content_style ($key_and_values)
{
	$style2="";
	if (isset($key_and_values['textcolor']))  $style2 .= "color:".$key_and_values['textcolor'].";";
	if (isset($key_and_values['padding']))  $style2 .= "margin:0 ".$key_and_values['padding'].";";
	if (isset($key_and_values['background']))  $style2 .= "background:".$key_and_values['background'].";";
	if (isset($key_and_values['font']))  $style2 .= "font-family:".$key_and_values['font'].";";
	$style2.= (isset($key_and_values['fontsize']) ? "font-size: ".$key_and_values['fontsize'].";" : '');
	$style2.= (isset($key_and_values['textalign']) ? "text-align: ".$key_and_values['textalign'].";" : 'text-align:justify;');
	return $style2;
}

function botniftybox_createbox (&$match)
// Create box. 
{
	global $mainframe, $is_gecko, $opt_padding;

	/* 1. Get Options */
	$key_and_values = get_options_from_user ($match[1]);
	$key_and_values = process_standard_options($key_and_values);

	$the_background=$key_and_values['background'];
	$the_edge=$key_and_values['border'];
	//Wrapper
	switch ($key_and_values['float'])
	{
		case "left":
		case "right":if (array_key_exists('clear',$key_and_values)) $the_clear = $key_and_values['clear']; else $the_clear = '';
		$html ="<div style=\"margin:0 4px;float:{$key_and_values['float']};width:{$key_and_values['width']};clear:{$the_clear};\">";break;
		default:	$html ="<div align=\"center\" style=\"margin:0 4px;clear:both;width:".$key_and_values['width']."\">";break;
	}
	// eof wrapper
 	$html.= roundtop($the_background, $the_edge);
 	$html.= "<div class=\"niftyroundbox_c\" style=\"background: $the_background; border-color: $the_edge;\"><b class=\"niftyroundbox_s\"></b>";
	// begin content //
 	$html.= "<div  style=\"".get_content_style($key_and_values)."\">".$match[2]." </div>";
	// eof content //
	$html.= "<b class=\"niftyroundbox_s\"></b></div>";
	$html.= roundbottom($the_background, $the_edge);
 	$html.= "</div>";

	return $html;
}

function botniftybox_niftynextcolumn (&$match)
// Create 2nd column and so forth ...
{
	global $parent_key_and_values,$columnincrement, $columnbreakincrement,$mainframe,$currentcolumn_width,$totalcolumncount,$opt_columngap, $is_gecko,$opt_padding, $column_key_and_values;

	$columnbreakincrement++;
	if (empty($match[1]))
	{	
		$key_and_values = $parent_key_and_values;
	}	else $key_and_values = array_merge($parent_key_and_values,get_options_from_user ($match[1]));
	$the_columngap = $opt_columngap;
	$column_key_and_values[$columnbreakincrement] = $key_and_values;
	$key_and_values['width'] = $currentcolumn_width."%";		
	$key_and_values['float'] = "left";	

//	$style = process_standard_options($key_and_values);
 	//$style.= "width:$currentcolumn_width%;float:".$key_and_values['float'].";margin:0 $the_columngap 0 0;";
	$curcol = $columnbreakincrement + 1;
$html = "</div></td><td width=\"1%\" valign=\"top\">&nbsp;</td><td valign=\"top\" style=\"".get_column_style ($key_and_values)."\" bgcolor=\"".$key_and_values['background']."\"><div style=\"padding:0 $opt_padding;\">";
	return $html;
}


function botniftybox_createcolumn (&$match)
// Create manual multi-column layout
{
	global $columnincrement,$array_columnbreak_foreach_column,$mainframe,
	$columnbreakincrement,$currentcolumn_width,$totalcolumncount,$parent_key_and_values,
	$opt_columngap,$opt_columncontainerwidth, $is_gecko,$opt_padding,$column_key_and_values;
	$columnbreakincrement  = 0;
	/* 1. Increment */
	$columnincrement++;
	/* 2. Get Options */
	$key_and_values = get_options_from_user ($match[1]);
	$key_and_values = process_standard_options($key_and_values);
	$text_tobe_processed = $match[2];
	/* 3. Make the thing */
	////// Process column break ///////

	$regex = '/{niftynextcolumn\s*([^\{]*)?}/i';
	//$regex = '/{niftynextcolumn\s*.*?}/i';	

	$totalcolumncount =   preg_match_all($regex, $text_tobe_processed, $unused_array); // how many niftynextcolumn?
	$currentcolumn_width = floor ( (100 - $totalcolumncount) / ($totalcolumncount + 1)) ;

	// Set so that other columns follow the current style
	$parent_key_and_values = $key_and_values;
	if (strpos($text_tobe_processed, 'niftynextcolumn' ))
	{
		  /* 4. Let's perform the replacement */
		$text_tobe_processed = preg_replace_callback( $regex, 'botniftybox_niftynextcolumn', $text_tobe_processed);
	} // endif ($is_there_a_box)

	$the_background=$key_and_values['background'];
	$the_edge=$key_and_values['border'];
	$html = "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"clear:both\"><tr>\n";	
    $column_key_and_values[0] = $key_and_values;
	for ($i = 1; $i <=  $totalcolumncount+1; $i++)
	{
		$the_background=$column_key_and_values[$i-1]['background'];
		$the_edge=$column_key_and_values[$i-1]['border'];

		$html.=  "<td width=\"$currentcolumn_width%\" valign=\"bottom\">";
	 	$html.= roundtop($the_background, $the_edge);
		//$html.= "<b class=\"niftyroundbox_s\" style=\"background: $the_background\"></b>";
		$html.= "</td>";
    	if ($i < $totalcolumncount+1)  $html.="<td width=\"1%\" valign=\"top\"></td>";
	}
	$html.="</tr><tr><td valign=top bgcolor=\"".$key_and_values['background']."\"  style=\"".get_column_style ($key_and_values)."\" >"; // end top row
	// Add content
 $html.= "<div style=\"padding:0 $opt_padding;\">".$text_tobe_processed."</div>";
	// eof content //

	// Add last rounded corner for the bottom
	$html.="</td></tr><tr>";
	for ($i = 1; $i <=  $totalcolumncount+1; $i++)
	{
		$the_background=$column_key_and_values[$i-1]['background'];
		$the_edge=$column_key_and_values[$i-1]['border'];

		$html.=  "<td width=\"$currentcolumn_width%\" valign=\"bottom\">";
	//	$html.= "<b class=\"niftyroundbox_s\" style=\"background: $the_background\"></b>";

		$html.= roundbottom($the_background, $the_edge);
	 	$html.= "</td>";
    	if ($i < $totalcolumncount+1)  $html.= "<td width=\"1%\" valign=\"top\"></td>";
	}
	$html.="</tr></table>";
	return $html;

}


?>