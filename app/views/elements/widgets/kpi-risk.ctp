<div class="group-content">
    <h3><span><?php echo __d(sprintf($_domain, 'KPI'), 'Risk', true);?></span></h3>
    <div class="wd-input separator">
        <?php
        // echo $this->Form->input('project_amr_risk_control_id', array('div' => false, 'label' => false,
        // 	'class' => 'selection-plus',
        // 	'name' => 'data[ProjectAmr][project_amr_risk_control_id]',
        // 	'value' => (!empty($this->data['ProjectAmr']['project_amr_risk_control_id'])) ? $this->data['ProjectAmr']['project_amr_risk_control_id'] : "",
        // 	"empty" => __("-- Select --", true),
        // ));
        ?>

        <div style="float: left; line-height: -40px; width:30%">
            <div class="wd-input wd-weather-list-dd">
                <ul style="float: left; display: inline;">
                    <li><input class="input_weather" checked="true" <?php echo !(($canModified && !$_isProfile)|| ($_isProfile && $_canWrite)) ? 'disabled' : '' ?> style="width: 25px; margin-top: 8px;" <?php echo @$this->data["ProjectAmr"]["risk_control_weather"] == 'sun' ? 'checked' : 'checked'; ?> value="sun" name="data[ProjectAmr][risk_control_weather][]" type="radio" /> <img title="Sun"  src="<?php echo $html->url('/img/sun.png') ?>"  /></li>
                    <li><input class="input_weather" <?php echo !(($canModified && !$_isProfile)|| ($_isProfile && $_canWrite)) ? 'disabled' : '' ?> type="radio" <?php echo @$this->data["ProjectAmr"]["risk_control_weather"] == 'cloud' ? 'checked' : ''; ?> value="cloud" name="data[ProjectAmr][risk_control_weather][]" style="width: 25px;margin-top: 8px;" /> <img title="Cloud" src="<?php echo $html->url('/img/cloud.png') ?>"  /></li>
                    <li><input class="input_weather" <?php echo !(($canModified && !$_isProfile) || ($_isProfile && $_canWrite)) ? 'disabled' : '' ?> type="radio" <?php echo @$this->data["ProjectAmr"]["risk_control_weather"] == 'rain' ? 'checked' : ''; ?> value="rain" name="data[ProjectAmr][risk_control_weather][]" style="width: 25px;margin-top: 8px;"   /> <img title="Rain"  src="<?php echo $html->url('/img/rain.png') ?>"  /></li>
                </ul>
                <?php //echo $this->Form->radio('weather', array('div'=>false, 'label'=>false)); ?>
            </div>
        </div>
    </div>
    <div id="cm-log" class="kpi-log">
        <ul>
            <?php
                $normal = array("á", "à", "ả", "ã", "ạ", "ắ", "ằ", "ẳ", "ẵ", "ặ", "ấ", "ầ", "ẩ", "ẫ", "ậ", "é", "è", "ẻ", "ẽ", "ẹ", "ế", "ề", "ể", "ễ", "ệ", "í", "ì", "ỉ", "ĩ", "ị", "ó", "ò", "ỏ", "õ", "ọ", "ố", "ồ", "ổ", "ỗ", "ộ", "ớ", "ờ", "ở", "ỡ", "ợ", "ú", "ù", "ủ", "ũ", "ụ", "ứ", "ừ", "ử", "ữ", "ự", "ý", "ỳ", "ỷ", "ỹ", "ỵ", "Á", "À", "Ả", "Ã", "Ạ", "Ắ", "Ằ", "Ẳ", "Ẵ", "Ặ", "Ấ", "Ầ", "Ẩ", "Ẫ", "Ậ", "É", "È", "Ẻ", "Ẽ", "Ẹ", "Ế", "Ề", "Ể", "Ễ", "Ệ", "Í", "Ì", "Ỉ", "Ĩ", "Ị", "Ó", "Ỏ", "Õ", "Ọ", "Ố", "Ồ", "Ổ", "Ỗ", "Ộ", "Ơ", "Ớ", "Ờ", "Ở", "Ỡ", "Ợ", "Ú", "Ù", "Ủ", "Ũ", "Ụ", "Ứ", "Ừ", "Ử", "Ữ", "Ự", "Ý", "Ỳ", "Ỷ", "Ỹ", "Ỵ", "ă", "â", "ê", "ô", "ơ", "ư", "đ", "Ă", "Â", "Ê", "Ô", "Ò", "Ư", "Đ");
                $flat = array("a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "e", "e", "e", "e", "e", "e", "e", "e", "e", "e", "i", "i", "i", "i", "i", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "y", "y", "y", "y", "y", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "I", "I", "I", "I", "I", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "Y", "Y", "Y", "Y", "Y", "a", "a", "e", "o", "o", "u", "d", "A", "A", "E", "O", "O", "U", "D");
                if(!empty($commentRisks)){
                    //$checkIsChang
                    foreach($commentRisks as $logSystem){
                        $linkAvatar = $linkAvatar = $this->UserFile->avatar($logSystem['employee_id']);
                        $name = str_replace( $normal, $flat, $logSystem['update_by_employee']);
            ?>
            <li id="sale-log-<?php echo $logSystem['id'] ?>" data-log-id="<?php echo $logSystem['id'] ?>">
                <img class="log-avatar" src="<?php echo $linkAvatar ?>">
                <div class="log-body">
                    <h4 class="log-author"><?php echo $name ?></h4>
                    <em class="log-time"><?php echo date('H:i d-m-Y', $logSystem['created']) ?></em>
                    <textarea class="log-content" rowspan="2" onfocus="autosize(this)" onblur="autosize.destroy(this)" <?php echo !(($canModified && !$_isProfile) || ($_isProfile && $_canWrite)) ? 'disabled' : '' ?> onchange="updateLog.call(this, 'ProjectRisk')"><?php echo $logSystem['description'] ?></textarea>
                </div>
            </li>
            <?php
                    }
                }
            ?>
        </ul>
    </div>
</div>
