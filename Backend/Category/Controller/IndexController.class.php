<?php
namespace Category\Controller;

use Think\Controller;

class IndexController extends Controller
{
    public function index()
    {
        $model = M('Category'); // return Object
        // Mysql CONCAT（）函数用于将多个字符串连接成一个字符串
        $result = $model->field("*,concat(path,',',id) as paths")->order('path')->select();
        foreach ($result as $key => $v) {
            $result[$key]['name'] = str_repeat('&nbsp;&nbsp;&nbsp;', $v['level']) . $v['name'];
        }
        $this->ajaxReturn();
        $this->categorys = $result;
        $this->display();
    }

    /**
     * 添加一个新分类
     */
    public function addCategory()
    {
        $data['pid'] = I('post.pid');
        $data['name'] = I('post.name');
        // 实例化一个数据表
        $model = M('Category');
        // 判断是否是顶级分类
        if (!empty($data['name']) && $data['pid'] != 0) {
            $path = $model->field('path')->find($data['pid']);
            //思路,先添加后修改
            $data['path'] = $path['path'];
            // substr_count() 函数计算子串在字符串中出现的次数
            $data['level'] = substr_count($path['path'], ',');
            // 在TP中 $data['pid'] = I('post.pid') 会返回当前插入的id
            $resultId = $model->add($data); // return add id
            $update['id'] = $resultId;
            $update['path'] = $path['path'] . ',' . $resultId;
            $update['level'] = $data['level'] + 1;
            $updateResult = $model->save($update);
            if (!$updateResult) {
                return $this->error('添加一个新分类失败');
            } else {
                return $this->success('添加一个新分类成功', U('Index/index'));
            }
        } elseif (!empty($data['name']) && $data['pid'] == 0) {
            $data['path'] = $data['pid'];
            $data['level'] = 1;
            // 在TP中 $data['pid'] = I('post.pid') 会返回当前插入的id
            $resultId = $model->add($data); // return add id
            $update['id'] = $resultId;
            $update['path'] = $data['path'] . ',' . $resultId;
            $updateResult = $model->save($update);
            if (!$updateResult) {
                return $this->error('添加一个新分类失败');
            } else {
                return $this->success('添加一个新分类成功', U('Index/index'));
            }
        } else {
            return $this->error('添加一个新分类失败');
        }
    }

    public function test(){
        echo 'category test';
    }
}