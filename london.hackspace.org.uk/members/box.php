<?php


if (isset($_GET['box_id'])) {
    $box_id = $_GET['box_id'];
    
    $mem_box = $boxes->getBoxByID($box_id);
    
    if ($mem_box >= 0) {
        //owned box
        echo '
        <div>
            Owned by blah blah
        </div>';
    } 
    else if ($mem_box == -1) {
        //not owned
        echo '
        <div>
            Not owned would you like to take it?
        </div>';
    }
    else {
        //unused id
        echo "
        <div>
            This ID hasn't been assigned. Please ensure you entered the correct information.
        </div>";
    }
}

?>
