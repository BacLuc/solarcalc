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
$fields = array('name', 'description', 'voltage', 'dod', 'loss', 'discharge', 'lifespan', 'capacity', 'max_const_current', 'max_peak_current', 'avg_const_current', 'max_humidity', 'max_temperature', 'price', 'stock');
$optionals = array('description');
if (($editId = handleModuleAction('battery', $fields, $optionals, $db, $_POST)) < 0) {
    // Edit parameter.
    if (key_exists('edit', $_GET)) {
        $editId = $_GET['edit'];
    } else {
        $editId = '';
    }
}

/** PAGE CONTENT **/

// Layout start.
t_start();

// Edit display.
$editCallback = function($row) use ($db)
{
    $query = "SELECT * FROM `battery` WHERE `id` = '{$row['id']}'";
    $result = $db->query($query) or die(mysqli_error($db));
    $data = $result->fetch_assoc();
    $result->free();
    t_editableBattery([$data], 'doEdit', 'editTable');
};

$addCallback = function()
{
    $data = array_with_defaults(['name', 'description', 'dod', 'voltage', 'loss', 'discharge', 'lifespan', 'capacity', 'max_const_current', 'max_peak_current', 'avg_const_current', 'max_humidity', 'max_temperature', 'price', 'stock']);
    t_editableBattery([$data], 'doAdd', 'addTable');
};

// Table query.
$query = "SELECT `id`, `name`, `description`, `lifespan`, `capacity`, `price`, `stock` FROM `battery` ORDER BY `name`";
$headers = array(
      'name'        => 'Name'
    , 'description' => 'Description'
    , 'lifespan'    => 'Lifespan' . T_Units::H
    , 'capacity'    => 'Capacity' . T_Units::Ah
    , 'price'       => 'Price'    . T_Units::CFA
    , 'stock'       => 'Stock'
);

// Execute query and show table.
$result = $db->query($query) or die(mysqli_error($db));
t_scroll_table($result, $headers, $editId, $editCallback, $addCallback);
$result->free();

// Layout end.
$db->close();
t_end();

?>
