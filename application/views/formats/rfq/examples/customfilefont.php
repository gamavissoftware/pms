<?php
require_once('tcpdf_include.php');

$fontFile = 'path/to/Arial.ttf';
$fontFileBold = 'path/to/ArialBold.ttf';
$fontFileItalic = 'path/to/ArialItalic.ttf';
$fontFileBoldItalic = 'path/to/ArialBoldItalic.ttf';

$fontname = TCPDF_FONTS::addTTFfont($fontFile, 'TrueTypeUnicode', '', 96);
$fontnameBold = TCPDF_FONTS::addTTFfont($fontFileBold, 'TrueTypeUnicode', '', 96);
$fontnameItalic = TCPDF_FONTS::addTTFfont($fontFileItalic, 'TrueTypeUnicode', '', 96);
$fontnameBoldItalic = TCPDF_FONTS::addTTFfont($fontFileBoldItalic, 'TrueTypeUnicode', '', 96);

echo "Font added: $fontname\n";
echo "Font added: $fontnameBold\n";
echo "Font added: $fontnameItalic\n";
echo "Font added: $fontnameBoldItalic\n";
?>