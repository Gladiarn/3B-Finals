<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index($studentId)
    {
        $query = Subject::where('student_id', $studentId);

        if (request()->has('remarks')) {
            $query->where('remarks', request()->remarks);
        }

        if (request()->has('sort')) {
            $sort = request()->sort;
            $direction = request()->has('direction') ? request()->direction : 'asc';
            $query->orderBy($sort, $direction);
        }

        $limit = request()->has('limit') ? request()->limit : 10;
        $subjects = $query->paginate($limit);

        return response()->json([
            'metadata' => [
                'count' => $subjects->total(),
                'search' => null,
                'limit' => $limit,
                'offset' => $subjects->currentPage(),
                'fields' => []
            ],
            'subjects' => $subjects->items()
        ]);
    }

    public function store(Request $request, $studentId)
{
    $validated = $request->validate([
        'subject_code' => 'required|string|max:255',
        'name' => 'required|string|max:255',
        'description' => 'required|string',
        'instructor' => 'required|string|max:255',
        'schedule' => 'required|string',
        'grades.prelims' => 'required|numeric|between:0,5',
        'grades.midterms' => 'required|numeric|between:0,5',
        'grades.pre_finals' => 'required|numeric|between:0,5',
        'grades.finals' => 'required|numeric|between:0,5',
        'date_taken' => 'required|date',
    ]);

    // Calculate average_grade
    $prelims = $validated['grades']['prelims'];
    $midterms = $validated['grades']['midterms'];
    $preFinals = $validated['grades']['pre_finals'];
    $finals = $validated['grades']['finals'];
    $averageGrade = ($prelims + $midterms + $preFinals + $finals) / 4;

    // Determine remarks
    $remarks = $averageGrade >= 3.0 ? 'FAILED' : 'PASSED';

    // Create the subject record
    $subject = new Subject([
        'subject_code' => $validated['subject_code'],
        'name' => $validated['name'],
        'description' => $validated['description'],
        'instructor' => $validated['instructor'],
        'schedule' => $validated['schedule'],
        'grades' => $validated['grades'],
        'average_grade' => $averageGrade,
        'remarks' => $remarks,
        'date_taken' => $validated['date_taken'],
    ]);

    $subject->student_id = $studentId;
    $subject->save();

    return response()->json($subject, 201);
}

    public function show($studentId, $subjectId)
    {
        $subject = Subject::where('student_id', $studentId)->findOrFail($subjectId);
        return response()->json($subject);
    }

    public function update(Request $request, $studentId, $subjectId)
{
    $validated = $request->validate([
        'subject_code' => 'sometimes|required|string|max:255',
        'name' => 'sometimes|required|string|max:255',
        'description' => 'sometimes|required|string',
        'instructor' => 'sometimes|required|string|max:255',
        'schedule' => 'sometimes|required|string',
        'grades.prelims' => 'sometimes|required|numeric|between:0,5',
        'grades.midterms' => 'sometimes|required|numeric|between:0,5',
        'grades.pre_finals' => 'sometimes|required|numeric|between:0,5',
        'grades.finals' => 'sometimes|required|numeric|between:0,5',
        'date_taken' => 'sometimes|required|date',
    ]);

    // Find the subject
    $subject = Subject::where('student_id', $studentId)->findOrFail($subjectId);

    // Update subject fields
    $subject->fill([
        'subject_code' => $validated['subject_code'] ?? $subject->subject_code,
        'name' => $validated['name'] ?? $subject->name,
        'description' => $validated['description'] ?? $subject->description,
        'instructor' => $validated['instructor'] ?? $subject->instructor,
        'schedule' => $validated['schedule'] ?? $subject->schedule,
        'grades' => [
            'prelims' => $validated['grades']['prelims'] ?? $subject->grades['prelims'],
            'midterms' => $validated['grades']['midterms'] ?? $subject->grades['midterms'],
            'pre_finals' => $validated['grades']['pre_finals'] ?? $subject->grades['pre_finals'],
            'finals' => $validated['grades']['finals'] ?? $subject->grades['finals'],
        ],
        'date_taken' => $validated['date_taken'] ?? $subject->date_taken,
    ]);

    // Calculate average_grade
    $prelims = $subject->grades['prelims'];
    $midterms = $subject->grades['midterms'];
    $preFinals = $subject->grades['pre_finals'];
    $finals = $subject->grades['finals'];
    $averageGrade = ($prelims + $midterms + $preFinals + $finals) / 4;

    // Determine remarks
    $remarks = $averageGrade >= 3.0 ? 'FAILED' : 'PASSED';

    // Update calculated fields
    $subject->average_grade = $averageGrade;
    $subject->remarks = $remarks;

    // Save updated subject
    $subject->save();

    return response()->json($subject);
}
}
