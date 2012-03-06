<? 
$page = 'storage';
$title = 'Storage';

require('../header.php');

if (!isset($user)) {
    fURL::redirect('/login.php?forward=/members/storage.php');
}

require_once('storage_lib.php');


$boxes = new Box();


if (isset($_POST['update_box'])) {
    try {
        fRequest::validateCSRFToken($_POST['token']);
        foreach($user->buildBoxes() as $box) {
            if (isset($_POST['loc_' . $box->getId()])) {
                $location = $_POST['location_' . $box->getId()];
                if ($location == "") {
                    $location = NULL;
                }

                $box->setLocation($location);
                $box->store();
                fURL::redirect('/members/storage.php');
            }
            else if (isset($_POST['show_' . $box->getId()])) {
                $box_loc = $box->getLocation();

            }
            else if (isset($_POST['disown' . $box->getId()])) {
                $box->setOwned(False);
                $box->setLocation(NULL);
                $box->store();
                fURL::redirect('/members/storage.php');
            }
            else if (isset($_POST['label_' . $box->getId()])) {
                //generate label
                $label_box_id = $box->getId();
            }
            
        }
    } catch (fValidationException $e) {
        echo "<p>" . $e->printMessage() . "</p>";
    } catch (fSQLException $e) {
        echo "<p>An unexpected error occurred, please try again later</p>";
        trigger_error($e);
    }
}

if (isset($_POST['claim_box'])) {
    try {
        fRequest::validateCSRFToken($_POST['token']);
        $box = new Box(array('id' => $_POST['box_id']));
        $box->setOwnerId($user->getId());
        $box->setOwned(True);
        $box->store();
    } catch (fValidationException $e) {
        echo "<p>" . $e->printMessage() . "</p>";
    } catch (fSQLException $e) {
        echo "<p>An unexpected error occurred, please try again later</p>";
        trigger_error($e);
    }
}

?>

<h2>Storage</h2>

<p>As a member you get access to a storage box.</p>

<h3>Your Storage Boxes</h3>

<form method="POST">
    <input type="hidden" name="token" value="<?=fRequest::generateCSRFToken()?>" />
    <input type="hidden" name="update_box" value="" />

    <? if (count($user->buildBoxes()) == 0): ?>
    <p>You don't have a box.</p>
    <? else: ?>
    <table style="text-align: center;">
        <tr>
            <th style="text-align: center;">Box ID</th>
            <th style="text-align: center;">Location</th>
            <th style="text-align: center;">Show Location</th>
            <th style="text-align: center;">Label</th>
            <th style="text-align: center;">Disown</th>
        </tr>
        <? foreach($user->buildBoxes() as $box): ?>
        <tr>
            <td><?=$box->getId()?></td>
            <td>
                <input type="text" name="location_<?=$box->getId()?>" value="<?=$box->getLocation()?>" size="6" />
                <input type="submit" name="loc_<?=$box->getId()?>" value="Save" />
            </td>
            <td>
                <input type="submit" name="show_<?=$box->getId()?>" value="Show" />
            </td>
            <td>
                <input type="submit" name="label_<?=$box->getId()?>" value="Generate" />
            </td>
            <td>
                <input type="submit" name="disown<?=$box->getId()?>" value="Yes" />
            </td>   
        </tr>
        <? endforeach ?>
    </table>
    <? endif ?>
</form>

<? if ($box_loc) {
    echo '
    <h3>Box Location</h3>
    <div style="margin:0 auto; text-align:center;">
        <img src="/members/storage_image.php?loc='.$box_loc.'" alt="box location" />
    </div>';
    echo '<p style="text-align:center;">Copy this <a href="/members/storage_image.php?loc='.$box_loc.'">link</a>
        to share this image.</p>';
}
else if ($label_box_id) { 
    echo '
    <h3>Box Label</h3>
    <div style="margin:0 auto; text-align:center;">
        <img src="/members/storage_image.php?id='.$label_box_id.'" alt="box label" />
    </div>';
    echo '<p style="text-align:center;">Copy this <a href="/members/storage_image.php?id='.$label_box_id.'">link</a>
        to share this image.</p>';
}
?>


<h3>Claim a Box</h3>

<p>If you've found an unused box you can use this to take claim of it. Just select the ID that appears on the box and click claim. Please only claim a box once you're in physical possession of it.</p>

<? if (count($boxes->getAvailable()) == 0): ?>
    <p>No available boxes.</p>
<? else: ?>
<form method="POST">
    <input type="hidden" name="token" value="<?=fRequest::generateCSRFToken()?>" />
    <input type="hidden" name="claim_box" value="" />

    <label for="box_id">Box ID:</label>
    <select name="box_id">
        <option value="" selected="selected"></option>
    <? foreach($boxes->getAvailable() as $box): ?>
        <option value="<?=$box->getId()?>"><?=$box->getId()?></option>
    <? endforeach ?>
    </select>

    <input type="submit" name="submit" value="Claim" />
</form>
<? endif; ?>


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
