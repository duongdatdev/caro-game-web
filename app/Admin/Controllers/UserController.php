<?php

namespace App\Admin\Controllers;

use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends AdminController
{
    protected $title = 'User Management';

    protected function grid()
    {
        $grid = new Grid(new User());

        $grid->column('id', 'ID');
        $grid->column('name', 'Name');
        $grid->column('email', 'Email');
        $grid->column('created_at', 'Registered');
        $grid->column('email_verified_at', 'Email Status')->display(function ($value) {
            if (is_null($value)) {
                return '<span class="badge" style="background-color: #ff4d4f">Unverified</span>';
            }
            return '<span class="badge" style="background-color: #52c41a">Verified</span>';
        });

        // Customize grid actions
        $grid->actions(function ($actions) {
            // Remove the view action
            $actions->disableView();

            // Keep edit and delete actions
            $actions->disableDelete(false);
            $actions->disableEdit(false);
        });

        return $grid;
    }

    protected function form()
    {
        $form = new Form(new User());

        // Basic user information
        $form->text('name', 'Name')
            ->rules('required|max:255')
            ->help('Enter full name');

        $form->email('email', 'Email')
            ->rules('required|email|max:255')
            ->help('Must be a valid email address');

        // Password field with proper validation
        $form->password('password', 'Password')
            ->rules('nullable|min:8|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9]).*$/')
            ->help('Minimum 8 characters. Leave empty to keep current password. Must contain letters and numbers.');

        $form->saving(function (Form $form) {
            // Handle password update
            if ($form->password) {
                $form->password = Hash::make($form->password);
            } else {
                $form->deleteInput('password');
            }

            // If email is changed, require reverification
            if ($form->model()->isDirty('email')) {
                $form->email_verified_at = null;
            }
        });
        return $form;
    }
}
