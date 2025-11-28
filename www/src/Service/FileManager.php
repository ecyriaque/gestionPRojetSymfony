<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;

class FileManager 
{

    public function streamPdf(string $html): string
    {
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $projectDir = dirname(__DIR__, 2); 
        $dompdfDir = $projectDir . '/var/dompdf';
        $fontDir = $dompdfDir . '/fonts';
        $fontCache = $dompdfDir . '/cache';
        $tempDir = $dompdfDir . '/tmp';

        if (!is_dir($fontDir)) {
            @mkdir($fontDir, 0777, true);
        }
        if (!is_dir($fontCache)) {
            @mkdir($fontCache, 0777, true);
        }
        if (!is_dir($tempDir)) {
            @mkdir($tempDir, 0777, true);
        }

        $options->setFontDir($fontDir);
        $options->setFontCache($fontCache);
        $options->setTempDir($tempDir);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }
}
