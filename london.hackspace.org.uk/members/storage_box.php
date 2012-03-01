<?php
$page = 'storage_box';
$title = 'Storage Box Management';

require('../header.php');

if (!isset($user)) {
    fURL::redirect('/login.php?forward=/members/storage_box.php');
}

if (isset($_POST['create_box'])) {
    try {
        fRequest::validateCSRFToken($_POST['token']);
        $box = new Box();
        $box->store();
        fURL::redirect('/members/storage_box.php');
        exit;
    } catch (fValidationException $e) {
        echo "<p>" . $e->printMessage() . "</p>";
    } catch (fSQLException $e) {
        echo "<p>An unexpected error occurred, please try again later</p>";
        trigger_error($e);
    }
}

if (isset($_POST['delete_box'])) {
    try {
        fRequest::validateCSRFToken($_POST['token']);
        $box = new Box(array('id' => $_POST['box_id']));
        $box->delete();
        fURL::redirect('/members/storage_box.php');
        exit;
    } catch (fValidationException $e) {
        echo "<p>" . $e->printMessage() . "</p>";
    } catch (fSQLException $e) {
        echo "<p>An unexpected error occurred, please try again later</p>";
        trigger_error($e);
    }
}
?>


<h2>Box Management</h2>

<? if (isset($_GET['box_id'])): ?>  
    <?php 
    $box_id = $_GET['box_id'];
    $box = new Box(array('id' => $box_id));
    if ($box->getUserId() != NULL):
        $owner = new User(array('id' => $box->getUserId()));
    ?>
        This box is assigned to:
        <table>
            <thead>
                <tr>
                    <th>Box ID</th>
                    <th>Owner's Member Number</th>
                    <th>Owner's Name</th>
                    <th>Member</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?=$box->getId()?></td>
                    <td><?=$owner->getMemberNumber()?></td>
                    <td><?=$owner->getFullName()?></td>
                    <td>
                        <?php 
                            if ($owner->isMember()) {
                                echo 'True';
                            } else {
                                echo 'False';
                            }
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>
    <?php elseif ($mem_box == -1): ?>
        //not owned
        echo '
        <div>
            Not owned would you like to take it?
        </div>';
    <? endif; ?>
<? else: ?>
    <?php 
    $boxes = $db->translatedQuery( 'SELECT id FROM boxes ORDER BY id' );
    if ($boxes->countReturnedRows() == 0):
    ?>
    <p>No boxes</p>
    <? else: ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach( $boxes as $row ): ?>
            <tr>
                <td><?=$row['id']?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="token" value="<?=fRequest::generateCSRFToken()?>" />
                        <input type="hidden" name="box_id" value="<?=$row['id']?>" />
                        <input type="submit" name="delete_box" value="Delete Box" />
                    </form>
                </td>
            </tr>
        <?php endforeach; ?> 
        </tbody>
    </table>
    <? endif; ?>


    <form method="POST">
        <input type="hidden" name="token" value="<?=fRequest::generateCSRFToken()?>" />
        <input type="submit" name="create_box" value="Create Box" />
    </form>
<? endif; ?>
<? require('footer.php'); ?>
