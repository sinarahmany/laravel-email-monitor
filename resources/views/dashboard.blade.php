@extends('email-monitor::layouts.app')

@section('title', 'Email Monitor Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-3xl font-bold tracking-tight text-foreground">Dashboard</h2>
            <p class="text-muted-foreground">Monitor and manage your email communications</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('email-monitor.index', ['days' => 7]) }}" 
               class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md border border-input bg-background hover:bg-accent hover:text-accent-foreground {{ $days == 7 ? 'bg-primary text-primary-foreground border-primary' : '' }}">
                Last 7 days
            </a>
            <a href="{{ route('email-monitor.index', ['days' => 30]) }}" 
               class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md border border-input bg-background hover:bg-accent hover:text-accent-foreground {{ $days == 30 ? 'bg-primary text-primary-foreground border-primary' : '' }}">
                Last 30 days
            </a>
            <a href="{{ route('email-monitor.index', ['days' => 90]) }}" 
               class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md border border-input bg-background hover:bg-accent hover:text-accent-foreground {{ $days == 90 ? 'bg-primary text-primary-foreground border-primary' : '' }}">
                Last 90 days
            </a>
            @if($statistics['pending_emails'] > 0)
            <form method="POST" action="{{ route('email-monitor.fix-stuck') }}" class="inline">
                @csrf
                <button type="submit" 
                        class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md border border-input bg-background hover:bg-accent hover:text-accent-foreground"
                        onclick="return confirm('This will mark stuck emails as failed. Continue?')">
                    <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i>
                    Fix Stuck Emails
                </button>
            </form>
            @endif
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Emails -->
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="tracking-tight text-sm font-medium text-muted-foreground">Total Emails</h3>
                <i data-lucide="mail" class="h-4 w-4 text-muted-foreground"></i>
            </div>
            <div class="p-6 pt-0">
                <div class="text-2xl font-bold text-foreground">{{ $statistics['total_emails'] }}</div>
                <p class="text-xs text-muted-foreground">All time</p>
            </div>
        </div>

        <!-- Sent Emails -->
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="tracking-tight text-sm font-medium text-muted-foreground">Sent Emails</h3>
                <i data-lucide="send" class="h-4 w-4 text-green-600"></i>
            </div>
            <div class="p-6 pt-0">
                <div class="text-2xl font-bold text-green-600">{{ $statistics['sent_emails'] }}</div>
                <p class="text-xs text-muted-foreground">Successfully delivered</p>
            </div>
        </div>

        <!-- Pending Emails -->
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="tracking-tight text-sm font-medium text-muted-foreground">Pending Emails</h3>
                <i data-lucide="clock" class="h-4 w-4 text-yellow-600"></i>
            </div>
            <div class="p-6 pt-0">
                <div class="text-2xl font-bold text-yellow-600">{{ $statistics['pending_emails'] }}</div>
                <p class="text-xs text-muted-foreground">In queue</p>
            </div>
        </div>

        <!-- Failed Emails -->
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="tracking-tight text-sm font-medium text-muted-foreground">Failed Emails</h3>
                <i data-lucide="alert-triangle" class="h-4 w-4 text-red-600"></i>
            </div>
            <div class="p-6 pt-0">
                <div class="text-2xl font-bold text-red-600">{{ $statistics['failed_emails'] }}</div>
                <p class="text-xs text-muted-foreground">Requires attention</p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="rounded-lg border bg-card">
        <div class="p-6">
            <h3 class="text-lg font-medium text-foreground mb-4">Filters</h3>
            <form method="GET" action="{{ route('email-monitor.index') }}" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" 
                           name="search" 
                           class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" 
                           placeholder="Search emails..." 
                           value="{{ $search }}">
                </div>
                <div class="flex-1">
                    <select name="status" 
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                        <option value="">All Statuses</option>
                        <option value="sending" {{ $status === 'sending' ? 'selected' : '' }}>Sending</option>
                        <option value="sent" {{ $status === 'sent' ? 'selected' : '' }}>Sent</option>
                        <option value="delivered" {{ $status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="failed" {{ $status === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="bounced" {{ $status === 'bounced' ? 'selected' : '' }}>Bounced</option>
                    </select>
                </div>
                <input type="hidden" name="days" value="{{ $days }}">
                <button type="submit" 
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                    <i data-lucide="search" class="w-4 h-4 mr-2"></i>
                    Filter
                </button>
            </form>
        </div>
    </div>

    <!-- Email Logs Table -->
    <div class="rounded-lg border bg-card">
        <div class="p-6">
            <h3 class="text-lg font-medium text-foreground mb-4">Email Logs</h3>
            <div class="rounded-md border">
                <div class="relative w-full overflow-auto">
                    <table class="w-full caption-bottom text-sm">
                        <thead class="[&_tr]:border-b">
                            <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&:has([role=checkbox])]:pr-0">Status</th>
                                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&:has([role=checkbox])]:pr-0">To</th>
                                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&:has([role=checkbox])]:pr-0">From</th>
                                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&:has([role=checkbox])]:pr-0">Subject</th>
                                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&:has([role=checkbox])]:pr-0">Sent At</th>
                                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&:has([role=checkbox])]:pr-0">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="[&_tr:last-child]:border-0">
                            @forelse($emailLogs as $emailLog)
                            <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                <td class="p-4 align-middle">
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
                                </td>
                                <td class="p-4 align-middle font-medium">{{ Str::limit($emailLog->to, 30) }}</td>
                                <td class="p-4 align-middle">{{ Str::limit($emailLog->from, 30) }}</td>
                                <td class="p-4 align-middle">{{ Str::limit($emailLog->subject, 50) }}</td>
                                <td class="p-4 align-middle text-muted-foreground">
                                    {{ $emailLog->sent_at ? $emailLog->sent_at->format('M j, Y H:i') : '-' }}
                                </td>
                                <td class="p-4 align-middle">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('email-monitor.show', $emailLog->id) }}" 
                                           class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 w-9">
                                            <i data-lucide="eye" class="h-4 w-4"></i>
                                        </a>
                                        @if($emailLog->status === 'failed')
                                        <form method="POST" action="{{ route('email-monitor.resend', $emailLog->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 w-9"
                                                    onclick="return confirm('Are you sure you want to resend this email?')">
                                                <i data-lucide="refresh-cw" class="h-4 w-4"></i>
                                            </button>
                                        </form>
                                        @endif
                                        @if($emailLog->status === 'sending')
                                        <form method="POST" action="{{ route('email-monitor.mark-sent', $emailLog->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 w-9"
                                                    onclick="return confirm('Mark this email as sent?')">
                                                <i data-lucide="check" class="h-4 w-4"></i>
                                            </button>
                                        </form>
                                        @endif
                                        <form method="POST" action="{{ route('email-monitor.destroy', $emailLog->id) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 w-9 text-red-600 hover:text-red-700"
                                                    onclick="return confirm('Are you sure you want to delete this email log?')">
                                                <i data-lucide="trash-2" class="h-4 w-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-muted-foreground">
                                    <div class="flex flex-col items-center gap-2">
                                        <i data-lucide="mail" class="h-8 w-8"></i>
                                        <p>No emails found.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="flex justify-center">
        {{ $emailLogs->appends(request()->query())->links() }}
    </div>
</div>
@endsection
