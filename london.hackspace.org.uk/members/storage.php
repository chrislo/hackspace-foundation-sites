<? 
$page = 'storage';
$title = 'Storage';

require('../header.php');

if (!isset($user)) {
    fURL::redirect('/login.php?forward=/members/storage.php');
}

require_once('storage_lib.php');

// Map Box IDs to Member IDs
class Box {
    function getBoxByID($id) {
        $boxes = array(
            0 => 1,
            1 => 1,
            2 => 1,
            6 => -1,
            7 => -1
        );
        
        if (array_key_exists($id, $boxes)) {
            return $boxes[$id];
        }
        return False;
    }
}

$boxes = new Box;



// Boxes for a member
class Mem {
    //get boxes for current user
    function buildBoxes() {
        return array(
            array(0, "s01b10"),
            array(1, "s05b01"),
            array(2, "s10b04")
        );
    }
}

$mem = new Mem;



if (isset($_POST['update_box'])) {
    try {
        fRequest::validateCSRFToken($_POST['token']);
        foreach($mem->buildBoxes() as $box) {
            if (isset($_POST['show_' . $box[0]])) {
                $box_loc = $box[1];

            }
            else if (isset($_POST['delete_' . $box[0]])) {
                //delete
            }
            else if (isset($_POST['label_' . $box[0]])) {
                //generate label
                $label_box_id = $box[0];
            }
        }
    } catch (fValidationException $e) {
        echo "<p>" . $e->printMessage() . "</p>";
    } catch (fSQLException $e) {
        echo "<p>An unexpected error occurred, please try again later</p>";
        trigger_error($e);
    }
}
?>

<h2>Storage</h2>

<? 
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

<p>As a member you get access to a storage box.</p>

<? if ($box_loc) {
    echo '
    <div style="margin:0 auto; text-align:center;">
        <img src="/members/storage_image.php?loc='.$box_loc.'" alt="box location" />
    </div>';
    echo '<p style="text-align:center;">Copy this <a href="/members/storage_image.php?loc='.$box_loc.'">link</a>
        to share this image.</p>';
}
else if ($label_box_id) { 
    echo '
    <div style="margin:0 auto; text-align:center;">
        <img src="/members/storage_image.php?id='.$label_box_id.'" alt="box label" />
    </div>';
}
?>

<h3>Your Storage Boxes</h3>

<form method="POST">
    <input type="hidden" name="token" value="<?=fRequest::generateCSRFToken()?>" />
    <input type="hidden" name="update_box" value="" />

    <table style="text-align: center;">
        <tr>
            <th style="text-align: center;">Box ID</th>
            <th style="text-align: center;">Location</th>
            <th style="text-align: center;">Show Location</th>
            <th style="text-align: center;">Label</th>
            <th style="text-align: center;">Remove</th>
        </tr>
        <? foreach($mem->buildBoxes() as $box): ?>
        <tr>
            <td><?=$box[0] ?></td>
            <td><?=$box[1]?></td>
            <td>
                <input type="submit" name="show_<?=$box[0]?>" value="Show" />
            </td>
            <td>
                <input type="submit" name="label_<?=$box[0]?>" value="Generate" />
            </td>
            <td>
                <input type="submit" name="delete_<?=$box[0]?>" value="Delete" />
            </td>   
        </tr>
        <? endforeach ?>
    </table>
</form>

<h3>Add a New Box</h3>

<form method="POST">
    <input type="hidden" name="token" value="<?=fRequest::generateCSRFToken()?>" />
    <input type="hidden" name="add_box" value="" />

    <label for="add_box_location">Box Loction:</label>
    <input type="text" name="add_box_location" size="10" />
    <input type="submit" name="submit" value="Add box" />
</form>

<h3>Lookup a Box</h3>

<form>
    <input type="hidden" name="token" value="<?=fRequest::generateCSRFToken()?>" />
    <input type="hidden" name="lookup_box_nick" value="" />
    
    <label for="owner_nick">By Owner's Nickname:</label>
    <input type="text" name="owner_nick" size="20" />
    <input type="submit" name="submit" value="Search" />
</form>

<form>
    <input type="hidden" name="token" value="<?=fRequest::generateCSRFToken()?>" />
    <input type="hidden" name="lookup_box_name" value="" />
    
    <label for="owner_name">By Owner's Name:</label>
    <input type="text" name="owner_name" size="20" />
    <input type="submit" name="submit" value="Search" />
</form>

<form>
    <input type="hidden" name="token" value="<?=fRequest::generateCSRFToken()?>" />
    <input type="hidden" name="lookup_box_id" value="" />
    
    <label for="box_id">By Box ID:</label>
    <input type="text" name="box_id" size="20" />
    <input type="submit" name="submit" value="Search" />
</form>

<form>
    <input type="hidden" name="token" value="<?=fRequest::generateCSRFToken()?>" />
    <input type="hidden" name="lookup_box_location" value="" />
    
    <label for="look_box_location">By Box Location:</label>
    <input type="text" name="look_box_location" size="20" />
    <input type="submit" name="submit" value="Search" />
</form>


<? require('footer.php'); ?>
