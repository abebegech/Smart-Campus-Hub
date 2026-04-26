<?php
/*******************************************************************************
* FPDF                                                                         *
*                                                                              *
* Copyright (c) 2001-2014  Olivier PLATHEY                                       *
*                                                                              *
* Version 1.8                                                                 *
*                                                                              *
* For the latest version and updates visit:                                    *
*      http://www.fpdf.org                                                     *
*------------------------------------------------------------------------------*
* This library is free software; you can redistribute it and/or                *
* modify it under the terms of the GNU Lesser General Public                   *
* License as published by the Free Software Foundation; either                 *
* version 2.1 of the License, or (at your option) any later version.           *
*                                                                              *
* This library is distributed in the hope that it will be useful,               *
* but WITHOUT ANY WARRANTY; without even the implied warranty of                *
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU               *
* Lesser General Public License for more details.                               *
*                                                                              *
* You should have received a copy of the GNU Lesser General Public              *
* License along with this library; if not, write to the Free Software            *
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA     *
*******************************************************************************/

define('FPDF_VERSION','1.8');

class FPDF
{
protected $page;               // current page number
protected $n;                  // current object number
protected $offsets;            // array of object offsets
protected $buffer;             // buffer holding in-memory PDF
protected $pages;              // array containing pages
protected $state;              // current document state
protected $compress;           // compression flag
protected $k;                  // scale factor (number of points in user unit)
protected $DefOrientation;     // default orientation
protected $CurOrientation;     // current orientation
protected $StdPageSizes;       // standard page sizes
protected $DefPageSize;         // default page size
protected $CurPageSize;         // current page size
protected $CurRotation;        // current page rotation
protected $PageInfo;           // page-related data
protected $wPt, $hPt;          // dimensions of current page in points
protected $w, $h;              // dimensions of current page in user unit
protected $lMargin;            // left margin
protected $tMargin;            // top margin
protected $rMargin;            // right margin
protected $bMargin;            // page break margin
protected $cMargin;            // cell margin
protected $x, $y;              // current position in user unit
protected $lasth;              // height of last printed cell
protected $LineWidth;          // line width in user unit
protected $fontpath;           // path containing fonts
protected $fonts;              // array of used fonts
protected $FontFiles;          // array of font files
protected $diffs;              // array of encoding differences
protected $FontFamily;         // current font family
protected $FontStyle;          // current font style
protected $underline;          // underlining flag
protected $CurrentFont;        // current font info
protected $FontSizePt;         // current font size in points
protected $FontSize;           // current font size in user unit
protected $DrawColor;          // draw color
protected $FillColor;          // fill color
protected $TextColor;          // text color
protected $ColorFlag;          // indicates whether fill and text colors are different
protected $ws;                 // word spacing
protected $images;             // array of used images
protected $PageLinks;          // array of links in pages
protected $links;              // array of internal links
protected $AutoPageBreak;      // automatic page breaking
protected $PageBreakTrigger;   // threshold used to trigger page breaks
protected $InHeader;           // flag set when processing header
protected $InFooter;           // flag set when processing footer
protected $AliasNbPages;       // alias for total number of pages
protected $PDFVersion;         // PDF version number

/*******************************************************************************
*                                                                              *
*                               Public methods                                 *
*                                                                              *
*******************************************************************************/
function __construct($orientation='P', $unit='mm', $size='A4')
{
    // Some checks
    $this->_dochecks();
    // Initialization of properties
    $this->state = 0;
    $this->page = 0;
    $this->n = 2;
    $this->buffer = '';
    $this->pages = array();
    $this->PageInfo = array();
    $this->fonts = array();
    $this->FontFiles = array();
    $this->diffs = array();
    $this->images = array();
    $this->links = array();
    $this->InHeader = false;
    $this->InFooter = false;
    $this->lasth = 0;
    $this->FontFamily = '';
    $this->FontStyle = '';
    $this->FontSizePt = 12;
    $this->underline = false;
    $this->DrawColor = '0 G';
    $this->FillColor = '0 g';
    $this->TextColor = '0 g';
    $this->ColorFlag = false;
    $this->ws = 0;
    // Font path
    if(defined('FPDF_FONTPATH'))
        $this->fontpath = FPDF_FONTPATH;
    else if(dirname($_SERVER['SCRIPT_FILENAME']).'/fonts')
        $this->fontpath = dirname($_SERVER['SCRIPT_FILENAME']).'/fonts';
    else
        $this->fontpath = '';
    // Core fonts
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
    $this->StdPageSizes = array('a3'=>array(841.89,1190.55), 'a4'=>array(595.28,841.89), 'a5'=>array(420.94,595.28),
    'letter'=>array(612,792), 'legal'=>array(612,1008));
    $size = $this->_getpagesize($size);
    $this->DefPageSize = $size;
    $this->CurPageSize = $size;
    // Page orientation
    $orientation = strtolower($orientation);
    if($orientation=='p' || $orientation=='portrait')
    {
        $this->DefOrientation = 'P';
        $this->w = $size[0];
        $this->h = $size[1];
    }
    elseif($orientation=='l' || $orientation=='landscape')
    {
        $this->DefOrientation = 'L';
        $this->w = $size[1];
        $this->h = $size[0];
    }
    else
        $this->Error('Incorrect orientation: '.$orientation);
    $this->CurOrientation = $this->DefOrientation;
    $this->wPt = $this->w*$this->k;
    $this->hPt = $this->h*$this->k;
    // Page margins (1 cm)
    $margin = 28.35/$this->k;
    $this->SetMargins($margin,$margin);
    // Interior cell margin (1 mm)
    $this->cMargin = $margin/10;
    // Line width (0.2 mm)
    $this->LineWidth = 567/$this->k;
    // Automatic page break
    $this->SetAutoPageBreak(true,2*$margin);
    // Default display mode
    $this->SetDisplayMode('default');
    // Enable compression
    $this->SetCompression(true);
    // Set default PDF version number
    $this->PDFVersion = '1.3';
}

function SetMargins($left, $top, $right=null)
{
    // Set left, top and right margins
    $this->lMargin = $left;
    $this->tMargin = $top;
    if($right===null)
        $right = $left;
    $this->rMargin = $right;
}

function SetLeftMargin($margin)
{
    // Set left margin
    $this->lMargin = $margin;
    if($this->page>0 && $this->x<$margin)
        $this->x = $margin;
}

function SetTopMargin($margin)
{
    // Set top margin
    $this->tMargin = $margin;
}

function SetRightMargin($margin)
{
    // Set right margin
    $this->rMargin = $margin;
}

function SetAutoPageBreak($auto, $margin=0)
{
    // Set auto page break mode and triggering margin
    $this->AutoPageBreak = $auto;
    $this->bMargin = $margin;
    return $this->PageBreakTrigger = $this->h-$margin;
}

function SetDisplayMode($zoom, $layout='default')
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

function SetCompression($compress)
{
    // Set page compression
    if(function_exists('gzcompress'))
        $this->compress = $compress ? true : false;
    else
        $this->compress = false;
}

function SetTitle($title, $isUTF8=false)
{
    // Title of document
    if($isUTF8)
        $title = $this->_UTF8toUTF16($title);
    $this->title = $title;
}

function SetAuthor($author, $isUTF8=false)
{
    // Author of document
    if($isUTF8)
        $author = $this->_UTF8toUTF16($author);
    $this->author = $author;
}

function SetSubject($subject, $isUTF8=false)
{
    // Subject of document
    if($isUTF8)
        $subject = $this->_UTF8toUTF16($subject);
    $this->subject = $subject;
}

function SetCreator($creator, $isUTF8=false)
{
    // Creator of document
    if($isUTF8)
        $creator = $this->_UTF8toUTF16($creator);
    $this->creator = $creator;
}

function SetKeywords($keywords, $isUTF8=false)
{
    // Keywords of document
    if($isUTF8)
        $keywords = $this->_UTF8toUTF16($keywords);
    $this->keywords = $keywords;
}

function AliasNbPages($alias='{nb}')
{
    // Define an alias for total number of pages
    $this->AliasNbPages = $alias;
}

function Error($msg)
{
    // Fatal error
    throw new Exception('FPDF error: '.$msg);
}

function Close()
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

function AddPage($orientation='', $size='', $rotation=0)
{
    // Start a new page
    if($this->state==3)
        $this->Error('The document is closed');
    $family = $this->FontFamily;
    $style = $this->FontStyle;
    $size = $this->FontSizePt;
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
    $this->_beginpage($orientation,$size,$rotation);
    // Set line cap style to butt
    $this->_out('2 J');
    // Set line join style to miter
    $this->_out('0 j');
    // Set line width
    $this->LineWidth = $lw;
    $this->_out(sprintf('%.2F w',$lw*$this->k));
    // Set font
    if($family)
        $this->SetFont($family,$style,$size);
    // Set colors
    $this->DrawColor = $dc;
    if($dc!='0 G')
        $this->_out($dc);
    $this->FillColor = $fc;
    if($fc!='0 g')
        $this->_out($fc);
    $this->TextColor = $tc;
    $this->ColorFlag = $cf;
    // Page header
    $this->InHeader = true;
    $this->Header();
    $this->InHeader = false;
    // Restore line width
    if($this->LineWidth!=$lw)
    {
        $this->LineWidth = $lw;
        $this->_out(sprintf('%.2F w',$lw*$this->k));
    }
    // Restore font
    if($family)
        $this->SetFont($family,$style,$size);
    // Restore colors
    if($this->DrawColor!=$dc)
    {
        $this->DrawColor = $dc;
        $this->_out($dc);
    }
    if($this->FillColor!=$fc)
    {
        $this->FillColor = $fc;
        $this->_out($fc);
    }
    $this->TextColor = $tc;
    $this->ColorFlag = $cf;
}

function Header()
{
    // To be implemented in your own inherited class
}

function Footer()
{
    // To be implemented in your own inherited class
}

function PageNo()
{
    // Get current page number
    return $this->page;
}

function SetDrawColor($r, $g=null, $b=null)
{
    // Set color for all stroking operations
    if(($r==0 && $g==0 && $b==0) || $g===null)
        $this->DrawColor = sprintf('%.3F G',$r/255);
    else
        $this->DrawColor = sprintf('%.3F %.3F %.3F RG',$r/255,$g/255,$b/255);
    if($this->page>0)
        $this->_out($this->DrawColor);
}

function SetFillColor($r, $g=null, $b=null)
{
    // Set color for all filling operations
    if(($r==0 && $g==0 && $b==0) || $g===null)
        $this->FillColor = sprintf('%.3F g',$r/255);
    else
        $this->FillColor = sprintf('%.3F %.3F %.3F rg',$r/255,$g/255,$b/255);
    $this->ColorFlag = ($this->FillColor!=$this->TextColor);
    if($this->page>0)
        $this->_out($this->FillColor);
}

function SetTextColor($r, $g=null, $b=null)
{
    // Set color for text
    if(($r==0 && $g==0 && $b==0) || $g===null)
        $this->TextColor = sprintf('%.3F g',$r/255);
    else
        $this->TextColor = sprintf('%.3F %.3F %.3F rg',$r/255,$g/255,$b/255);
    $this->ColorFlag = ($this->FillColor!=$this->TextColor);
}

function GetStringWidth($s)
{
    // Get width of a string in the current font
    $s = (string)$s;
    $cw = &$this->CurrentFont['cw'];
    $w = 0;
    $l = strlen($s);
    for($i=0;$i<$l;$i++)
        $w += $cw[$s[$i]];
    return $w*$this->FontSize/1000;
}

function SetLineWidth($width)
{
    // Set line width
    $this->LineWidth = $width;
    if($this->page>0)
        $this->_out(sprintf('%.2F w',$width*$this->k));
}

function Line($x1, $y1, $x2, $y2)
{
    // Draw a line
    $this->_out(sprintf('%.2F %.2F m %.2F %.2F l S',$x1*$this->k,($this->h-$y1)*$this->k,$x2*$this->k,($this->h-$y2)*$this->k));
}

function Rect($x, $y, $w, $h, $style='')
{
    // Draw a rectangle
    if($style=='F')
        $op = 'f';
    elseif($style=='FD' || $style=='DF')
        $op = 'B';
    else
        $op = 'S';
    $this->_out(sprintf('%.2F %.2F %.2F %.2F re %s',$x*$this->k,($this->h-$y)*$this->k,$w*$this->k,-$h*$this->k,$op));
}

function AddFont($family, $style='', $file='')
{
    // Add a TrueType, OpenType or Type1 font
    $family = strtolower($family);
    if($file=='')
        $file = str_replace(' ', '', $family).strtolower($style).'.php';
    $style = strtoupper($style);
    if($style=='IB')
        $style = 'BI';
    $fontkey = $family.$style;
    if(isset($this->fonts[$fontkey]))
        return;
    $info = $this->_loadfont($file);
    $info['i'] = count($this->fonts)+1;
    if(!empty($info['file']))
    {
        // Embedded font
        if($info['type']=='TrueType')
            $this->FontFiles[$info['file']] = array('length1'=>$info['originalsize']);
        else
            $this->FontFiles[$info['file']] = array('length1'=>$info['size1'], 'length2'=>$info['size2']);
    }
    $this->fonts[$fontkey] = $info;
}

function SetFont($family, $style='', $size=0)
{
    // Select a font; size given in points
    global $fpdf_charwidths;

    $family = strtolower($family);
    if($family=='')
        $family = $this->FontFamily;
    if($family=='arial')
        $family = 'helvetica';
    elseif($family=='symbol' || $family=='zapfdingbats')
        $family = strtolower($family);
    $style = strtoupper($style);
    if(strpos($style,'U')!==false)
    {
        $this->underline = true;
        $style = str_replace('U','',$style);
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
    // Test if used for the first time
    $fontkey = $family.$style;
    if(!isset($this->fonts[$fontkey]))
    {
        // Check if one of the core fonts
        if($family=='times' || $family=='helvetica' || $family=='courier')
        {
            if($family=='times')
            {
                if($style=='I')
                    $family = 'timesi';
                elseif($style=='B')
                    $family = 'timesb';
                elseif($style=='BI')
                    $family = 'timesbi';
            }
            elseif($family=='helvetica')
            {
                if($style=='I')
                    $family = 'helveticai';
                elseif($style=='B')
                    $family = 'helveticab';
                elseif($style=='BI')
                    $family = 'helveticabi';
            }
            else
            {
                if($style=='I')
                    $family = 'courieri';
                elseif($style=='B')
                    $family = 'courierb';
                elseif($style=='BI')
                    $family = 'courierbi';
            }
            if(!isset($fpdf_charwidths[$family]))
                $this->Error('Undefined font: '.$family.' '.$style);
            $i = count($this->fonts)+1;
            $name = $this->CoreFonts[$family];
            $this->fonts[$fontkey] = array('i'=>$i, 'type'=>'core', 'name'=>$name, 'up'=>-100, 'ut'=>50, 'cw'=>$fpdf_charwidths[$family]);
        }
        else
            $this->Error('Undefined font: '.$family.' '.$style);
    }
    // Select it
    $this->FontFamily = $family;
    $this->FontStyle = $style;
    $this->FontSizePt = $size;
    $this->FontSize = $size/$this->k;
    $this->CurrentFont = &$this->fonts[$fontkey];
    if($this->page>0)
        $this->_out(sprintf('BT /F%d %.2F Tf ET',$this->CurrentFont['i'],$this->FontSizePt));
}

function SetFontSize($size)
{
    // Set font size in points
    if($size>0)
        $this->FontSizePt = $size;
    else
        $size = $this->FontSizePt;
    $this->FontSize = $size/$this->k;
    if($this->page>0)
        $this->_out(sprintf('BT /F%d %.2F Tf ET',$this->CurrentFont['i'],$this->FontSizePt));
}

function AddLink()
{
    // Create a new internal link
    $n = count($this->links)+1;
    $this->links[$n] = array(0, 0);
    return $n;
}

function SetLink($link, $y=0, $page=-1)
{
    // Set destination of internal link
    if($y==-1)
        $y = $this->y;
    if($page==-1)
        $page = $this->page;
    $this->links[$link] = array($page, $y);
}

function Link($x, $y, $w, $h, $link)
{
    // Put a link on the page
    $this->PageLinks[$this->page][] = array($x*$this->k, $this->hPt-$y*$this->k, $w*$this->k, $h*$this->k, $link);
}

function Text($x, $y, $txt)
{
    // Output a string
    $s = sprintf('BT %.2F %.2F Td (%s) Tj ET',$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
    if($this->underline && $txt!='')
        $s .= ' '.$this->_dounderline($x,$y,$txt);
    if($this->ColorFlag)
        $s = 'q '.$this->TextColor.' '.$s.' Q';
    $this->_out($s);
}

function AcceptPageBreak()
{
    // Accept automatic page break or not
    return $this->AutoPageBreak;
}

function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
{
    // Output a cell
    $k = $this->k;
    if($this->y+$h>$this->PageBreakTrigger && !$this->InHeader && !$this->InFooter && $this->AcceptPageBreak())
    {
        // Automatic page break
        $x = $this->x;
        $y = $this->y;
        $ws = $this->ws;
        if($ws>0)
        {
            $this->ws = 0;
            $this->_out('0 Tw');
        }
        $this->AddPage($this->CurOrientation,$this->CurPageSize,$this->CurRotation);
        $this->x = $x;
        $this->y = $y;
        if($ws>0)
        {
            $this->ws = $ws;
            $this->_out(sprintf('%.3F Tw',$ws*$k));
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
        $s = sprintf('%.2F %.2F %.2F %.2F re %s ',$this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
    }
    if(is_string($border))
    {
        $x = $this->x;
        $y = $this->y;
        if(strpos($border,'L')!==false)
            $s .= sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
        if(strpos($border,'T')!==false)
            $s .= sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
        if(strpos($border,'R')!==false)
            $s .= sprintf('%.2F %.2F m %.2F %.2F l S ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
        if(strpos($border,'B')!==false)
            $s .= sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
    }
    if($txt!=='')
    {
        if($align=='R')
            $dx = $w-$this->cMargin-$this->GetStringWidth($txt);
        elseif($align=='C')
            $dx = ($w-$this->GetStringWidth($txt))/2;
        else
            $dx = $this->cMargin;
        if($this->ColorFlag)
            $s .= 'q '.$this->TextColor.' ';
        $txt2 = str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
        $s .= sprintf('BT %.2F %.2F Td (%s) Tj ET',($this->x+$dx)*$k,($this->h-($this->y+.5*$h+.3*$this->FontSize))*$k,$txt2);
        if($this->underline)
            $s .= ' '.$this->_dounderline($this->x+$dx,$this->y+.5*$h+.3*$this->FontSize,$txt);
        if($this->ColorFlag)
            $s .= ' Q';
        if($link)
            $this->Link($this->x+$dx,$this->y+.5*$h-.5*$this->FontSize,$this->GetStringWidth($txt),$this->FontSize,$link);
    }
    if($s)
        $this->_out($s);
    $this->lasth = $h;
    if($ln>0)
    {
        // Go to next line
        $this->y += $h;
        if($ln==1)
            $this->x = $this->lMargin;
    }
    else
        $this->x += $w;
}

function MultiCell($w, $h, $txt, $border=0, $align='J', $fill=false)
{
    // Output text with automatic or explicit line breaks
    $cw = &$this->CurrentFont['cw'];
    if($w==0)
        $w = $this->w-$this->rMargin-$this->x;
    $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
    $s = str_replace("\r",'',$txt);
    $nb = strlen($s);
    if($nb>0 && $s[$nb-1]=="\n")
        $nb--;
    $b = 0;
    if($border)
    {
        if($border==1)
        {
            $border = 'LTRB';
            $b = 'LRT';
            $b2 = 'LR';
        }
        else
        {
            $b2 = '';
            if(strpos($border,'L')!==false)
                $b2 .= 'L';
            if(strpos($border,'R')!==false)
                $b2 .= 'R';
            $b = ($border=='L') ? 'L' : (($border=='R') ? 'R' : $b2);
            if(strpos($border,'T')!==false)
                $b .= 'T';
            $b .= 'B';
        }
    }
    $sep = -1;
    $i = 0;
    $j = 0;
    $l = 0;
    $nl = 1;
    while($i<$nb)
    {
        // Get next character
        $c = $s[$i];
        if($c=="\n")
        {
            // Explicit line break
            if($this->ws>0)
            {
                $this->ws = 0;
                $this->_out('0 Tw');
            }
            $this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
            $i++;
            $sep = -1;
            $j = $i;
            $l = 0;
            $nl++;
            if($border && $nl==2)
                $b = $b2;
            continue;
        }
        if($c==' ')
        {
            $sep = $i;
            $ls = $l;
        }
        $l += $cw[$c];
        if($l>$wmax)
        {
            // Automatic line break
            if($sep==-1)
            {
                if($i==$j)
                    $i++;
                if($this->ws>0)
                {
                    $this->ws = 0;
                    $this->_out('0 Tw');
                }
                $this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
            }
            else
            {
                if($align=='J')
                {
                    $this->ws = ($wmax-$ls)/1000*$this->FontSize;
                    $this->_out(sprintf('%.3F Tw',$this->ws*$this->k));
                }
                $this->Cell($w,$h,substr($s,$j,$sep-$j),$b,2,$align,$fill);
                $i = $sep+1;
            }
            $sep = -1;
            $j = $i;
            $l = 0;
            $nl++;
            if($border && $nl==2)
                $b = $b2;
        }
        else
            $i++;
    }
    // Last chunk
    if($this->ws>0)
    {
        $this->ws = 0;
        $this->_out('0 Tw');
    }
    if($border && strpos($border,'B')!==false)
        $b .= 'B';
    $this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
    $this->x = $this->lMargin;
}

function Write($h, $txt, $link='')
{
    // Output text in flowing mode
    $cw = &$this->CurrentFont['cw'];
    $w = $this->w-$this->rMargin-$this->x;
    $wmax = ($w)*1000/$this->FontSize;
    $s = str_replace("\r",'',$txt);
    $nb = strlen($s);
    $sep = -1;
    $i = 0;
    $j = 0;
    $l = 0;
    $nl = 1;
    while($i<$nb)
    {
        // Get next character
        $c = $s[$i];
        if($c=="\n")
        {
            // Explicit line break
            $this->Cell($w,$h,substr($s,$j,$i-$j),0,2,'',0,$link);
            $i++;
            $sep = -1;
            $j = $i;
            $l = 0;
            if($nl==1)
            {
                $this->x = $this->lMargin;
                $w = $this->w-$this->rMargin-$this->x;
                $wmax = ($w)*1000/$this->FontSize;
            }
            $nl++;
            continue;
        }
        if($c==' ')
        {
            $sep = $i;
            $ls = $l;
        }
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
                    $wmax = ($w)*1000/$this->FontSize;
                    $nl++;
                    continue;
                }
                if($i==$j)
                    $i++;
                $this->Cell($w,$h,substr($s,$j,$i-$j),0,2,'',0,$link);
            }
            else
            {
                $this->Cell($w,$h,substr($s,$j,$sep-$j),0,2,'',0,$link);
                $i = $sep+1;
            }
            $sep = -1;
            $j = $i;
            $l = 0;
            if($nl==1)
            {
                $this->x = $this->lMargin;
                $w = $this->w-$this->rMargin-$this->x;
                $wmax = ($w)*1000/$this->FontSize;
            }
            $nl++;
        }
        else
            $i++;
    }
    // Last chunk
    if($i!=$j)
        $this->Cell($l/1000*$this->FontSize,$h,substr($s,$j,$i-$j),0,0,'',0,$link);
}

function Ln($h=null)
{
    // Line feed; default value is last cell height
    $this->x = $this->lMargin;
    if($h===null)
        $this->y += $this->lasth;
    else
        $this->y += $h;
}

function Image($file, $x=null, $y=null, $w=0, $h=0, $type='', $link='')
{
    // Put an image on the page
    if($file=='')
        $this->Error('Image file name is empty');
    if(!isset($type))
        $type = strtolower(substr(strrchr($file,'.'),1));
    $mtd = '_parse'.$type;
    if(!method_exists($this,$mtd))
        $this->Error('Unsupported image type: '.$type);
    $info = $this->$mtd($file);
    $info['i'] = count($this->images)+1;
    $this->images[$file] = $info;
    // Automatic width and height calculation if needed
    if($w==0 && $h==0)
    {
        // Put image at 72 dpi
        $w = $info['w']/72*25.4;
        $h = $info['h']/72*25.4;
    }
    if($w==0)
        $w = $h*$info['w']/$info['h'];
    if($h==0)
        $h = $w*$info['h']/$info['w'];
    // Define dimensions
    $this->_out(sprintf('q %.2F 0 0 %.2F %.2F %.2F cm /I%d Do Q',$w*$this->k,$h*$this->k,$x*$this->k,($this->h-($y+$h))*$this->k,$info['i']));
    if($link)
        $this->Link($x,$y,$w,$h,$link);
}

function GetX()
{
    // Get x position
    return $this->x;
}

function SetX($x)
{
    // Set x position
    if($x>=0)
        $this->x = $x;
    else
        $this->x = $this->w+$x;
}

function GetY()
{
    // Get y position
    return $this->y;
}

function SetY($y)
{
    // Set y position and reset x
    $this->x = $this->lMargin;
    if($y>=0)
        $this->y = $y;
    else
        $this->y = $this->h+$y;
}

function SetXY($x, $y)
{
    // Set x and y positions
    $this->SetY($y);
    $this->SetX($x);
}

function Output($name='', $dest='')
{
    // Output PDF to some destination
    if($this->state<3)
        $this->Close();
    $dest = strtoupper($dest);
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
            $this->_checkoutput();
            if(PHP_SAPI!='cli')
            {
                // We send to a browser
                header('Content-Type: application/pdf');
                header('Content-Disposition: inline; filename="'.$name.'"');
                header('Cache-Control: private, max-age=0, must-revalidate');
                header('Pragma: public');
                header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
            }
            echo $this->buffer;
            break;
        case 'D':
            // Download file
            $this->_checkoutput();
            header('Content-Type: application/x-download');
            header('Content-Disposition: attachment; filename="'.$name.'"');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');
            header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
            echo $this->buffer;
            break;
        case 'F':
            // Save to local file
            $f = fopen($name,'wb');
            if(!$f)
                $this->Error('Unable to create output file: '.$name);
            fwrite($f,$this->buffer,strlen($this->buffer));
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

/*******************************************************************************
*                                                                              *
*                              Protected methods                               *
*                                                                              *
*******************************************************************************/
function _dochecks()
{
    // Check for locale-related bug
    if(1.1==1)
        $this->Error('Don\'t alter the locale before including class file');
    // Check for decimal separator
    if(sprintf('%.1f',1.0)!='1.0')
        setlocale(LC_NUMERIC,'C');
}

function _getpagesize($size)
{
    if(is_string($size))
    {
        $size = strtolower($size);
        if(isset($this->StdPageSizes[$size]))
            $a = $this->StdPageSizes[$size];
        else
        {
            if(strpos($size,'l')===false)
                $this->Error('Unknown page size: '.$size);
            $a = array($this->StdPageSizes[substr($size,0,1)][1], $this->StdPageSizes[substr($size,0,1)][0]);
        }
    }
    else
    {
        $a = $size;
    }
    if(count($a)!=2)
        $this->Error('Invalid page size definition');
    return array($a[0]/$this->k, $a[1]/$this->k);
}

function _beginpage($orientation, $size, $rotation)
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
        $orientation = strtoupper($orientation[0]);
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
        $this->PageInfo[$this->page]['size'] = array($this->wPt, $this->hPt);
    if($rotation!=0)
    {
        if($rotation%90!=0)
            $this->Error('Invalid rotation angle: '.$rotation);
        $this->CurRotation = $rotation;
    }
    else
        $this->CurRotation = 0;
    $this->PageInfo[$this->page]['rotation'] = $this->CurRotation;
}

function _endpage()
{
    $this->state = 1;
}

function _loadfont($font)
{
    // Load a font file
    $file = $this->fontpath.$font;
    if(!file_exists($file))
        $this->Error('Font file not found: '.$file);
    include($file);
    if(!isset($name))
        $this->Error('Incorrect font file: '.$file);
    if(isset($enc))
        $enc = strtolower($enc);
    else
        $enc = 'ansi';
    if(isset($file))
        $file = strtolower($file);
    else
        $file = '';
    if(isset($type))
        $type = $type;
    else
        $type = 'TrueType';
    return compact('name','type','enc','file');
}

function _escape($s)
{
    // Escape special characters
    return str_replace(array('\\','(',')','[',']'), array('\\\\','\\(','\\)','\\[','\\]'), $s);
}

function _textstring($s)
{
    // Format a text string
    return '('.$this->_escape($s).')';
}

function _UTF8toUTF16($s)
{
    // Convert UTF-8 to UTF-16BE with BOM
    $res = "\xFE\xFF";
    $nb = strlen($s);
    $i = 0;
    while($i<$nb)
    {
        $c1 = ord($s[$i++]);
        if($c1>=224)
        {
            // 3-byte character
            $c2 = ord($s[$i++]);
            $c3 = ord($s[$i++]);
            $res .= chr((($c1 & 0x0F)<<4) + (($c2 & 0x3C)>>2));
            $res .= chr((($c2 & 0x03)<<6) + ($c3 & 0x3F));
        }
        elseif($c1>=192)
        {
            // 2-byte character
            $c2 = ord($s[$i++]);
            $res .= chr(($c1 & 0x1C)>>2);
            $res .= chr((($c1 & 0x03)<<6) + ($c2 & 0x3F));
        }
        else
        {
            // Single-byte character
            $res .= "\x00".chr($c1);
        }
    }
    return $res;
}

function _dounderline($x, $y, $txt)
{
    // Underline text
    $up = $this->CurrentFont['up'];
    $ut = $this->CurrentFont['ut'];
    $w = $this->GetStringWidth($txt)+$this->ws*substr_count($txt,' ');
    return sprintf('%.2F %.2F %.2F %.2F re f',$x*$this->k,($this->h-($y-$up/1000*$this->FontSize))*$this->k,$w*$this->k,-$ut/1000*$this->FontSizePt);
}

function _parsejpg($file)
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
    return array('w'=>$a[0], 'h'=>$a[1], 'cs'=>$colspace, 'bpc'=>$bpc, 'f'=>'DCTDecode', 'data'=>$data);
}

function _parsepng($file)
{
    // Extract info from a PNG file
    $f = fopen($file,'rb');
    if(!$f)
        $this->Error('Can\'t open image file: '.$file);
    $info = $this->_parsepngstream($f,$file);
    fclose($f);
    return $info;
}

function _parsepngstream($f, $file)
{
    // Check signature
    if($this->_readstream($f,8)!=chr(137).'PNG'.chr(13).chr(10).chr(26).chr(10))
        $this->Error('Not a PNG file: '.$file);
    // Read header chunk
    $this->_readstream($f,4);
    if($this->_readstream($f,4)!='IHDR')
        $this->Error('Incorrect PNG file: '.$file);
    $w = $this->_readint($f);
    $h = $this->_readint($f);
    $bpc = ord($this->_readstream($f,1));
    if($bpc>8)
        $this->Error('16-bit depth not supported: '.$file);
    $ct = ord($this->_readstream($f,1));
    if($ct==0 || $ct==4)
        $colspace = 'DeviceGray';
    elseif($ct==2 || $ct==6)
        $colspace = 'DeviceRGB';
    elseif($ct==3)
        $colspace = 'Indexed';
    else
        $this->Error('Unknown color type in PNG file: '.$file);
    if(ord($this->_readstream($f,1))!=0)
        $this->Error('Unknown compression method in PNG file: '.$file);
    if(ord($this->_readstream($f,1))!=0)
        $this->Error('Unknown filter method in PNG file: '.$file);
    if(ord($this->_readstream($f,1))!=0)
        $this->Error('Interlacing not supported: '.$file);
    $this->_readstream($f,4);
    $dp = '/Predictor 15 /Colors '.($colspace=='DeviceRGB' ? 3 : 1).' /BitsPerComponent '.$bpc.' /Columns '.$w;
    // Scan chunks looking for palette, transparency and image data
    $pal = '';
    $trns = '';
    $data = '';
    do
    {
        $n = $this->_readint($f);
        $type = $this->_readstream($f,4);
        if($type=='PLTE')
        {
            // Read palette
            $pal = $this->_readstream($f,$n);
            $this->_readstream($f,4);
        }
        elseif($type=='tRNS')
        {
            // Read transparency info
            $t = $this->_readstream($f,$n);
            if($ct==0)
                $trns = array(ord(substr($t,1,1)));
            elseif($ct==2)
                $trns = array(ord(substr($t,1,1)), ord(substr($t,3,1)), ord(substr($t,5,1)));
            else
            {
                $pos = strpos($t,chr(0));
                if($pos!==false)
                    $trns = array($pos);
            }
            $this->_readstream($f,4);
        }
        elseif($type=='IDAT')
        {
            // Read image data
            $data .= $this->_readstream($f,$n);
            $this->_readstream($f,4);
        }
        elseif($type=='IEND')
            break;
        else
            $this->_readstream($f,$n+4);
    }
    while($n);
    if($colspace=='Indexed' && empty($pal))
        $this->Error('Missing palette in PNG file: '.$file);
    if($colspace=='Indexed' && !empty($trns))
    {
        $cnt = count($pal);
        for($i=3;$i<$cnt;$i+=3)
        {
            if($pal[$i]==$trns[0])
            {
                $pal[$i+1] = chr(ord($pal[$i+1]) - 1);
                break;
            }
        }
    }
    return array('w'=>$w, 'h'=>$h, 'cs'=>$colspace, 'bpc'=>$bpc, 'f'=>'FlateDecode', 'dp'=>$dp, 'pal'=>$pal, 'trns'=>$trns, 'data'=>$data);
}

function _readstream($f, $n)
{
    // Read n bytes from stream
    $res = '';
    while($n>0 && !feof($f))
    {
        $s = fread($f,$n);
        if($s===false)
            $this->Error('Error while reading stream');
        $res .= $s;
        $n -= strlen($s);
    }
    if($n>0)
        $this->Error('Unexpected end of stream');
    return $res;
}

function _readint($f)
{
    // Read a 4-byte integer from stream
    $a = unpack('Ni',$this->_readstream($f,4));
    return $a['i'];
}

function _parsegif($file)
{
    // Extract info from a GIF file
    $a = getimagesize($file);
    if(!$a)
        $this->Error('Missing or incorrect image file: '.$file);
    if($a[2]!=1)
        $this->Error('Not a GIF file: '.$file);
    if($a[0]!=1 && $a[0]!=2)
        $this->Error('GIF format not supported: '.$file);
    $data = file_get_contents($file);
    return array('w'=>$a[0], 'h'=>$a[1], 'cs'=>'Indexed', 'bpc'=>8, 'f'=>'LZWDecode', 'data'=>$data);
}

function _out($s)
{
    // Add a line to the document
    if($this->state==2)
        $this->pages[$this->page] .= $s."\n";
    elseif($this->state==1)
        $this->_put($s);
    elseif($this->state==0)
        $this->Error('No page has been added yet');
}

function _put($s)
{
    $this->buffer .= $s."\n";
}

function _checkoutput()
{
    if(PHP_SAPI!='cli' && headers_sent())
        $this->Error('Some data has already been output, can\'t send PDF file');
}

function _getoffset()
{
    return strlen($this->buffer);
}

function _newobj($n=null)
{
    // Begin a new object
    if($n===null)
        $n = ++$this->n;
    $this->offsets[$n] = $this->_getoffset();
    $this->_out($n.' 0 obj');
    return $n;
}

function _endobj()
{
    $this->_out('endobj');
}

function _beginstream()
{
    $this->_out('stream');
}

function _endstream()
{
    $this->_out('endstream');
}

function _putstream($s)
{
    $this->_out('trailer');
    $this->_out('<<');
    $this->_put('/Size '.($this->n+1));
    $this->_put('/Root '.$this->n.' 0 R');
    $this->_put('/Info '.$this->n.' 0 R');
    if($this->encrypt)
        $this->_put('/Encrypt '.$this->encrypt['obj'].' 0 R');
    $this->_out('>>');
    $this->_out('startxref');
    $this->_out($this->offset);
    $this->_out('%%EOF');
    $this->state = 3;
}
}
?>
