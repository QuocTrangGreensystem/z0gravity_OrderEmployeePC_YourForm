<div class="group-content">
    <h3><?php echo __d(sprintf($_domain, 'KPI'), 'Customer Point Of View', true);?></h3>
    <div class="wd-input wd-weather-list-dd">
        <ul style="float: left; display: inline;">
            <li><input class="input_weather" checked="true" <?php echo !(($canModified  && !$_isProfile )|| ($_isProfile && $_canWrite)) ? 'disabled' : '' ?> style="width: 25px; margin-top: 8px;" <?php echo @$this->data["ProjectAmr"]["customer_point_of_view"] == 'sun' ? 'checked' : 'checked'; ?> value="sun" name="data[ProjectAmr][customer_point_of_view][]" type="radio" /> <img title="Sun"  src="<?php echo $html->url('/img/sun.png') ?>"  /></li>
            <li><input class="input_weather" <?php echo !(($canModified  && !$_isProfile)|| ($_isProfile && $_canWrite)) ? 'disabled' : '' ?> type="radio" <?php echo @$this->data["ProjectAmr"]["customer_point_of_view"] == 'cloud' ? 'checked' : ''; ?> value="cloud" name="data[ProjectAmr][customer_point_of_view][]" style="width: 25px;margin-top: 8px;" /> <img title="Cloud" src="<?php echo $html->url('/img/cloud.png') ?>"  /></li>
            <li><input class="input_weather" <?php echo !(($canModified  && !$_isProfile ) || ($_isProfile && $_canWrite)) ? 'disabled' : '' ?> type="radio" <?php echo @$this->data["ProjectAmr"]["customer_point_of_view"] == 'rain' ? 'checked' : ''; ?> value="rain" name="data[ProjectAmr][customer_point_of_view][]" style="width: 25px;margin-top: 8px;"   /> <img title="Rain"  src="<?php echo $html->url('/img/rain.png') ?>"  /></li>
        </ul>
    </div>
</div>
