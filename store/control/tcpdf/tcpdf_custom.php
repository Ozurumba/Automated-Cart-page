<?php

class mcTCPPDF extends TCPDF {

  public function Header() {
  }

  public function Footer() {
    $this->SetY(-15);
    //$this->SetFont('helvetica', 'I', 8);
    $this->Cell(0, 10, $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
  }
}

?>