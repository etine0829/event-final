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
                'admin.staff.index' => __('Admin - Manage Admin Staff'),
                'admin.employee.index' => __('Admin - Manage Employee'),
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

            $this->title = $titles[$routeName] ?? __('University of Bohol Attendance System');

        }
        else if (Auth::user()->hasRole('admin_staff')) {

            $titles = [

                'staff.dashboard' => __('Staff | Dashboard'),
                'staff.school.index' => __('Staff | Manage School Year'),
                'staff.department.index' => __('Staff | Manage Department'),
                'staff.workinghour.index' => __('Staff | Manage Dept Working Hour'),
                'staff.course.index' => __('Staff | Manage Courses'),
                'staff.employee.index' => __('Staff | Manage Employee'),
                'staff.student.index' => __('Staff | Manage Student'),

            ];

            $this->title = $titles[$routeName] ?? __('University of Bohol Attendance System');

        }
        else if (Auth::user()->hasRole('employee')) {

            $titles = [

                //employee route pages name
                'hr.dashboard' => __('Human Resource | Dashboard'),
            ];

            $this->title = $titles[$routeName] ?? __('University of Bohol Attendance System');

        }
         else {
            $this->title = __('University of Bohol Attendance System');
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