<? 
$page = 'storage';
$title = 'Storage';

require('../header.php');

if (!isset($user)) {
    fURL::redirect('/login.php?forward=/members/storage.php');
}

class Mem {
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

            } else if (isset($_POST['delete_' . $box[0]])) {
                //delete
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

<h3>Your Storage Boxes</h3>

<? if ($box_loc) {
    echo '
    <div style="margin:0 auto; text-align:center;">
        <img src="/members/storage_image.php?box='.$box_loc.'" alt="box location" />
    </div>';
    echo '<p style="text-align:center;">Use this <a href="/members/storage_image.php?box='.$box_loc.'">link</a>
        to share this image.</p>';
} ?>


<p>As a member you get access to a storage box.</p>

<form method="POST">
    <input type="hidden" name="token" value="<?=fRequest::generateCSRFToken()?>" />
    <input type="hidden" name="update_box" value="" />

    <table style="text-align: center">
        <tr>
            <th>Box ID</th>
            <th>Location</th>
            <th>Show Location</th>
            <th>Remove</th>
        </tr>
        <? foreach($mem->buildBoxes() as $box): ?>
        <tr>
            <td><?=$box[0] ?></td>
            <td><?=$box[1]?></td>
            <td>
                <input type="submit" name="show_<?=$box[0]?>" value="Show" />
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

    <table>
        <tr>
            <td><label for="box_id">Box ID:</label></td>
            <td><input type="text" name="box_id" size="10" /></td>
        </tr>
        <tr>
            <td><label for="box_location">Box Loction:</label></td>
            <td><input type="text" name="box_location" size="10" /></td>
        </tr>
        <tr>
            <td colspan="2"><input type="submit" name="submit" value="Add box" /></td>
        </tr>
    </table>
</form>

<h3>Lookup a Member's Box</h3>
<form>
    <input type="hidden" name="token" value="<?=fRequest::generateCSRFToken()?>" />
    <input type="hidden" name="lookup_user" value="" />
    
    <label for="user_nick">Nickname:</label>
    <input type="text" name="box_location" size="20" />
    <input type="submit" name="submit" value="Search" />
</form>

<? require('footer.php'); ?>
