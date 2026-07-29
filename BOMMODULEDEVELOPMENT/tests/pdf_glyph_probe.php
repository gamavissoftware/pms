<?php
/**
 * pdf_glyph_probe.php
 *
 * TCPDF core fonts (helvetica and friends) are NOT Unicode. Three
 * characters this module depends on are affected:
 *
 *   Ω  U+03A9  in "DYNAMIC BRAKING RESISTOR (DBR), 6.7Ω, 500W"
 *   ✎  U+270E  the quantity-edited marker the design depends on
 *   —  U+2014  the em dash used for an empty Side
 *
 * Rendered with a core font these come out as '?'. "6.7?, 500W" reads
 * as a plausible spec rather than as an error, which is why this is
 * checked mechanically rather than by eye.
 *
 * Writes one probe PDF per font, extracts the text back out, and reports
 * which glyphs survived. Needs pdftotext (poppler) for the extraction
 * step; without it the PDFs are still written for manual inspection.
 *
 *   php BOMMODULEDEVELOPMENT/tests/pdf_glyph_probe.php
 *
 * Exit code 0 = the configured font carries every glyph.
 *
 * PHP 7.4 compatible.
 */

define('BASEPATH', true);
require dirname(dirname(__DIR__)) . '/application/libraries/tcpdf/tcpdf.php';

$glyphs = array(
    'OMEGA (U+03A9)'      => "\xCE\xA9",
    'PENCIL (U+270E)'     => "\xE2\x9C\x8E",
    'EM DASH (U+2014)'    => "\xE2\x80\x94",
    'EN DASH (U+2013)'    => "\xE2\x80\x93",
    'DEGREE (U+00B0)'     => "\xC2\xB0",
    'WARNING (U+26A0)'    => "\xE2\x9A\xA0",
    'MIDDLE DOT (U+00B7)' => "\xC2\xB7",
    'MINUS (U+2212)'      => "\xE2\x88\x92",
);

$results = array();

foreach (array('dejavusans', 'freeserif', 'helvetica') as $font) {
    $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetPrintHeader(false); $pdf->SetPrintFooter(false);
    $pdf->AddPage();
    $pdf->SetFont($font, '', 14);
    foreach ($glyphs as $name => $ch) {
        $pdf->Cell(0, 8, $name . ' -> [' . $ch . ']', 0, 1);
    }
    $pdf->Cell(0, 8, 'REAL DATA: DYNAMIC BRAKING RESISTOR (DBR), 6.7' . "\xCE\xA9" . ', 500W', 0, 1);
    $path = sys_get_temp_dir() . '/abom_probe_' . $font . '.pdf';
    $pdf->Output($path, 'F');

    $text = '';
    if (trim(shell_exec('command -v pdftotext')) !== '') {
        $text = (string) shell_exec('pdftotext -enc UTF-8 ' . escapeshellarg($path) . ' - 2>/dev/null');
    }

    $survived = array();
    foreach ($glyphs as $name => $ch) {
        $survived[$name] = ($text !== '') ? (strpos($text, $ch) !== false) : null;
    }
    $results[$font] = $survived;
}

$configured = 'dejavusans';   // $config['abom_pdf_font']
$bad = 0;

printf("\n  %-22s", 'GLYPH');
foreach (array_keys($results) as $font) { printf("%-14s", $font); }
echo "\n  " . str_repeat('-', 22 + 14 * count($results)) . "\n";

foreach (array_keys($glyphs) as $name) {
    printf("  %-22s", $name);
    foreach ($results as $font => $survived) {
        $v = $survived[$name];
        printf("%-14s", $v === null ? 'unknown' : ($v ? 'ok' : 'LOST'));
        if ($font === $configured && $v === false) { $bad++; }
    }
    echo "\n";
}

echo "\n  Configured font: " . $configured . "\n";
if ($bad === 0) {
    echo "  Every glyph survives. PDFs in " . sys_get_temp_dir() . "/abom_probe_*.pdf\n\n";
} else {
    printf("  %d GLYPH(S) LOST in the configured font. Change \$config['abom_pdf_font']\n"
         . "  in application/config/abom.php, or substitute a text marker and record it\n"
         . "  in MODULE_CHANGELOG.md. Do NOT emit an invisible character.\n\n", $bad);
}

exit($bad === 0 ? 0 : 1);
