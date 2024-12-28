<?php

// Author: de77
// Licence: MIT
// First-version: 9.02.2010
// Version: 24.08.2010
// http://de77.com

function rle_decode($data, $datalen, $pixel_size)
{
    $len = strlen($data);

    $out = '';

    $i = 0;
    $k = 0;
    while ($i<$len)
    {
        dec_bits(ord($data[$i]), $type, $value);
        if ($k >= $datalen)
        {
            break;
        }

        $i++;

        if ($type == 0) //raw
        {
            for ($j=0; $j<$pixel_size*$value; $j++)
            {
                $out .= $data[$j+$i];
                $k++;           
            }
            $i += $value*$pixel_size;
        }
        else //rle
        {
            for ($j=0; $j<$value; $j++)
            {
                $out .= $data[$i] . $data[$i+1] . $data[$i+2];
                if ($pixel_size == 4) $out .= $data[$i+3];
                $k++;               
            }
            $i += $pixel_size;
        }   
    }
    return $out;
}

function dec_bits($byte, &$type, &$value)
{
    $type = ($byte & 0x80) >> 7;
    $value = 1 + ($byte & 0x7F);
}

function getimagesizetga($filename)
{
    $f = fopen($filename, 'rb');
    $header = fread($f, 18);
    $header = @unpack(  "cimage_id_len/ccolor_map_type/cimage_type/vcolor_map_origin/vcolor_map_len/" .
                        "ccolor_map_entry_size/vx_origin/vy_origin/vwidth/vheight/" .
                        "cpixel_size/cdescriptor", $header);
    fclose($f);

    $types = array(0,1,2,3,9,10,11,32,33);      
    if (!in_array($header['image_type'], $types))
    {
        return array(0, 0, 0, 0, 0);
    }

    if ($header['pixel_size'] > 32)
    {
        return array(0, 0, 0, 0, 0);
    }

    return array($header['width'], $header['height'], 'tga', $header['pixel_size'], $header['image_type']);
}

function imagecreatefromtga2($filename)
{
    $f = fopen($filename, 'rb');
    if (!$f)
    {
        return false;
    }
    $header = fread($f, 18);
    $header = unpack(   "cimage_id_len/ccolor_map_type/cimage_type/vcolor_map_origin/vcolor_map_len/" .
                        "ccolor_map_entry_size/vx_origin/vy_origin/vwidth/vheight/" .
                        "cpixel_size/cdescriptor", $header);

    switch ($header['image_type'])
    {
        case 2:     echo "";//no palette, uncompressed
        case 10:    //no palette, rle
                    break;
        default:    die('Unsupported TGA format');                  
    }

    if ($header['pixel_size'] != 24 && $header['pixel_size'] != 32)
    {
        die('Unsupported TGA color depth'); 
    }

    $bytes = $header['pixel_size'] / 8;

    if ($header['image_id_len'] > 0)
    {
        $header['image_id'] = fread($f, $header['image_id_len']);
    }
    else
    {
        $header['image_id'] = '';   
    }

    $im = imagecreatetruecolor($header['width'], $header['height']);

    $size = $header['width'] * $header['height'] * $bytes;

    //-- check whether this is NEW TGA or not
    $pos = ftell($f);
    fseek($f, -26, SEEK_END);   
    $newtga = fread($f, 26);
    if (substr($newtga, 8, 16) != 'TRUEVISION-XFILE')
    {
        $newtga = false;
    }

    fseek($f, 0, SEEK_END);
    $datasize = ftell($f) - $pos; 
    if ($newtga)
    {
        $datasize -= 26;
    }

    fseek($f, $pos, SEEK_SET);

    //-- end of check
    $data = fread($f, $datasize);
    if ($header['image_type'] == 10)
    {
        $data = rle_decode($data, $size, $bytes);                   
    }
    if (bit5($header['descriptor']) == 1)
    {
        $reverse = true;    
    }
    else
    {
        $reverse = false;
    }    


    $i = 0;
    $pixels = str_split($data, $bytes);

    //read pixels 
    if ($reverse)
    {   
        for ($y=0; $y<$header['height']; $y++)
        {       
            for ($x=0; $x<$header['width']; $x++)
            {
                imagesetpixel($im, $x, $y, dwordize($pixels[$i]));
                $i++;
            }
        }
    }
    else
    {
        for ($y=$header['height']-1; $y>=0; $y--)
        {       
            for ($x=0; $x<$header['width']; $x++)
            {
                imagesetpixel($im, $x, $y, dwordize($pixels[$i]));
                $i++;
            }
        }
    }
    fclose($f);         

    return $im;
}

function dwordize($str)
{
    $a = ord($str[0]);
    $b = ord($str[1]);
    $c = ord($str[2]);
    return $c*256*256 + $b*256 + $a;
}

function bit5($x)
{
    return ($x & 32) >> 5;  
}