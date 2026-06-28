<?php
require __DIR__ . '/../vendor/autoload.php';
$p = new \PhpOffice\PhpWord\TemplateProcessor(__DIR__ . '/../public/uploads/templates/template_rekom_25_opd_26_1782491523.docx');
print_r($p->getVariables());
