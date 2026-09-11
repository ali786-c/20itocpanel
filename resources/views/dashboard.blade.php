@extends('layouts.app')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2 style="font-size: 1.8rem; font-weight: 600;">Active Migrations</h2>
    <a href="{{ route('migrations.create') }}" class="btn">
        <svg style="width: 20px; height: 20px; margin-right: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
        New Migration
    </a>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
            <tr style="border-bottom: 1px solid var(--border); background: rgba(0,0,0,0.3);">
                <th style="padding: 1.25rem 1.5rem; color: var(--text-muted); font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">Organization</th>
                <th style="padding: 1.25rem 1.5rem; color: var(--text-muted); font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">Source (20i)</th>
                <th style="padding: 1.25rem 1.5rem; color: var(--text-muted); font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">Destination (WHM)</th>
                <th style="padding: 1.25rem 1.5rem; color: var(--text-muted); font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">Status</th>
                <th style="padding: 1.25rem 1.5rem; color: var(--text-muted); font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jobs as $job)
            <tr style="border-bottom: 1px solid var(--border); transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='rgba(255,255,255,0.03)'" onmouseout="this.style.backgroundColor='transparent'">
                <td style="padding: 1.25rem 1.5rem; font-weight: 500;">
                    <div style="display: flex; align-items: center;">
                        <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--secondary)); display: flex; align-items: center; justify-content: center; font-weight: bold; margin-right: 1rem;">
                            {{ substr($job->organization->name, 0, 1) }}
                        </div>
                        {{ $job->organization->name }}
                    </div>
                </td>
                <td style="padding: 1.25rem 1.5rem;">
                    <span style="display: block; font-weight: 500; color: #e2e8f0;">{{ $job->sourceConnection->name }}</span>
                </td>
                <td style="padding: 1.25rem 1.5rem;">
                    <span style="display: block; font-weight: 500; color: #e2e8f0;">{{ $job->destinationConnection->name }}</span>
                    <span style="font-size: 0.8rem; color: var(--text-muted); display: block; margin-top: 0.25rem;">{{ parse_url($job->destinationConnection->hostname, PHP_URL_HOST) ?? $job->destinationConnection->hostname }}</span>
                </td>
                <td style="padding: 1.25rem 1.5rem;">
                    @php
                        $color = $job->status === 'failed' ? '#ef4444' : 
                                ($job->status === 'completed' ? '#10b981' : '#6366f1');
                        
                        $bg = $job->status === 'failed' ? 'rgba(239, 68, 68, 0.1)' : 
                             ($job->status === 'completed' ? 'rgba(16, 185, 129, 0.1)' : 'rgba(99, 102, 241, 0.1)');
                        
                        $isSpinning = !in_array($job->status, ['completed', 'failed', 'draft', 'awaiting_mapping']);
                    @endphp
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <span style="display: inline-flex; align-items: center; gap: 0.5rem; background: {{ $bg }}; color: {{ $color }}; padding: 0.35rem 0.85rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; border: 1px solid rgba(255,255,255,0.05);">
                            @if($isSpinning)
                                <svg style="animation: spin 1s linear infinite; width: 0.875rem; height: 0.875rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" stroke-opacity="0.25"></circle>
                                    <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            @elseif($job->status === 'completed')
                                <svg style="width: 0.875rem; height: 0.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            @endif
                            {{ str_replace('_', ' ', $job->status) }}
                        </span>
                        
                        @if($job->status === 'awaiting_mapping')
                            <a href="{{ route('migrations.map', $job->id) }}" style="color: var(--text-main); font-size: 0.85rem; font-weight: 600; text-decoration: none; padding: 0.35rem 0.75rem; border-radius: 0.375rem; background: rgba(255,255,255,0.05); border: 1px solid var(--border); transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">
                                View Packages ({{ count($job->source_manifest ?? []) }})
                            </a>
                        @endif

                        <a href="{{ route('migrations.logs', $job->id) }}" style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; text-decoration: none; padding: 0.35rem 0.75rem; border-radius: 0.375rem; transition: all 0.2s; border: 1px solid transparent;" onmouseover="this.style.border='1px solid var(--border)'" onmouseout="this.style.border='1px solid transparent'">
                            View Logs
                        </a>

                        <form action="{{ route('migrations.destroy', $job->id) }}" method="POST" style="margin: 0; display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this job?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background: none; border: 1px solid transparent; color: #ef4444; font-size: 0.85rem; font-weight: 500; cursor: pointer; padding: 0.35rem 0.75rem; border-radius: 0.375rem; transition: all 0.2s;" onmouseover="this.style.border='1px solid rgba(239, 68, 68, 0.3)'; this.style.background='rgba(239, 68, 68, 0.1)'" onmouseout="this.style.border='1px solid transparent'; this.style.background='none'">
                                Delete
                            </button>
                        </form>
                    </div>
                    <style>@keyframes spin { 100% { transform: rotate(360deg); } }</style>
                </td>
                <td style="padding: 1.25rem 1.5rem; color: var(--text-muted); font-size: 0.9rem;">
                    {{ $job->created_at->diffForHumans() }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="padding: 4rem 2rem; text-align: center;">
                    <div style="background: rgba(255,255,255,0.02); display: inline-block; padding: 2rem; border-radius: 1rem; border: 1px dashed var(--border);">
                        <svg style="width: 48px; height: 48px; color: var(--text-muted); margin: 0 auto 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        <h3 style="color: white; margin-bottom: 0.5rem; font-weight: 500;">No migrations found</h3>
                        <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Start your first migration to see it appear here.</p>
                        <a href="{{ route('migrations.create') }}" class="btn">Create Migration</a>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
