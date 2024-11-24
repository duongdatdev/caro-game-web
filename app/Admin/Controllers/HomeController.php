<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use OpenAdmin\Admin\Admin;
use OpenAdmin\Admin\Controllers\Dashboard;
use OpenAdmin\Admin\Layout\Column;
use OpenAdmin\Admin\Layout\Content;
use OpenAdmin\Admin\Layout\Row;

use App\Models\User;
use App\Models\Room;

class HomeController extends Controller
{
    public function index(Content $content)
    {

        $totalPlayers = User::count();
        $totalRooms = Room::count();


        return $content
            ->css_file(Admin::asset("open-admin/css/pages/dashboard.css"))
            ->title('Dashboard')
            ->description('This is a dashboard page for admin ')
            // ->row(Dashboard::title())
            ->row(function (Row $row) use ($totalPlayers, $totalRooms) {


                $row->column(4, function (Column $column) use ($totalPlayers) {
                    $column->append(view('admin.dashboard.stats-card', [
                        'title' => 'Total Players',
                        'value' => number_format($totalPlayers),
                        'icon' => 'users',
                        'color' => 'primary',
                        'link' => '/admin/users',
                    ]));
                });
                
                $row->column(4, function (Column $column) use ($totalRooms) {
                    $column->append(view('admin.dashboard.stats-card', [
                        'title' => 'Total Rooms',
                        'value' => number_format($totalRooms),
                        'icon' => 'gamepad',
                        'color' => 'success',
                        'link' => '/admin/rooms',
                    ]));
                });

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::environment());
                });
                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::dependencies());
                });
            });
    }
}
