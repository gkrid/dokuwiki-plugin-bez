<?php

if(!defined('DOKU_INC')) die('meh.');
if(!defined('NL')) define('NL',"\n");

function bez_html_array_to_style_list($arr) {
    $output = '';
    foreach ($arr as $k => $v) {
        $output .= $k.': '. $v . ';';
    }
    return $output;
}

function bez_html_irrtable($style) {
    $argv = func_get_args();
    $argc = func_num_args();
    if (isset($style['table'])) {
         $output = '<table style="'.bez_html_array_to_style_list($style['table']).'">';
    } else {
         $output = '<table>';
    }
    
    $tr_style  = '';
    if (isset($style['tr'])) {
        $tr_style = 'style="'.bez_html_array_to_style_list($style['tr']).'"';
    }
    
    $td_style  = '';
    if (isset($style['td'])) {
        $td_style = 'style="'.bez_html_array_to_style_list($style['td']).'"';
    }
   
    $row_max = 0;
    
    for ($i = 1; $i < $argc; $i++) {
        $row = $argv[$i];
        $c = count($row);
        if ($c > $row_max) {
            $row_max = $c;
        }
    }
    
    for ($j = 1; $j < $argc; $j++) {
        $row = $argv[$j];
        $output .= '<tr '.$tr_style.'>' . NL;
        $c = count($row);
        for ($i = 0; $i < $c; $i++) {
            //last element
            if ($i === $c - 1 && $c < $row_max) {
                $output .= '<td '.$td_style.' colspan="' . ( $row_max - $c + 1 ) . '">' . NL;
            } else {
                $output .= '<td '.$td_style.'>' . NL;
            }
            $output .= $row[$i] . NL;
            $output .= '</td>' . NL;
        }
        $output .= '</tr>' . NL;
    }
    $output .= '</table>' . NL;
    return $output;
}