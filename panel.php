<?php

require_once('init.php');

/** PARAMETERS **/

// Edit parameter.
$editId = '';
if (key_exists('edit', $_GET)) {
    $editId = $_GET['edit'];
}

// Database connection.
$db = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME) or die(mysqli_connect_error());

// Handle actions.
$fields = array('name', 'description', 'voltage', 'power', 'peak_power', 'price', 'stock');
$optionals = array('description');
$editId = handleModuleAction('panel', $fields, $optionals, $db, $editId, $_POST);

/** PAGE CONTENT **/

// Layout start.
t_start();

//Edit dislpay.
$editCallback = function($row) use ($db)
{
    $query = "SELECT * FROM `panel` WHERE `id` = '{$row['id']}'";
    $result = $db->query($query) or die(mysqli_error($db));
    $data = $result->fetch_assoc();
    $result->free();
    t_editableLoad($data, 'doEdit', 'editTable');
};

$addCallback = function()
{
    t_editableLoad(array(), 'doAdd', 'addTable');
};

function t_editableLoad($data, $submitButtonName, $id)
{
    ?>
        <form action="" method="POST">
        <table cellspacing=0 cellpadding=0 id="<?php echo $id; ?>">
            <?php
                if (key_exists('id', $data)) {
                    ?>
                        <tr>
                            <td class="tbl_key">id</td>
                            <td class="tbl_value"><?php echo $data['id']; ?></td>
                        </tr>
                    <?php
                }
            ?>
            <tr>
                <td class="tbl_key">Name</td>
                <td class="tbl_value"><input type="text" name="name" class="textinput" value="<?php echo isset($data['name']) ? $data['name'] : ''; ; ?>" required /></td>
            </tr>
            <tr>
                <td class="tbl_key">Description</td>
                <td class="tbl_value"><textarea name="description"><?php echo isset($data['description']) ? $data['description'] : ''; ?></textarea></td>
            </tr>
            <tr>
                <td class="tbl_key">Voltage<?php echo T_Units::V; ?></td>
                <td class="tbl_value"><input type="text" name="voltage" class="textinput" value="<?php echo isset($data['voltage']) ? $data['voltage'] : ''; ?>" pattern="[\d.]+" required /></td>
            </tr>
            <tr>
                <td class="tbl_key">Power<?php echo T_Units::W; ?></td>
                <td class="tbl_value"><input type="text" name="power" class="textinput" value="<?php echo isset($data['power']) ? $data['power'] : ''; ?>" pattern="[\d.]+" required /></td>
            </tr>
            <tr>
                <td class="tbl_key">Peak Power<?php echo T_Units::W; ?></td>
                <td class="tbl_value"><input type="text" name="peak_power" class="textinput" value="<?php echo isset($data['peak_power']) ? $data['peak_power'] : ''; ?>" pattern="[\d.]+" required /></td>
            </tr>
            <tr>
                <td class="tbl_key">Price<?php echo T_Units::CFA; ?></td>
                <td class="tbl_value"><input type="text" name="price" class="textinput" value="<?php echo isset($data['price']) ? $data['price'] : ''; ?>" pattern="[\d.]+" required /></td>
            </tr>
            <tr>
                <td class="tbl_key">Stock</td>
                <td class="tbl_value"><input type="number" name="stock" class="textinput" value="<?php echo isset($data['stock']) ? $data['stock'] : ''; ?>" pattern="[+-]?[\d]+" required /></td>
            </tr>
            <tr>
                <td class="tbl_key"></td>
                <td class="tbl_value">
                    <input type="reset" value="Cancel" />
                    <input type="submit" name="<?php echo $submitButtonName; ?>" value="OK" />
                </td>
            </tr>
        </table>
        </form>
    <?php
}

// Table query.
$query = " SELECT `id`, `name`, `description`, `power`, `peak_power`, `price`, `stock` FROM `panel` ORDER BY `name`";
$headers = array(
      'name'          => 'Name'
    , 'description'   => 'Description'
    , 'power'         => 'Power'      . T_Units::W
    , 'peak_power'    => 'Peak power' . T_Units::W
    , 'price'         => 'Price'      . T_Units::CFA
    , 'stock'         => 'Stock'
);

// Execute query and show table.
$result = $db->query($query) or die(mysqli_error($db));
t_scroll_table($result, $headers, $editId, $editCallback, $addCallback);
$result->free();

// Layout end.
$db->close();
t_end();

?>
