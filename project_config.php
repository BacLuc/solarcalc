<?php

require_once("init.php");

// Argument checking
if (!isset($_POST['load']) or !isset($_POST['sunhours'])) {
    t_argumentError();
}

// POST cleanup
if (!isset($_POST['custom'])) {
    $_POST['custom'] = array();
}

$db = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME) or die(mysqli_connect_error());

t_start();
t_project_loadSummary($_POST['load'], $_POST['custom'], $db);

// $commonList = mergeLoads($_POST['load'], $_POST['custom']);
// compute some stuff

$solution = solaradapter($_POST['sunhours'], $_POST['load'], $_POST['custom'], $db);
?>

<table cellspacing=0 cellpadding=0 class="configtable">
    <tr class="confighead">
        <td>Panel</td>
        <td>Battery</td>
        <td>Controller</td>
        <td>Inverter</td>
    </tr>
<?php
foreach ($solution as $idx => $currentsol) {

    // CONFIGROW: describes number and type of possible panel/battery/controller/inverter configurations
    echo "<tr class='configrow' onclick='toggleConfigOverview(this, \"shortTable_$idx\", \"longTable_$idx\");'>";
        
    echo "  <td>";
    t_project_moduleSummary($currentsol, 'panel', $db);
    echo "  </td>";
    
    echo "  <td>";
    t_project_moduleSummary($currentsol, 'battery', $db);
    echo "  </td>";
    
    echo "  <td>";
    t_project_moduleSummary($currentsol, 'controller', $db);
    echo "  </td>";
    
    echo "  <td>";
    t_project_moduleSummary($currentsol, 'inverter', $db);
    echo "  </td>";

    echo "  </td>";
    echo "</tr>";

?>
    <!-- Add data to short overview table -->

    <tr class='statsrow'> 
        <td colspan=4> 
            <table cellpadding=0 cellspacing=0 class='tbl_detail' style='display:table-row' id='shortTable_<?php echo $idx; ?>'>
                <tr>
                    <td class='tbl_key'>Total price<? echo T_Units::CFA; ?></td>
                    <td class="tbl_value"><?php echo number_format($currentsol['numbers']['totalPrice'], "0", ".", "'"); ?></td>
                    <td class='tbl_key'>Price per kwh<? echo T_Units::CFA; ?> </td>
                    <td class='tbl_value'><?php echo number_format($currentsol['numbers']['pricekWh'],2,'.',"'"); ?></td>
                  </tr>
                  <tr>
                    <td class='tbl_key'>Battery capacity<? echo T_Units::Ah; ?></td>
                    <td class='tbl_value'><?php echo $currentsol['numbers']['batteryCapacity']; ?></td> 
                    <td class='tbl_key'>Panel power<? echo T_Units::W; ?></td>
                    <td class='tbl_value'><?php echo $currentsol['numbers']['panelPower']; ?></td> 
                  </tr>
                  <tr>
                    <td class='tbl_key'>Expected lifetime<? echo T_Units::Y; ?></td>
                    <td class='tbl_value'><?php echo number_format($currentsol['numbers']['lifetime'],1,'.',"'"); ?></td> 
                    <td class='tbl_key'>In stock</td>
                    <td class='tbl_value'><?php echo $currentsol['numbers']['inStock']; ?></td>
                  </tr>
            </table>

            <!--  Add data to long overview table -->
            <table cellpadding=0 cellspacing=0 class='tbl_detail' style='display:none' id='longTable_<?php echo $idx; ?>'>
                <tr>
                    <td>
                        <table cellpadding=0 cellspacing=0 class='tbl_detail_long' style='display:table-row' id='longTable_<?php echo $idx; ?>'>
                            <tr>
                                <td class="tbl_key">In stock</td>
                                <td class="tbl_value"><?php echo $currentsol['numbers']['inStock']; ?></td>
                            </tr>
                            <tr>
                                <td class="tbl_key">Total price<? echo T_Units::CFA; ?></td>
                                <td class="tbl_value"><?php echo number_format($currentsol['numbers']['totalPrice'], "0", ".", "'"); ?></td>
                            </tr>
                            <tr>
                                <td class="tbl_key">Price per kwh<? echo T_Units::CFA; ?></td>
                                <td class="tbl_value"><?php echo number_format($currentsol['numbers']['pricekWh'],2,'.',"'"); ?></td>
                            </tr>
                            <tr>
                                <td class="tbl_key">Price detail<? echo T_Units::CFA; ?></td>
                                <td class="tbl_value">
                                    <table class="tbl_detail">
            
                                        <?php
                                        t_project_modulePrice($currentsol, 'panel', $db);
                                        t_project_modulePrice($currentsol, 'battery', $db);
                                        t_project_modulePrice($currentsol, 'controller', $db);
                                        t_project_modulePrice($currentsol, 'inverter', $db);
                                        ?>
            
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>     
                        <table cellpadding=0 cellspacing=0 class='tbl_detail_long' style='display:table-row' id='longTable_<?php echo $idx; ?>'>
                            <tr>
                                <td class="tbl_key">Input voltage<? echo T_Units::V; ?></td>
                                <td class="tbl_value"><?php echo $currentsol['numbers']['inputVoltage']; ?></td>
                            </tr>
                            <tr>
                                <td class="tbl_key">Expected lifetime<? echo T_Units::Y; ?></td>
                                <td class="tbl_value"><?php echo number_format($currentsol['numbers']['lifetime'],1,'.',"'"); ?></td>
                            </tr>
                            <tr>
                                <td class="tbl_key">Total battery capacity<? echo T_Units::Ah; ?></td>
                                <td class="tbl_value"><?php echo $currentsol['numbers']['batteryCapacity']; ?></td>
                            </tr>
                            <tr>
                                <td class="tbl_key">Unused battery capacity<? echo T_Units::Ah; ?></td>
                                <td class="tbl_value"><?php echo number_format($currentsol['numbers']['batteryReserve'],1,'.',"'"); ?></td>
                            </tr>
                            <tr>
                                <td class="tbl_key">Total panel power<? echo T_Units::W; ?></td>
                                <td class="tbl_value"><?php echo $currentsol['numbers']['panelPower']; ?></td>
                            </tr>
                            <tr>
                                <td class="tbl_key">Unused panel power<? echo T_Units::W; ?></td>
                                <td class="tbl_value"><?php echo number_format($currentsol['numbers']['panelReserve'],1,'.',"'"); ?></td>
                            </tr>
                            <tr class="buttonrow">
                                <td colspan=1>
                                    <form action="project_create.php" method="post">
                                    <input type="submit" name="chosenSolution" value="To infinity and beyond... >>" />
                                    <input type="hidden" name="load" value='<?php echo serialize($_POST['load']); ?>' />
                                    <input type="hidden" name="custom" value='<?php echo serialize($_POST['custom']); ?> ' />
                                    <input type="hidden" name="sunhours" value='<?php echo $_POST['sunhours']; ?>' />
                                    <input type="hidden" name="panel" value='<?php echo serialize($currentsol['panel']); ?>' />
                                    <input type="hidden" name="battery" value='<?php echo serialize($currentsol['battery']); ?>' />
                                    <input type="hidden" name="controller" value='<?php echo serialize($currentsol['controller']); ?>' />
                                    <input type="hidden" name="inverter" value='<?php echo serialize($currentsol['inverter']); ?>' />
                                    </form>
                                </td>
                                <td colspan=1>
                                    <form action="project_explanation.php" method="post"i target="_blank">
                                    <input type="submit" name="explanationDemand" value="Are you talking to me? >>" />
                                    <input type="hidden" name="load" value='<?php echo serialize($_POST['load']); ?>' />
                                    <input type="hidden" name="custom" value='<?php echo serialize($_POST['custom']); ?> ' />
                                    <input type="hidden" name="sunhours" value='<?php echo $_POST['sunhours']; ?>' />
                                    <input type="hidden" name="panel" value='<?php echo serialize($currentsol['panel']); ?>' />
                                    <input type="hidden" name="battery" value='<?php echo serialize($currentsol['battery']); ?>' />
                                    <input type="hidden" name="controller" value='<?php echo serialize($currentsol['controller']); ?>' />
                                    <input type="hidden" name="inverter" value='<?php echo serialize($currentsol['inverter']); ?>' />
                                    </form>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr> <td colspan=4> </td> </tr>


<?php
}

echo "</table>";

?>

<?php
$db->close();
t_end();
?>
