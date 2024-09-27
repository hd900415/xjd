<?php
/**
 * Created by PhpStorm.
 * User: qiujunlin
 * Date: 2019/5/26
 * Time: 6:47 PM
 */

/**
 * 类说明：
 * 类名称：ProductAction
 * 类中文名：用户管理控制器
 * 作者：
 * 类型：Controller 控制器 Model 数据模型 Util 工具类 ClassLibrary 公共类库
 * 继承关系：
 **/
class ProductAction extends CommonAction
{

    // 商品列表页
    public function index()
    {
        $productModel = D('Product');
        $where = array();
        import("ORG.Util.Page");
        $count = $productModel->where($where)->count();
        $Page = new Page($count, C("PAGE_NUM_ONE"));
        $Page->setConfig("header", "条记录,每页显示" . C("PAGE_NUM_ONE") . "条");
        $Page->setConfig("prev", "<");
        $Page->setConfig("next", ">");
        $Page->setConfig("theme", C("PAGE_STYLE"));
        $show = $Page->show();
        $list = $productModel->where($where)->order("id Desc")->limit($Page->firstRow . "," . $Page->listRows)->select();
        $this->assign("list", $list);
        $this->assign("page", $show);
        $this->display();
    }


    public function edit()
    {
        $id = I('get.id');
        if($id){
            $product = D('Product')->where(['id'=>$id])->find();
            $this->assign("product",$product);
        }
        $this->assign("id",$id);
        $this->display();
    }

    // 新增商品页与编辑
    public function save()
    {
        if(empty($_POST['name'])){
            $this->error("产品名称不得为空");
        }
        $productModel = D("product");
        if($_POST['id']){
            $id = $_POST['id'];
            unset($_POST['id']);
            // 编辑
            $res = $productModel->where(['id' => $id])->save($_POST);
            if($res){
                $this->success("保存成功");
            }else{
                $this->error("保存失败");
            }
        }else{
            // 新增
            unset($_POST['id']);
            $_POST['addtime'] = time();
            $res = $productModel->add($_POST);
            if($res){
                $this->success("新增成功");
            }else{
                $this->error("新增失败");
            }
        }
    }

    public function delete()
    {
        $id = I("id");
        if (!$id) {
            $this->error("参数有误");
        }
        $productModel = D("Product");
        $info = $productModel->where(array("id" => $id))->find();
        if (!$info) {
            $this->error("商品不存在！");
        }
        $result = $productModel->where(array("id" => $id))->delete();
        if (!$result) {
            $this->error("数据操作失败");
        }
        $this->success("删除成功");
    }


}