<?php

namespace App\Http\Controllers;

use App\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Team::all();
        return view('backend.team.index', compact('teams'));
    }


    public function create()
    {
        return view('backend.team.create');
    }


    public function store(Request $request)
    {
           $data = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'original_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'hover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'twitter_url' => 'nullable|url',
            'linkedin_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
        ]);

            $originalImagePath = null;
            if ($request->hasFile('original_image')) {
                $originalImage = $request->file('original_image');
                $originalImageName = time() . '_original.' . $originalImage->getClientOriginalExtension();
                $originalImagePath = $originalImage->storeAs('uploads/team', $originalImageName, 'public');
            }

            $hoverImagePath = null;
            if ($request->hasFile('hover_image')) {
                $hoverImage = $request->file('hover_image');
                $hoverImageName = time() . '_hover.' . $hoverImage->getClientOriginalExtension();
                $hoverImagePath = $hoverImage->storeAs('uploads/team', $hoverImageName, 'public');
            }

        Team::create([
            'name' => $data['name'],
            'position' => $data['position'],
            'original_image' => 'storage/' . $originalImagePath,
            'hover_image' => $hoverImagePath ? 'storage/' . $hoverImagePath : null,
            'twitter_url' => $data['twitter_url'] ?? null,
            'linkedin_url' => $data['linkedin_url'] ?? null,
            'instagram_url' => $data['instagram_url'] ?? null,
        ]);

        
        return response()->json([
            'message' => 'Team member created successfully.',
            'data' => $data
        ], 201);
    }

    // redirect()->route('team.index')->with('success', 'Team member created successfully.'


    public function edit($id)
    {
        $team = Team::findOrFail($id);
        return view('team.edit', compact('team'));
    }


    // public function update(Request $request, $id)
    // {
    //     $team = Team::findOrFail($id);

    //     $data = $request->validate([
    //         'image' => 'nullable|image|max:2048',
    //         'name' => 'required|string|max:255',
    //         'role' => 'required|string|max:255',
    //         'bio' => 'nullable|string',
    //         'email_link' => 'nullable|url',
    //         'portfolio_link' => 'nullable|url',
    //         'twitter_link' => 'nullable|url',
    //     ]);

    //     if ($request->hasFile('image')) {
    //         if ($team->image) {
    //             \storage_path()::disk('public')->delete($team->image);
    //         }
    //         $data['image'] = $request->file('image')->store('team_images', 'public');
    //     }


    //     $team->update($data);

    //     return redirect()->route('team.index')->with('success', 'Team member updated successfully.');
    // }


    public function destroy($id)
    {
        $team = Team::findOrFail($id);
        
        $team->delete();

        return redirect()->route('team.index')->with('success', 'Team member deleted successfully.');
    }
}
