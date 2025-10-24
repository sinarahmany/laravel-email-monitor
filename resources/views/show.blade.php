@extends('email-monitor::layouts.app')

@section('title', 'Email Details')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-3xl font-bold tracking-tight text-foreground">Email Details</h2>
            <p class="text-muted-foreground">View detailed information about this email</p>
        </div>
        <div>
            <a href="{{ route('email-monitor.index') }}" 
               class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Email Information Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Basic Information -->
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="p-6">
                <h3 class="text-lg font-medium text-foreground mb-4 flex items-center">
                    <i data-lucide="info" class="w-5 h-5 mr-2"></i>
                    Basic Information
                </h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm font-medium text-muted-foreground">Status:</div>
                        <div class="col-span-2">
                            @if($emailLog->status === 'sent' || $emailLog->status === 'delivered')
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-green-100 text-green-800">
                                    <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                    {{ $emailLog->formatted_status }}
                                </span>
                            @elseif($emailLog->status === 'failed')
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-red-100 text-red-800">
                                    <i data-lucide="x-circle" class="w-3 h-3 mr-1"></i>
                                    {{ $emailLog->formatted_status }}
                                </span>
                            @elseif($emailLog->status === 'sending')
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                    {{ $emailLog->formatted_status }}
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $emailLog->formatted_status }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm font-medium text-muted-foreground">Message ID:</div>
                        <div class="col-span-2">
                            <code class="relative rounded bg-muted px-[0.3rem] py-[0.2rem] font-mono text-sm font-semibold">{{ $emailLog->message_id }}</code>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm font-medium text-muted-foreground">From:</div>
                        <div class="col-span-2 text-sm">{{ $emailLog->from }}</div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm font-medium text-muted-foreground">To:</div>
                        <div class="col-span-2 text-sm">{{ $emailLog->to }}</div>
                    </div>
                    @if($emailLog->cc)
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm font-medium text-muted-foreground">CC:</div>
                        <div class="col-span-2 text-sm">{{ $emailLog->cc }}</div>
                    </div>
                    @endif
                    @if($emailLog->bcc)
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm font-medium text-muted-foreground">BCC:</div>
                        <div class="col-span-2 text-sm">{{ $emailLog->bcc }}</div>
                    </div>
                    @endif
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm font-medium text-muted-foreground">Subject:</div>
                        <div class="col-span-2 text-sm font-medium">{{ $emailLog->subject }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timing Information -->
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="p-6">
                <h3 class="text-lg font-medium text-foreground mb-4 flex items-center">
                    <i data-lucide="clock" class="w-5 h-5 mr-2"></i>
                    Timing Information
                </h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm font-medium text-muted-foreground">Created:</div>
                        <div class="col-span-2 text-sm">{{ $emailLog->created_at->format('M j, Y H:i:s') }}</div>
                    </div>
                    @if($emailLog->sent_at)
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm font-medium text-muted-foreground">Sent:</div>
                        <div class="col-span-2 text-sm">{{ $emailLog->sent_at->format('M j, Y H:i:s') }}</div>
                    </div>
                    @endif
                    @if($emailLog->delivered_at)
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm font-medium text-muted-foreground">Delivered:</div>
                        <div class="col-span-2 text-sm">{{ $emailLog->delivered_at->format('M j, Y H:i:s') }}</div>
                    </div>
                    @endif
                    @if($emailLog->failed_at)
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm font-medium text-muted-foreground">Failed:</div>
                        <div class="col-span-2 text-sm">{{ $emailLog->failed_at->format('M j, Y H:i:s') }}</div>
                    </div>
                    @endif
                    @if($emailLog->time_since_sent)
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-sm font-medium text-muted-foreground">Time Since Sent:</div>
                        <div class="col-span-2 text-sm">{{ $emailLog->time_since_sent }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($emailLog->error_message)
    <!-- Error Information -->
    <div class="rounded-lg border border-red-200 bg-red-50">
        <div class="p-6">
            <h3 class="text-lg font-medium text-red-800 mb-4 flex items-center">
                <i data-lucide="alert-circle" class="w-5 h-5 mr-2"></i>
                Error Information
            </h3>
            <div class="rounded-md border border-red-200 bg-white p-4">
                <p class="text-sm text-red-800 font-medium">Error Message:</p>
                <p class="text-sm text-red-700 mt-1">{{ $emailLog->error_message }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Email Body -->
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
        <div class="p-6">
            <h3 class="text-lg font-medium text-foreground mb-4 flex items-center">
                <i data-lucide="file-text" class="w-5 h-5 mr-2"></i>
                Email Body
            </h3>
            <div class="rounded-md border bg-muted/50 p-4 max-h-96 overflow-y-auto">
                <div class="prose prose-sm max-w-none text-foreground">
                    {!! nl2br(e($emailLog->body)) !!}
                </div>
            </div>
        </div>
    </div>

    @if($emailLog->metadata)
    <!-- Metadata -->
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
        <div class="p-6">
            <h3 class="text-lg font-medium text-foreground mb-4 flex items-center">
                <i data-lucide="database" class="w-5 h-5 mr-2"></i>
                Metadata
            </h3>
            <div class="rounded-md border bg-muted/50 p-4">
                <pre class="text-sm overflow-x-auto"><code>{{ json_encode($emailLog->metadata, JSON_PRETTY_PRINT) }}</code></pre>
            </div>
        </div>
    </div>
    @endif

    <!-- Actions -->
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
        <div class="p-6">
            <h3 class="text-lg font-medium text-foreground mb-4">Actions</h3>
            <div class="flex flex-col sm:flex-row gap-4">
                @if($emailLog->status === 'failed')
                <form method="POST" action="{{ route('email-monitor.resend', $emailLog->id) }}" class="inline">
                    @csrf
                    <button type="submit" 
                            class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-yellow-600 text-white hover:bg-yellow-700 h-10 px-4 py-2"
                            onclick="return confirm('Are you sure you want to resend this email?')">
                        <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i>
                        Resend Email
                    </button>
                </form>
                @endif
                <form method="POST" action="{{ route('email-monitor.destroy', $emailLog->id) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-destructive text-destructive-foreground hover:bg-destructive/90 h-10 px-4 py-2"
                            onclick="return confirm('Are you sure you want to delete this email log?')">
                        <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
                        Delete Email Log
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
