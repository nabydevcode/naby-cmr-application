<?php


namespace App\Service;

use Nucleos\DompdfBundle\Factory\DompdfFactoryInterface;

/**
 * Service permettant de générer un PDF à partir d'un contenu HTML.
 */
class PdfServices
{
    private DompdfFactoryInterface $factory;

    public function __construct(DompdfFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Génère un PDF à partir du HTML donné.
     * 
     * @param string $html Contenu HTML à convertir en PDF
     * @return string Données brutes du PDF généré
     */
    public function output(string $html): string
    {
        $dompdf = $this->factory->create();
        $dompdf->set_option('isHtml5ParserEnabled', true);
        $dompdf->set_option('isRemoteEnabled', true);
        $dompdf->set_option('defaultFont', 'Helvetica');
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }
}
