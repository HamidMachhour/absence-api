<?php

 function customImageUrl($width, $height, $color, $text,$domain='http://0.0.0.0:5000')
 {
     $color = ltrim($color, '#'); // Remove the '#' symbol from the hex color if present
     $url = "{$domain}/{$width}x{$height}/{$color}.png/?text={$text}";
     return $url;
 }

 function imageToBase64($path)
 {
     if (file_exists($path) && is_readable($path)) {
         $data = file_get_contents($path);
         $type = pathinfo($path, PATHINFO_EXTENSION);
         
         // Correctly handle the SVG MIME type
         if (strtolower($type) === 'svg') {
             return 'data:image/svg+xml;base64,' . base64_encode($data);
         }
         
         // Handle other image types
         return 'data:image/' . $type . ';base64,' . base64_encode($data);
     }
     return '';
 }