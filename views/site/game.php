<?php
use yii\helpers\Url;
use yii\web\View;
?>

<?php
$this->title = Yii::t('app', 'Game');
?>
<div style="float: right; width: 500px; height: 700px;">
    <h3><?= Yii::t('app', 'Game Turns') ?></h3>
    <?php
        $counter = 1;
        foreach($moves as $move){
    ?>
        <p><?="{$counter}. {$move['username']} : X - {$move['abs']}, Y - {$move['ord']}"?></p>
    <?php
            $counter += 1;
        }
    ?>
    <?php
        if(empty($symbol)) {
    ?>
        <h4><?= Yii::t('app', '{username} is winner!', ['username' => $moves[$counter-2]['username']]) ?></h4>
    <?php
        }
    ?>
</div>

<table border="1" cellspacing="0" cellpadding="8"  style="border-color:#cc0000">
<?php
    for($x = 1; $x <= 3; $x++){
?>
    <tr>
        <?php
            for($y = 1; $y <= 3; $y++){
                $content = '';
                $class = 'active';
                foreach($moves as $move){
                    if((int)$move['abs'] === $x && (int)$move['ord'] === $y){
                        $content = $move['symbol'];
                        $class = 'inactive';
                        break;
                    }
                }
        ?>
            <td class="<?=$class ?>" width="200" height="200" style="border-color:#cc0000; font-size:100pt; text-align: center;" data-abs = '<?=$x ?>' data-ord = '<?=$y ?>'>
                <?php
                    echo $content;
                ?>
            </td>
        <?php
            }
        ?>
    </tr>
<?php
    }
?>
</table>

<?php
if(!empty($symbol)) {
    $this->registerJs('
        function editTable(){
            $("td.active").bind("click.editTable", function(event){
                    $(event.target).text("' . $symbol . '");
                    $("*").unbind("click.editTable");
                    var abs = $(event.target).attr("data-abs");
                    var ord = $(event.target).attr("data-ord");
                    $.post("' . Url::to(["site/make-move"], true) . '", {
                            game_id: "' . $game_id . '",
                            abs: abs,
                            ord: ord,
                            gamer_id: "' . $current . '"
                        }
                    ).success(function(data){
                            var data = jQuery.parseJSON(data);
                            if(data.length !== 0){
                                alert(data);
                            }
                            window.location = "' . Url::to(["site/index"], true) . '"
                        }
                    );
                }
            );
        }
        editTable();
    ', View::POS_END);
}
?>