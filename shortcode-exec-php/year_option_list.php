<?php
$current_year = date("Y");
$oldest_year = 1990;
extract(shortcode_atts(array("selected" => null), $atts));
if($selected == "first"){
  $selected = $oldest_year;
}

if($selected == "last"){
  $selected = $current_year;
}

for ($ano = $oldest_year; $ano <= $current_year; $ano++){
 echo "<option value=\\"$ano\\"";
 if ($ano == $selected){
   echo " selected=\\"selected\\"";
 }
 echo ">$ano</option>\\n";
}
?>
