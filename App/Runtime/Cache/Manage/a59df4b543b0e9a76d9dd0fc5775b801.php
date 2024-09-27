<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no,minimal-ui">
<link rel="stylesheet" href="__PUBLIC__/Manage/css/bootstrap.css">
<link rel="stylesheet" href="__PUBLIC__/Manage/fonts/web-icons/web-icons.css">
<link rel="stylesheet" href="__PUBLIC__/Manage/fonts/font-awesome/font-awesome.css">
<link href="__PUBLIC__/Manage/js/layer/css/layui.css" rel="stylesheet"/>
<link rel="stylesheet" href="__PUBLIC__/Manage/css/mobile.css">
<link rel="stylesheet" href="__PUBLIC__/Manage/css/myapp.css">
<link rel="stylesheet" href="__PUBLIC__/Manage/css/daterangepicker.css">
<script src="__PUBLIC__/Manage/js/jquery.js"></script>
<script src="__PUBLIC__/Manage/js/jquery.form.js"></script>
<script src="__PUBLIC__/Manage/js/bootstrap.js"></script>
<script src="__PUBLIC__/Manage/js/layer/layer.js"></script>
<script src="__PUBLIC__/Manage/js/cvphp.js"></script>
<script src="__PUBLIC__/Manage/js/layer/layui.js"></script>
<script src="__PUBLIC__/Manage/js/moment.min.js"></script>
<script src="__PUBLIC__/Manage/js/daterangepicker.js"></script>
<script src="__PUBLIC__/Manage/js/app.js"></script>
    <script src="__PUBLIC__/Manage/js/layer/layui.js"></script>
    <link rel="stylesheet" href="__PUBLIC__/Manage/js/layer/css/layui.css">
    <link rel="stylesheet" href="__PUBLIC__/Manage/css/table.css"/>
    <script type="text/javascript" src="__PUBLIC__/Manage/js/wangEditor/wangEditor.min.js"></script>
    <title>借款设置-汇码网（www.huimaw.com）</title>
    <style type="text/css">
        .layer-anim {
            top: 180px !important;
        }
        .public-selectArea1 .input-lang{
            width: 80px;
        }
        .lang-append{
            display: flex;
        }
        .input-addons{
            padding: 0 5px;
            border: 1px solid #ccc;
            margin: 0;
            border-left: 0;
            text-align: center;
            align-items: center;
            display: flex;
        }
        .check-lang{
            display: flex;
        }
        .check-lang .layui-unselect{
            margin-right: 0;
        }
        .check-lang .layui-form-checkbox i{
            height: 30px;
        }
        .btn-del-addon{
            border: 1px solid #ccc;
            border-left: 0;
            margin: 0;
            padding: 0 5px;
            display: flex;
            align-items: center;
            background: crimson;
        }
        .btn-del-addon i{
            color: white;
        }
    </style>
</head>

<body>
<div class="nestable" id="app">
    <div class="console-title console-title-border drds-detail-title clearfix">
        <h5>借款设置</h5>
    </div>

    <div class="public-selectArea public-selectArea1 margin_10">
        <form action="<?php echo U('Setting/loan');?>" method="post" class="layui-form">
            <div class="clearfix clearfix1">
                <div class="wp_box  col-xs-8">
                    <dl>
                        <dt>借款验证码：</dt>
                        <dd>
                            <input type="text" name="verifycode" value="<?php echo ((C("verifycode"))?(C("verifycode")):''); ?>"/>
                        </dd>
                        <em class="tishi">借款时的验证码</em>
                    </dl>
                </div>
            </div>
            <div class="clearfix clearfix1">
                <div class="wp_box  col-xs-8">
                    <dl>
                        <dt>放款类型：</dt>
                        <dd>
                            <label><input type="radio" name="Loan_TYPE" value="0"
                                <?php if(C("Loan_TYPE")== 0): ?>checked<?php endif; ?>
                                >单期（期限单位：天）</label>
