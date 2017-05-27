<?php
$default_top1 =81;
$default_top2 =132;

$top1 = $default_top1 + ($numGroup-1) * 204;
$top2 = $default_top2 + ($numGroup-1) * 204;

$top = $default_top1 + ($numGroup-1) * 204;


?>


<div class="vertical-connector" style="top: {{ $top }}px; left: 168px; height: 54px;"></div>
<div class="horizontal-connector" style="top: {{ $top2 }}px; left: 150px;"></div>
<div class="horizontal-connector" style="top: {{ $top }}px; left: 170px;"></div>
