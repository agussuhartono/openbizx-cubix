<?php
/**
 * Openbiz Cubi Application Platform
 *
 * LICENSE http://code.google.com/p/openbiz-cubi/wiki/CubiLicense
 *
 * @package   cubi.service
 * @copyright Copyright (c) 2005-2011, Openbiz Technology LLC
 * @license   http://code.google.com/p/openbiz-cubi/wiki/CubiLicense
 * @link      http://code.google.com/p/openbiz-cubi/
 * @version   $Id: html2pdfService.php 3371 2012-05-31 06:17:21Z rockyswen@gmail.com $
 */

/**
 * @package PluginService
 */
 
require_once("dompdf/dompdf_config.inc.php");
/**
 * pdfService - 
 * class pdfService is the plug-in service of printing openbiz form to pdf 
 * 
 * @package PluginService
 * @author rocky swen 
 * @copyright Copyright (c) 2003
 * @version $Id: html2pdfService.php 3371 2012-05-31 06:17:21Z rockyswen@gmail.com $
 * @access public 
 */
class html2pdfService
{
    protected $pdfObj;
    
    public function __construct() 
    {
        $this->pdfObj = new DOMPDF();
    }
    
    public function getPDFObject()
    {
        return $this->pdfObj;
    }
   
   /**
    * pdfService::render() - render the pdf output
    * 
    * @param string $objname object name which is the bizform name
    * @return void 
    */
    public function html2pdf($html, $pdfName)
    {
        $dompdf = new DOMPDF();
        $this->pdfObj->set_protocol("http://");
        $this->pdfObj->set_host("localhost");
		$this->pdfObj->set_base_path(OPENBIZ_APP_PATH);
        $this->pdfObj->load_html($html);
        //$dompdf->set_paper($_POST["paper"], $_POST["orientation"]);
        $this->pdfObj->render();
        //$this->Output($dompdf);
        $this->pdfObj->stream("$pdfName");
    }
   
    function Output($dompdf)
    {
        //$tmpfile = getcwd()."/tmpfiles";
        $tmpfile = OPENBIZ_APP_PATH."/tmpfiles";
        //echo $tmpfile;
        $this->CleanFiles($tmpfile, 100);
        //Determine a temporary file name in the current directory
        $file_tmp=tempnam($tmpfile,'tmp');
        $file=$file_tmp.'.pdf';
        $file=str_replace("\\","/",$file);
        unlink($file_tmp);
        //Save PDF to file
        $pdfText = $dompdf->output();
        $h_file = fopen($file, 'w') or die("can't open pdf file to write");
        fwrite($h_file, $pdfText) or die("can't write to the pdf file");
        fclose($h_file);
        //JavaScript redirection
        $path_parts = pathinfo($file);
        $file_download = "tmpfiles/".$path_parts['basename'];
        echo "<HTML><BODY onload=\"window.location.href='../$file_download';\"</BODY></HTML>";
    }
   
    function CleanFiles($dir, $seconds)
    {
        //Delete temporary files
        $t=time();
        $h=opendir($dir);
        while($file=readdir($h))
        {
            if(substr($file,0,3)=='tmp' && substr($file,-4)=='.pdf')
            {
                $path=$dir.'/'.$file;
                if($t-filemtime($path)>$seconds)
                    unlink($path);
            }
        }
        closedir($h);
    }
}
