<?php

namespace App\Admin\Controllers;

use App\Models\Ride;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class RideController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Ride';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Ride());

        $grid->column('id', __('Id'));
        $grid->column('uuid', __('Uuid'));
        $grid->column('host_user_uuid', __('Host user uuid'));
        $grid->column('meeting_places_uuid', __('Meeting places uuid'));
        $grid->column('ride_routes_uuid', __('Ride routes uuid'));
        $grid->column('name', __('Name'));
        $grid->column('time_appoint', __('Time appoint'));
        $grid->column('intensity', __('Intensity'));
        $grid->column('num_of_laps', __('Num of laps'));
        $grid->column('comment', __('Comment'));
        $grid->column('publish_status', __('Publish status'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

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
        $show = new Show(Ride::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('uuid', __('Uuid'));
        $show->field('host_user_uuid', __('Host user uuid'));
        $show->field('meeting_places_uuid', __('Meeting places uuid'));
        $show->field('ride_routes_uuid', __('Ride routes uuid'));
        $show->field('name', __('Name'));
        $show->field('time_appoint', __('Time appoint'));
        $show->field('intensity', __('Intensity'));
        $show->field('num_of_laps', __('Num of laps'));
        $show->field('comment', __('Comment'));
        $show->field('publish_status', __('Publish status'));
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
        $form = new Form(new Ride());

        $form->text('uuid', __('Uuid'));
        $form->text('host_user_uuid', __('Host user uuid'));
        $form->text('meeting_places_uuid', __('Meeting places uuid'));
        $form->text('ride_routes_uuid', __('Ride routes uuid'));
        $form->text('name', __('Name'));
        $form->datetime('time_appoint', __('Time appoint'))->default(date('Y-m-d H:i:s'));
        $form->switch('intensity', __('Intensity'));
        $form->switch('num_of_laps', __('Num of laps'));
        $form->text('comment', __('Comment'));
        $form->switch('publish_status', __('Publish status'));

        return $form;
    }
}
