<?php

namespace App\View\Components;

use App\Models\Student;
use Illuminate\View\Component;

class StudentDashboardFilter extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $student=Student::where('userId',auth()->user()->id)->latest()->first();

        return view('components.student-dashboard-filter',['student'=>$student]);
    }
}
