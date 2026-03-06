<?php
function sentenceCase($string) { 
    $sentences = preg_split('/([.?!]+)/', $string, -1,PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE); 
    $newString = ''; 
    foreach ($sentences as $key => $sentence) { 
        $newString .= ($key & 1) == 0? 
            ucfirst(strtolower(trim($sentence))) : 
            $sentence.' '; 
    } 
    return trim($newString); 
}
?>

