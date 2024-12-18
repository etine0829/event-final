<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;

class UserRoutePageName extends Component
{
    /**
     * Create a new component instance.
     */public $title;

    public function __construct(string $routeName)
    {
        $this->setTitle($routeName);
    }

     protected function setTitle(string $routeName)
    {
        if (!Auth::check()) {
            $this->title = __('Event Tabulation Management System');
            return;
        }

         if (Auth::user()->hasRole('admin')) {
            
            $titles = [
                //admin route pages name
                'admin.dashboard' => __('Admin Dashboard'),
                //route page name for managing school
                'admin.event.index' => __('Admin - Manage Event'),
                'admin.category.index' => __('Admin - Manage Category'),
                'admin.criteria.index' => __('Admin - Manage Criteria'),
                'admin.group.index' => __('Admin - Manage Participant Group'),
                'admin.participant.index' => __('Admin - Manage Participant'),
                'admin.judge.index' => __('Admin - Add Judges'),
                'admin.user.index' => __('Admin - Add User'),
                'admin.result.index' => __('Admin - Result'),
                
                'admin.student.index' => __('Admin - Manage Student'),
                'admin.course.index' => __('Admin - Manage Courses'),
                'admin.attendance.employee_attendance' => __('Admin - Employee Attendance'),
                'admin.attendance.employeeSearch' => __('Admin - Employee Attendance Search'),
                'admin.attendance.student_attendance' => __('Admin - Student Attendance'),
                'admin.attendance.employee_attendance.payroll' =>  __('Admin - Manage Attendance Reports by Department'),
                'admin.attendance.employee_attendance.payroll.all' =>  __('Admin - Manage All Employees Attendance Reports'),
                'admin.attendance.employee_attendance.portal' => __('Employee Attendance Portal'),
                'admin.attendance.gracePeriodSet' => __('Admin - Attendance Grace Period'),
                'admin.attendance.holiday' => __('Admin - Add Holiday'),

                
            ];

            $this->title = $titles[$routeName] ?? __('Event Tabulation Management System');

        }
        else if (Auth::user()->hasRole('event_manager')) {

            $titles = [

                //admin route pages name
                'event_manager.dashboard' => __('Event Manager | Dashboard'),
                //route page name for managing school
                'event_manager.event.index' => __('Event Manager | Manage Event'),
                'event_manager.category.index' => __('Event Manager | Manage Category'),
                'event_manager.criteria.index' => __('Event Manager | Manage Criteria'),
                'event_manager.participant.index' => __('Event Manager | Manage Participant'),
                'event_manager.judge.index' => __('Event Manager | Add Judges'),

            ];

            $this->title = $titles[$routeName] ?? __('Event Tabulation Management System');

        }
        else if (Auth::user()->hasRole('judge')) {

            $titles = [

                //employee route pages name
                'judge.dashboard' => __('Judges | Dashboard'),
            ];

            $this->title = $titles[$routeName] ?? __('Event Tabulation Management System');

        }
         else {
            $this->title = __('Event Tabulation Management System');
        }

    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.user-route-page-name');
    }
}