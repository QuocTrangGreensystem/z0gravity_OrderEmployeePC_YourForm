
<?php $widget_title = !empty( $widget_title) ? $widget_title : __('Message(s)', true);
$read_only = !(($canModified && !$_isProfile) || $_canWrite) ? 1 : 0; ?>
<div class="wd-widget project-message-widget">
    <div class="wd-widget-inner">
        <div class="widget-title">
            <h3 class="title"> <?php echo $widget_title; ?> </h3>
            <div class="widget-action">
                <a href="javascript:void(0);" onclick="wd_message_expand(this)" class="primary-object-expand"><img src="/img/new-icon/expand_white.png"></a>
                <a href="javascript:void(0);" onclick="wd_message_collapse(this)" class="primary-object-collapse" style="display: none;"><img src="/img/new-icon/close-light.png"></a>
            </div>
        </div>
        <div class="widget_content" style="padding-right: 20px; background: #F8FAFB">
            <div class="add-message" style="width: 100%; margin-bottom: 20px">
                <?php if(!$read_only){ ?>
                <form id="form-wd-zog_msg">
                    <textarea class="textarea-ct" name="content" rows="2" cols="40" value="" placeholder ="<?php echo __('Votre message...', true); ?>"></textarea>
                    <input type="hidden" class="parent_id" name="parent_id">
                    <input type="hidden" class="project_id" name="project_id" value="<?php echo $project_id; ?>">
                    <button data-id="<?php echo $project_id; ?>" class="submit-btn-msg" id="wd-submit-msg" type="button">
                        <img class="add" src="/img/new-icon/icon-add.png" alt=""/>
                    </button>
                </form>
                <?php } ?>
            </div>
            <div class="wd-zog-messages">
                <div class="comment-ct" id="wd-comment-ct">

                </div>
            </div>


        </div>
    </div>
</div>
<script type="text/javascript">
    function wd_message_expand(_element) {
        var _this = $(_element);
        var _wg_container = _this.closest('.wd-widget');
        _wg_container.addClass('fullScreen');
        _wg_container.closest('li').addClass('wd_on_top');
        _this.hide();
        _wg_container.find('.primary-object-collapse').show();
        _wg_container.find('.wd-zog-messages').height($(window).height() - 120);

    }
    function wd_message_collapse(_element) {
        var _this = $(_element);
        var _wg_container = _this.closest('.wd-widget');
        _wg_container.removeClass('fullScreen');
        _wg_container.closest('li').removeClass('wd_on_top');
        _this.hide();
        _wg_container.find('.primary-object-expand').show();
        _wg_container.find('.wd-zog-messages').height(270);
    }
    $(document).ready(function () {
        // submit comment.
        $('#wd-submit-msg').on('click', function () {
            var result = '';
            var data = $('form#form-wd-zog_msg').serialize();
            $('.textarea-ct').attr('value', 'Loading...');
            $(this).hide();
            $.ajax({
                url: '/zog_msgs/saveComment',
                type: 'POST',
                data: data,
                success: function (rs) {
                    var obj = JSON.parse(rs);
                    if (obj.project_id) {
                        $('.textarea-ct').attr('value', '');
                        getComment(obj.project_id, '', 'wd-comment-ct');
                        $(this).show();
                    }

                }
            });
        });
        getComment('<?php echo $project_id; ?>', '', 'wd-comment-ct');
        var _message_resizable = $('.project-message-widget .wd-zog-messages');
        function initresizable() {
            var _max_height = 0;
            var _min_height = 248;
            _message_resizable.children().each(function () {
                _max_height += $(this).is(":visible") ? ($(this).height() + parseInt($(this).css('margin-bottom')) + parseInt($(this).css('margin-top')) + parseInt($(this).css('padding-bottom')) + parseInt($(this).css('padding-top')) + parseInt(_message_resizable.css('padding-top')) + parseInt(_message_resizable.css('padding-bottom'))) : 0;
            });
            _max_height = Math.max(_min_height, _max_height);
            _min_height = Math.min(_min_height, _max_height);
            _message_resizable.resizable({
                handles: "s",
                maxHeight: _max_height,
                minHeight: _min_height,
                resize: function (e, ui) {
                    _max_height = 0;
                    _min_height = 248;
                    _message_resizable.children().each(function () {
                        _max_height += $(this).is(":visible") ? ($(this).height() + parseInt($(this).css('margin-bottom')) + parseInt($(this).css('margin-top')) + parseInt($(this).css('padding-bottom')) + parseInt($(this).css('padding-top')) + parseInt(_message_resizable.css('padding-top')) + parseInt(_message_resizable.css('padding-bottom'))) : 0;
                    });
                    _max_height = Math.max(_min_height, _max_height);
                    _min_height = Math.min(_min_height, _max_height);
                    _message_resizable.resizable("option", 'maxHeight', _max_height);
                    _message_resizable.resizable("option", 'minHeight', _min_height);

                }
            });
            $(window).trigger('resize');

        }
        function destroyresizable() {
            _message_resizable.resizable("destroy");
            _message_resizable.css({
                width: '',
                height: ''
            });
        }
        initresizable();


        // To Viet
        // Nho goi function destroyresizable() khi Expand va goi function initresizable() sau khi collapse
    });

</script>
<?php echo $html->css('preview/zog-message-widget'); ?>
