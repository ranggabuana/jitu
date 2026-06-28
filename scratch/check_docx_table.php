<?php
$zip = new ZipArchive();
$docxPath = 'C:\laragon\www\perijinan\public\uploads/perijinan/generated_REG_20260628_95765/Surat_Rekomendasi_REG_20260628_95765_Draft.docx';

if ($zip->open($docxPath) === TRUE) {
    $xml = $zip->getFromName('word/document.xml');
    if ($xml) {
        echo "Found word/document.xml!\n";
        
        // Find all r:embed references in the entire document
        preg_match_all('/r:embed="([^"]+)"/', $xml, $matches);
        echo "All Embedded Image rIDs in the document:\n";
        $rIds = array_unique($matches[1]);
        print_r($rIds);
        
        $relsXml = $zip->getFromName('word/_rels/document.xml.rels');
        foreach ($rIds as $rId) {
            if ($relsXml) {
                preg_match('/Id="' . $rId . '"[^>]+Target="([^"]+)"/', $relsXml, $relMatches);
                $target = $relMatches[1] ?? 'N/A';
                echo "rId $rId points to: " . $target . "\n";
                
                // Let's find where this rId is used in document.xml
                $pos = strpos($xml, $rId);
                // Print 100 characters before and after the occurrence to see the context
                if ($pos !== false) {
                    $start = max(0, $pos - 150);
                    $length = min(300, strlen($xml) - $start);
                    $snippet = substr($xml, $start, $length);
                    echo "Context snippet for $rId:\n" . htmlspecialchars(strip_tags($snippet)) . "\n";
                }
                echo "----------------------------------------\n";
            }
        }
    }
    $zip->close();
} else {
    echo "Could not open generated DOCX at " . $docxPath . "\n";
}
