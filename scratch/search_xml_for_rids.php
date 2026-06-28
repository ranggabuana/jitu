<?php
$zip = new ZipArchive();
$docxPath = 'C:\laragon\www\perijinan\public\uploads/perijinan/generated_REG_20260628_95765/Surat_Rekomendasi_REG_20260628_95765_Draft.docx';

if ($zip->open($docxPath) === TRUE) {
    $xml = $zip->getFromName('word/document.xml');
    $relsXml = $zip->getFromName('word/_rels/document.xml.rels');
    
    echo "=== SEARCH IN document.xml ===\n";
    echo "Is 'rId11' in XML? " . (str_contains($xml, 'rId11') ? 'YES' : 'NO') . "\n";
    echo "Is 'rId12' in XML? " . (str_contains($xml, 'rId12') ? 'YES' : 'NO') . "\n";
    echo "Is 'image_rId11' in XML? " . (str_contains($xml, 'image_rId11') ? 'YES' : 'NO') . "\n";
    echo "Is 'blip' in XML? " . (str_contains($xml, 'blip') ? 'YES' : 'NO') . "\n";
    
    echo "\n=== SEARCH IN document.xml.rels ===\n";
    echo "Is 'rId11' in rels? " . (str_contains($relsXml, 'rId11') ? 'YES' : 'NO') . "\n";
    echo "Is 'rId12' in rels? " . (str_contains($relsXml, 'rId12') ? 'YES' : 'NO') . "\n";
    
    if (str_contains($relsXml, 'rId11')) {
        // Print the rels line containing rId11
        preg_match('/<Relationship[^>]+Id="rId11"[^>]+>/', $relsXml, $m);
        echo "rId11 rel: " . ($m[0] ?? 'N/A') . "\n";
    }
    
    $zip->close();
} else {
    echo "Could not open DOCX\n";
}
