@php
    $kopOrg = \App\Models\Organization::first();
    if (auth()->check() && auth()->user()->organization) {
        $kopOrg = auth()->user()->organization;
    }
@endphp
@if($kopOrg)
<div style="border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; text-align: center; position: relative;">
    @if($kopOrg->logo)
        <img src="{{ public_path('storage/' . $kopOrg->logo) }}" style="max-height: 70px; position: absolute; left: 0; top: 0;" alt="Logo">
    @endif
    <h2 style="margin: 0; font-size: 20px; font-weight: bold; text-transform: uppercase;">{{ $kopOrg->name }}</h2>
    @if($kopOrg->legal_number)
        <p style="margin: 3px 0; font-size: 12px; color: #555;">Badan Hukum: {{ $kopOrg->legal_number }}</p>
    @endif
    <p style="margin: 3px 0; font-size: 12px; color: #555;">{{ $kopOrg->address }}</p>
    <p style="margin: 3px 0; font-size: 12px; color: #555;">Telp: {{ $kopOrg->phone ?? '-' }} | Email: {{ $kopOrg->email ?? '-' }}</p>
</div>
@endif
