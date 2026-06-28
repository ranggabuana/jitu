<?php
$zip = new ZipArchive();
$templatePath = __DIR__ . '/../public/uploads/templates/template_rekom_25_opd_26_1782491523.docx';

if ($zip->open($templatePath) === TRUE) {
    $xml = $zip->getFromName('word/document.xml');
    if ($xml) {
        echo "Found word/document.xml!\n";
        
        // Find placeholders with ${...}
        preg_match_all('/\$\{([^}]+)\}/', $xml, $matches);
        echo "Placeholders starting with \${}:\n";
        print_r(array_unique($matches[0]));
        
        // Find placeholders with [...]
        preg_match_all('/\[([^\]]+)\]/', $xml, $matches2);
        echo "Placeholders starting with []:\n";
        print_r(array_unique($matches2[0]));
        
        // Let's search if "table" or "foto_dokumentasi" is in the XML
        echo "Is 'table' in XML? " . (str_contains(strtolower($xml), 'table') ? 'YES' : 'NO') . "\n";
        echo "Is 'foto_dokumentasi' in XML? " . (str_contains(strtolower($xml), 'foto_dokumentasi') ? 'YES' : 'NO') . "\n";
        echo "Is 'foto_dokumentasi' in XML with clean text? " . (str_contains(strtolower(strip_tags($xml)), 'foto_dokumentasi') ? 'YES' : 'NO') . "\n";
    } else {
        echo "Could not find word/document.xml inside ZIP.\n";
    }
    $zip->close();
} else {
    echo "Failed to open ZIP archive at " . $templatePath . "\n";
}
