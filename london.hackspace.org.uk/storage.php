<?
// input
$box_loc = "s01b01";
if (array_key_exists('box', $_GET)) {
    $box_loc = $_GET['box'];
}

// image locations
$shelves_filename = "images/members_storage/shelves.png";
$shelf_filename = "images/members_storage/shelf.png";
$shelfmarker_filename = "images/members_storage/shelf_marker.png";
$box_filename = "images/members_storage/box.png";

// image settings
$marker_offset = 5; // used to offset position so corner reference works when image rotated
$spacing_x = 20; // spacing between floorplan and shelf
$spacing_y = 0; 

// canvas settings
$img_width = 500;
$img_height = 360;

function get_shelf_locs() {
    return array(
        '01' => array(11, 40, 1),
        '02' => array(11, 101, 1),
        '03' => array(11, 163, 1),
        '04' => array(11, 225, 1),
        '05' => array(75, 11, 3),
        '06' => array(75, 73, 3),
        '07' => array(75, 135, 3),
        '08' => array(75, 197, 3),
        '09' => array(75, 259, 3),
        '10' => array(76, 321, 2),
        '11' => array(107, 11, 1),
        '12' => array(107, 73, 1),
        '13' => array(107, 135, 1),
        '14' => array(107, 197, 1),
        '15' => array(107, 259, 1),
        '16' => array(171, 11, 3),
        '17' => array(171, 73, 3),
        '18' => array(203, 11, 1),
        '19' => array(203, 73, 1)
    );
}

function get_box_locs() {
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

function decode_location($loc, $output) {
    $shelf_locs = get_shelf_locs();
    $box_locs = get_box_locs();

    $do = preg_match("/^s(\d\d)b(\d\d)$/i", $loc, $matches);
    if ($do) {
        if (array_key_exists($matches[1], $shelf_locs) && array_key_exists($matches[2], $box_locs)) {
            return array($shelf_locs[$matches[1]], $box_locs[$matches[2]]);
        }
    }
    //invalid box location
    return False;
}

function transfer_image($dst, $src, $dst_x, $dst_y) {
    $src_x = 0;
    $src_y = 0;
    $src_w = imagesx($src);
    $src_h = imagesy($src);
    imagecopy($dst, $src, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h);
}

function generate_base($base_file1, $base_file2, $img_w, $img_h, $space_x, $space_y) {
    // load images
    $shelves_img = imagecreatefrompng($base_file1);
    $shelf_img = imagecreatefrompng($base_file2);

    if ($shelves_img && $shelf_img) {
        // create canvas
        $img = imagecreate($img_w, $img_h);

        // draw shelves
        $dst_x = 0;
        $dst_y = 0;
        transfer_image($img, $shelves_img, $dst_x, $dst_y);

        // draw shelf
        $dst_x = imagesx($shelves_img) + $space_x;
        $dst_y = 0 + $space_y;
        transfer_image($img, $shelf_img, $dst_x, $dst_y);

        return array($img, $shelves_img, $shelf_img);
    }
    else {
        echo('Invalid base image(s)');
        return False;
    }
}

function generate_image($imgs, $marker_locs, $shelfmarker_filename, $box_filename, $spacing_x, $spacing_y) {
    // load images
    $shelfmarker_img = imagecreatefrompng($shelfmarker_filename);
    $box_img = imagecreatefrompng($box_filename);

    if ($shelfmarker_img && $box_img) {
        // draw shelf marker onto canvas
        $dst_x = $marker_locs[0][0];
        $dst_y = $marker_locs[0][1];

        // if rotating 270 then offset position to maintain top-left reference points
        if ($marker_locs[0][2] == 3) {
            $dst_x = $dst_x - $marker_offset;
        }

        // rotate
        $angle = $marker_locs[0][2] * -90;
        $rotated = imagerotate($shelfmarker_img, $angle, 0);
        transfer_image($imgs[0], $rotated, $dst_x, $dst_y);



        // draw box
        $dst_x = imagesx($imgs[1]) + $marker_locs[1][0] + $spacing_x;
        $dst_y = $marker_locs[1][1] + $spacing_y;
        transfer_image($imgs[0], $box_img, $dst_x, $dst_y);

        //output
        header('Content-type: image/png');
        imagepng($img);
    }
    else {
        echo('Invalid image type or location');
    }
}




//determine location
$locs = decode_location($box_loc);

//generate base image
$imgs = generate_base($shelves_filename, $shelf_filename, $img_width, $img_height, $spacing_x, $spacing_y);
if ($imgs) {
    if ($locs) {
        // amend base image to have markers
        generate_image($imgs, $locs, $shelfmarker_filename, $box_filename, $spacing_x, $spacing_y);
    }
    else {
        echo('Invalid box location');
    }
}
?>