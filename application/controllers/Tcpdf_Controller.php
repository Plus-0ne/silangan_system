<?php 
class Tcpdf_Controller extends CI_Controller{
	function __construct() { 
		parent::__construct();
		$this->load->model('Model_Selects');
	} 
	public function GeneratePaySlip() {

		$id = $this->input->get('id');
		$data = array();
		$htmlContent='';
		

		$getPayslip = $this->Model_Selects->getPayslip($id);
		$gtData = $getPayslip->row_array();
		$data['ngetPayslip'] = $gtData;

		// $BranchID = $gtData['BranchID'];
		$ApplicantID = $gtData['ApplicantID'];

		// $GetBranchDet = $this->Model_Selects->GetBranchDet($id);
		$GetApplicantDet = $this->Model_Selects->GetApplicantDet($ApplicantID);

		// $data['nGetBranchDet'] = $GetBranchDet->row_array();
		$data['nGetApplicantDet'] = $GetApplicantDet->row_array();
		$data['nPayslipDet'] = $gtData;
		$htmlContent = $this->load->view('pdf/pay_slips', $data, TRUE);       
		$createPDFFile = time().'.pdf';
		$this->CreatePdfPay($createPDFFile, $htmlContent);
		redirect('assets/pdf/'.$createPDFFile);
	}

	public function CreatePdfPay($fileName,$html) {

		ob_start(); 
		$this->load->library('Pdf');
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Silangan Lumber');
		$pdf->SetTitle('Employee Payslip');
		$pdf->SetSubject('Current PaySlip');
		$pdf->SetKeywords('Silangan Lumber');
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		$pdf->SetPrintHeader(false);
		$pdf->SetPrintFooter(false);
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$pdf->SetMargins(PDF_MARGIN_LEFT, 0, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(0);
		$pdf->SetFooterMargin(0);
		$pdf->SetAutoPageBreak(TRUE, 0);
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			require_once(dirname(__FILE__).'/lang/eng.php');
			$pdf->setLanguageArray($l);
		}
		$pdf->SetFont('dejavusans', '', 10);
		$pdf->AddPage();
		$pdf->writeHTML($html, true, false, true, false, '');
		$pdf->lastPage();       
		ob_end_clean();
		$OutputFiless = 'assets/pdf/' . $fileName;
		$pdf->Output(FCPATH . $OutputFiless, 'F');
	}
}

