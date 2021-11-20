<?php

class PDF extends FPDF
{
function Footer()
{
    // Go to 1.5 cm from bottom
    $this->SetY(-15);
    // Select Arial italic 8
    $this->SetFont('Arial','I',8);
    // Print centered page number

	$footerCredits = get_option("footerCredits","");
	$footerText = get_option("footerText","");
	if($footerText!='' && $footerCredits!='')
    	$this->Cell(0,10, $footerText,0,0,'C');
}
}