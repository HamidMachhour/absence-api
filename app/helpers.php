<?php

 function customImageUrl($width, $height, $color, $text,$domain='http://127.0.0.1:5000')
 {
     $color = ltrim($color, '#'); // Remove the '#' symbol from the hex color if present
     $url = "{$domain}/{$width}x{$height}/{$color}.png/?text={$text}";
     return $url;
 }
