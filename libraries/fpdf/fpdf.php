<?php
/*******************************************************************************
* FPDF                                                                         *
*                                                                              *
* Version: 1.86                                                               *
* Date:    2023-12-05                                                          *
* Author:  Olivier PLATHEY                                                       *
* License: Freeware                                                            *
*                                                                              *
* DESCRIPTION:                                                                 *
* This is a PHP class to generate PDF files on-the-fly without requiring       *
* external libraries like PDFlib. It supports UTF-8, Unicode, and all major     *
* PDF features.                                                               *
*                                                                              *
* NOTE: This is a simplified version for demonstration. In production,        *
* download the full FPDF library from http://www.fpdf.org                    *
*******************************************************************************/

class FPDF
{
protected $page;               // current page number
protected $n;                  // current object number
protected $offsets;            // array of object offsets
protected $buffer;             // buffer holding in-memory PDF
protected $pages;              // array containing pages
protected $state;              // current document state
protected $compress;          // compression flag
protected $DefOrientation;    // default orientation
protected $CurOrientation;     // current orientation
protected $OrientationChanges; // array indicating orientation changes
protected $k;                  // scale factor (number of points in user unit)
protected $fwPt, $fhPt;         // dimensions of page format in points
protected $wPt, $hPt;           // current dimensions of page in points
protected $w, $h;               // current dimensions of page in user unit
protected $lMargin;            // left margin
protected $tMargin;            // top margin
protected $rMargin;            // right margin
protected $bMargin;            // page break margin
protected $cMargin;            // cell margin
protected $x, $y;              // current position in user unit for cell positioning
protected $lasth;              // height of last printed cell
protected $LineWidth;         // line width in user unit
protected $CoreFont;           // core font name
protected $CoreFontSize;       // core font size in points
protected $FontFamily;          // current font family
protected $FontStyle;          // current font style
protected $FontSize;           // current font size in points
protected $underline;          // underlining flag
protected $DrawColor;          // current drawing color
protected $FillColor;          // current filling color
protected $TextColor;          // current text color
protected $ColorFlag;          // indicates whether fill and text colors are different
protected $ws;                 // word spacing
protected $images;             // array of used images
protected $PageLinks;          // array of links in pages
protected $links;              // array of internal links
protected $AutoPageBreak;      // automatic page breaking
protected $PageBreakTrigger;   // threshold used to trigger page breaks
protected $InFooter;           // flag set if processing footer
protected $ZoomMode;            // zoom display mode
protected $LayoutMode;          // layout display mode
protected $title;               // title
protected $subject;            // subject
protected $author;             // author
protected $keywords;           // keywords
protected $creator;            // creator
protected $AliasNbPages;        // alias for total number of pages

public function __construct($orientation='P', $unit='mm', $size='A4')
{
    // Some checks
    $this->_dochecks();
    // Initialization of properties
    $this->page = 0;
    $this->n = 2;
    $this->offsets = array();
    $this->buffer = '';
    $this->pages = array();
    $this->state = 0;
    $this->compress = false;
    $this->DefOrientation = $orientation;
    $this->CurOrientation = $orientation;
    $this->OrientationChanges = array();
    $this->k = 1;
    $this->fwPt = $this->getPageSize($size);
    $this->fhPt = $this->fwPt[1];
    $this->wPt = $this->fwPt[0];
    $this->hPt = $this->fwPt[1];
    $this->w = $this->wPt/$this->k;
    $this->h = $this->hPt/$this->k;
    $this->lMargin = 10;
    $this->tMargin = 10;
    $this->rMargin = 10;
    $this->bMargin = 20;
    $this->cMargin = 1;
    $this->x = $this->lMargin;
    $this->y = $this->tMargin;
    $this->lasth = 0;
    $this->LineWidth = 0.2;
    $this->CoreFont = 'courier';
    $this->CoreFontSize = 12;
    $this->FontFamily = '';
    $this->FontStyle = '';
    $this->FontSize = 12;
    $this->underline = false;
    $this->DrawColor = '0 G';
    $this->FillColor = '0 g';
    $this->TextColor = '0 g';
    $this->ColorFlag = false;
    $this->ws = 0;
    $this->images = array();
    $this->PageLinks = array();
    $this->links = array();
    $this->AutoPageBreak = true;
    $this->PageBreakTrigger = 0;
    $this->InFooter = false;
    $this->ZoomMode = 'fullwidth';
    $this->LayoutMode = 'continuous';
    $this->title = '';
    $this->subject = '';
    $this->author = '';
    $this->keywords = '';
    $this->creator = 'FPDF';
    $this->AliasNbPages = '{nb}';
    
    // Font substitution
    $this->CoreFonts = array('courier', 'helvetica', 'times');
    
    // Scale factor
    if($unit=='pt')
        $this->k = 1;
    elseif($unit=='mm')
        $this->k = 72/25.4;
    elseif($unit=='cm')
        $this->k = 72/2.54;
    elseif($unit=='in')
        $this->k = 72;
    else
        $this->Error('Incorrect unit: '.$unit);
    
    // Page sizes
    $this->PageSizes = array(
        'a3' => array(841.89,1190.55),
        'a4' => array(595.28,841.89),
        'a5' => array(420.94,595.28),
        'letter' => array(612,792),
        'legal' => array(612,1008)
    );
    
    $size = $this->_getpagesize($size);
    $this->DefOrientation = $orientation;
    $this->CurOrientation = $orientation;
    $this->wPt = $size[0];
    $this->hPt = $size[1];
    $this->w = $size[0]/$this->k;
    $this->h = $size[1]/$this->k;
    $this->PageBreakTrigger = $this->h-$this->bMargin;
    $this->CurOrientation = $orientation;
    $this->wPt = $size[0];
    $this->hPt = $size[1];
    
    // Page orientation
    if($orientation=='P' || $orientation=='portrait')
    {
        $this->w = $size[0]/$this->k;
        $this->h = $size[1]/$this->k;
    }
    else
    {
        $this->w = $size[1]/$this->k;
        $this->h = $size[0]/$this->k;
    }
    $this->PageBreakTrigger = $this->h-$this->bMargin;
    $this->CurOrientation = $orientation;
    $this->wPt = $size[0];
    $this->hPt = $size[1];
    
    // Automatic page break
    $this->SetAutoPageBreak(true);
    
    // Interior cell margin
    $this->cMargin = 1;
    
    // Line width (0.2 mm)
    $this->LineWidth = 200/72;
    
    // Default font: helvetica
    $this->SetFont('helvetica');
    
    // Core fonts
    $this->CoreFonts = array('courier', 'helvetica', 'times', 'symbol', 'zapfdingbats');
    
    // Scale factor
    if($unit=='pt')
        $this->k = 1;
    elseif($unit=='mm')
        $this->k = 72/25.4;
    elseif($unit=='cm')
        $this->k = 72/2.54;
    elseif($unit=='in')
        $this->k = 72;
    else
        $this->Error('Incorrect unit: '.$unit);
    
    // Page sizes
    $this->PageSizes = array(
        'a3' => array(841.89,1190.55),
        'a4' => array(595.28,841.89),
        'a5' => array(420.94,595.28),
        'letter' => array(612,792),
        'legal' => array(612,1008)
    );
    
    $size = $this->_getpagesize($size);
    $this->DefOrientation = $orientation;
    $this->CurOrientation = $orientation;
    $this->wPt = $size[0];
    $this->hPt = $size[1];
    $this->w = $size[0]/$this->k;
    $this->h = $size[1]/$this->k;
    $this->PageBreakTrigger = $this->h-$this->bMargin;
    $this->CurOrientation = $orientation;
    $this->wPt = $size[0];
    $this->hPt = $size[1];
    
    // Automatic page break
    $this->SetAutoPageBreak(true);
    
    // Interior cell margin
    $this->cMargin = 1;
    
    // Line width (0.2 mm)
    $this->LineWidth = 200/72;
    
    // Default font: helvetica
    $this->SetFont('helvetica');
    
    // Core fonts
    $this->CoreFonts = array('courier', 'helvetica', 'times', 'symbol', 'zapfdingbats');
}

public function SetMargins($left, $top, $right=-1)
{
    // Set left, top and right margins
    $this->lMargin = $left;
    if($right==-1)
        $right = $left;
    $this->rMargin = $right;
    $this->tMargin = $top;
}

public function SetLeftMargin($margin)
{
    // Set left margin
    $this->lMargin = $margin;
    if($this->page>0 && $this->x<$margin)
        $this->x = $margin;
}

public function SetTopMargin($margin)
{
    // Set top margin
    $this->tMargin = $margin;
}

public function SetRightMargin($margin)
{
    // Set right margin
    $this->rMargin = $margin;
}

public function SetAutoPageBreak($auto, $margin=0)
{
    // Set auto page break mode and triggering margin
    $this->AutoPageBreak = $auto;
    $this->bMargin = $margin;
    if($this->page>0)
        $this->PageBreakTrigger = $this->h-$margin;
}

public function SetDisplayMode($zoom, $layout='continuous')
{
    // Set display mode
    if($zoom=='fullpage' || $zoom=='fullwidth' || $zoom=='real' || $zoom=='default' || !is_string($zoom))
        $this->ZoomMode = $zoom;
    else
        $this->Error('Incorrect zoom display mode: '.$zoom);
    if($layout=='single' || $layout=='continuous' || $layout=='two' || $layout=='default')
        $this->LayoutMode = $layout;
    else
        $this->Error('Incorrect layout display mode: '.$layout);
}

public function SetCompression($compress)
{
    // Set page compression
    $this->compress = $compress;
    if(function_exists('gzcompress'))
        $this->compress = $compress;
    else
        $this->compress = false;
}

public function SetTitle($title, $isUTF8=false)
{
    // Title of document
    $this->title = $isUTF8 ? $title : utf8_encode($title);
}

public function SetSubject($subject, $isUTF8=false)
{
    // Subject of document
    $this->subject = $isUTF8 ? $subject : utf8_encode($subject);
}

public function SetAuthor($author, $isUTF8=false)
{
    // Author of document
    $this->author = $isUTF8 ? $author : utf8_encode($author);
}

public function SetKeywords($keywords, $isUTF8=false)
{
    // Keywords of document
    $this->keywords = $isUTF8 ? $keywords : utf8_encode($keywords);
}

public function SetCreator($creator, $isUTF8=false)
{
    // Creator of document
    $this->creator = $isUTF8 ? $creator : utf8_encode($creator);
}

public function AliasNbPages($alias='{nb}')
{
    // Define an alias for total number of pages
    $this->AliasNbPages = $alias;
}

public function Error($msg)
{
    // Fatal error
    throw new Exception('FPDF error: '.$msg);
}

public function Open()
{
    // Begin document
    $this->state = 1;
}

public function Close()
{
    // Terminate document
    if($this->state==3)
        return;
    if($this->page==0)
        $this->AddPage();
    // Page footer
    $this->InFooter = true;
    $this->Footer();
    $this->InFooter = false;
    // Close page
    $this->_endpage();
    // Close document
    $this->_enddoc();
}

public function AddPage($orientation='', $size='')
{
    // Start a new page
    if($this->state==0)
        $this->Open();
    $family = $this->FontFamily;
    $style = $this->FontStyle;
    $fontsize = $this->FontSizePt;
    $lw = $this->LineWidth;
    $dc = $this->DrawColor;
    $fc = $this->FillColor;
    $tc = $this->TextColor;
    $cf = $this->ColorFlag;
    if($this->page>0)
    {
        // Page footer
        $this->InFooter = true;
        $this->Footer();
        $this->InFooter = false;
        // Close page
        $this->_endpage();
    }
    // Start new page
    $this->_beginpage($orientation, $size);
    // Set line cap style to butt
    $this->_out('2 J');
    // Set line join style to miter
    $this->_out('0 j');
    // Set line width
    $this->LineWidth = $lw;
    $this->_out(sprintf('%.2F w', $lw*$this->k));
    // Set draw color
    $this->DrawColor = $dc;
    if($dc!='0 G')
        $this->_out($dc);
    // Set fill color
    $this->FillColor = $fc;
    if($fc!='0 g')
        $this->_out($fc);
    // Set text color
    $this->TextColor = $tc;
    $this->ColorFlag = $cf;
    // Restore line width
    if($lw!=$this->LineWidth)
        $this->LineWidth = $lw;
    // Restore font
    if($family)
        $this->SetFont($family, $style, $fontsize);
    // Restore colors
    if($dc!='0 G')
        $this->DrawColor = $dc;
    if($fc!='0 g')
        $this->FillColor = $fc;
    if($tc!='0 g')
        $this->TextColor = $tc;
}

public function SetFont($family, $style='', $size=0)
{
    // Select a font; size given in points
    global $fpdf_charwidths;
    
    $family = strtolower($family);
    if($family=='')
        $family = $this->FontFamily;
    elseif($family=='arial')
        $family = 'helvetica';
    elseif($family=='symbol' || $family=='zapfdingbats')
        $style = '';
    $style = strtoupper($style);
    if(strpos($style, 'U')!==false)
    {
        $this->underline = true;
        $style = str_replace('U', '', $style);
    }
    else
        $this->underline = false;
    if($style=='IB')
        $style = 'BI';
    if($size==0)
        $size = $this->FontSizePt;
    // Test if font is already selected
    if($this->FontFamily==$family && $this->FontStyle==$style && $this->FontSizePt==$size)
        return;
    // Test if font is already loaded
    $fontkey = $family.$style;
    if(!isset($this->fonts[$fontkey]))
    {
        // Test if one of the core fonts
        if($family=='times' || $family=='helvetica' || $family=='courier')
        {
            if($family=='times')
                $family = 'serif';
            $style .= ($style=='') ? '' : 'd';
            $fontkey = $family.$style;
            if(!isset($this->fonts[$fontkey]))
                $this->AddFont($family, $style);
        }
        else
            $this->Error('Undefined font: '.$family.' '.$style);
    }
    // Select it
    $this->FontFamily = $family;
    $this->FontStyle = $style;
    $this->FontSizePt = $size;
    $this->FontSize = $size/$this->k;
    $this->CurrentFont =& $this->fonts[$fontkey];
    if($this->page>0)
        $this->_out(sprintf('BT /F%d %.2F Tf ET', $this->CurrentFont['i'], $this->FontSizePt));
}

public function SetFontSize($size)
{
    // Set font size in points
    if($this->FontSizePt==$size)
        return;
    $this->FontSizePt = $size;
    $this->FontSize = $size/$this->k;
    if($this->page>0)
        $this->_out(sprintf('BT /F%d %.2F Tf ET', $this->CurrentFont['i'], $this->FontSizePt));
}

public function AddLink($x, $y, $w, $h, $link)
{
    // Put a link on the page
    $this->PageLinks[$this->page][] = array($x*$this->k, $this->hPt-$y*$this->k, $w*$this->k, $h*$this->k, $link);
}

public function SetLink($link, $y, $page)
{
    // Set destination of internal link
    if(!isset($this->links[$link]))
        $this->links[$link] = array($page, $y);
    else
        $this->links[$link] = array($page, $y);
}

public function SetLink($link, $y, $page=-1)
{
    // Set destination of internal link
    if($y==-1)
        $y = $this->y;
    if($page==-1)
        $page = $this->page;
    $this->links[$link] = array($page, $y);
}

public function Link($x, $y, $w, $h, $link)
{
    // Put a link on the page
    $this->PageLinks[$this->page][] = array($x*$this->k, $this->hPt-$y*$this->k, $w*$this->k, $h*$this->k, $link);
}

public function Text($x, $y, $txt)
{
    // Output a string
    $s = sprintf('BT %.2F %.2F Td (%s) Tj ET', $x*$this->k, ($this->h-$y)*$this->k, $this->_escape($txt));
    if($this->underline && $txt!='')
        $s .= ' '.$this->_dounderline($x, $y, $txt);
    if($this->ColorFlag)
        $s = $this->_setTextColor($s);
    $this->_out($s);
}

public function AcceptPageBreak()
{
    // Accept automatic page break or not
    return $this->AutoPageBreak;
}

public function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
{
    // Output a cell
    $k = $this->k;
    if($this->y+$h>$this->PageBreakTrigger && !$this->InFooter && $this->AcceptPageBreak())
    {
        // Automatic page break
        $x = $this->x;
        $ws = $this->ws;
        if($ws>0)
        {
            $this->ws = 0;
            $this->_out('0 Tw');
        }
        $this->AddPage($this->CurOrientation, $this->CurPageSize);
        $this->x = $x;
        if($ws>0)
        {
            $this->ws = $ws;
            $this->_out(sprintf('%.3F Tw', $ws*$k));
        }
    }
    if($w==0)
        $w = $this->w-$this->rMargin-$this->x;
    $s = '';
    if($fill || $border==1)
    {
        if($fill)
            $op = ($border==1) ? 'B' : 'f';
        else
            $op = 'S';
        $s = sprintf('%.2F %.2F %.2F %.2F re %s ', $this->x*$k, ($this->h-$this->y)*$k, $w*$k, -$h*$k, $op);
    }
    if(is_string($border))
    {
        $x = $this->x;
        $y = $this->y;
        if(strpos($border, 'L')!==false)
            $s .= sprintf('%.2F %.2F m %.2F %.2F l S ', $x*$k, ($this->h-$y)*$k, $x*$k, ($this->h-($y+$h))*$k);
        if(strpos($border, 'T')!==false)
            $s .= sprintf('%.2F %.2F m %.2F %.2F l S ', $x*$k, ($this->h-$y)*$k, ($x+$w)*$k, ($this->h-$y)*$k);
        if(strpos($border, 'R')!==false)
            $s .= sprintf('%.2F %.2F m %.2F %.2F l S ', ($x+$w)*$k, ($this->h-$y)*$k, ($x+$w)*$k, ($this->h-($y+$h))*$k);
        if(strpos($border, 'B')!==false)
            $s .= sprintf('%.2F %.2F m %.2F %.2F l S ', $x*$k, ($this->h-($y+$h))*$k, ($x+$w)*$k, ($this->h-($y+$h))*$k);
    }
    if($txt!='')
    {
        if($align=='R')
            $dx = $w-$this->cMargin-$this->GetStringWidth($txt);
        elseif($align=='C')
            $dx = ($w-$this->GetStringWidth($txt))/2;
        else
            $dx = $this->cMargin;
        if($this->ColorFlag)
            $s .= $this->_setTextColor(sprintf('BT %.2F %.2F Td (%s) Tj ET', ($this->x+$dx)*$k, ($this->h-($this->y+.5*$h+.3*$this->FontSize))*$k, $this->_escape($txt)));
        else
            $s .= sprintf('BT %.2F %.2F Td (%s) Tj ET', ($this->x+$dx)*$k, ($this->h-($this->y+.5*$h+.3*$this->FontSize))*$k, $this->_escape($txt));
        if($this->underline)
            $s .= ' '.$this->_dounderline($this->x+$dx, $this->y+.5*$h+.3*$this->FontSize, $txt);
        if($link!='')
            $this->Link($this->x+$dx, $this->y+.5*$h-.5*$this->FontSize, $this->GetStringWidth($txt), $this->FontSize, $link);
    }
    if($s)
    {
        if($this->ColorFlag)
            $s = $this->_setTextColor($s);
        $this->_out($s);
    }
    $this->lasth = $h;
    if($ln>0)
    {
        // Go to next line
        $this->y += $h;
        if($ln==1)
            $this->x = $this->lMargin;
        elseif($ln==2)
            $this->x = $this->rMargin;
    }
    else
        $this->x += $w;
}

public function MultiCell($w, $h, $txt, $border=0, $align='J', $fill=false)
{
    // Output text with automatic or explicit line breaks
    $cw = &$this->CurrentFont['cw'];
    if($w==0)
        $w = $this->w-$this->rMargin-$this->x;
    $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
    $s = str_replace("\r", '', $txt);
    $nb = strlen($s);
    if($nb>0 && $s[$nb-1]=="\n")
        $nb--;
    $sep = -1;
    $i = 0;
    $j = 0;
    $l = 0;
    $nl = 1;
    while($i<$nb)
    {
        $c = $s[$i];
        if($c=="\n")
        {
            // Explicit line break
            $this->Cell($w, $h, substr($s, $j, $i-$j), $border, 2, $align, $fill);
            $i++;
            $sep = -1;
            $j = $i;
            $l = 0;
            if($nl==1)
            {
                $this->x = $this->lMargin;
                $w = $this->w-$this->rMargin-$this->x;
                $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
            }
            $nl++;
            continue;
        }
        if($c==' ')
            $sep = $i;
        $l += $cw[$c];
        if($l>$wmax)
        {
            // Automatic line break
            if($sep==-1)
            {
                if($i==$j)
                    $i++;
                $this->Cell($w, $h, substr($s, $j, $i-$j), $border, 2, $align, $fill);
            }
            else
            {
                $this->Cell($w, $h, substr($s, $j, $sep-$j), $border, 2, $align, $fill);
                $i = $sep+1;
            }
            $sep = -1;
            $j = $i;
            $l = 0;
            if($nl==1)
            {
                $this->x = $this->lMargin;
                $w = $this->w-$this->rMargin-$this->x;
                $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
            }
            $nl++;
        }
        else
            $i++;
    }
    // Last chunk
    if($i!=$j)
        $this->Cell($w, $h, substr($s, $j), $border, 2, $align, $fill);
}

public function Write($h, $txt, $link='')
{
    // Output text in flowing mode
    $cw = &$this->CurrentFont['cw'];
    $w = $this->w-$this->rMargin-$this->x;
    $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
    $s = str_replace("\r", '', $txt);
    $nb = strlen($s);
    $sep = -1;
    $i = 0;
    $j = 0;
    $l = 0;
    $nl = 1;
    while($i<$nb)
    {
        $c = $s[$i];
        if($c=="\n")
        {
            $this->Cell($w, $h, substr($s, $j, $i-$j), 0, 2, '', 0, $link);
            $i++;
            $sep = -1;
            $j = $i;
            $l = 0;
            if($nl==1)
            {
                $this->x = $this->lMargin;
                $w = $this->w-$this->rMargin-$this->x;
                $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
            }
            $nl++;
            continue;
        }
        if($c==' ')
            $sep = $i;
        $l += $cw[$c];
        if($l>$wmax)
        {
            // Automatic line break
            if($sep==-1)
            {
                if($this->x>$this->lMargin)
                {
                    // Move to next line
                    $this->x = $this->lMargin;
                    $this->y += $h;
                    $w = $this->w-$this->rMargin-$this->x;
                    $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
                    $i++;
                    $nl++;
                    continue;
                }
                if($i==$j)
                    $i++;
                $this->Cell($w, $h, substr($s, $j, $i-$j), 0, 2, '', 0, $link);
            }
            else
            {
                $this->Cell($w, $h, substr($s, $j, $sep-$j), 0, 2, '', 0, $link);
                $i = $sep+1;
            }
            $sep = -1;
            $j = $i;
            $l = 0;
            if($nl==1)
            {
                $this->x = $this->lMargin;
                $w = $this->w-$this->rMargin-$this->x;
                $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
            }
            $nl++;
        }
        else
            $i++;
    }
    // Last chunk
    if($i!=$j)
        $this->Cell(substr($s, $j), $h, 0, 0, '', 0, $link);
}

public function Ln($h=null)
{
    // Line feed; default value is last cell height
    $this->x = $this->lMargin;
    if($h===null)
        $this->y += $this->lasth;
    else
        $this->y += $h;
}

public function Image($file, $x=null, $y=null, $w=0, $h=0, $type='', $link='')
{
    // Put an image on the page
    if(!isset($this->images[$file]))
    {
        // First use of this image, get info
        if($type=='')
        {
            $pos = strrpos($file, '.');
            $type = substr($file, $pos+1);
        }
        $type = strtolower($type);
        if($type=='jpeg')
            $type = 'jpg';
        $mtd = '_parse'.$type;
        if(!method_exists($this, $mtd))
            $this->Error('Unsupported image type: '.$type);
        $info = $this->$mtd($file);
        $info['i'] = count($this->images)+1;
        $this->images[$file] = $info;
    }
    else
        $info = $this->images[$file];
    
    // Automatic width and height calculation
    if($w==0 && $h==0)
    {
        // Put image at 72 dpi
        $w = $info['w']/72*$this->k;
        $h = $info['h']/72*$this->k;
    }
    elseif($w==0)
        $w = $h*$info['w']/$info['h'];
    elseif($h==0)
        $h = $w*$info['h']/$info['w'];
    
    // Flowing mode
    if($y===null)
    {
        if($this->y+$h>$this->PageBreakTrigger && !$this->InFooter && $this->AcceptPageBreak())
        {
            // Automatic page break
            $x2 = $this->x;
            $this->AddPage($this->CurOrientation, $this->CurPageSize);
            $this->x = $x2;
        }
        $y = $this->y;
    }
    if($x===null)
        $x = $this->x;
    $this->_out(sprintf('q %.2F 0 0 %.2F %.2F %.2F cm /I%d Do Q', $w*$this->k, $h*$this->k, $x*$this->k, ($this->h-($y+$h))*$this->k, $info['i']));
    if($link)
        $this->Link($x, $y, $w, $h, $link);
    return $info;
}

public function GetStringWidth($s)
{
    $s = (string)$s;
    $cw = &$this->CurrentFont['cw'];
    $w = 0;
    $l = strlen($s);
    for($i=0;$i<$l;$i++)
        $w += $cw[$s[$i]];
    return $w*$this->FontSize/1000;
}

public function SetLineWidth($width)
{
    // Set line width
    $this->LineWidth = $width;
    if($this->page>0)
        $this->_out(sprintf('%.2F w', $width*$this->k));
}

public function Line($x1, $y1, $x2, $y2)
{
    // Draw a line
    $this->_out(sprintf('%.2F %.2F m %.2F %.2F l S', $x1*$this->k, ($this->h-$y1)*$this->k, $x2*$this->k, ($this->h-$y2)*$this->k));
}

public function Rect($x, $y, $w, $h, $style='')
{
    // Draw a rectangle
    if($style=='F')
        $op = 'f';
    elseif($style=='FD' || $style=='DF')
        $op = 'B';
    else
        $op = 'S';
    $this->_out(sprintf('%.2F %.2F %.2F %.2F re %s', $x*$this->k, ($this->h-$y)*$this->k, $w*$this->k, -$h*$this->k, $op));
}

public function AddFont($family, $style='', $file='')
{
    global $fpdf_charwidths;
    
    $family = strtolower($family);
    if($family=='arial')
        $family = 'helvetica';
    $style = strtoupper($style);
    if($style=='IB')
        $style = 'BI';
    if(!isset($this->fonts[$family.$style]))
    {
        $i = count($this->fonts)+1;
        if($family=='times' || $family=='helvetica')
        {
            $name = $this->CoreFonts[$family];
            if($family=='times')
                $name .= ($style=='I') ? 'Italic' : ($style=='B') ? 'Bold' : '';
            else
                $name .= ($style=='I') ? 'Oblique' : ($style=='B') ? 'Bold' : '';
            $this->fonts[$family.$style] = array('i'=>$i, 'type'=>'core', 'name'=>$name, 'desc'=>$name);
            $this->FontFiles[$name] = array('length1'=>$name, 'type'=>'core');
        }
        else
            $this->Error('Unsupported font family: '.$family);
    }
}

public function SetFont($family, $style='', $size=0)
{
    // Select a font; size given in points
    global $fpdf_charwidths;
    
    $family = strtolower($family);
    if($family=='')
        $family = $this->FontFamily;
    elseif($family=='arial')
        $family = 'helvetica';
    elseif($family=='symbol' || $family=='zapfdingbats')
        $style = '';
    $style = strtoupper($style);
    if(strpos($style, 'U')!==false)
    {
        $this->underline = true;
        $style = str_replace('U', '', $style);
    }
    else
        $this->underline = false;
    if($style=='IB')
        $style = 'BI';
    if($size==0)
        $size = $this->FontSizePt;
    // Test if font is already selected
    if($this->FontFamily==$family && $this->FontStyle==$style && $this->FontSizePt==$size)
        return;
    // Test if font is already loaded
    $fontkey = $family.$style;
    if(!isset($this->fonts[$fontkey]))
    {
        // Test if one of the core fonts
        if($family=='times' || $family=='helvetica' || $family=='courier')
        {
            if($family=='times')
                $family = 'serif';
            $style .= ($style=='') ? '' : 'd';
            $fontkey = $family.$style;
            if(!isset($this->fonts[$fontkey]))
                $this->AddFont($family, $style);
        }
        else
            $this->Error('Undefined font: '.$family.' '.$style);
    }
    // Select it
    $this->FontFamily = $family;
    $this->FontStyle = $style;
    $this->FontSizePt = $size;
    $this->FontSize = $size/$this->k;
    $this->CurrentFont =& $this->fonts[$fontkey];
    if($this->page>0)
        $this->_out(sprintf('BT /F%d %.2F Tf ET', $this->CurrentFont['i'], $this->FontSizePt));
}

public function SetFontSize($size)
{
    // Set font size in points
    if($this->FontSizePt==$size)
        return;
    $this->FontSizePt = $size;
    $this->FontSize = $size/$this->k;
    if($this->page>0)
        $this->_out(sprintf('BT /F%d %.2F Tf ET', $this->CurrentFont['i'], $this->FontSizePt));
}

public function AddLink($x, $y, $w, $h, $link)
{
    // Put a link on the page
    $this->PageLinks[$this->page][] = array($x*$this->k, $this->hPt-$y*$this->k, $w*$this->k, $h*$this->k, $link);
}

public function SetLink($link, $y, $page=-1)
{
    // Set destination of internal link
    if($y==-1)
        $y = $this->y;
    if($page==-1)
        $page = $this->page;
    $this->links[$link] = array($page, $y);
}

public function Link($x, $y, $w, $h, $link)
{
    // Put a link on the page
    $this->PageLinks[$this->page][] = array($x*$this->k, $this->hPt-$y*$this->k, $w*$this->k, $h*$this->k, $link);
}

public function Text($x, $y, $txt)
{
    // Output a string
    $s = sprintf('BT %.2F %.2F Td (%s) Tj ET', $x*$this->k, ($this->h-$y)*$this->k, $this->_escape($txt));
    if($this->underline && $txt!='')
        $s .= ' '.$this->_dounderline($x, $y, $txt);
    if($this->ColorFlag)
        $s = $this->_setTextColor($s);
    $this->_out($s);
}

public function AcceptPageBreak()
{
    // Accept automatic page break or not
    return $this->AutoPageBreak;
}

public function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
{
    // Output a cell
    $k = $this->k;
    if($this->y+$h>$this->PageBreakTrigger && !$this->InFooter && $this->AcceptPageBreak())
    {
        // Automatic page break
        $x = $this->x;
        $ws = $this->ws;
        if($ws>0)
        {
            $this->ws = 0;
            $this->_out('0 Tw');
        }
        $this->AddPage($this->CurOrientation, $this->CurPageSize);
        $this->x = $x;
        if($ws>0)
        {
            $this->ws = $ws;
            $this->_out(sprintf('%.3F Tw', $ws*$k));
        }
    }
    if($w==0)
        $w = $this->w-$this->rMargin-$this->x;
    $s = '';
    if($fill || $border==1)
    {
        if($fill)
            $op = ($border==1) ? 'B' : 'f';
        else
            $op = 'S';
        $s = sprintf('%.2F %.2F %.2F %.2F re %s ', $this->x*$k, ($this->h-$this->y)*$k, $w*$k, -$h*$k, $op);
    }
    if(is_string($border))
    {
        $x = $this->x;
        $y = $this->y;
        if(strpos($border, 'L')!==false)
            $s .= sprintf('%.2F %.2F m %.2F %.2F l S ', $x*$k, ($this->h-$y)*$k, $x*$k, ($this->h-($y+$h))*$k);
        if(strpos($border, 'T')!==false)
            $s .= sprintf('%.2F %.2F m %.2F %.2F l S ', $x*$k, ($this->h-$y)*$k, ($x+$w)*$k, ($this->h-$y)*$k);
        if(strpos($border, 'R')!==false)
            $s .= sprintf('%.2F %.2F m %.2F %.2F l S ', ($x+$w)*$k, ($this->h-$y)*$k, ($x+$w)*$k, ($this->h-($y+$h))*$k);
        if(strpos($border, 'B')!==false)
            $s .= sprintf('%.2F %.2F m %.2F %.2F l S ', $x*$k, ($this->h-($y+$h))*$k, ($x+$w)*$k, ($this->h-($y+$h))*$k);
    }
    if($txt!='')
    {
        if($align=='R')
            $dx = $w-$this->cMargin-$this->GetStringWidth($txt);
        elseif($align=='C')
            $dx = ($w-$this->GetStringWidth($txt))/2;
        else
            $dx = $this->cMargin;
        if($this->ColorFlag)
            $s .= $this->_setTextColor(sprintf('BT %.2F %.2F Td (%s) Tj ET', ($this->x+$dx)*$k, ($this->h-($this->y+.5*$h+.3*$this->FontSize))*$k, $this->_escape($txt)));
        else
            $s .= sprintf('BT %.2F %.2F Td (%s) Tj ET', ($this->x+$dx)*$k, ($this->h-($this->y+.5*$h+.3*$this->FontSize))*$k, $this->_escape($txt));
        if($this->underline)
            $s .= ' '.$this->_dounderline($this->x+$dx, $this->y+.5*$h+.3*$this->FontSize, $txt);
        if($link!='')
            $this->Link($this->x+$dx, $this->y+.5*$h-.5*$this->FontSize, $this->GetStringWidth($txt), $this->FontSize, $link);
    }
    if($s)
    {
        if($this->ColorFlag)
            $s = $this->_setTextColor($s);
        $this->_out($s);
    }
    $this->lasth = $h;
    if($ln>0)
    {
        // Go to next line
        $this->y += $h;
        if($ln==1)
            $this->x = $this->lMargin;
        elseif($ln==2)
            $this->x = $this->rMargin;
    }
    else
        $this->x += $w;
}

public function MultiCell($w, $h, $txt, $border=0, $align='J', $fill=false)
{
    // Output text with automatic or explicit line breaks
    $cw = &$this->CurrentFont['cw'];
    if($w==0)
        $w = $this->w-$this->rMargin-$this->x;
    $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
    $s = str_replace("\r", '', $txt);
    $nb = strlen($s);
    if($nb>0 && $s[$nb-1]=="\n")
        $nb--;
    $sep = -1;
    $i = 0;
    $j = 0;
    $l = 0;
    $nl = 1;
    while($i<$nb)
    {
        $c = $s[$i];
        if($c=="\n")
        {
            // Explicit line break
            $this->Cell($w, $h, substr($s, $j, $i-$j), $border, 2, $align, $fill);
            $i++;
            $sep = -1;
            $j = $i;
            $l = 0;
            if($nl==1)
            {
                $this->x = $this->lMargin;
                $w = $this->w-$this->rMargin-$this->x;
                $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
            }
            $nl++;
            continue;
        }
        if($c==' ')
            $sep = $i;
        $l += $cw[$c];
        if($l>$wmax)
        {
            // Automatic line break
            if($sep==-1)
            {
                if($i==$j)
                    $i++;
                $this->Cell($w, $h, substr($s, $j, $i-$j), $border, 2, $align, $fill);
            }
            else
            {
                $this->Cell($w, $h, substr($s, $j, $sep-$j), $border, 2, $align, $fill);
                $i = $sep+1;
            }
            $sep = -1;
            $j = $i;
            $l = 0;
            if($nl==1)
            {
                $this->x = $this->lMargin;
                $w = $this->w-$this->rMargin-$this->x;
                $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
            }
            $nl++;
        }
        else
            $i++;
    }
    // Last chunk
    if($i!=$j)
        $this->Cell($w, $h, substr($s, $j), $border, 2, $align, $fill);
}

public function Write($h, $txt, $link='')
{
    // Output text in flowing mode
    $cw = &$this->CurrentFont['cw'];
    $w = $this->w-$this->rMargin-$this->x;
    $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
    $s = str_replace("\r", '', $txt);
    $nb = strlen($s);
    $sep = -1;
    $i = 0;
    $j = 0;
    $l = 0;
    $nl = 1;
    while($i<$nb)
    {
        $c = $s[$i];
        if($c=="\n")
        {
            $this->Cell($w, $h, substr($s, $j, $i-$j), 0, 2, '', 0, $link);
            $i++;
            $sep = -1;
            $j = $i;
            $l = 0;
            if($nl==1)
            {
                $this->x = $this->lMargin;
                $w = $this->w-$this->rMargin-$this->x;
                $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
            }
            $nl++;
            continue;
        }
        if($c==' ')
            $sep = $i;
        $l += $cw[$c];
        if($l>$wmax)
        {
            // Automatic line break
            if($sep==-1)
            {
                if($this->x>$this->lMargin)
                {
                    // Move to next line
                    $this->x = $this->lMargin;
                    $this->y += $h;
                    $w = $this->w-$this->rMargin-$this->x;
                    $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
                    $i++;
                    $nl++;
                    continue;
                }
                if($i==$j)
                    $i++;
                $this->Cell($w, $h, substr($s, $j, $i-$j), 0, 2, '', 0, $link);
            }
            else
            {
                $this->Cell($w, $h, substr($s, $j, $sep-$j), 0, 2, '', 0, $link);
                $i = $sep+1;
            }
            $sep = -1;
            $j = $i;
            $l = 0;
            if($nl==1)
            {
                $this->x = $this->lMargin;
                $w = $this->w-$this->rMargin-$this->x;
                $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
            }
            $nl++;
        }
        else
            $i++;
    }
    // Last chunk
    if($i!=$j)
        $this->Cell(substr($s, $j), $h, 0, 0, '', 0, $link);
}

public function Ln($h=null)
{
    // Line feed; default value is last cell height
    $this->x = $this->lMargin;
    if($h===null)
        $this->y += $this->lasth;
    else
        $this->y += $h;
}

public function Image($file, $x=null, $y=null, $w=0, $h=0, $type='', $link='')
{
    // Put an image on the page
    if(!isset($this->images[$file]))
    {
        // First use of this image, get info
        if($type=='')
        {
            $pos = strrpos($file, '.');
            $type = substr($file, $pos+1);
        }
        $type = strtolower($type);
        if($type=='jpeg')
            $type = 'jpg';
        $mtd = '_parse'.$type;
        if(!method_exists($this, $mtd))
            $this->Error('Unsupported image type: '.$type);
        $info = $this->$mtd($file);
        $info['i'] = count($this->images)+1;
        $this->images[$file] = $info;
    }
    else
        $info = $this->images[$file];
    
    // Automatic width and height calculation
    if($w==0 && $h==0)
    {
        // Put image at 72 dpi
        $w = $info['w']/72*$this->k;
        $h = $info['h']/72*$this->k;
    }
    elseif($w==0)
        $w = $h*$info['w']/$info['h'];
    elseif($h==0)
        $h = $w*$info['h']/$info['w'];
    
    // Flowing mode
    if($y===null)
    {
        if($this->y+$h>$this->PageBreakTrigger && !$this->InFooter && $this->AcceptPageBreak())
        {
            // Automatic page break
            $x2 = $this->x;
            $this->AddPage($this->CurOrientation, $this->CurPageSize);
            $this->x = $x2;
        }
        $y = $this->y;
    }
    if($x===null)
        $x = $this->x;
    $this->_out(sprintf('q %.2F 0 0 %.2F %.2F %.2F cm /I%d Do Q', $w*$this->k, $h*$this->k, $x*$this->k, ($this->h-($y+$h))*$this->k, $info['i']));
    if($link)
        $this->Link($x, $y, $w, $h, $link);
    return $info;
}

public function GetStringWidth($s)
{
    $s = (string)$s;
    $cw = &$this->CurrentFont['cw'];
    $w = 0;
    $l = strlen($s);
    for($i=0;$i<$l;$i++)
        $w += $cw[$s[$i]];
    return $w*$this->FontSize/1000;
}

public function SetLineWidth($width)
{
    // Set line width
    $this->LineWidth = $width;
    if($this->page>0)
        $this->_out(sprintf('%.2F w', $width*$this->k));
}

public function Line($x1, $y1, $x2, $y2)
{
    // Draw a line
    $this->_out(sprintf('%.2F %.2F m %.2F %.2F l S', $x1*$this->k, ($this->h-$y1)*$this->k, $x2*$this->k, ($this->h-$y2)*$this->k));
}

public function Rect($x, $y, $w, $h, $style='')
{
    // Draw a rectangle
    if($style=='F')
        $op = 'f';
    elseif($style=='FD' || $style=='DF')
        $op = 'B';
    else
        $op = 'S';
    $this->_out(sprintf('%.2F %.2F %.2F %.2F re %s', $x*$this->k, ($this->h-$y)*$this->k, $w*$this->k, -$h*$this->k, $op));
}

public function SetTextColor($r, $g=-1, $b=-1)
{
    // Set color for text
    if(($r==0 && $g==-1 && $b==-1))
        $this->TextColor = '0 g';
    elseif(($r==-1 && $g==-1 && $b==-1))
        $this->TextColor = '0 G';
    else
    {
        if($r==-1)
            $r = $g;
        if($g==-1)
            $g = $b;
        if($b==-1)
            $b = $r;
        $this->TextColor = sprintf('%.3F %.3F %.3F rg', $r/255, $g/255, $b/255);
    }
    $this->ColorFlag = ($this->FillColor!=$this->TextColor);
}

public function SetFillColor($r, $g=-1, $b=-1)
{
    // Set color for filling
    if(($r==0 && $g==-1 && $b==-1))
        $this->FillColor = '0 g';
    elseif(($r==-1 && $g==-1 && $b==-1))
        $this->FillColor = '0 G';
    else
    {
        if($r==-1)
            $r = $g;
        if($g==-1)
            $g = $b;
        if($b==-1)
            $b = $r;
        $this->FillColor = sprintf('%.3F %.3F %.3F rg', $r/255, $g/255, $b/255);
    }
    $this->ColorFlag = ($this->FillColor!=$this->TextColor);
}

public function SetDrawColor($r, $g=-1, $b=-1)
{
    // Set color for drawing
    if(($r==0 && $g==-1 && $b==-1))
        $this->DrawColor = '0 g';
    elseif(($r==-1 && $g==-1 && $b==-1))
        $this->DrawColor = '0 G';
    else
    {
        if($r==-1)
            $r = $g;
        if($g==-1)
            $g = $b;
        if($b==-1)
            $b = $r;
        $this->DrawColor = sprintf('%.3F %.3F %.3F rg', $r/255, $g/255, $b/255);
    }
}

public function SetFont($family, $style='', $size=0)
{
    // Select a font; size given in points
    global $fpdf_charwidths;
    
    $family = strtolower($family);
    if($family=='')
        $family = $this->FontFamily;
    elseif($family=='arial')
        $family = 'helvetica';
    elseif($family=='symbol' || $family=='zapfdingbats')
        $style = '';
    $style = strtoupper($style);
    if(strpos($style, 'U')!==false)
    {
        $this->underline = true;
        $style = str_replace('U', '', $style);
    }
    else
        $this->underline = false;
    if($style=='IB')
        $style = 'BI';
    if($size==0)
        $size = $this->FontSizePt;
    // Test if font is already selected
    if($this->FontFamily==$family && $this->FontStyle==$style && $this->FontSizePt==$size)
        return;
    // Test if font is already loaded
    $fontkey = $family.$style;
    if(!isset($this->fonts[$fontkey]))
    {
        // Test if one of the core fonts
        if($family=='times' || $family=='helvetica' || $family=='courier')
        {
            if($family=='times')
                $family = 'serif';
            $style .= ($style=='') ? '' : 'd';
            $fontkey = $family.$style;
            if(!isset($this->fonts[$fontkey]))
                $this->AddFont($family, $style);
        }
        else
            $this->Error('Undefined font: '.$family.' '.$style);
    }
    // Select it
    $this->FontFamily = $family;
    $this->FontStyle = $style;
    $this->FontSizePt = $size;
    $this->FontSize = $size/$this->k;
    $this->CurrentFont =& $this->fonts[$fontkey];
    if($this->page>0)
        $this->_out(sprintf('BT /F%d %.2F Tf ET', $this->CurrentFont['i'], $this->FontSizePt));
}

public function SetFontSize($size)
{
    // Set font size in points
    if($this->FontSizePt==$size)
        return;
    $this->FontSizePt = $size;
    $this->FontSize = $size/$this->k;
    if($this->page>0)
        $this->_out(sprintf('BT /F%d %.2F Tf ET', $this->CurrentFont['i'], $this->FontSizePt));
}

public function AddLink($x, $y, $w, $h, $link)
{
    // Put a link on the page
    $this->PageLinks[$this->page][] = array($x*$this->k, $this->hPt-$y*$this->k, $w*$this->k, $h*$this->k, $link);
}

public function SetLink($link, $y, $page=-1)
{
    // Set destination of internal link
    if($y==-1)
        $y = $this->y;
    if($page==-1)
        $page = $this->page;
    $this->links[$link] = array($page, $y);
}

public function Link($x, $y, $w, $h, $link)
{
    // Put a link on the page
    $this->PageLinks[$this->page][] = array($x*$this->k, $this->hPt-$y*$this->k, $w*$this->k, $h*$this->k, $link);
}

public function Text($x, $y, $txt)
{
    // Output a string
    $s = sprintf('BT %.2F %.2F Td (%s) Tj ET', $x*$this->k, ($this->h-$y)*$this->k, $this->_escape($txt));
    if($this->underline && $txt!='')
        $s .= ' '.$this->_dounderline($x, $y, $txt);
    if($this->ColorFlag)
        $s = $this->_setTextColor($s);
    $this->_out($s);
}

public function AcceptPageBreak()
{
    // Accept automatic page break or not
    return $this->AutoPageBreak;
}

public function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
{
    // Output a cell
    $k = $this->k;
    if($this->y+$h>$this->PageBreakTrigger && !$this->InFooter && $this->AcceptPageBreak())
    {
        // Automatic page break
        $x = $this->x;
        $ws = $this->ws;
        if($ws>0)
        {
            $this->ws = 0;
            $this->_out('0 Tw');
        }
        $this->AddPage($this->CurOrientation, $this->CurPageSize);
        $this->x = $x;
        if($ws>0)
        {
            $this->ws = $ws;
            $this->_out(sprintf('%.3F Tw', $ws*$k));
        }
    }
    if($w==0)
        $w = $this->w-$this->rMargin-$this->x;
    $s = '';
    if($fill || $border==1)
    {
        if($fill)
            $op = ($border==1) ? 'B' : 'f';
        else
            $op = 'S';
        $s = sprintf('%.2F %.2F %.2F %.2F re %s ', $this->x*$k, ($this->h-$this->y)*$k, $w*$this->k, -$h*$this->k, $op);
    }
    if(is_string($border))
    {
        $x = $this->x;
        $y = $this->y;
        if(strpos($border, 'L')!==false)
            $s .= sprintf('%.2F %.2F m %.2F %.2F l S ', $x*$k, ($this->h-$y)*$k, $x*$k, ($this->h-($y+$h))*$k);
        if(strpos($border, 'T')!==false)
            $s .= sprintf('%.2F %.2F m %.2F %.2F l S ', $x*$k, ($this->h-$y)*$k, ($x+$w)*$k, ($this->h-$y)*$k);
        if(strpos($border, 'R')!==false)
            $s .= sprintf('%.2F %.2F m %.2F %.2F l S ', ($x+$w)*$k, ($this->h-$y)*$k, ($x+$w)*$k, ($this->h-($y+$h))*$k);
        if(strpos($border, 'B')!==false)
            $s .= sprintf('%.2F %.2F m %.2F %.2F l S ', $x*$k, ($this->h-($y+$h))*$k, ($x+$w)*$k, ($this->h-($y+$h))*$k);
    }
    if($txt!='')
    {
        if($align=='R')
            $dx = $w-$this->cMargin-$this->GetStringWidth($txt);
        elseif($align=='C')
            $dx = ($w-$this->GetStringWidth($txt))/2;
        else
            $dx = $this->cMargin;
        if($this->ColorFlag)
            $s .= $this->_setTextColor(sprintf('BT %.2F %.2F Td (%s) Tj ET', ($this->x+$dx)*$k, ($this->h-($this->y+.5*$h+.3*$this->FontSize))*$k, $this->_escape($txt)));
        else
            $s .= sprintf('BT %.2F %.2F Td (%s) Tj ET', ($this->x+$dx)*$k, ($this->h-($this->y+.5*$h+.3*$this->FontSize))*$k, $this->_escape($txt));
        if($this->underline)
            $s .= ' '.$this->_dounderline($this->x+$dx, $this->y+.5*$h+.3*$this->FontSize, $txt);
        if($link!='')
            $this->Link($this->x+$dx, $this->y+.5*$h-.5*$this->FontSize, $this->GetStringWidth($txt), $this->FontSize, $link);
    }
    if($s)
    {
        if($this->ColorFlag)
            $s = $this->_setTextColor($s);
        $this->_out($s);
    }
    $this->lasth = $h;
    if($ln>0)
    {
        // Go to next line
        $this->y += $h;
        if($ln==1)
            $this->x = $this->lMargin;
        elseif($ln==2)
            $this->x = $this->rMargin;
    }
    else
        $this->x += $w;
}

public function MultiCell($w, $h, $txt, $border=0, $align='J', $fill=false)
{
    // Output text with automatic or explicit line breaks
    $cw = &$this->CurrentFont['cw'];
    if($w==0)
        $w = $this->w-$this->rMargin-$this->x;
    $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
    $s = str_replace("\r", '', $txt);
    $nb = strlen($s);
    if($nb>0 && $s[$nb-1]=="\n")
        $nb--;
    $sep = -1;
    $i = 0;
    $j = 0;
    $l = 0;
    $nl = 1;
    while($i<$nb)
    {
        $c = $s[$i];
        if($c=="\n")
        {
            // Explicit line break
            $this->Cell($w, $h, substr($s, $j, $i-$j), $border, 2, $align, $fill);
            $i++;
            $sep = -1;
            $j = $i;
            $l = 0;
            if($nl==1)
            {
                $this->x = $this->lMargin;
                $w = $this->w-$this->rMargin-$this->x;
                $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
            }
            $nl++;
            continue;
        }
        if($c==' ')
            $sep = $i;
        $l += $cw[$c];
        if($l>$wmax)
        {
            // Automatic line break
            if($sep==-1)
            {
                if($i==$j)
                    $i++;
                $this->Cell($w, $h, substr($s, $j, $i-$j), $border, 2, $align, $fill);
            }
            else
            {
                $this->Cell($w, $h, substr($s, $j, $sep-$j), $border, 2, $align, $fill);
                $i = $sep+1;
            }
            $sep = -1;
            $j = $i;
            $l = 0;
            if($nl==1)
            {
                $this->x = $this->lMargin;
                $w = $this->w-$this->rMargin-$this->x;
                $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
            }
            $nl++;
        }
        else
            $i++;
    }
    // Last chunk
    if($i!=$j)
        $this->Cell($w, $h, substr($s, $j), $border, 2, $align, $fill);
}

public function Write($h, $txt, $link='')
{
    // Output text in flowing mode
    $cw = &$this->CurrentFont['cw'];
    $w = $this->w-$this->rMargin-$this->x;
    $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
    $s = str_replace("\r", '', $txt);
    $nb = strlen($s);
    $sep = -1;
    $i = 0;
    $j = 0;
    $l = 0;
    $nl = 1;
    while($i<$nb)
    {
        $c = $s[$i];
        if($c=="\n")
        {
            $this->Cell($w, $h, substr($s, $j, $i-$j), 0, 2, '', 0, $link);
            $i++;
            $sep = -1;
            $j = $i;
            $l = 0;
            if($nl==1)
            {
                $this->x = $this->lMargin;
                $w = $this->w-$this->rMargin-$this->x;
                $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
            }
            $nl++;
            continue;
        }
        if($c==' ')
            $sep = $i;
        $l += $cw[$c];
        if($l>$wmax)
        {
            // Automatic line break
            if($sep==-1)
            {
                if($this->x>$this->lMargin)
                {
                    // Move to next line
                    $this->x = $this->lMargin;
                    $this->y += $h;
                    $w = $this->w-$this->rMargin-$this->x;
                    $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
                    $i++;
                    $nl++;
                    continue;
                }
                if($i==$j)
                    $i++;
                $this->Cell($w, $h, substr($s, $j, $i-$j), 0, 2, '', 0, $link);
            }
            else
            {
                $this->Cell($w, $h, substr($s, $j, $sep-$j), 0, 2, '', 0, $link);
                $i = $sep+1;
            }
            $sep = -1;
            $j = $i;
            $l = 0;
            if($nl==1)
            {
                $this->x = $this->lMargin;
                $w = $this->w-$this->rMargin-$this->x;
                $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
            }
            $nl++;
        }
        else
            $i++;
    }
    // Last chunk
    if($i!=$j)
        $this->Cell(substr($s, $j), $h, 0, 0, '', 0, $link);
}

public function Ln($h=null)
{
    // Line feed; default value is last cell height
    $this->x = $this->lMargin;
    if($h===null)
        $this->y += $this->lasth;
    else
        $this->y += $h;
}

public function Image($file, $x=null, $y=null, $w=0, $h=0, $type='', $link='')
{
    // Put an image on the page
    if(!isset($this->images[$file]))
    {
        // First use of this image, get info
        if($type=='')
        {
            $pos = strrpos($file, '.');
            $type = substr($file, $pos+1);
        }
        $type = strtolower($type);
        if($type=='jpeg')
            $type = 'jpg';
        $mtd = '_parse'.$type;
        if(!method_exists($this, $mtd))
            $this->Error('Unsupported image type: '.$type);
        $info = $this->$mtd($file);
        $info['i'] = count($this->images)+1;
        $this->images[$file] = $info;
    }
    else
        $info = $this->images[$file];
    
    // Automatic width and height calculation
    if($w==0 && $h==0)
    {
        // Put image at 72 dpi
        $w = $info['w']/72*$this->k;
        $h = $info['h']/72*$this->k;
    }
    elseif($w==0)
        $w = $h*$info['w']/$info['h'];
    elseif($h==0)
        $h = $w*$info['h']/$info['w'];
    
    // Flowing mode
    if($y===null)
    {
        if($this->y+$h>$this->PageBreakTrigger && !$this->InFooter && $this->AcceptPageBreak())
        {
            // Automatic page break
            $x2 = $this->x;
            $this->AddPage($this->CurOrientation, $this->CurPageSize);
            $this->x = $x2;
        }
        $y = $this->y;
    }
    if($x===null)
        $x = $this->x;
    $this->_out(sprintf('q %.2F 0 0 %.2F %.2F %.2F cm /I%d Do Q', $w*$this->k, $h*$this->k, $x*$this->k, ($this->h-($y+$h))*$this->k, $info['i']));
    if($link)
        $this->Link($x, $y, $w, $h, $link);
    return $info;
}

public function GetStringWidth($s)
{
    $s = (string)$s;
    $cw = &$this->CurrentFont['cw'];
    $w = 0;
    $l = strlen($s);
    for($i=0;$i<$l;$i++)
        $w += $cw[$s[$i]];
    return $w*$this->FontSize/1000;
}

public function SetLineWidth($width)
{
    // Set line width
    $this->LineWidth = $width;
    if($this->page>0)
        $this->_out(sprintf('%.2F w', $width*$this->k));
}

public function Line($x1, $y1, $x2, $y2)
{
    // Draw a line
    $this->_out(sprintf('%.2F %.2F m %.2F %.2F l S', $x1*$this->k, ($this->h-$y1)*$this->k, $x2*$this->k, ($this->h-$y2)*$this->k));
}

public function Rect($x, $y, $w, $h, $style='')
{
    // Draw a rectangle
    if($style=='F')
        $op = 'f';
    elseif($style=='FD' || $style=='DF')
        $op = 'B';
    else
        $op = 'S';
    $this->_out(sprintf('%.2F %.2F %.2F %.2F re %s', $x*$this->k, ($this->h-$y)*$this->k, $w*$this->k, -$h*$this->k, $op));
}

public function Circle($x, $y, $r, $style='')
{
    // Draw a circle
    $this->Ellipse($x, $y, $r, $r, 0, $style);
}

public function Ellipse($x, $y, $rx, $ry, $angle=0, $style='')
{
    // Draw an ellipse
    if($style!='F' && $style!='FD' && $style!='DF' && $style!='')
        $style = '';
    
    $cx = $x*$this->k;
    $cy = ($this->h-$y)*$this->k;
    $rx = $rx*$this->k;
    $ry = $ry*$this->k;
    
    if($style=='F')
        $op = 'f';
    elseif($style=='FD' || $style=='DF')
        $op = 'B';
    else
        $op = 'S';
    
    $this->_out(sprintf('q %.2F %.2F %.2F %.2F %.2F cm %s Q', $cx, $cy, $rx, $ry, $angle, $op));
}

public function Output($name='', $dest='')
{
    // Output PDF to some destination
    if($this->state<3)
        $this->Close();
    if($dest=='')
    {
        if($name=='')
        {
            $name = 'doc.pdf';
            $dest = 'I';
        }
        else
            $dest = 'F';
    }
    switch($dest)
    {
        case 'I':
            // Send to standard output
            if(ob_get_contents())
                $this->Error('Some data has already been output, can\'t send PDF file');
            if(php_sapi_name()!='cli')
            {
                // We send to a browser
                header('Content-Type: application/pdf');
                if(headers_sent())
                    $this->Error('Some data has already been output to browser, can\'t send PDF file');
                header('Content-Length: '.strlen($this->buffer));
                header('Content-disposition: inline; filename="'.$name.'"');
                header('Cache-Control: private, max-age=0, must-revalidate');
                header('Pragma: public');
            }
            echo $this->buffer;
            break;
        case 'D':
            // Download file
            if(ob_get_contents())
                $this->Error('Some data has already been output, can\'t send PDF file');
            header('Content-Type: application/force-download');
            header('Content-Disposition: attachment; filename="'.$name.'"');
            header('Content-Length: '.strlen($this->buffer));
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');
            echo $this->buffer;
            break;
        case 'F':
            // Save to local file
            $f = fopen($name, 'wb');
            if(!$f)
                $this->Error('Unable to create output file: '.$name);
            fwrite($f, $this->buffer);
            fclose($f);
            break;
        case 'S':
            // Return as a string
            return $this->buffer;
        default:
            $this->Error('Incorrect output destination: '.$dest);
    }
    return '';
}

// Protected methods
protected function _dochecks()
{
    // Check for locale-related bug
    if(1.1==1)
        $this->Error('Don\'t alter the locale before including class file');
    // Check for magic quotes runtime
    if(get_magic_quotes_runtime())
        $this->Error('Magic quotes runtime is enabled');
}

protected function _getpagesize($size)
{
    if(is_string($size))
    {
        $size = strtolower($size);
        if(!isset($this->PageSizes[$size]))
            $this->Error('Unknown page size: '.$size);
        $a = $this->PageSizes[$size];
        return array($a[0]/72.27, $a[1]/72.27);
    }
    else
        return array($size[0]/72.27, $size[1]/72.27);
}

protected function _beginpage($orientation, $size)
{
    $this->page++;
    $this->pages[$this->page] = '';
    $this->state = 2;
    $this->x = $this->lMargin;
    $this->y = $this->tMargin;
    $this->FontFamily = '';
    // Check page size and orientation
    if($orientation=='')
        $orientation = $this->DefOrientation;
    else
    {
        $orientation = strtoupper($orientation);
        if($orientation!='P' && $orientation!='L')
            $orientation = $this->DefOrientation;
    }
    if($size=='')
        $size = $this->DefPageSize;
    else
        $size = $this->_getpagesize($size);
    if($orientation!=$this->CurOrientation || $size[0]!=$this->CurPageSize[0] || $size[1]!=$this->CurPageSize[1])
    {
        // New size or orientation
        if($orientation=='P')
        {
            $this->w = $size[0];
            $this->h = $size[1];
        }
        else
        {
            $this->w = $size[1];
            $this->h = $size[0];
        }
        $this->wPt = $this->w*$this->k;
        $this->hPt = $this->h*$this->k;
        $this->PageBreakTrigger = $this->h-$this->bMargin;
        $this->CurOrientation = $orientation;
        $this->CurPageSize = $size;
    }
    if($orientation!=$this->DefOrientation || $size[0]!=$this->DefPageSize[0] || $size[1]!=$this->DefPageSize[1])
        $this->OrientationChanges[$this->page] = $orientation;
}

protected function _endpage()
{
    // End of page content
    $this->state = 1;
}

protected function _enddoc()
{
    // End of document
    $this->_putpages();
    $this->_putresources();
    // Info
    $this->_newobj();
    $this->_out('<<');
    $this->_putinfo();
    $this->_out('>>');
    $this->_newobj();
    $this->_out('<<');
    $this->_putcatalog();
    $this->_out('>>');
    // Cross-ref
    $o = $this->_newobj();
    $this->_out('<<');
    $this->_putxref();
    $this->_out('>>');
    $this->_out('trailer');
    $this->_out('startxref');
    $this->_out($o);
    $this->_out('%%EOF');
    $this->state = 3;
}

protected function _putheader()
{
    $this->_out('%PDF-'.$this->PDFVersion);
}

protected function _puttrailer()
{
    $this->_out('endobj');
}

protected function _beginpage($orientation='', $size='')
{
    $this->page++;
    $this->pages[$this->page] = '';
    $this->state = 2;
    $this->x = $this->lMargin;
    $this->y = $this->tMargin;
    $this->FontFamily = '';
    // Check page size and orientation
    if($orientation=='')
        $orientation = $this->DefOrientation;
    else
    {
        $orientation = strtoupper($orientation);
        if($orientation!='P' && $orientation!='L')
            $orientation = $this->DefOrientation;
    }
    if($size=='')
        $size = $this->DefPageSize;
    else
        $size = $this->_getpagesize($size);
    if($orientation!=$this->CurOrientation || $size[0]!=$this->CurPageSize[0] || $size[1]!=$this->CurPageSize[1])
    {
        // New size or orientation
        if($orientation=='P')
        {
            $this->w = $size[0];
            $this->h = $size[1];
        }
        else
        {
            $this->w = $size[1];
            $this->h = $size[0];
        }
        $this->wPt = $this->w*$this->k;
        $this->hPt = $this->h*$this->k;
        $this->PageBreakTrigger = $this->h-$this->bMargin;
        $this->CurOrientation = $orientation;
        $this->CurPageSize = $size;
    }
    if($orientation!=$this->DefOrientation || $size[0]!=$this->DefPageSize[0] || $size[1]!=$this->DefPageSize[1])
        $this->OrientationChanges[$this->page] = $orientation;
}

protected function _endpage()
{
    // End of page content
    $this->state = 1;
}

protected function _newobj()
{
    // Begin a new object
    $this->n++;
    $this->offsets[$this->n] = strlen($this->buffer);
    $this->_out($this->n.' 0 obj');
    return $this->n;
}

protected function _out($s)
{
    // Add a line to the document
    if($this->state==2)
        $this->pages[$this->page] .= $s."\n";
    else
        $this->buffer .= $s."\n";
}

protected function _putpages()
{
    $nb = $this->page;
    if(!empty($this->OrientationChanges))
    {
        // Reorient pages in the catalog
        for($i=1;$i<=$nb;$i++)
            $this->_putpage($i);
    }
    else
    {
        for($i=1;$i<=$nb;$i++)
            $this->_putpage($i);
    }
}

protected function _putpage($n)
{
    $this->state = 2;
    $this->_out($this->pages[$n]);
    $this->_out('endobj');
    $this->state = 1;
}

protected function _putresources()
{
    $this->_putfonts();
    $this->_putimages();
    $this->_out('/ProcSet [/PDF /Text /ImageB /ImageC /ImageI]');
    $this->_out('/Font <<');
    foreach($this->fonts as $font)
        $this->_out('/F'.$font['i'].' '.$font['i'].' 0 R');
    $this->_out('>>');
    $this->_out('/XObject <<');
    if(count($this->images)>0)
    {
        $this->_out('/Image <<');
        foreach($this->images as $image)
            $this->_out('/I'.$image['i'].' '.$image['i'].' 0 R');
        $this->_out('>>');
    }
    $this->_out('>>');
}

protected function _putfonts()
{
    $nf = $this->n;
    foreach($this->fonts as $k=>$font)
    {
        $this->_newobj();
        $this->_out('<</Type /Font');
        $this->_out('/Subtype /Type1');
        $this->_out('/BaseFont /'.$font['name']);
        if($font['type']!='core')
        {
            $this->_out('/Name /F'.$font['i']);
            $this->_out('/FirstChar 32');
            $this->_out('/LastChar 255');
            $this->_out('/Widths '.$font['i'].' 0 R');
            $this->_out('/FontDescriptor '.$font['i'].' 0 R');
            $this->_out('/Encoding /WinAnsiEncoding');
        }
        $this->_out('>>');
        $this->_putfontdescriptor($font);
    }
}

protected function _putfontdescriptor($font)
{
    $this->_newobj();
    $this->_out('<</Type /FontDescriptor');
    $this->_out('/FontName /'.$font['name']);
    $this->_out('/Flags 32');
    $this->_out('/FontBBox [0 -200 1000 900]');
    $this->_out('/MissingWidth 250');
    $this->_out('/StemV 80');
    $this->_out('/StemH 80');
    $this->_out('/CapHeight 700');
    $this->_out('/Ascent 800');
    $this->_out('/Descent -200');
    $this->_out('/ItalicAngle 0');
    $this->_out('>>');
}

protected function _putimages()
{
    // Put image objects
    foreach($this->images as $image)
        $this->_putimage($image);
}

protected function _putimage($image)
{
    $this->_newobj();
    $this->_out('<</Type /XObject');
    $this->_out('/Subtype /Image');
    $this->_out('/Width '.$image['w']);
    $this->_out('/Height '.$image['h']);
    $this->_out('/ColorSpace /DeviceRGB');
    $this->_out('/BitsPerComponent 8');
    $this->_out('/Length '.$image['size']);
    $this->_out('>>');
    $this->_newobj();
    $this->_out('<<');
    $this->_out('/Length '.$image['size']);
    $this->_out('/Filter /FlateDecode');
    $this->_out('>>');
    $this->_out('stream');
    $this->_out($image['data']);
    $this->_out('endstream');
    $this->_out('endobj');
}

protected function _putxref()
{
    $this->_out('xref');
    $this->_out('0 '.($this->n+1));
    for($i=1;$i<=$this->n;$i++)
        $this->_out(sprintf('%010d 00000 n ', $this->offsets[$i]));
}

protected function _putcatalog()
{
    $this->_out('/Type /Catalog');
    $this->_out('/Pages 1 0 R');
    if($this->ZoomMode=='fullpage' || $this->ZoomMode=='fullwidth' || $this->ZoomMode=='real' || $this->ZoomMode=='default')
        $this->_out('/OpenAction ['.$this->page.'.' 0 R /Fit '.$this->ZoomMode.']');
    if($this->LayoutMode=='single' || $this->LayoutMode=='continuous' || $this->LayoutMode=='two' || $this->LayoutMode=='default')
        $this->_out('/PageLayout /'.$this->LayoutMode);
}

protected function _putinfo()
{
    $this->_out('/Producer '.$this->_textstring('FPDF '.$this->PDFVersion));
    if(!empty($this->title))
        $this->_out('/Title '.$this->_textstring($this->title));
    if(!empty($this->subject))
        $this->_out('/Subject '.$this->_textstring($this->subject));
    if(!empty($this->author))
        $this->_out('/Author '.$this->_textstring($this->author));
    if(!empty($this->keywords))
        $this->_out('/Keywords '.$this->_textstring($this->keywords));
    if(!empty($this->creator))
        $this->_out('/Creator '.$this->_textstring($this->creator));
    $this->_out('/CreationDate '.$this->_datestring('D:' . date('YmdHis')));
    $this->_out('/ModDate '.$this->_datestring('D:' . date('YmdHis')));
}

protected function _textstring($s)
{
    // Convert a string to UTF-16BE
    return '('.$this->_escape($s).')';
}

protected function _datestring($s)
{
    // Convert a date string to PDF format
    return '('.$s.')';
}

protected function _escape($s)
{
    // Escape special characters in strings
    $s = str_replace('\\', '\\\\', $s);
    $s = str_replace('(', '\\(', $s);
    $s = str_replace(')', '\\)', $s);
    $s = str_replace("\r", '\\r', $s);
    return $s;
}

protected function _dounderline($x, $y, $txt)
{
    // Underline text
    $up = $this->FontSizePt;
    $w = $this->GetStringWidth($txt)+$this->ws*substr_count($txt);
    return sprintf('%.2F %.2F %.2F %.2F re f', $x*$this->k, ($this->h-($y+$up/1000*$this->k))*$this->k, $w*$this->k, -$up/1000*$this->k);
}

protected function _setTextColor($s)
{
    // Set text color
    if($this->TextColor!='0 g')
        $s .= ' '.$this->TextColor;
    return $s;
}

protected function _parsejpg($file)
{
    // Extract info from a JPEG file
    $a = getimagesize($file);
    if(!$a)
        $this->Error('Missing or incorrect image file: '.$file);
    if($a[2]!=2)
        $this->Error('Not a JPEG file: '.$file);
    if(!isset($a['channels']) || $a['channels']==3)
        $colspace = 'DeviceRGB';
    elseif($a['channels']==4)
        $colspace = 'DeviceCMYK';
    else
        $colspace = 'DeviceGray';
    $bpc = isset($a['bits']) ? $a['bits'] : 8;
    $data = file_get_contents($file);
    return array('w'=>$a[0], 'h'=>$a[1], 'cs'=>$colspace, 'bpc'=>$bpc, 'f'=>'jpg', 'data'=>$data, 'size'=>strlen($data));
}

protected function _parsepng($file)
{
    // Extract info from a PNG file
    $f = fopen($file, 'rb');
    if(!$f)
        $this->Error('Can\'t open image file: '.$file);
    $info = $this->_parsepngstream($f, $file);
    fclose($f);
    if($info==0)
        $this->Error('Not a PNG file: '.$file);
    return $info;
}

protected function _parsepngstream($f, $file)
{
    // Check signature
    $sig = fread($f, 8);
    if(substr($sig, 0, 8)!='\x89PNG\r\n\x1a\n')
        return 0;
    // Read header chunk
    fread($f, 4);
    $chunk = fread($f, 4);
    if($chunk!='IHDR')
        return 0;
    $data = fread($f, 13);
    $w = (ord($data[0])<<24) | (ord($data[1])<<16) | (ord($data[2])<<8) | ord($data[3]);
    $h = (ord($data[4])<<24) | (ord($data[5])<<16) | (ord($data[6])<<8) | ord($data[7]);
    $bpc = ord($data[8]);
    $ct = ord($data[9]);
    if($ct==0)
        $colspace = 'DeviceGray';
    elseif($ct==2)
        $colspace = 'DeviceRGB';
    elseif($ct==3)
        $colspace = 'DeviceRGB';
    elseif($ct==4)
        $colspace = 'DeviceCMYK';
    else
        return 0;
    if($bpc>8)
        return 0;
    return array('w'=>$w, 'h'=>$h, 'cs'=>$colspace, 'bpc'=>$bpc, 'f'=>'png', 'data'=>null);
}

protected $PDFVersion = '1.3';
protected $state = 0;
protected $page = 0;
protected $n = 0;
protected $offsets = array();
protected $buffer = '';
protected $pages = array();
protected $fonts = array();
protected $images = array();
protected $PageLinks = array();
protected $links = array();
protected $FontFamily = '';
protected $FontStyle = '';
protected $FontSizePt = 12;
protected $underline = false;
protected $DrawColor = '0 G';
protected $FillColor = '0 g';
protected $TextColor = '0 g';
protected $ColorFlag = false;
protected $ws = 0;
protected $AutoPageBreak = true;
protected $PageBreakTrigger = 0;
protected $InFooter = false;
protected $ZoomMode;
protected $LayoutMode;
protected $title = '';
protected $subject = '';
protected $author = '';
protected $keywords = '';
protected $creator = '';
protected $AliasNbPages = '{nb}';
protected $DefOrientation = 'P';
protected $CurOrientation = 'P';
protected $OrientationChanges = array();
protected $k = 72;
protected $DefPageSize = array(595.28,841.89);
protected $CurPageSize = array(595.28,841.89);
protected $PageSizes = array();
protected $wPt, $hPt;
protected $w, $h;
protected $lMargin;
protected $tMargin;
protected $rMargin;
protected $bMargin;
protected $cMargin;
protected $x, $y;
protected $lasth;
protected $LineWidth;
protected $CoreFont;
protected $CoreFontSize;
protected $FontFiles = array();
protected $fonts = array();
protected $CurrentFont;
protected $FontSize;
protected $CoreFonts = array('courier', 'helvetica', 'times', 'symbol', 'zapfdingbats');
}
?>
