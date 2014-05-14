<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Document
 *
 * @copyright   Copyright (C) 2013 - 2014 Studio42 France, All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;


if(!file_exists(JPATH_LIBRARIES.'/tcpdf/tcpdf.php')){
	JError::raiseError(500, JText::sprintf('JLIB_APPLICATION_ERROR_APPLICATION_LOAD','TCPDF'));
} else {
	if(!class_exists('TCPDF'))	require(JPATH_LIBRARIES.'/tcpdf/tcpdf.php');
}
/**
 * JDocumentRendererTcpdf is a pdf that implements the tcpdf PHP class for generating PDF documents
 * @package     Joomla.Platform
 * @subpackage  Document
 * @see         http://www.tcpdf.org/
 */
class JDocumentRendererTcpdf extends TCPDF
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
		$unit		 = isset($doc->unit) 		? $doc->unit 		: 'mm';
		$format 	 = isset($doc->format) 		? $doc->format 		: 'A4';
		$unicode	 = isset($doc->unicode) 	? $doc->unicode		: true;
		$pdfa		 = isset($doc->pdfa) 		? $doc->pdfa		: false; 
		// PDFA true include more datas but render same in all computers. For a web site this can be very longer setted to true.
		// $encoding always utf8
		$diskcache=false; // true : compatible with joomla ?
		parent::__construct($orientation, $unit, $format, $unicode, 'UTF-8', $diskcache, $pdfa); 

	}

	/**
	 * Render the pdf.
	 *
	 * @param   string  $name     The name of the element to render
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
		$app = JFactory::getApplication();
		$content = $this->fullPaths($content);
		$full = juri::root(); 
		$short = juri::root(true).'/';
		// echo $content; jexit();
	// parse variable from JDocument to header
		if (isset($this->jdoc->header_logo)) {
			$file = $this->jdoc->header_logo;
			if (file_exists($file)) {
				$finfo = finfo_open(FILEINFO_MIME_TYPE); 
				$mime = finfo_file($finfo, $file);
				if ($mime ==="image/gif" || $mime ==="image/jpeg") {
					$this->header_logo = $file ;
				}
			}
		}
		if ($this->header_logo && isset($this->jdoc->header_logo_width))  $this->header_logo_width =  (int)$this->jdoc->header_logo_width ;
		else $this->header_logo_width = 0 ;
		if (isset($this->jdoc->header_title))  $this->header_title =  $this->jdoc->header_title ;
		if (isset($this->jdoc->header_string))  $this->header_string =  $this->jdoc->header_string ;
		if (isset($this->jdoc->header_text_color))  $this->header_text_color =  $this->jdoc->header_text_color ;
		if (isset($this->jdoc->header_line_color))  $this->header_line_color =  $this->jdoc->header_line_color ;
		if (empty($this->header_title)) $this->header_title = $this->jdoc->title;
		
		// $this->setHeaderData();
		// var_dump($this->getHeaderData()); jexit();
		// add css
		$this->cssStyles = '<style>';
		foreach ($this->jdoc->_styleSheets as $src => $css) {
			// relative to absolute path
			$pos = strpos($src, $short);
			if ($pos === 0) $src = str_replace($short, $full, $src);
			// relative path with missing joomla root
			$pos = strpos($src, '//');
			if ($pos === false) $src = $full.$src;
			$stylesheet = file_get_contents($src);
			// if ($src[1] === "/" ) $src = str_replace($short, $full, $src);
			$this->cssStyles .= $stylesheet ;
		}
		$this->cssStyles .= '</style>';
		// html debug output 
		if (JRequest::getInt('print', null) == 2) return $content;
		
		// set header and footer fonts
		$this->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$this->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		$this->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$this->SetHeaderMargin(PDF_MARGIN_HEADER);
		$this->SetFooterMargin(PDF_MARGIN_FOOTER);
		// $this->SetFont(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN, '', 'false'); 
		// set default monospaced font
		// $this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$this->AddPage();
		// $this->writeHTML($header.$content, true, false, true, false, '');
		$this->writeHTML($this->cssStyles.$content, true, false, true, false, '');

		if ($destination ==="F") {
			// $destination = "S";
			$this->Output($name, $destination ) ;
			// file_put_contents($name, $pdf); 
			return $name;
		} else return $this->Output($name, $destination ) ;

	}
	// add full path to relative path
	private function fullPaths($src)
	{
		$full = juri::root(); 
		$short = juri::root(true).'/';
		$src = str_replace('"'.$short, '"'.$full, $src);
		return str_replace("'".$short, "'".$full, $src);
	}
	/**
	 * This method is used to render the page footer.
	 * It is automatically called by AddPage() and could be overwritten in your own inherited class.
	 * @public
	 */
	public function Footer() {
		$cur_y = $this->y;
		$this->SetTextColorArray($this->footer_text_color);
		//set style for cell border
		$line_width = (0.85 / $this->k);
		$this->SetLineStyle(array('width' => $line_width, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $this->footer_line_color));
		//print document barcode REMOVED !!!
		$footer ='';
		if (!empty($this->HTMLfooter)) {
		//@page { margin: 180px 50px; }
			$footer .= '<div class="pdf-footer">'.$this->HTMLfooter.'</div>' ;
		} else {
			$siteUrl = JURI::getInstance()->toString();
			$siteUrl = str_replace("&format=pdf", "", $siteUrl);
			$siteUrl = str_replace("?format=pdf", "", $siteUrl);
			$app = JFactory::getApplication();
			$title = $app->getCfg('sitename').' - '.$this->title;
			$footer .= '<div class="pdf-footer"><a href="'.$siteUrl.'">'.$title.'</a></div>' ;
		
		}
		$w_page = isset($this->l['w_page']) ? $this->l['w_page'].' ' : '';
		if (empty($this->pagegroups)) {
			$pagenumtxt = JText::sprintf('JLIB_HTML_PAGE_CURRENT_OF_TOTAL',$this->getAliasNumPage(),$this->getAliasNbPages());
			// $pagenumtxt = $w_page.$this->getAliasNumPage().' / '.$this->getAliasNbPages();
		} else {
			$pagenumtxt = JText::sprintf('JLIB_HTML_PAGE_CURRENT_OF_TOTAL',$this->getPageNumGroupAlias(),$this->getPageGroupAlias());
			// $pagenumtxt = $w_page.$this->getPageNumGroupAlias().' / '.$this->getPageGroupAlias();
		}
		// echo $footer; jexit();
		$this->SetY($cur_y-$this->jdoc->_margin_footer);
		// $this->writeHTML($this->cssStyles.$content, true, false, true, false, '');
		$this->writeHTMLCell($w=0, $h=0, $x='', $y='', $this->cssStyles.$footer, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
		//Print page number
		$this->SetY($cur_y);
		if ($this->getRTL()) {
			$this->SetX($this->original_rMargin);
			$this->Cell(0, 0, $pagenumtxt, 'T', 0, 'L');
		} else {
			$this->SetX($this->original_lMargin);
			$this->Cell(0, 0, $this->getAliasRightShift().$pagenumtxt, 'T', 0, 'R');
		}
	}
	/**
	 * This method is used to render the page header.
	 * It is automatically called by AddPage() and could be overwritten in your own inherited class.
	 * @public
	 */
	public function Header() {
		if (!empty($this->HTMLHeader)) {
			$logo_height = $this->jdoc->params->get('logo_height',48);
			// if (empty($this->jdoc->_styleSheets)) {

			// }
			$header = '<div class="pdf-header">'.$this->HTMLHeader.'</div>' ;
			$this->writeHTMLCell($w=0, $h=0, $x='', $y='', $this->cssStyles.$header, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
		} else {
			parent::Header();
		}
	}
}
