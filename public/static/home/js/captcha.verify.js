layui.use(['jquery', 'layer', 'util', 'helper'], function () {

    var $ = layui.jquery;
    var layer = layui.layer;
    var util = layui.util;
    var helper = layui.helper;

    var timeCounting = false;
    var $account = $('#cv-account');
    var $emit = $('#cv-emit-btn');
    var $submit = $('#cv-submit-btn');

    if ($('#cv-captcha-enabled').val() === '1') {
        var captcha = new TencentCaptcha(
            $emit[0],
            $('#cv-captcha-appId').val(),
            function (res) {
                if (res.ret === 0) {
                    $('#cv-captcha-ticket').val(res.ticket);
                    $('#cv-captcha-rand').val(res.randstr);
                    sendVerifyCode();
                }
            }
        );
    } else {
        $emit.on('click', function () {
            sendVerifyCode();
        });
    }

    $account.on('keyup', function () {
        var account = $(this).val();
        var accountOk = helper.isPhone(account) || helper.isEmail(account);
        if (accountOk && !timeCounting) {
            $emit.removeClass('layui-btn-disabled').removeAttr('disabled');
        } else {
            $emit.addClass('layui-btn-disabled').attr('disabled', 'disabled');
        }
    });

    function sendVerifyCode() {
        if (helper.isEmail($account.val()) || helper.isPhone($account.val())) {
            var postUrl;
            var postData = {
                captcha: {
                    ticket: $('#cv-captcha-ticket').val(),
                    rand: $('#cv-captcha-rand').val(),
                }
            };
            if (helper.isPhone($account.val())) {
                postData.phone = $account.val();
                postUrl = '/verify/sms/code';
            } else if (helper.isEmail($account.val())) {
                postData.email = $account.val();
                postUrl = '/verify/mail/code';
            }
            $.ajax({
                type: 'POST',
                url: postUrl,
                data: postData,
                success: function () {
                    layer.msg('发送验证码成功', {icon: 1});
                }
            });
            $submit.removeClass('layui-btn-disabled').removeAttr('disabled');
            $emit.addClass('layui-btn-disabled').attr('disabled', 'disabled');
            showCountDown($emit);
        }
    }

    function showCountDown() {
        var serverTime = new Date().getTime();
        var endTime = serverTime + 60 * 1000;
        util.countdown(endTime, serverTime, function (date, serverTime, timer) {
            var left = date[0] * 86400 + date[1] * 3600 + date[2] * 60 + date[3];
            $emit.text(left + '秒');
            if (left === 0) {
                $emit.removeClass('layui-btn-disabled').removeAttr('disabled').text('重新发送');
                clearInterval(timer);
                timeCounting = false;
            }
        });
        timeCounting = true;
    }

});