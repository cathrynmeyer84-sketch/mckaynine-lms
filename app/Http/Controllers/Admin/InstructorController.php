<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Instructor, User};
use Illuminate\Http\Request;

class InstructorController extends Controller
{
    public function index() {
        $instructors = Instructor::with(['user', 'classes'])->get();
        return view('admin.instructors.index', compact('instructors'));
    }
    public function create() { return view('admin.instructors.create'); }
    public function store(Request $request) {
        $request->validate(['first_name'=>'required','last_name'=>'required','email'=>'required|email|unique:users,email']);
        $user = User::create(['name' => $request->first_name . ' ' . $request->last_name, 'email' => $request->email, 'password' => bcrypt(str()->random(16)), 'is_instructor' => true, 'is_handler' => false]);
        Instructor::create(['user_id'=>$user->id,'first_name'=>$request->first_name,'last_name'=>$request->last_name,'email'=>$request->email,'phone'=>$request->phone,'bio'=>$request->bio]);
        return redirect()->route('admin.instructors.index')->with('success','Instructor created. An invite email should be sent.');
    }
    public function show(Instructor $instructor) {
        $instructor->load(['classes.dates','user']);
        return view('admin.instructors.show', compact('instructor'));
    }
    public function edit(Instructor $instructor) { return view('admin.instructors.edit', compact('instructor')); }
    public function update(Request $request, Instructor $instructor) {
        $request->validate([
            'payment_frequency' => 'nullable|in:termly,monthly',
        ]);
        $instructor->update(array_merge(
            $request->only(['first_name','last_name','phone','bio','is_active']),
            ['payment_frequency' => $request->input('payment_frequency', 'termly')]
        ));
        return redirect()->route('admin.instructors.show', $instructor)->with('success','Instructor updated.');
    }
}