<!--                            <label><input type="radio" name="Loan_TYPE" value="1"-->
<!--                                <?php if(C("Loan_TYPE")== 1): ?>checked<?php endif; ?>-->
<!--                                >分期（期限单位：月）</label>-->
                        </dd>
                    </dl>
                </div>
            </div>

            <div class="clearfix clearfix1">
                <div class="wp_box  col-xs-8">
                    <dl>
                        <dt>利率repay_interest：</dt>
                        <dd>
                            <input type="text" name="Interest_D" value="<?php echo ((C("Interest_D"))?(C("Interest_D")):''); ?>"/>
                        </dd>
                        <em class="tishi">放款类型为单期时生效</em>
                    </dl>
                </div>
            </div>
            <div class="clearfix clearfix1">
                <div class="wp_box  col-xs-8">
                    <dl>
                        <dt>服务费serviceFee：</dt>
                        <dd>
                            <input type="text" name="REPAY_COST" value="<?php echo ((C("REPAY_COST"))?(C("REPAY_COST")):''); ?>"/>
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="clearfix clearfix1">
                <div class="wp_box  col-xs-8">
                    <dl>
                        <dt>续期费率：</dt>
                        <dd>
                            <input type="text" name="PostPone_RATE" value="<?php echo ((C("PostPone_RATE"))?(C("PostPone_RATE")):''); ?>"/>
                        </dd>
                        <em class="tishi">延期费率按日计算</em>
                    </dl>
                </div>
            </div>
            <div class="clearfix clearfix1">
                <div class="wp_box  col-xs-8">
                    <dl>
                        <dt>逾期费率：</dt>
                        <dd>
                            <input type="text" name="Overdue" value="<?php echo ((C("Overdue"))?(C("Overdue")):''); ?>"/>
                        </dd>
                        <em class="tishi">逾期日开始按日计算</em>
                    </dl>
                </div>
            </div>
            <div class="clearfix clearfix1">
                <div class="wp_box  col-xs-8">
                    <dl>
                        <dt>手续费Commissions：</dt>
                        <dd>
                            <input type="text" name="Commissions" value="<?php echo ((C("Commissions"))?(C("Commissions")):''); ?>"/>
                        </dd>
                        <em class="tishi">手续费Commissions</em>
                    </dl>
                </div>
            </div>
            <div class="clearfix clearfix1">
                <div class="wp_box col-xs-8 layui-form-item">
                    <dl>
                        <dt>代收是否提交到支付商：</dt>
                        <dd class="long-switch layui-input-block">
                            <input type="checkbox" name="DAISHOU_SUBMIT_TO_PAYER" lay-skin="switch"
                            <?php if(C("DAISHOU_SUBMIT_TO_PAYER")== 1): ?>checked="checked"<?php endif; ?>
                            value="<?php echo ((C("DAISHOU_SUBMIT_TO_PAYER"))?(C("DAISHOU_SUBMIT_TO_PAYER")):1); ?>"/>
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="clearfix clearfix1">
                <div class="wp_box  col-xs-8  layui-form-item">
                    <dl>
                        <dt>代付是否提交到支付商：</dt>
                        <dd class="long-switch layui-input-block">
                            <input type="checkbox" name="DAIFU_SUBMIT_TO_PAYER" lay-skin="switch"
                            <?php if(C("DAIFU_SUBMIT_TO_PAYER")== 1): ?>checked="checked"<?php endif; ?>
                            value="<?php echo ((C("DAIFU_SUBMIT_TO_PAYER"))?(C("DAIFU_SUBMIT_TO_PAYER")):1); ?>"/>
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="clearfix clearfix1">
                <div class="wp_box  col-xs-8">
                    <dl>
                        <dt>期限范围（单期）：</dt>
                        <?php if(C('Deadline_D')) $str = implode(',',C('Deadline_D')); ?>
                        <dd>
                            <input type="text" name="Deadline_D" value="<?php echo (($str)?($str):''); ?>"/>
                        </dd>
                        <em class="tishi">放款类型为单期时生效，以逗号隔开</em>
                    </dl>
                </div>
            </div>

            <div class="clearfix clearfix1">
                <div class="wp_box  col-xs-8">
                    <dl>
                        <dt>期限范围（分期）：</dt>
                        <?php if(C('Deadline_D')) $str = implode(',',C('Deadline_M')); ?>
                        <dd>
                            <input type="text" name="Deadline_M" value="<?php echo (($str)?($str):''); ?>"/>
                        </dd>
                        <em class="tishi">放款类型为分期时生效，以逗号隔开</em>
                    </dl>
                </div>
            </div>
            <div class="clearfix clearfix1">
                <div class="wp_box  col-xs-8">
                    <dl>
                        <dt>币种：</dt>
                        <dd>
                            <input type="text" name="CURRENCY" value="<?php echo (C("CURRENCY")); ?>"/>
                        </dd>
                    </dl>
                </div>
                <div class="wp_box  col-xs-8">
                    <dl>
                        <dt>货币符号：</dt>
                        <dd>
                            <input type="text" name="CURRENCY_SYMBOL" value="<?php echo (C("CURRENCY_SYMBOL")); ?>"/>
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="clearfix clearfix1">
                <div class="wp_box  col-xs-8">
                    <dl>
                        <dt>用户默认额度：</dt>
                        <dd>
                            <input type="text" name="DEFAULT_QUOTA"
                                   value="<?php echo ((C("DEFAULT_QUOTA"))?(C("DEFAULT_QUOTA")):'1000'); ?>"/>
                        </dd>
                        <em class="tishi">单位：<?php echo (C("CURRENCY")); ?></em>
                    </dl>
                </div>
            </div>
            <div class="clearfix clearfix1">
                <div class="wp_box  col-xs-8">
                    <dl>
                        <dt>单次借款最低金额：</dt>
                        <dd>
                            <input type="text" name="Money_MIN" value="<?php echo ((C("Money_MIN"))?(C("Money_MIN")):'100'); ?>"/>
                        </dd>
                        <em class="tishi">单位：<?php echo (C("CURRENCY")); ?></em>
                    </dl>
                </div>
            </div>

            <div class="clearfix clearfix1">
                <div class="wp_box  col-xs-8">
                    <dl>
                        <dt>单次借款最高金额：</dt>
                        <dd>
                            <input type="text" name="Money_MAX" value="<?php echo ((C("Money_MAX"))?(C("Money_MAX")):'1000'); ?>"/>
                        </dd>
                        <em class="tishi">单位：<?php echo (C("CURRENCY")); ?></em>
                    </dl>
                </div>
            </div>

            <div class="clearfix clearfix1">
                <div class="wp_box  col-xs-8">
                    <dl>
                        <dt>借款金额选择跨度：</dt>
                        <dd>
                            <input type="text" name="Money_STEP" value="<?php echo ((C("Money_STEP"))?(C("Money_STEP")):'100'); ?>"/>
                        </dd>
                        <em class="tishi">单位：<?php echo (C("CURRENCY")); ?></em>
                    </dl>
                </div>
            </div>
            <div class="clearfix clearfix1">
                <div class="wp_box  col-xs-8">
                    <dl>
                        <dt>下一个可用贷款金额：</dt>
                        <dd>
                            <input type="text" name="Next_Loan" value="<?php echo ((C("Next_Loan"))?(C("Next_Loan")):'20000'); ?>"/>
                        </dd>
                        <em class="tishi">单位：<?php echo (C("CURRENCY")); ?></em>
                    </dl>
                </div>
            </div>
            <div class="clearfix clearfix1">
                <div class="wp_box  col-xs-8">
                    <dl>
                        <dt>批准率：</dt>
                        <dd>
                            <input type="text" name="Approval_Rate" value="<?php echo ((C("Approval_Rate"))?(C("Approval_Rate")):'0.96'); ?>"/>
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="clearfix clearfix1">
                <div class="wp_box  col-xs-8">
                    <dl>
                        <dt>下一个贷款期限：</dt>
                        <dd>
                            <input type="text" name="Next_Loan_Period" value="<?php echo ((C("Next_Loan_Period"))?(C("Next_Loan_Period")):'14'); ?>"/>
                        </dd>
                    </dl>
                </div>
            </div>
      <!--      <div class="clearfix clearfix1">
                <div class="wp_box  col-xs-8">
                    <input type="hidden" name="AllLanguages" value="<?php echo (C("AllLanguages")); ?>">
                    <dl class="lang-box">
                        <dt>可选择语言：</dt>
                        <input type="hidden" name="Languages" value="<?php echo (C("Languages")); ?>">
                        <?php if(is_array($AllLanguages)): $i = 0; $__LIST__ = $AllLanguages;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$lang): $mod = ($i % 2 );++$i;?><dd class="check-lang">
                            <input name="lang" type="checkbox" value="<?php echo ($lang); ?>" title="<?php echo ($lang); ?>" lay-filter='languages' <?php if(in_array($lang,$Languages)): ?>checked<?php endif; ?>>
                            <?php if(!in_array($lang,$Languages)): ?><span class="btn-del-addon"><i class="layui-icon layui-icon-close"></i></span><?php endif; ?>
                        </dd><?php endforeach; endif; else: echo "" ;endif; ?>
                        <dd>
                            <button type="button" class="layui-btn layui-btn-primary layui-btn-sm" id="add-lang">
                                <i class="layui-icon layui-icon-add-1"></i>
                            </button>
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="clearfix clearfix1">
                <div class="wp_box  col-xs-8">
                    <dl>
                        <dt>默认语言：</dt>
                        <?php if(is_array($Languages)): $i = 0; $__LIST__ = $Languages;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$lang): $mod = ($i % 2 );++$i;?><dd>
                            <input type="radio" name="Default_Language" <?php if(C("Default_Language")== $lang): ?>checked<?php endif; ?>  value='<?php echo ($lang); ?>' title='<?php echo ($lang); ?>'>
                        </dd><?php endforeach; endif; else: echo "" ;endif; ?>
                    </dl>
                </div>
            </div>-->
            <div class="clearfix clearfix1">
                <div class="wp_box  col-xs-8">
                    <input type="hidden" name="AllLanguages" v-model="allLangs" value="<?php echo (C("AllLanguages")); ?>">
                    <input type="hidden" name="Languages" v-model="hasLangs" value="<?php echo (C("Languages")); ?>">
                    <dl class="lang-box">
                        <dt>可选择语言：</dt>
                        <template v-for="(lang,index) in allLanguages">
                            <dd class="check-lang">
                                <div class="layui-unselect layui-form-checkbox layui-form-checked" @click="toggleLangs(lang,true)" v-if="Languages.indexOf(lang)>=0"><span>{{lang}}</span><i class="layui-icon layui-icon-ok"></i></div>
                                <div class="layui-unselect layui-form-checkbox" @click="toggleLangs(lang,true)"  v-if="Languages.indexOf(lang)==-1"><span>{{lang}}</span><i class="layui-icon layui-icon-ok"></i></div>
                               <span class="btn-del-addon" @click="delLang(index)" v-if="Languages.indexOf(lang)==-1"><i class="layui-icon layui-icon-close"></i></span>
                            </dd>
                        </template>
                        <template v-if="newlangs.length>0">
                            <dd class='lang-append' v-for="(nlang,index) in newlangs">
                                <input v-model="newlangs[index]" class='input-lang'/>
                                <span class='input-addons' @click="addLang(index)"><i class='layui-icon layui-icon-ok'></i></span>
                            </dd>
                        </template>
                        <dd>
                            <button type="button" class="layui-btn layui-btn-primary layui-btn-sm" id="add-lang" @click="addLangInput">
                                <i class="layui-icon layui-icon-add-1"></i>
                            </button>
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="clearfix clearfix1">
                <div class="wp_box  col-xs-8">
                    <dl class="lang-box">
                        <dt>默认语言：</dt>
                        <input name="Default_Language" type="hidden" v-model="default_language">
                        <dd v-for="lan in Languages">
                            <div v-if="lan==default_language" class="layui-unselect layui-form-radio" @click="changeDefault(lan)">
                                <i class="layui-anim layui-icon layui-icon-radio" style="color:#5FB878 "></i><div>{{lan}}</div>
                            </div>
                            <div v-else class="layui-unselect layui-form-radio"  @click="changeDefault(lan)">
                                <i class="layui-anim layui-icon layui-icon-circle" style="color:#5FB878 "></i><div>{{lan}}</div>
                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="clearfix clearfix1">
                <div class="wp_box  col-xs-8">
                    <dl>
                        <dt>默认跳转目标：</dt>
                        <input name="Default_Target" type="hidden" v-model="target">
                        <input name="AllTargets" type="hidden" v-model="allTargetStr">
                        <dd v-for="tag in targets">
                            <div class="layui-unselect layui-form-radio"  @click="changeTarget(tag)" style="float:left"> 
                                <i :class="'layui-anim layui-icon '+(target==tag?'layui-icon-radio':'layui-icon-circle') " style="color:#5FB878 "></i><div>{{tag}}</div> <div style="padding-left:2rem"><button @click="shanchuTarget(tag)">删除</button></div> 
                            </div>
                        </dd>
                     
                        <template v-if="newTargets.length>0">
                            <dd class='lang-append' v-for="(tag,index) in newTargets">
                                <input v-model="newTargets[index]" class='input-lang'/>
                                <span class='input-addons' @click="addTag(index)"><i class='layui-icon layui-icon-ok'></i></span>
                            </dd>
                        </template>
                        <dd>
                            <button type="button" class="layui-btn layui-btn-primary layui-btn-sm" id="add-target" @click="addNewTarget">
                                <i class="layui-icon layui-icon-add-1"></i>
                            </button>
                        </dd>
                    </dl>
                </div>
            </div>
        </form>
        <div class="btnArea margin_20">
            <a href="javascript:;" class="btn btn-grayBg">
                <span class="public-label">提交</span>
            </a>
        </div>
    </div>
