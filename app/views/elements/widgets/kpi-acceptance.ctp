<div class="group-content">
    <h3><span><?php echo __d(sprintf($_domain, 'KPI'), 'Acceptance', true);?></span></h3>
    <table id="acceptance">
        <tbody>
        <?php
        foreach($acceptances as $acc){
            if( !$acc['ProjectAcceptance']['weather'] )$acc['ProjectAcceptance']['weather'] = 'sun';
            $accId = $acc['ProjectAcceptance']['id'];
?>
        <tr class="acceptance">
            <td width="300"><?php echo @$types[ $acc['ProjectAcceptance']['project_acceptance_type_id'] ] ?></td>
            <td width="300" class="wd-weather-list-dd">
                <ul style="float: left; display: inline;">
                    <li><input checked="true" <?php echo !(($canModified  && !$_isProfile )|| ($_isProfile && $_canWrite)) ? 'disabled' : '' ?> style="width: 25px; margin-top: 8px;" <?php echo @$acc["ProjectAcceptance"]["weather"] == 'sun' ? 'checked' : 'checked'; ?> value="sun" name="data[ProjectAcceptance][<?php echo $accId ?>]" type="radio" class="weather" data-id="<?php echo $accId ?>" /> <img title="Sun"  src="<?php echo $html->url('/img/sun.png') ?>"  /></li>
                    <li><input <?php echo !(($canModified  && !$_isProfile )|| ($_isProfile && $_canWrite)) ? 'disabled' : '' ?> type="radio" <?php echo @$acc["ProjectAcceptance"]["weather"] == 'cloud' ? 'checked' : ''; ?> value="cloud" name="data[ProjectAcceptance][<?php echo $accId ?>]" style="width: 25px;margin-top: 8px;" class="weather" data-id="<?php echo $accId ?>" /> <img title="Cloud" src="<?php echo $html->url('/img/cloud.png') ?>"  /></li>
                    <li><input <?php echo !(($canModified  && !$_isProfile )|| ($_isProfile && $_canWrite)) ? 'disabled' : '' ?> type="radio" <?php echo @$acc["ProjectAcceptance"]["weather"] == 'rain' ? 'checked' : ''; ?> value="rain" name="data[ProjectAcceptance][<?php echo $accId ?>]" style="width: 25px;margin-top: 8px;" class="weather" data-id="<?php echo $accId ?>" /> <img title="Rain"  src="<?php echo $html->url('/img/rain.png') ?>"  /></li>
                </ul>
            </td>
            <td><?php echo $acc['ProjectAcceptance']['progress'] ? $acc['ProjectAcceptance']['progress'] : '0.00' ?> %</td>
        </tr>
<?php
        }
        ?>
        </tbody>
    </table>
</div>
