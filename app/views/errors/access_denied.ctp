<style>
.error{background-color:#ffcece;background-position:15px -1000px;border-radius:3px;-moz-border-radius:3px;-webkit-border-radius:3px;position:relative;zoom:1;color:#000;border-color:#ec7e8b;margin:5px auto;padding:8px 15px 15px 45px;}
</style>
<div id="content">
    <div class="container_panes" style="height: 400px;">
        <div class="top_shadow"></div>
        <div class="panes" align="center" style="padding-top: 100px;">          
            <div class="message error" style="width: 600px;">
                <?php echo $msg; ?> 
                <a href="javascript:void(0)" class="close">x</a>
            </div>
            Click <a href="javascript:void(0)" onclick="history.back()">here</a> to back!
        </div>
    </div>
</div>
<script>
    $("a.close").click(function(){
        $("div.error").hide();
    })
</script>