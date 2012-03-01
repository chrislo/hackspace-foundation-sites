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

<h2>Box Management</h2>

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
