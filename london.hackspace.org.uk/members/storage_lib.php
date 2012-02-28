<?php
function get_string_center_pos($string, $font_size, $image_width) {
    $text_width = imagefontwidth($font_size) * strlen($string);
    $center = floor($image_width / 2);
    $x = $center - (floor($text_width/2));
    return $x;
}

class BoxIdImage {
    var $box_id, $img, $url;

    function BoxIdImage($box_id) {
        $this->box_id = $box_id;

        // Segments to create query that gets the QR code
        $start_qr_url = "https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=";
        $end_qr_url = "&.png";
        $site = "london.hackspace.org.uk";
        $action = "/members/storage.php?box_id=";
        $this->url = $start_qr_url . $site . $action. $this->box_id . $end_qr_url;

        // Fetch QR code
        $this->img = imagecreatefrompng($this->url);

        // modify QR code image to include ID
        $font = 2;
        $x = 30;
        $y = 5;
        $string = "Box ID: " . $this->box_id;
        $textcolor = imagecolorallocate($img, 0, 0, 0);
        imagestring($this->img, $font, $x, $y, $string, $textcolor);
    }

    function get_url() {
        return $this->url;
    }

    function get_image() {
        //output
        header('Content-type: image/png');
        imagepng($this->img);
    }
}



class StorageLocationImage {
    var $shelves_filename, $shelf_filename, $shelfmarker_filename, $box_filename;
    var $marker_offset, $spacing_x, $spacing_y;
    var $img_width, $img_height, $img, $scale, $box_loc;

    function StorageLocationImage($box_loc) {
        // image locations
        $this->shelves_filename = "../images/members_storage/shelves.png";
        $this->shelf_filename = "../images/members_storage/shelf.png";
        $this->shelfmarker_filename = "../images/members_storage/shelf_marker.png";
        $this->box_filename = "../images/members_storage/box.png";

        // image settings
        $this->marker_offset_x = -5;   // used to offset position so corner reference works when image rotated
        $this->marker_offset_y = 0;
        $this->spacing_x = 45;      // spacing between floorplan and shelf sub-images
        $this->spacing_y = 0;
        $this->header_spacing_y = 30;

        // canvas settings
        $this->img_width = 500;
        $this->img_height = 400;
        $this->scale = 0.5;         // to rescale the resultant image

        $this->box_loc = $box_loc;
    }

    function get_shelf_locs() {
        // x, y, and orientation of each shelf
        // x and y in pixels
        // orientation: N = 0, E = 1, S = 2, W = 3
        return array(
            '01' => array(11, 225, 1),
            '02' => array(11, 163, 1),
            '03' => array(11, 101, 1),
            '04' => array(11, 40, 1),
            '05' => array(75, 11, 3),
            '06' => array(75, 73, 3),
            '07' => array(75, 135, 3),
            '08' => array(75, 197, 3),
            '09' => array(75, 259, 3),
            '10' => array(76, 321, 2),
            '11' => array(107, 259, 1),
            '12' => array(107, 197, 1),
            '13' => array(107, 135, 1),
            '14' => array(107, 73, 1),
            '15' => array(107, 11, 1),
            '16' => array(171, 11, 3),
            '17' => array(171, 73, 3),
            '18' => array(203, 73, 1),
            '19' => array(203, 11, 1)
        );
    }

    function get_box_locs() {
        // x and y coords in pixels of each box on a shelf
        // origin top left.
        return array(
            '01' => array(18, 303),
            '02' => array(104, 303),
            '03' => array(18, 255),
            '04' => array(104, 255),
            '05' => array(18, 207),
            '06' => array(104, 207),
            '07' => array(18, 159),
            '08' => array(104, 159),
            '09' => array(18, 111),
            '10' => array(104, 111),
            '11' => array(18, 63),
            '12' => array(104, 63),
            '13' => array(18, 15),
            '14' => array(104, 15)
        );
    }

    function decode_location() {
        $shelf_locs = $this->get_shelf_locs();
        $box_locs = $this->get_box_locs();

        $do = preg_match("/^s(\d\d)b(\d\d)$/i", $this->box_loc, $matches);
        if ($do) {
            if (array_key_exists($matches[1], $shelf_locs) && array_key_exists($matches[2], $box_locs)) {
                return array($shelf_locs[$matches[1]], $box_locs[$matches[2]]);
            }
        }
        //invalid box location
        return False;
    }

