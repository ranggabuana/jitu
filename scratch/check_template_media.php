<?php
$zip = new ZipArchive();
$templatePath = __DIR__ . '/../public/uploads/templates/template_rekom_25_opd_26_1782491523.docx';

if ($zip->open($templatePath) === TRUE) {
    echo "Files inside original template DOCX:\n";
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $filename = $zip->getNameIndex($i);
        if (str_contains($filename, 'media/')) {
            echo "Media file: " . $filename . " (" . strlen($zip->getFromName($filename)) . " bytes)\n";
        }
    }
    $zip->close();
} else {
    echo "Failed to open template DOCX at " . $templatePath . "\n";
}
