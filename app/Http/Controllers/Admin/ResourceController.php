<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Resource, ClassType};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ResourceController extends Controller
{
    private array $categories = [
        'General',
        'About Your Course',
        'Our Methods',
        'About Your Puppy',
        'Puppy & Dog Problems',
        'Obedience Pointers',
        'Canine Behaviour',
        'Canine Care',
        'Theory',
        'Nutrition',
        'Health',
        'Training Tips',
        'Socialisation',
        'Behaviour',
    ];

    public function index() {
        $resources = Resource::with('createdBy')->orderBy('sort_order')->get();
        return view('admin.resources.index', compact('resources'));
    }

    public function create() {
        $categories = $this->categories;
        $classTypes = ClassType::orderBy('name')->get();
        return view('admin.resources.create', compact('categories', 'classTypes'));
    }

    public function store(Request $request) {
        $request->validate([
            'title'      => 'required',
            'image_file' => 'nullable|image|max:5120',
        ]);

        $data = $request->only(['title','content','external_url','category','class_categories','is_published','sort_order']);
        $data['created_by'] = auth()->id();
        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('image_file')) {
            $data['image_path'] = $request->file('image_file')->store('resources/images', 'public');
        }

        Resource::create($data);
        return redirect()->route('admin.resources.index')->with('success', 'Resource created.');
    }

    public function edit(Resource $resource) {
        $categories = $this->categories;
        $classTypes = ClassType::orderBy('name')->get();
        return view('admin.resources.edit', compact('resource', 'categories', 'classTypes'));
    }

    public function update(Request $request, Resource $resource) {
        $request->validate([
            'title'      => 'required',
            'image_file' => 'nullable|image|max:5120',
        ]);

        $data = $request->only(['title','content','external_url','category','class_categories','sort_order']);
        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('image_file')) {
            // Delete old image if present
            if ($resource->image_path) {
                Storage::disk('public')->delete($resource->image_path);
            }
            $data['image_path'] = $request->file('image_file')->store('resources/images', 'public');
        }

        if ($request->boolean('remove_image') && $resource->image_path) {
            Storage::disk('public')->delete($resource->image_path);
            $data['image_path'] = null;
        }

        $resource->update($data);
        return redirect()->route('admin.resources.index')->with('success', 'Resource updated.');
    }

    public function toggle(Resource $resource) {
        $resource->update(['is_published' => !$resource->is_published]);
        return back()->with('success', 'Resource visibility updated.');
    }
}
