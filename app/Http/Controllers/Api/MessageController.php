<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMessage;

class MessageController extends Controller
{
    // Store message & send email
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);

        $message = Message::create($validated);

        // Send email to admin
        Mail::to('your-admin@gmail.com')->send(new ContactMessage($message));

        return response()->json([
            'status' => 'success',
            'message' => 'Your message has been sent!'
        ]);
    }

    // Fetch all messages (admin dashboard)
    public function index()
    {
        // Get 10 messages per page, newest first
    $messages = Message::orderBy('created_at', 'desc')->paginate(5);

    // Return as JSON
    return response()->json($messages);
    }

    // Mark message as read
    public function markAsRead($id)
    {
        $message = Message::findOrFail($id);
        $message->update(['is_read' => true]);
        return response()->json(['status'=>'success']);
    }
    public function unreadCount()
    {
        return Message::where('is_read', false)->count();
    }
}
