<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head lang="zh-CN">
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="address=no">
    <title></title>
    <meta name="keywords" content=""/>
    <meta name="description" content=""/>
    <link rel="stylesheet" href="__PUBLIC__/Wchat/quikaoas/css/slicy.css"/>
    <link rel="stylesheet" href="__PUBLIC__/Wchat/quikaoas/css/swiper.min.css"/>
    <link rel="stylesheet" href="__PUBLIC__/Wchat/quikaoas/css/style.css"/>
    <link rel="stylesheet" href="__PUBLIC__/Manage/js/layer/css/layui.css">
    <style>
        .hpage1 .bds .btn {
            width: auto;
            padding: 1px 5px;
        }

        .hpage1 .bds .file {
            width: 100%;
        }

        .flx-box {
            display: flex;
            flex-direction: row;
        }

        .img-content {
            display: flex;
            flex-direction: row;
        }

        .hpage1 .bds .file .img {
            width: 56px;
        }

        .hpage1 .bds .file .img img.btn-add-img {
            width: 56px;
            height: 56px;
        }
    </style>
</head>

<body>
<div class="layout">
    <div class="hpage1">
        <div class="hds">
            <div class="ico">
                <img src="__PUBLIC__/Wchat/quikaoas/images/qms1.png" alt="">
            </div>
            <h3>Cantidad de pago </h3>
            <h5><?php echo (C("CURRENCY_SYMBOL")); echo ($needRepayMoney); ?></h5>
            <ul>
                <li class="on"><a onClick="fullRepay(event)">Pago
                    completo</a></li>
                <li ><a href="<?php echo U('Repay/fullRepay',array('next'=>'delay','oid'=>$oid));?>">Préstamo
                    extendido</a>
                </li>
            </ul>
            <dl>
                <dd>
                    <h4>Monto total del préstamo</h4>
                    <h6><?php echo (C("CURRENCY_SYMBOL")); ?> <?php echo ($needRepayMoney); ?></h6>
                </dd>
                <dd>
                    <h4>Fecha de pago</h4>
                    <h6><?php echo ($needRepaymentDay); ?></h6>
                </dd>
            </dl>
        </div>
        <div class="text">
            Si el pago se ha efectuado y el reembolso aún no se ha completado, por favor proporcione el número de
            comprobante de la transacción y una captura de pantalla del mismo. Revisaremos y procesaremos dentro de
            media hora.
        </div>
        <div class="bds">
            <div class="dfsw">
                <h4>Número de recibo:</h4>
                <a href="#" class="btn" onclick="doRepayProof(event)">Actualización</a>
            </div>
            <div class="txt">
                <input type="text" style="min-height: 40px;width: 100%;outline: none;background: none;border:none;"
                       name="utr"
                       placeholder="Número de recibo de pago"/>
            </div>
        </div>
        <div class="bds">
            <div class="dfsw">
                <h4>Captura de recibo:</h4>
                <a href="#" class="btn" onclick="doRepayProof(event)">Actualización</a>
            </div>
            <div class="file">
                <form action="" method="post" class="flx-box">
                    <!--                    <input class="f1" type="file" name="" id="">-->
                    <div class="img-content">
                    </div>
                    <div class="img upload-image-btn">
                        <img src="__PUBLIC__/Wchat/quikaoas/images/qms2.png" alt="" class="btn-add-img">
                        <input type="hidden" name="repay_image"/>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<input type="hidden" name="oid" value="<?php echo ($oid); ?>">
