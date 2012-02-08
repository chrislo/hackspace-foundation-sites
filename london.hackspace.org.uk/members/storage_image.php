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
?>