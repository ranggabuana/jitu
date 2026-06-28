<?php
$zip = new ZipArchive();
$docxPath = 'C:\laragon\www\perijinan\public\uploads/perijinan/generated_REG_20260628_95765/Surat_Rekomendasi_REG_20260628_95765_Draft.docx';

if ($zip->open($docxPath) === TRUE) {
    $xml = $zip->getFromName('word/document.xml');
    if ($xml) {
        echo "Found word/document.xml!\n";
        
        $rIds = ['rId11', 'rId12', 'rId13'];
        foreach ($rIds as $rId) {
            $pos = 0;
            while (($pos = strpos($xml, $rId, $pos)) !== false) {
                echo "Reference to $rId found at position $pos\n";
                $start = max(0, $pos - 150);
                $snippet = substr($xml, $start, 300);
                echo "Snippet:\n" . htmlspecialchars($snippet) . "\n";
                echo "----------------------------------------\n";
                $pos += strlen($rId);
            }
        }
    }
    $zip->close();
} else {
    echo "Could not open DOCX\n";
}
