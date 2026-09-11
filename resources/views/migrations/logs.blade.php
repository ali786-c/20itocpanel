@extends('layouts.app')

@section('content')
<div style="max-width: 1000px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h2 style="font-size: 2rem; font-weight: 700; margin-bottom: 0.25rem;">Live Job Logs</h2>
            <p style="color: var(--text-muted); font-size: 1rem;">Tracking execution for Job ID: {{ substr($job->id, 0, 8) }}...</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn" style="padding: 0.75rem 1.5rem; background: rgba(255,255,255,0.05); color: var(--text-main);">Back to Dashboard</a>
    </div>

    <div class="card" style="padding: 0; overflow: hidden; background: #0a0a0a; border: 1px solid #333;">
        <div style="background: #1a1a1a; padding: 0.75rem 1.5rem; border-bottom: 1px solid #333; display: flex; gap: 0.5rem; align-items: center;">
            <div style="width: 12px; height: 12px; border-radius: 50%; background: #ef4444;"></div>
            <div style="width: 12px; height: 12px; border-radius: 50%; background: #eab308;"></div>
            <div style="width: 12px; height: 12px; border-radius: 50%; background: #22c55e;"></div>
            <span style="margin-left: 1rem; font-family: monospace; color: #888; font-size: 0.85rem;">root@migration-orchestrator:~</span>
        </div>
        
        <div style="padding: 1.5rem; font-family: 'Courier New', Courier, monospace; font-size: 0.9rem; line-height: 1.6; height: 600px; overflow-y: auto; color: #e2e8f0;">
            @forelse($job->logs ?? [] as $log)
                @php
                    $color = '#e2e8f0';
                    if (($log['level'] ?? '') === 'error') $color = '#ef4444';
                    if (($log['level'] ?? '') === 'warning') $color = '#eab308';
                @endphp
                <div style="margin-bottom: 0.5rem; color: {{ $color }};">
                    <span style="color: #64748b;">[{{ \Carbon\Carbon::parse($log['timestamp'])->format('Y-m-d H:i:s') }}]</span> 
                    <span style="color: #10b981;">[orchestrator]</span> 
                    {{ $log['message'] }}
                </div>
            @empty
                <div style="color: #64748b; font-style: italic;">No logs recorded yet.</div>
            @endforelse
            
            @if($job->status !== 'completed' && $job->status !== 'failed')
                <div style="margin-top: 1rem; display: flex; align-items: center; color: #10b981;">
                    <span style="margin-right: 0.5rem;">></span>
                    <span class="blinking-cursor" style="width: 8px; height: 16px; background: #10b981; display: inline-block;"></span>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0; } }
    .blinking-cursor { animation: blink 1s step-end infinite; }
</style>

<script>
    // Auto-refresh the page every 3 seconds if the job is still active
    @if($job->status !== 'completed' && $job->status !== 'failed' && $job->status !== 'draft' && $job->status !== 'awaiting_mapping')
        setTimeout(function() {
            window.location.reload();
        }, 3000);
    @endif
</script>
@endsection
