
<?
	include "colortable.php";
	
	function print_table($item2, $key)
	{
    	echo "<tr>";
	    echo "<td bgcolor=$key><font color=\"white\">$key</font></td><td bgcolor=$key><font color=\"white\">$item2</font></td>";
	    echo "<td bgcolor=$key>$key</td><td bgcolor=$key>$item2</td>";
		echo "<td><font color=$key>$key</font></td><td><font color=$key>$item2</font></td>";
		echo "<td bgcolor=\"black\"><font color=$item2>$key</font></td><td  bgcolor=\"black\"><font color=$item2>$item2</font></td>";		

		echo "</tr>\n\n";
	}
	

	echo "<HTML><HEAD><TITLE>NIFTY COLOR TABLE - COMPLETE CHART</TITLE></HEAD>";
	echo "<style type=\"text/css\" media=\"screen\">\n";
	echo "body ,table,td,tr,p{font:12px 'Lucida Grande','Trebuchet MS',Arial,Helvetica}</style>";
	echo "<BODY>";
	echo "<a name=\"top\" id=\"top\"></a>";
	echo "<TABLE border=0 cellspacing=0 cellpadding=5 align=center>";
	echo "<caption align=center>These are safe NIFTYBOX colors that you can specify in your box/column. This list has been tested with Safari, MSIE 6 & 7, Opera 9.x, Firefox 1.5 and 2.x, Seamonkey 1.11, Camino 1.x.<br/><br/>You can use the color table to find the easiest match of border and background color. As the HTML-rendered boxes are naturally non-anti-aliased, your best bet is to find a color combination of light & dark colors to make the border looks smoother. e.g: background = greenyellow, border=chartreuse</caption>";

	echo " <tr>
    <th colspan=\"2\" bgcolor=\"#CCFF66\">White on Color Background </th>
    <th colspan=\"2\" bgcolor=\"#CCFF66\">Black  on Color Background </th>
    <th colspan=\"2\" bgcolor=\"#CCFF66\">Color on Bright Background </th>
    <th colspan=\"2\" bgcolor=\"#CCFF66\">Color on Dark Background </th>
  </tr>";
	array_walk($nifty_color_table, 'print_table');
	echo "</table><center><p>Credit: <A HREF=\"http://halflife.ukrpack.net/csfiles/help/colors.shtml\">http://halflife.ukrpack.net/csfiles/help/colors.shtml</a>, with royalblue entry corrected (April 20, 2007)<br/><br/><a href=\"#top\">Back to Top</a></center></body></html>";

?>