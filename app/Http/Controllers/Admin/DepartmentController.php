<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Department\CheckDepartmentAttach;
use App\Models\Department;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class DepartmentController extends Controller
{
    public $anchor = '#active-departments';

    public function index()
    {
        $activeDepartments = Department::with('company')->orderBy('company_id')->paginate(20);
        $deletedDepartmetns = Department::with('company')->onlyTrashed()->orderBy('company_id')->paginate(20);
        $allCompanies = DB::table('companies')->get();

        return view('pages.admin.departments.index', compact('activeDepartments', 'deletedDepartmetns', 'allCompanies'));
    }

    public function update(CheckDepartmentAttach $request, Department $department)
    {
        try {
            $department->update([
                'company_id' => (int)$request->company_id
            ]);
            $successMessage = !is_null($request->company_id) ? __('Company attached successfully.') : __('Company detached successfully.');
            return back()->withSuccess($successMessage);
        } catch (\Throwable $exception) {
            return back()->withError(__('Something went wrong.'));
        }
    }

    public function delete(Department $department)
    {
        try {
            $department->delete();
            return back()->withSuccess(__('Department delete successfully.'));
        } catch (\Throwable $exception) {
            return back()->withError(__('Something went wrong.'));
        }

    }

    public function activate($id)
    {
        try {
            Department::where('id', $id)->restore();
            $this->anchor = '#inactive-departments';

            return redirect()->route('admin.department.index', $this->anchor)->withSuccess(__('Department activate successfully.'));
        } catch (\Throwable $exception) {
            return Redirect::to(URL::previous() . $this->anchor)
                ->withError(__('Something went wrong.'));
        }

    }
}