    function transfer_image($dst, $src, $dst_x, $dst_y) {
        imagecolortransparent($dst, imagecolorallocatealpha($dst, 0, 0, 0, 127));

        $src_x = 0;
        $src_y = 0;
        $src_w = imagesx($src);
        $src_h = imagesy($src);
        imagealphablending($dst, true);
        imagecopy($dst, $src, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h);
        imagealphablending($dst, false);
    }

    function generate_base_image() {
        $shelves_img = imagecreatefrompng($this->shelves_filename);
        $shelf_img = imagecreatefrompng($this->shelf_filename);

        if ($shelves_img && $shelf_img) {
            //create base
            $this->img = imagecreatetruecolor($this->img_width, $this->img_height);
            imagecolortransparent($this->img, imagecolorallocatealpha($this->img, 0, 0, 0, 127));
            imagealphablending($this->img, false);
            imagesavealpha($this->img, true);

            // draw background
            $white = imagecolorallocate($this->img, 255, 255, 255);
            imagefilledrectangle($this->img, 0, 0, $this->img_width, $this->img_height, $white);

            // write header
            $font = 'arial';
            $font_size = 20;
            $string = "Box Location: " . $this->box_loc;
            $x = get_string_center_pos($string, $font_size, $this->img_width);
            $y = 20;
            $textcolor = imagecolorallocate($this->img, 0, 0, 0);
            imagealphablending($this->img, true);
            $result = imagettftext($this->img, $font_size, 0, $x, $y, $textcolor, $font, $string);
            imagealphablending($this->img, false);
 
            // draw shelves floor plan
            $this->transfer_image($this->img, $shelves_img, 0, $this->header_spacing_y);

            // draw shelf
            $dst_x = imagesx($shelves_img) + $this->spacing_x;
            $dst_y = $this->spacing_y;
            $this->transfer_image($this->img, $shelf_img, $dst_x, $dst_y+$this->header_spacing_y);
            return True;
        }
        echo('Invalid base image(s)');
        return False;     
    }

    function generate_image($marker_locs) {
        //load markers
        $shelfmarker_img = imagecreatefrompng($this->shelfmarker_filename);
        $boxmarker_img = imagecreatefrompng($this->box_filename);

        //load base image to help with positioning
        $shelves_img = imagecreatefrompng($this->shelves_filename);
        if ($shelfmarker_img && $boxmarker_img && $shelves_img) {
            // draw shelf marker onto canvas
            $dst_x = $marker_locs[0][0];
            $dst_y = $marker_locs[0][1] + $this->header_spacing_y;

            // if rotating 270 then offset position to maintain top-left reference points
            if ($marker_locs[0][2] == 3) {
                $dst_x = $dst_x + $this->marker_offset_x;
                $dst_y = $dst_y + $this->marker_offset_y;
            }

            // rotate
            $angle = $marker_locs[0][2] * -90;
            $rotated = imagerotate($shelfmarker_img, $angle, 0);
            $this->transfer_image($this->img, $rotated, $dst_x, $dst_y);

            // draw box        
            $dst_x = imagesx($shelves_img) + $marker_locs[1][0] + $this->spacing_x;
            $dst_y = $marker_locs[1][1] + $this->spacing_y + $this->header_spacing_y;
            $this->transfer_image($this->img, $boxmarker_img, $dst_x, $dst_y);

            return True;
        }
        echo('Invalid image type or location');
        return False;
    }

    function get_image() {
        $do = $this->generate_base_image();
        if ($do) {
            $loc = $this->decode_location();
            if ($loc) {
                $this->generate_image($loc);
            }

            //resize
            $new_w = $this->img_width * $this->scale;
            $new_h = $this->img_height * $this->scale;
            $img = imagecreatetruecolor($new_w, $new_h);

            imagecopyresized($img, $this->img, 0, 0, 0, 0, $new_w, $new_h, $this->img_width, $this->img_height);

            //output
            header('Content-type: image/png');
            imagepng($img);
        }
    }
}
?>
