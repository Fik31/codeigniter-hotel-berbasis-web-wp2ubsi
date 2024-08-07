<?php
use Dompdf\Dompdf;
use Dompdf\Options;
class transaksi extends ci_controller{
    
        function __construct() { 
        parent::__construct();
        $this->load->model(array('model_barang','model_transaksi'));
        chek_session();
    }
    
    function index()
    {
        if(isset($_POST['submit']))
        {
            $this->model_transaksi->simpan_barang();
            redirect('transaksi');
        }
        else
        {
            $data['barang']= $this->model_barang->daftar_kamar(); //input no ketersediaan kamar
            $data['detail']= $this->model_transaksi->tampilkan_detail_transaksi()->result(); //tabel total sewa
            $this->template->load('template','transaksi/form_transaksi',$data);
        }
    }
    
    
    function hapusitem()
    {
        $id = $this->uri->segment(3);
        $this->model_transaksi->hapusitem($id);
        redirect('transaksi');
    }
    
    
    function selesai_belanja() //isi data operator
    {
        $tanggal=date('Y-m-d');
        $user=  $this->session->userdata('username');
        $id_op=  $this->db->get_where('operator',array('username'=>$user))->row_array();
       
        $data=array(
                        'operator_id'=>$id_op['operator_id'],
                        'tanggal_transaksi'=>$tanggal
                    );
        $this->model_transaksi->M_selesai_belanja($data);
        redirect('transaksi');
    }

    function selesai_bayar() //bayar lunas = ubah status ke true 1
    {   
            $Lunas = $this->input->post('Proses_Lunas');
            $statusTrue = array( 'status'=> 1 );
            $result = $this->model_transaksi->M_selesai_bayar($Lunas, $statusTrue);
            if ($result > 0) { 
                echo(json_encode(array('status'=>TRUE))); 
            }
            else { 
                echo(json_encode(array('status'=>FALSE))); 
            }
    }
    
    
    function laporan()
    {
        if(isset($_POST['submit']))
        {
            $tanggal1=  $this->input->post('tanggal1');
            $tanggal2=  $this->input->post('tanggal2');
            $data['record']=  $this->model_transaksi->laporan_periode($tanggal1,$tanggal2);
            $this->template->load('template','transaksi/laporan',$data);
        }
        else
        {
            $data['record']=  $this->model_transaksi->laporan_default();
            $this->template->load('template','transaksi/laporan',$data);
        }
    }
    
    
    function excel()
    {
        header("Content-type=appalication/vnd.ms-excel");
        header("content-disposition:attachment;filename=laporantransaksi.xls");
        $data['record']=  $this->model_transaksi->laporan_default();
        $this->load->view('transaksi/laporan_excel',$data);
    }
    
    function pdf()
    {
        $this->load->library('cfpdf');
        $pdf=new FPDF('P','mm','A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial','B','L');
        $pdf->SetFontSize(14);
        $pdf->Text(10, 10, 'LAPORAN TRANSAKSI');
        $pdf->SetFont('Arial','B','L');
        $pdf->SetFontSize(10);
        $pdf->Cell(10, 10,'','',1);
        $pdf->Cell(10, 7, 'No', 1,0);
        $pdf->Cell(27, 7, 'Tanggal', 1,0);
        $pdf->Cell(30, 7, 'Operator', 1,0);
        $pdf->Cell(38, 7, 'Total Transaksi', 1,1);
        // tampilkan dari database
        $pdf->SetFont('Arial','','L');
        $data=  $this->model_transaksi->laporan_default();
        $no=1;
        $total=0;
        foreach ($data->result() as $r)
        {
            $pdf->Cell(10, 7, $no, 1,0);
            $pdf->Cell(27, 7, $r->tanggal_transaksi, 1,0);
            $pdf->Cell(30, 7, $r->nama_lengkap, 1,0);
            $pdf->Cell(38, 7, $r->total, 1,1);
            $no++;
            $total=$total+$r->total;
        }
        // end
        $pdf->Cell(67,7,'Total',1,0,'R');
        $pdf->Cell(38,7,$total,1,0);
        $pdf->Output();
    }

    function download_pdf() {
        // Load the detail data
        $data['detail'] = $this->model_transaksi->tampilkan_detail_transaksi()->result();

        // Load the HTML content
        $html = $this->load->view('pdf_template', $data, true);

        // Instantiate Dompdf with options
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF (1 = download and 0 = preview)
        $dompdf->stream("Reservasi_Kamar.pdf", array("Attachment" => 1));
    }
}