<?php
declare(strict_types=1);
?>
<script>
function isBlank(str) {
    return (!str || /^\s*$/.test(str));
}

if (typeof sessionStorage !== 'undefined') {
    if (isBlank(sessionStorage.getItem('tab'))) {
        sessionStorage.setItem('tab', 'tab1');
    }
}
</script>

<aside class="left-side">
    <section class="sidebar">

        <div class="user-panel">
            <div class="pull-left image">
                <?php echo $Info->User_pic(WISession::get('user_id')); ?>
            </div>

            <div class="pull-left info">
                <p>Hello</p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <script>
        $(function () {
            var selectedIndex = localStorage.getItem('selected');
            var active = (selectedIndex !== null && selectedIndex !== '')
                ? parseInt(selectedIndex, 10)
                : false;

            $('#accordion').accordion({
                active: active,
                collapsible: true,
                heightStyle: 'content',
                animate: 300,
                activate: function (event, ui) {
                    if (ui.newHeader.length) {
                        var index = $('#accordion h3').index(ui.newHeader);

                        if (index > -1) {
                            localStorage.setItem('selected', index);
                        }
                    }
                }
            });

            $('#accordion h3').on('click', function () {
                var self = this;

                setTimeout(function () {
                    var theOffset = $(self).offset();

                    $('html, body').animate({
                        scrollTop: theOffset.top - 100
                    });
                }, 310);
            });
        });
        </script>

        <?php $web->AdminSideBar(); ?>

    </section>
</aside>