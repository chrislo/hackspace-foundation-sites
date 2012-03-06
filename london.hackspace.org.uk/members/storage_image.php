<?
require('storage_lib.php');

// input
if (isset($_GET['loc'])) {
    $box_loc = $_GET['loc'];

    $img = new StorageLocationImage($box_loc);
    $img->get_image();
}
else if (isset($_GET['id'])) {
    $box_id = $_GET['id'];

    $img = new BoxIdImage($box_id);
    $img->get_image();
}
else if (isset($_GET['start_id'])) {
    $start_id = $_GET['start_id'];
    $end_id = $_GET['start_id'];
    if (isset($_GET['end_id'])) {
        $end_id = $_GET['end_id'];
    }

    $url = "/members/storage_image.php?id=";
    $columns = 3;
    $num_items = ($end_id - $start_id) + 1;
    for ($i = 0; $i <= $num_items / $columns; $i++) {
        echo '<html><head><title>Box QR Sheet</title></head><body><div>';

        $upper_limit = $columns;
        $marker = $columns * $i;
        if ($num_items - $marker < $columns) {
            $upper_limit = $num_items - $marker;
        }

        for ($j = 0; $j < $upper_limit; $j++) {
            $id = $j + ($i * $columns) + $start_id;
            $image_url = $url . (string) $id;
            echo '
            <span>
                <img src="'.$image_url.'" />
                <img src="'.$image_url.'" />
            </span>';
        }
        echo '</div></body></html>';
    }
}
?>