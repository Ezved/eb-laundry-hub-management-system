<?php

// app/Http/Controllers/FeedbackController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Feedback;

class FeedbackController extends Controller
{
    // Show the user feedback page (form + recent public feedback)
    public function create()
    {
        $visible = Feedback::where('is_visible', true)
            ->latest()
            ->take(12)
            ->get();

        $mine = Feedback::where('user_id', Auth::id())
            ->latest()
            ->take(10)
            ->get();

        return view('user.feedback.feedback', [
            'visibleFeedback' => $visible,
            'myFeedback'      => $mine,
        ]);
    }

    // Save a feedback; admin can manage in Admin → Feedbacks
    public function store(Request $request)
    {
        $data = $request->validate([
            'rating'  => ['required','integer','min:1','max:5'],
            'message' => ['required','string','max:1000'],
        ]);

        $user = Auth::user();

        Feedback::create([
            'user_id'    => $user?->id,
            'name'       => $user?->name,
            'rating'     => $data['rating'],
            'message'    => $data['message'],
            'is_visible' => true,  // set to false if you prefer admin moderation first
        ]);

        // go back to the user feedback page
        return redirect()->route('user.feedback')->with('success', 'Thanks for the feedback!');
    }

    // app/Http/Controllers/FeedbackController.php

public function destroy(\App\Models\Feedback $feedback)
{
    // only the owner can delete
    if (auth()->id() !== $feedback->user_id) {
        abort(403, 'You are not allowed to delete this feedback.');
    }

    $feedback->delete();

    return back()->with('success', 'Your feedback has been deleted.');
}

}

