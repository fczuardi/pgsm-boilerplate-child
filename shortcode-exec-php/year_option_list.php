<?php
$current_year = date('Y');
$oldest_year = 1990;
for ($ano = $oldest_year; $ano <= $current_year; $ano++){
 echo "<option value=\"$ano\">$ano</option>\n";
}
?>