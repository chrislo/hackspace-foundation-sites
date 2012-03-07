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
        $box->setCreatorId($user->getId());
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

?>


<h2>Box Management</h2>

<? if (isset($_GET['box_id'])): ?>  
    <?php 
    $box_id = $_GET['box_id'];
    $box = new Box(array('id' => $box_id));
    if ($box->getOwnerId() != NULL):
        $owner = new User(array('id' => $box->getOwnerId()));
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
    $boxes = new Box();
    if (count(Box::getAll()) == 0):
    ?>
        <p>No boxes</p>
    <? else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Owner</th>
                    <th>Location</th>
                    <th>Box Creator</th>
                    <th>Owned</th>
                    <th>Active</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach (Box::getAll() as $row): ?>
                <tr>
                    <td><?=$row->getId()?></td>
                    <td>
                    <? if ($row->isOwned()) {
                        $owner = $row->getOwner();
                        echo $owner->getFullName();
                    } ?>
                    </td>
                    <td><?=$row->getLocationName()?></td>
                    <td>
                    <?
                        $creator = $row->getCreator();
                        echo $creator->getFullName();
                    ?>
                    </td>
                    <td><? if ($row->getOwned()) { echo 'True'; } else { echo 'False'; } ?></td>
                    <td><? if ($row->getActive()) { echo 'True'; } else { echo 'False'; } ?></td>
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
