<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Document
 *
 * @copyright   Copyright (C) 2013 - 2014 Studio42 France, All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;


if(!file_exists(JPATH_LIBRARIES.'/mpdf/mpdf.php')){
	JError::raiseError(500, JText::sprintf('JLIB_APPLICATION_ERROR_APPLICATION_LOAD','mPDF'));
} else {
	if(!class_exists('mPDF'))	require_once(JPATH_LIBRARIES.'/mpdf/mpdf.php');
}
/**
 * JDocumentRendererMpdf is a pdf renderer that implements the mpdf PHP class for generating PDF documents
 *
 * @package     Joomla.Platform
 * @subpackage  Document
 * @see         http://www.tcpdf.org/
 * @since       11.1
 */
class JDocumentRendererMpdf extends mPDF
{
	/**
	 * Document mime type
	 *
	 * @var    string
	 * @since  11.1
	 */
		//set mime type
	protected $_mime = 'application/pdf';
	public $_doc ;
	/**
	 * Class constructor
	 *
	 * @param   JDocument  &$doc  A reference to the JDocument object that instantiated the renderer
	 *
	 * @since   11.1
	 */
	public function __construct(&$doc)
	{
		$this->jdoc = &$doc;
		// set default values
		$orientation = isset($doc->orientation) ? $doc->orientation	: 'P';
		// $unit		 = isset($doc->unit) 		? $doc->unit 		: 'mm';
		$format 	 = isset($doc->format) 		? $doc->format 		: 'A4';
		// $unicode	 = isset($doc->unicode) 	? $doc->unicode		: true;
		// $pdfa		 = isset($doc->pdfa) 		? $doc->pdfa		: false; 

		// PDFA true include more datas but render same in all computers. For a web site this can be very longer setted to true.
		// $encoding always utf8
		// $diskcache=false; // true : compatible with joomla ?
		parent::__construct('c',$format,'','',$doc->_margin_left,$doc->_margin_right,$doc->_margin_top,$doc->_margin_bottom,$doc->_margin_header,$doc->_margin_footer,$orientation);
		$app = JFactory::getApplication();

	}  

	//Standard autoset Page header TODO more flexible.
 /*    public function Header() {

	// parse variable from JDocument to header
		if (!empty($this->jdoc->header_logo) && $this->jdoc->header_logo !== $this->header_logo) {
			$file = $this->jdoc->header_logo;
			if (file_exists($file)) {
				$finfo = finfo_open(FILEINFO_MIME_TYPE); 
				$mime = finfo_file($finfo, $file);
				if ($mime ==="image/gif" || $mime ==="image/jpeg") {
					$this->header_logo = $file ;
				}
			}
		}
		if (!empty($this->jdoc->header_logo_width))  $this->header_logo_width =  (int)$this->jdoc->header_logo_width ;
		if (!empty($this->jdoc->header_title))  $this->header_title =  $this->jdoc->header_title ;
		if (!empty($this->jdoc->header_string))  $this->header_string =  $this->jdoc->header_string ;
		if (!empty($this->jdoc->header_text_color))  $this->header_text_color =  $this->jdoc->header_text_color ;
		if (!empty($this->jdoc->header_line_color))  $this->header_line_color =  $this->jdoc->header_line_color ;
        parent::Header();
	} */

	/**
	 * Render the pdf.
	 *
	 * @param   string  $name     The file name of the pdf to render
	 * @param   array   $params   Array of values
	 * @param   string  $content  Override the output of the renderer
	 *
	 * @return  string  The output of the script
	 *
	 * @see JDocumentRenderer::render()
	 * @since   11.1
	 */
	public function render($name = '', $destination = 'I', $content = null)
	{
		// $app = JFactory::getApplication();
		// html debug output 
		if (JRequest::getInt('print', null) == 2) return $content;

		$content = str_replace('<hr class="page-break">', '', $content);
		$full = '"'.juri::root(); 
		$short = '"'.juri::root(true).'/';
		foreach ($this->jdoc->_styleSheets as $src => $css) {
			if ($src[1] === "/" ) $src = str_replace($short, $full, $src);
			$stylesheet = file_get_contents($src);
			$this->WriteHTML($stylesheet,1);	// The parameter 1 tells that this is css/style only and no body/html/text
		}
		$footer ='';
		if (empty($this->HTMLHeader['html'])) {
			if (!empty($this->jdoc->header_title))  $title =  $this->jdoc->header_title ;
			else  $title =  $this->jdoc->title ;
			$this->SetHTMLHeader('<div class="pdf-header"><span class="right"><h3>'.$title.'</h3></span></div>');
		}
		if (!empty($this->HTMLfooter)) {
		//@page { margin: 180px 50px; }
			$htmlFooter .= $this->HTMLfooter ;
			
		} else {
			$siteUrl = JURI::getInstance()->toString();
			$siteUrl = str_replace("&format=pdf", "", $siteUrl);
			$siteUrl = str_replace("?format=pdf", "", $siteUrl);
			$app = JFactory::getApplication();
			$title = $app->getCfg('sitename').' - '.$this->title;
			$htmlFooter .= '<p class="page"><a href="'.$siteUrl.'">'.$title.'</a></p>' ;
			//.'<div>'{PAGENO}/{nb}.'</div>'
		// jexit($htmlFooter);
		}
		$footer .= '<table width="100%" style="background:none"><tr><td width="66%">';
		$footer .= $htmlFooter ;
		$PAGE_CURRENT_OF_TOTAL = JText::sprintf('JLIB_HTML_PAGE_CURRENT_OF_TOTAL', '{PAGENO}','{nbpg}');
		$footer .= '</td><td width="33%" style="text-align: right;">'.$PAGE_CURRENT_OF_TOTAL.'</td></tr></table>';

		$this->setHTMLFooter( $footer );
		$content = '<div class="pdf-body">'.$content .'</div>';
		// echo '<div class="pdf-body">'.$content.'</div >'; jexit();
		$this->WriteHTML($content);
		if ($destination === "F") {
			$this->Output($name, $destination ) ;
			return $name;
		}
		else return $this->Output($name, $destination ) ;
		// render base 64
		// $content = $this->Output('', 'S');
		// $content = chunk_split(base64_encode($content));
	}
	public function setRTL($isRtl = false) {
		$this->directionality = $isRtl ? 'rtl' : 'ltr';
	}
}
