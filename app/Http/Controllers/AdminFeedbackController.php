<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;

class AdminFeedbackController extends Controller
{
    public function index()
{
    $feedback = \App\Models\Feedback::with('user')
        ->latest()
        ->paginate(20);

    return view('admin.feedback.feedback', compact('feedback'));
}

    public function hide(Feedback $feedback)
    {
        $feedback->update(['is_visible' => false]);
        return back()->with('success', 'Feedback hidden.');
    }

    public function unhide(Feedback $feedback)
    {
        $feedback->update(['is_visible' => true]);
        return back()->with('success', 'Feedback visible.');
    }

    public function destroy(Feedback $feedback)
    {
        $feedback->delete();
        return back()->with('success', 'Feedback deleted.');
    }
}
