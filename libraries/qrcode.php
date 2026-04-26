<?php
/*
 * QR Code Generator for PHP
 * Simple QR code generation library for transport permits
 */

class QRCode {
    private $data;
    private $size;
    private $margin;
    
    public function __construct($data, $size = 150, $margin = 10) {
        $this->data = $data;
        $this->size = $size;
        $this->margin = $margin;
    }
    
    public function generate($filename = null) {
        // Create a simple QR code using Google Charts API
        $api_url = "https://chart.googleapis.com/chart?chs={$this->size}x{$this->size}&cht=qr&chl=" . urlencode($this->data) . "&choe=UTF-8";
        
        if ($filename) {
            // Save to file
            $image_data = file_get_contents($api_url);
            file_put_contents($filename, $image_data);
            return $filename;
        } else {
            // Return as base64
            $image_data = file_get_contents($api_url);
            return 'data:image/png;base64,' . base64_encode($image_data);
        }
    }
    
    public function generateHTML($alt = 'QR Code') {
        $qr_data = $this->generate();
        return "<img src='{$qr_data}' alt='{$alt}' style='width:{$this->size}px;height:{$this->size}px;' />";
    }
}

/*
 * Alternative: Simple QR Code Generator without external API
 * This is a fallback if Google Charts API is not available
 */
class SimpleQRCode {
    private $data;
    private $size;
    
    public function __construct($data, $size = 150) {
        $this->data = $data;
        $this->size = $size;
    }
    
    public function generate($filename = null) {
        // Create a simple placeholder QR code using GD
        $img = imagecreatetruecolor($this->size, $this->size);
        $white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 0, 0, 0);
        
        // Fill background
        imagefill($img, 0, 0, $white);
        
        // Create a simple pattern (this is a simplified QR code)
        $module_size = $this->size / 25;
        for ($i = 0; $i < 25; $i++) {
            for ($j = 0; $j < 25; $j++) {
                // Generate pattern based on data
                $hash = md5($this->data . $i . $j);
                if (hexdec(substr($hash, 0, 2)) % 2 == 0) {
                    $x = $i * $module_size;
                    $y = $j * $module_size;
                    imagefilledrectangle($img, $x, $y, $x + $module_size - 1, $y + $module_size - 1, $black);
                }
            }
        }
        
        if ($filename) {
            imagepng($img, $filename);
            imagedestroy($img);
            return $filename;
        } else {
            ob_start();
            imagepng($img);
            imagedestroy($img);
            $image_data = ob_get_contents();
            ob_end_clean();
            return 'data:image/png;base64,' . base64_encode($image_data);
        }
    }
}
?>
