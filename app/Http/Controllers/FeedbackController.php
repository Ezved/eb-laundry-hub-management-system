<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Feedback;

class FeedbackController extends Controller
{
    // Show the user feedback page
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

        $hasFeedback = $mine->isNotEmpty();

        return view('user.feedback.feedback', [
            'visibleFeedback' => $visible,
            'myFeedback'      => $mine,
            'hasFeedback'     => $hasFeedback,
        ]);
    }

    // Save only one feedback per user
    public function store(Request $request)
    {
        $user = Auth::user();

        $existingFeedback = Feedback::where('user_id', $user->id)->first();

        if ($existingFeedback) {
            return redirect()
                ->route('user.feedback')
                ->with('fail', 'You can only submit feedback once.');
        }

        $data = $request->validate([
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'message' => ['required', 'string', 'max:1000'],
        ]);

        Feedback::create([
            'user_id'    => $user->id,
            'name'       => $user->name,
            'rating'     => $data['rating'],
            'message'    => $data['message'],
            'is_visible' => true,
        ]);

        return redirect()
            ->route('user.feedback')
            ->with('success', 'Thanks for the feedback!');
    }

    public function destroy(Feedback $feedback)
    {
        if (auth()->id() !== $feedback->user_id) {
            abort(403, 'You are not allowed to delete this feedback.');
        }

        $feedback->delete();

        return back()->with('success', 'Your feedback has been deleted.');
    }
}