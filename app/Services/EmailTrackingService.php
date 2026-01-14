<?php

namespace App\Services;

use App\Models\EmailThread;
use App\Models\EmailMessage;
use App\Models\EmailAttachment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EmailTrackingService
{
    /**
     * Send an email with tracking
     */
    public function sendEmail(array $data): EmailMessage
    {
        $threadId = $data['thread_id'] ?? null;
        
        // Create or get thread
        if (!$threadId) {
            $thread = EmailThread::create([
                'subject' => $data['subject'],
                'participants' => json_encode(array_merge([$data['to']], $data['cc'] ?? [], $data['bcc'] ?? [])),
                'user_id' => Auth::id(),
            ]);
            $threadId = $thread->id;
        } else {
            $thread = EmailThread::findOrFail($threadId);
        }
        
        // Create email message
        $message = EmailMessage::create([
            'thread_id' => $threadId,
            'from_email' => $data['from'] ?? Auth::user()->email,
            'to_email' => $data['to'],
            'cc' => isset($data['cc']) ? json_encode($data['cc']) : null,
            'bcc' => isset($data['bcc']) ? json_encode($data['bcc']) : null,
            'subject' => $data['subject'],
            'body' => $data['body'],
            'direction' => 'sent',
            'sent_at' => now(),
            'relatable_type' => $data['relatable_type'] ?? null,
            'relatable_id' => $data['relatable_id'] ?? null,
        ]);
        
        // Handle attachments
        if (isset($data['attachments'])) {
            foreach ($data['attachments'] as $attachment) {
                EmailAttachment::create([
                    'email_message_id' => $message->id,
                    'file_name' => $attachment['name'],
                    'file_path' => $attachment['path'],
                    'file_size' => $attachment['size'],
                    'mime_type' => $attachment['mime_type'],
                ]);
            }
        }
        
        // Send actual email (placeholder - would use Mail facade)
        // Mail::to($data['to'])->send(new GenericMail($data));
        
        return $message->fresh(['thread', 'attachments']);
    }

    /**
     * Track received email
     */
    public function receiveEmail(array $data): EmailMessage
    {
        // Find or create thread based on subject or in-reply-to header
        $thread = $this->findOrCreateThread($data);
        
        $message = EmailMessage::create([
            'thread_id' => $thread->id,
            'from_email' => $data['from'],
            'to_email' => $data['to'],
            'cc' => isset($data['cc']) ? json_encode($data['cc']) : null,
            'subject' => $data['subject'],
            'body' => $data['body'],
            'direction' => 'received',
            'sent_at' => $data['sent_at'] ?? now(),
        ]);
        
        // Handle attachments
        if (isset($data['attachments'])) {
            foreach ($data['attachments'] as $attachment) {
                EmailAttachment::create([
                    'email_message_id' => $message->id,
                    'file_name' => $attachment['name'],
                    'file_path' => $attachment['path'],
                    'file_size' => $attachment['size'],
                    'mime_type' => $attachment['mime_type'],
                ]);
            }
        }
        
        return $message->fresh(['thread', 'attachments']);
    }

    /**
     * Mark email as read
     */
    public function markAsRead(EmailMessage $message): EmailMessage
    {
        return $message->markAsRead();
    }

    /**
     * Mark email as unread
     */
    public function markAsUnread(EmailMessage $message): EmailMessage
    {
        $message->update(['read_at' => null]);
        return $message;
    }

    /**
     * Toggle star on email
     */
    public function toggleStar(EmailMessage $message): EmailMessage
    {
        $message->update(['is_starred' => !$message->is_starred]);
        return $message;
    }

    /**
     * Archive email thread
     */
    public function archiveThread(EmailThread $thread): EmailThread
    {
        return $thread->archive();
    }

    /**
     * Unarchive email thread
     */
    public function unarchiveThread(EmailThread $thread): EmailThread
    {
        return $thread->unarchive();
    }

    /**
     * Create draft email
     */
    public function createDraft(array $data): EmailMessage
    {
        $threadId = $data['thread_id'] ?? null;
        
        // Create thread if not exists
        if (!$threadId) {
            $thread = EmailThread::create([
                'subject' => $data['subject'] ?? 'Draft',
                'participants' => json_encode([$data['to'] ?? '']),
                'user_id' => Auth::id(),
            ]);
            $threadId = $thread->id;
        }
        
        return EmailMessage::create([
            'thread_id' => $threadId,
            'from_email' => Auth::user()->email,
            'to_email' => $data['to'] ?? '',
            'cc' => isset($data['cc']) ? json_encode($data['cc']) : null,
            'bcc' => isset($data['bcc']) ? json_encode($data['bcc']) : null,
            'subject' => $data['subject'] ?? '',
            'body' => $data['body'] ?? '',
            'direction' => 'sent',
            'is_draft' => true,
        ]);
    }

    /**
     * Search emails
     */
    public function searchEmails(string $query, array $filters = [])
    {
        $searchQuery = EmailMessage::query()
            ->with(['thread', 'attachments'])
            ->where(function ($q) use ($query) {
                $q->where('subject', 'like', "%{$query}%")
                  ->orWhere('body', 'like', "%{$query}%")
                  ->orWhere('from_email', 'like', "%{$query}%")
                  ->orWhere('to_email', 'like', "%{$query}%");
            });
        
        // Apply filters
        if (isset($filters['direction'])) {
            $searchQuery->where('direction', $filters['direction']);
        }
        
        if (isset($filters['is_read'])) {
            if ($filters['is_read']) {
                $searchQuery->whereNotNull('read_at');
            } else {
                $searchQuery->whereNull('read_at');
            }
        }
        
        if (isset($filters['is_starred'])) {
            $searchQuery->where('is_starred', $filters['is_starred']);
        }
        
        if (isset($filters['is_archived'])) {
            $searchQuery->whereHas('thread', function ($q) use ($filters) {
                $q->where('is_archived', $filters['is_archived']);
            });
        }
        
        return $searchQuery->latest('sent_at')->paginate(20);
    }

    /**
     * Get inbox view
     */
    public function getInbox(array $filters = [])
    {
        $query = EmailThread::with(['latestMessage', 'messages'])
            ->where('user_id', Auth::id())
            ->active();
        
        // Apply filters
        if (isset($filters['unread']) && $filters['unread']) {
            $query->unread();
        }
        
        if (isset($filters['starred']) && $filters['starred']) {
            $query->starred();
        }
        
        return $query->latest('updated_at')->paginate(20);
    }

    /**
     * Get email thread with messages
     */
    public function getThread(int $threadId): EmailThread
    {
        return EmailThread::with(['messages.attachments'])
            ->findOrFail($threadId);
    }

    /**
     * Get unread count
     */
    public function getUnreadCount(): int
    {
        return EmailMessage::where('direction', 'received')
            ->whereNull('read_at')
            ->whereHas('thread', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->count();
    }

    /**
     * Find or create thread
     */
    private function findOrCreateThread(array $data): EmailThread
    {
        // Try to find existing thread by subject
        $thread = EmailThread::where('subject', $data['subject'])
            ->where('user_id', Auth::id())
            ->first();
        
        if (!$thread) {
            $thread = EmailThread::create([
                'subject' => $data['subject'],
                'participants' => json_encode([$data['from'], $data['to']]),
                'user_id' => Auth::id(),
            ]);
        }
        
        return $thread;
    }
}