<input type="hidden" name="toid" value="<?php echo ($bill["toid"]); ?>">
<script src="__PUBLIC__/Wchat/quikaoas/js/jquery-3.5.1.min.js"></script>
<script src="__PUBLIC__/Wchat/quikaoas/js/swiper.min.js"></script>
<script src="__PUBLIC__/Wchat/quikaoas/js/wow.min.js"></script>
<script src="__PUBLIC__/Manage/js/layer/layui.js"></script>
<script src="__PUBLIC__/Wchat/quikaoas/js/script.js"></script>
<?php $domainRules = C('APP_SUB_DOMAIN_RULES'); $domans = array_keys($domainRules); $baseUrl = $domans [1]; if(!strstr($baseUrl,"http")){ $baseUrl="http://".$baseUrl; } ?>
<script type="text/javascript">
    layui.use('upload', function () {
        var upload = layui.upload;
        var dl = null;
        var uploadInst = upload.render({
            elem: '.upload-image-btn', //绑定<?php echo (C("CURRENCY")); ?>素
            url: "<?php echo U('Info/uploadImg');?>", //上传接口
            accept: "images",
            data: {"fileName": "file"},
            before: function () {
                dl = layer.load(1);
            },
            done: function (res) {
                //上传完毕回调
                console.log(res);
                if (dl) layer.close(dl);
                if (res.status == 1) {
                    console.log(uploadInst);
                    var _ele = uploadInst.config.item[0]
                    _image_url = "<?php echo ($baseUrl); ?>/Public/Upload/" + res.info
                    layer.msg("Uploaded successfully")
                    var url = $(_ele).find('input[type="hidden"]').val()
                    var h_url = ''
                    if (url && url.length > 0) {
                        h_url = url + ',' + _image_url;
                    } else {
                        h_url = _image_url;
                    }
                    $(_ele).find('input[type="hidden"]').val(h_url)
                    $('.img-content').append('<div class="im-bd"><img src="' + _image_url + '"/ style="width:56px;height:56px;margin-right:5px;"><i class="rm"></i></div>')
                    if ($('.img-content').children().length == 3) {
                        $(".upload-image-btn").hide();
                    }
                    // $(_ele).find('img').attr('src', _image_url);
                } else {
                    layer.msg("upload failed");
                }

            }
            , error: function () {
                //请求异常回调
                if (dl) layer.close(dl);
            }
        });
    })
    $("#payfee").click(function () {
        var orderno = $('input[name="oid"]').val();//订单号
        //还款业务类型
        var ld = layer.open({type: 3});

        $.ajax({
            type: "POST",
            url: "<?php echo U('Repay/nextpay');?>",
            dataType: "json",
            data: {'oid': orderno},
            success: function (data) {
                layer.close(ld);
                if (data.status == 1) {
                    window.location.href = data.info;
                } else {
                    layer.msg("Error");
                }
            },
            error: function (jqXHR) {
                console.log("Error: " + jqXHR.status);
            }
        });
    });

    function fullRepay(event) {
        event.preventDefault();
        var ll = layer.open({type: 3});
        var orderno = $('input[name="oid"]').val();//订单号
        $.ajax({
            type: "POST",
            url: "<?php echo U('Repay/payUrl');?>",
            dataType: "json",
            data: {'oid': orderno},
            success: function (response) {
                if (response.status == 200) {
                    window.location.href = response.data;
                } else {
                    layer.msg("Error" + response.data);
                }
                layer.close(ll);
            },
            error: function (jqXHR) {
                console.log("Error: " + jqXHR.status);
            }
        });
    }


    function doRepayProof(evt) {
        evt.preventDefault();
        var utr = $("input[name='utr']").val();

        var toid = $("input[name='oid']").val();
        var utr_image = $("input[name='utr_image']").val();
        var repay_image = $("input[name='repay_image']").val();
        utr = utr ? utr : '';
        repay_image = repay_image ? repay_image : '';
        if (utr.length < 2 && repay_image.length < 2) {
            layer.msg('Please enter current UTR Number OR Upload Repayment Image');
            return false;
        }
        $.ajax({
            type: "POST",
            url: "<?php echo U('Repay/repayProof');?>",
            dataType: 'JSON',
            data: {
                oid: toid,
                utr: utr,
                utr_image: utr_image,
                repay_image: repay_image
            },
            success: function (data) {
                console.log(data.info);
                if (data.status == 1) {
                    layer.msg('Save Successfully');
                    // window.location.reload();
                } else {
                    layer.msg('The submission failed. Please replace the UTR and try again');
                }
            }
        })
    }

    $(".update-proof").click(function (evt) {
        doRepayProof(evt);
    });
</script>

</body>
</html>