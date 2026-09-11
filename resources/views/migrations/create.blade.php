@extends('layouts.app')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div style="text-align: center; margin-bottom: 3rem;">
        <h2 style="font-size: 2.2rem; font-weight: 700; margin-bottom: 0.5rem;">New Migration</h2>
        <p style="color: var(--text-muted); font-size: 1.1rem;">Configure your 20i source and WHM destination to begin.</p>
    </div>
    
    <div class="card" style="padding: 3rem;">
        <form action="{{ route('migrations.store') }}" method="POST">
            @csrf
            
            <div class="form-group" style="margin-bottom: 3rem;">
                <label>Organization Name (Client)</label>
                <input type="text" name="org_name" class="form-control" required placeholder="e.g., Nexloop Corp" value="Nexloop Corp">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; margin-bottom: 2rem;">
                <!-- 20i Section -->
                <div style="background: rgba(0,0,0,0.2); padding: 1.5rem; border-radius: 0.75rem; border: 1px solid var(--border);">
                    <div style="display: flex; align-items: center; margin-bottom: 1.5rem;">
                        <div style="width: 8px; height: 8px; border-radius: 50%; background: var(--primary); margin-right: 0.75rem; box-shadow: 0 0 10px var(--primary);"></div>
                        <h3 style="font-size: 1.2rem; font-weight: 600; color: white;">Source: 20i</h3>
                    </div>
                    
                    <div class="form-group">
                        <label>General API Key</label>
                        <input type="text" name="token_20i" class="form-control" required placeholder="Paste 20i key..." value="c3ecb30255aed3b60">
                    </div>
                </div>

                <!-- cPanel Section -->
                <div style="background: rgba(0,0,0,0.2); padding: 1.5rem; border-radius: 0.75rem; border: 1px solid var(--border);">
                    <div style="display: flex; align-items: center; margin-bottom: 1.5rem;">
                        <div style="width: 8px; height: 8px; border-radius: 50%; background: var(--secondary); margin-right: 0.75rem; box-shadow: 0 0 10px var(--secondary);"></div>
                        <h3 style="font-size: 1.2rem; font-weight: 600; color: white;">Destination: WHM</h3>
                    </div>
                    
                    <div class="form-group">
                        <label>WHM Hostname (with port 2087)</label>
                        <input type="url" name="whm_host" class="form-control" required placeholder="https://host.com:2087" value="https://client.webhosting.co.nz:2087">
                    </div>
                    <div class="form-group">
                        <label>WHM API Token</label>
                        <input type="password" name="whm_token" class="form-control" required placeholder="Paste WHM token..." value="1ZPCWS9Q6A55FOL1RAB5N0F2G52180DO">
                    </div>
                </div>
            </div>

            <div style="text-align: center; margin-top: 3rem; padding-top: 2rem; border-top: 1px dashed var(--border);">
                <button type="submit" class="btn" style="padding: 1.25rem 4rem; font-size: 1.15rem; letter-spacing: 0.5px;">
                    <svg style="width: 20px; height: 20px; margin-right: 0.75rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Run Preflight & Initialize Migration
                </button>
                <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 1rem;">This will securely connect to both APIs and verify credentials.</p>
            </div>
        </form>
    </div>
</div>
@endsection
