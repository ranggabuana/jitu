<?php
$zip = new ZipArchive();
$templatePath = __DIR__ . '/../public/uploads/templates/template_rekom_25_opd_26_1782491523.docx';

if ($zip->open($templatePath) === TRUE) {
    $relsXml = $zip->getFromName('word/_rels/document.xml.rels');
    if ($relsXml) {
        echo "Original Template Relationships:\n";
        // Parse relationships
        preg_match_all('/<Relationship Id="([^"]+)"[^>]+Target="([^"]+)"/', $relsXml, $matches);
        for ($i = 0; $i < count($matches[0]); $i++) {
            echo "Id: " . $matches[1][$i] . " | Target: " . $matches[2][$i] . "\n";
        }
    }
    $zip->close();
} else {
    echo "Failed to open template DOCX\n";
}
