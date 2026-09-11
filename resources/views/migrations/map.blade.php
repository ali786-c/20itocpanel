@extends('layouts.app')

@section('content')
<div style="max-width: 1000px; margin: 0 auto;">
    <div style="text-align: center; margin-bottom: 2rem;">
        <h2 style="font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">Select Packages to Migrate</h2>
        <p style="color: var(--text-muted); font-size: 1.1rem;">We discovered {{ count($job->source_manifest ?? []) }} packages on your 20i server.</p>
    </div>

    <form action="{{ route('migrations.submit_mapping', $job->id) }}" method="POST">
        @csrf
        <div class="card" style="padding: 0; overflow: hidden; margin-bottom: 2rem;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="background: rgba(0,0,0,0.3); border-bottom: 1px solid var(--border);">
                        <th style="padding: 1rem 1.5rem; width: 50px;">
                            <input type="checkbox" id="selectAll" style="width: 18px; height: 18px; cursor: pointer;">
                        </th>
                        <th style="padding: 1.25rem 1.5rem; color: var(--text-muted); font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">Package Info</th>
                        <th style="padding: 1.25rem 1.5rem; color: var(--text-muted); font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($job->source_manifest ?? [] as $package)
                    <tr style="border-bottom: 1px solid var(--border); transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='rgba(255,255,255,0.03)'" onmouseout="this.style.backgroundColor='transparent'">
                        <td style="padding: 1.25rem 1.5rem;">
                            <input type="checkbox" name="selected_packages[]" value="{{ $package['id'] ?? $package['name'] ?? json_encode($package) }}" class="package-cb" style="width: 18px; height: 18px; cursor: pointer;">
                        </td>
                        <td style="padding: 1.25rem 1.5rem;">
                            <span style="display: block; font-weight: 600; font-size: 1.05rem; color: #e2e8f0;">
                                {{ $package['name'] ?? $package['domain'] ?? 'Unnamed Package' }}
                            </span>
                            <span style="font-size: 0.85rem; color: var(--text-muted); display: block; margin-top: 0.25rem;">
                                ID: {{ $package['id'] ?? 'N/A' }} | Type: {{ $package['type'] ?? 'Unknown' }}
                            </span>
                        </td>
                        <td style="padding: 1.25rem 1.5rem;">
                            <span style="background: rgba(16, 185, 129, 0.15); color: #34d399; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; border: 1px solid rgba(16, 185, 129, 0.3);">
                                Ready
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="padding: 4rem; text-align: center; color: var(--text-muted);">
                            No packages found in the source manifest.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div style="text-align: right;">
            <a href="{{ route('dashboard') }}" style="color: var(--text-muted); margin-right: 1.5rem; text-decoration: none; font-weight: 500;">Cancel</a>
            <button type="submit" class="btn" style="padding: 1rem 3rem; font-size: 1.1rem;">
                Migrate Selected Packages
                <svg style="width: 18px; height: 18px; margin-left: 0.5rem; display: inline; vertical-align: text-bottom;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>
        </div>
    </form>
</div>

<script>
    document.getElementById('selectAll').addEventListener('change', function(e) {
        var checkboxes = document.querySelectorAll('.package-cb');
        for (var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = e.target.checked;
        }
    });
</script>
@endsection
