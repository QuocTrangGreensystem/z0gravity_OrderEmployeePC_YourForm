<div class="group-content">
    <h3 class="half-padding">
        <span><?php echo __d(sprintf($_domain, 'KPI'), 'Comment', true);?></span>
        <?php if(($canModified  && !$_isProfile )|| ($_isProfile && $_canWrite)):?>
            <span><a href="javascript:void(0);" id="add-activity" class="new_log" onclick="addLog('#sale-lead-log')"></a></span>
        <?php endif;?>
    </h3>
    <div id="sale-lead-log" class="kpi-log">
        <ul>
            <?php if(($canModified  && !$_isProfile )|| ($_isProfile && $_canWrite)):
                $normal = array("á", "à", "ả", "ã", "ạ", "ắ", "ằ", "ẳ", "ẵ", "ặ", "ấ", "ầ", "ẩ", "ẫ", "ậ", "é", "è", "ẻ", "ẽ", "ẹ", "ế", "ề", "ể", "ễ", "ệ", "í", "ì", "ỉ", "ĩ", "ị", "ó", "ò", "ỏ", "õ", "ọ", "ố", "ồ", "ổ", "ỗ", "ộ", "ớ", "ờ", "ở", "ỡ", "ợ", "ú", "ù", "ủ", "ũ", "ụ", "ứ", "ừ", "ử", "ữ", "ự", "ý", "ỳ", "ỷ", "ỹ", "ỵ", "Á", "À", "Ả", "Ã", "Ạ", "Ắ", "Ằ", "Ẳ", "Ẵ", "Ặ", "Ấ", "Ầ", "Ẩ", "Ẫ", "Ậ", "É", "È", "Ẻ", "Ẽ", "Ẹ", "Ế", "Ề", "Ể", "Ễ", "Ệ", "Í", "Ì", "Ỉ", "Ĩ", "Ị", "Ó", "Ỏ", "Õ", "Ọ", "Ố", "Ồ", "Ổ", "Ỗ", "Ộ", "Ơ", "Ớ", "Ờ", "Ở", "Ỡ", "Ợ", "Ú", "Ù", "Ủ", "Ũ", "Ụ", "Ứ", "Ừ", "Ử", "Ữ", "Ự", "Ý", "Ỳ", "Ỷ", "Ỹ", "Ỵ", "ă", "â", "ê", "ô", "ơ", "ư", "đ", "Ă", "Â", "Ê", "Ô", "Ò", "Ư", "Đ");
                $flat = array("a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "e", "e", "e", "e", "e", "e", "e", "e", "e", "e", "i", "i", "i", "i", "i", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "y", "y", "y", "y", "y", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "I", "I", "I", "I", "I", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "Y", "Y", "Y", "Y", "Y", "a", "a", "e", "o", "o", "u", "d", "A", "A", "E", "O", "O", "U", "D");

            ?>
            <li class="new-log" data-log-id="">
                <img class="log-avatar" src="<?php echo $this->UserFile->avatar($employee_info['Employee']['id']) ?>">
                <div class="log-body">
                    <h4 class="log-author"><?php 
                    echo str_replace( $normal, $flat, $employee_info['Employee']['fullname'] ); ?>
                  </h4>
                    <em class="log-time"></em>
                    <textarea class="log-content" rowspan="2" onfocus="autosize(this)" onblur="autosize.destroy(this)" onchange="updateLog.call(this, 'ProjectAmr')"></textarea>
                </div>
            </li>
            <?php endif ?>
            <?php
                if(!empty($logSystems)){
                    foreach($logSystems as $logSystem){
                        $linkAvatar = $linkAvatar = $this->UserFile->avatar($logSystem['employee_id']);
                        $name = str_replace( $normal, $flat, $logSystem['update_by_employee']);
            ?>
            <li id="sale-log-<?php echo $logSystem['id'] ?>" data-log-id="<?php echo $logSystem['id'] ?>">
                <img class="log-avatar" src="<?php echo $linkAvatar ?>">
                <div class="log-body">
                    <h4 class="log-author"><?php echo $name ?></h4>
                    <em class="log-time"><?php echo date('H:i d-m-Y', $logSystem['created']) ?></em>
                    <textarea class="log-content" rowspan="2" onfocus="autosize(this)" onblur="autosize.destroy(this)" <?php echo !(($canModified  && !$_isProfile)|| ($_isProfile && $_canWrite)) ? 'disabled' : '' ?> onchange="updateLog.call(this, 'ProjectAmr')"><?php echo $logSystem['description'] ?></textarea>
                </div>
            </li>
            <?php
                    }
                }
            ?>
        </ul>
    </div>
</div>
