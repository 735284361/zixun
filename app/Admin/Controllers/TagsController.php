<?php

namespace App\Admin\Controllers;

use App\Models\Tag;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class TagsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '标签';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Tag);

        $grid->column('id', __('编号'));
        $grid->column('tag', __('标签'))->editable();
        $grid->column('status', __('状态'))->select(Tag::getStatus());
        $grid->column('created_at', __('创建时间'));
        $grid->column('updated_at', __('更新时间'));

        $grid->quickCreate(function (Grid\Tools\QuickCreate $create) {
            $create->text('tag','标签');
            $create->select('status','标签')->default(Tag::STATUS_ENABLE)->options(Tag::getStatus());
        });

        $grid->disableActions();
        $grid->disableCreateButton();
        $grid->filter(function($filter) {
            $filter->disableIdFilter();
            $filter->like('tag','标签');
        });

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
        $show = new Show(Tag::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('tag', __('Tag'));
        $show->field('status', __('Status'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Tag);

        $form->text('tag', __('Tag'));
        $form->switch('status', __('Status'))->default(10);

        return $form;
    }
}
