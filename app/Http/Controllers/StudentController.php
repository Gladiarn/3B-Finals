<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::query();

        if ($request->has('year')) {
            $query->where('year', $request->year);
        }
        if ($request->has('course')) {
            $query->where('course', $request->course);
        }
        if ($request->has('section')) {
            $query->where('section', $request->section);
        }

        if ($request->has('sort')) {
            $sort = $request->sort;
            $direction = $request->has('direction') ? $request->direction : 'asc';
            $query->orderBy($sort, $direction);
        }

        $limit = $request->has('limit') ? $request->limit : 20;
        $offset = $request->has('offset') ? $request->offset : 0;

        $students = $query->offset($offset)->limit($limit)->get();

        return response()->json([
            'metadata' => [
                'count' => $students->count(),
                'search' => null,
                'limit' => $limit,
                'offset' => $offset,
                'fields' => []
            ],
            'students' => $students
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'birthdate' => 'required|date',
            'sex' => 'required|in:MALE,FEMALE',
            'address' => 'required|string',
            'year' => 'required|integer',
            'course' => 'required|string',
            'section' => 'required|string',
        ]);

        $student = Student::create($validated);
        return response()->json($student, 201);
    }

    public function show($id)
    {
        $student = Student::findOrFail($id);
        return response()->json($student);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'firstname' => 'sometimes|required|string|max:255',
            'lastname' => 'sometimes|required|string|max:255',
            'birthdate' => 'sometimes|required|date',
            'sex' => 'sometimes|required|in:MALE,FEMALE',
            'address' => 'sometimes|required|string',
            'year' => 'sometimes|required|integer',
            'course' => 'sometimes|required|string',
            'section' => 'sometimes|required|string',
        ]);

        $student = Student::findOrFail($id);
        $student->update($validated);
        return response()->json($student);
    }
}
