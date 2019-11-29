<?php

namespace App\Admin\Controllers;

use App\Models\Teacher;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class TeachersController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Models\Teacher';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Teacher);

        $grid->column('id', __('编号'));
        $grid->column('user_id', __('用户编号'));
        $grid->column('name', __('姓名'));
        $grid->column('phone', __('电话'))->display(function() {
            return $this->makeVisible(['phone'])->phone;
        });
        $grid->column('title', __('讲师Title'));
        $grid->column('list_img_url', __('图片'))->lightbox();
        $grid->column('details_img_url', __('Details img url'))->hide();
        $grid->column('work_years', __('工作年限'))->editable();
        $grid->column('original_price', __('划线价'))->editable();
        $grid->column('price', __('咨询价'))->editable();
        $grid->column('background', __('行业背景'));
        $grid->column('good_at_filed', __('擅长领域'));
        $grid->column('page_view', __('访客'))->editable();
//        $grid->column('score', __('Score'));
//        $grid->column('number_reputation', __('Number reputation'));
        $grid->column('duration', __('咨询时长（分钟）'))->editable();
        $grid->column('consultants', __('咨询人数'));
        $grid->column('eval_num', __('评论数'));
        $grid->column('reputation', __('信誉分'))->hide();
        $grid->column('status', __('状态'))->select(Teacher::getStatus());
        $grid->column('created_at', __('创建时间'));
        $grid->column('updated_at', __('更新时间'))->hide();

        $grid->fixColumns(4, -1);

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Teacher::findOrFail($id));

        $show->field('id', __('编号'));
        $show->field('user_id', __('用户编号'));
        $show->field('name', __('姓名'));
        $show->field('phone', __('电话'));
        $show->field('title', __('讲师Title'));
        $show->field('list_img_url', __('图片'))->image('',100,100);
        $show->field('details_img_url', __('详情页配图'))->image('',100,100);
        $show->field('work_years', __('工作年限'));
        $show->field('original_price', __('划线价'));
        $show->field('price', __('咨询价'));
        $show->field('background', __('行业背景'));
        $show->field('good_at_filed', __('擅长领域'));
        $show->field('page_view', __('访客'));
//        $show->field('score', __('Score'));
//        $show->field('number_reputation', __('Number reputation'));
        $show->field('duration', __('咨询时长（分钟）'));
        $show->field('consultants', __('咨询人数'));
        $show->field('eval_num', __('评论数'));
        $show->field('reputation', __('信誉分'));
        $show->field('status', __('状态'))->using(Teacher::getStatus());
        $show->field('created_at', __('创建时间'));
        $show->field('updated_at', __('更新时间'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Teacher);

        $form->number('user_id', __('用户编号'))->disable();
        $form->text('name', __('讲师姓名'))->required();
        $form->mobile('phone', __('电话'))->required()->rules(function($form) {
            return 'unique:zx_teachers,phone,'.$form->model()->id.',id';
        });
        $form->editing(function ($form) {
            $form->model()->makeVisible('phone');
        });
        $form->text('title', __('讲师Title'))->required();
        $form->image('list_img_url', __('列表配图'))->required();
        $form->image('details_img_url', __('详情页配图'))->required();
        $form->text('work_years', __('工作年限'))->required();
        $form->number('original_price', __('划线价'))->required();
        $form->number('price', __('咨询价'))->required();
        $form->text('background', __('讲师背景'))->required();
        $form->text('good_at_filed', __('擅长领域'))->required();
        $form->number('page_view', __('访客量'))->required();
        $form->number('score', __('总评分'))->required();
        $form->number('number_reputation', __('评分数'))->required();
        $form->number('duration', __('咨询总时长'))->required();
        $form->number('consultants', __('咨询总人次'))->required();
        $form->number('eval_num', __('评论数'))->required();
        $form->number('reputation', __('信誉分'))->default(100)->required()->rules(function($form) {
            return 'required|between:0,100';
        });
        $form->select('status', __('讲师状态'))->options(Teacher::getStatus())->default(10)->required();

        return $form;
    }
}