</div>
</body>
<script src="__PUBLIC__/Manage/js/vue.js"></script>
<script type="text/javascript">
    var defaultLang='<?php echo (C("Default_Language")); ?>';
    var allLang='<?php echo (C("AllLanguages")); ?>';
    var hasLang='<?php echo (C("Languages")); ?>';
    var Default_Target='<?php echo (C("Default_Target")); ?>';
    var targerStr='<?php echo (C("AllTargets")); ?>';
    var allLanguages=<?php if(empty($AllLanguages)){ echo '[]';}else{ echo json_encode($AllLanguages);}?>;
    var Languages=<?php if(empty($Languages)){ echo '[]';}else{ echo json_encode($Languages);}?>;
    var targetarr=<?php if(empty($allTargets)){ echo '[]';}else{ echo json_encode($allTargets);}?>;
    new Vue({
        el:'#app',
        data:{
            // AllLanguages:JSON.stringify()
            default_language:defaultLang,
            allLanguages:allLanguages,
            allLangs:allLang,
            hasLangs:hasLang,
            Languages:Languages,
            newlangs:[],
            targets:targetarr,
            target:Default_Target,
            newTargets:[],
            allTargetStr:targerStr
        },
        methods:{
            addLangInput:function(){
                this.newlangs.push('');
                console.log('addLangInput',this.newlangs);
            },
            addLang:function (index){
                if(this.newlangs[index].length==0)return layer.msg('语言名不能为空');
                var lang=this.newlangs[index];
                this.newlangs.splice(index,1);
                this.allLanguages.push(lang);
                this.allLangs=this.allLanguages.join(',');
            },
            delLang:function (index){
                var _index=this.Languages.indexOf(this.allLanguages[index]);
                if(_index>=0)this.Languages.splice(_index,1);
                this.hasLangs=this.Languages.join(',');

                this.allLanguages.splice(index,1);
                this.allLangs=this.allLanguages.join(',');

            },
            toggleLangs:function (lang,flag){
                var _index=this.Languages.indexOf(lang);
                console.log(lang,flag,this.Languages,_index);
                if(_index==-1) this.Languages.push(lang);
                if(_index>=0)this.Languages.splice(_index,1);
                this.hasLangs=this.Languages.join(',');
            },
            changeDefault:function (lang){
                this.default_language=lang;
            },
            changeTarget:function (title){
                this.target=title;
            },
            addTag:function (index){
                if(this.newTargets[index].length==0)return layer.msg('语言名不能为空');
                var tag=this.newTargets[index];
                this.newTargets.splice(index,1);
                this.targets.push(tag);
                this.allTargetStr=this.targets.join(',');
            },
            addNewTarget:function (){
                this.newTargets.push('');
            },
            shanchuTarget: function(tag) {
                const index = this.targets.indexOf(tag);
                if (index !== -1) {
                    this.targets.splice(index, 1);
                    this.allTargetStr = this.targets.join(',');
                    if (this.target === tag) {
                        this.target = this.targets.length > 0 ? this.targets[0] : '';
                    }
                }
            }
        }
    });
    $(function () {
        $(".btnArea a").on('click', function () {
            cvphp.submit($("form"), function (data) {
                if (data.status != 1) {
                    layer.msg(data.info);
                } else {
                    layer.alert('保存成功');
                }
            });
        });

    });

</script>
</html>