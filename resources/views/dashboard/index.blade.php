@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')
    <div class="header">
        <h1 class="greeting">Hi, {{Auth::user()->name}} 👋</h1>
        <p class="greeting-subtitle">Here is your dashboard, let's do some thing!</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-value">29,895</div>
                    <div class="stat-label">All Bookings</div>
                </div>
                <div class="stat-icon bookings">📚</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-value">1,836</div>
                    <div class="stat-label">All Orders</div>
                </div>
                <div class="stat-icon orders">⭐</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-value">1,243</div>
                    <div class="stat-label">All Vendors</div>
                </div>
                <div class="stat-icon vendors">👥</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-value">1,535</div>
                    <div class="stat-label">All Restaurants</div>
                </div>
                <div class="stat-icon restaurants">🍽️</div>
            </div>
        </div>
    </div>
@endsection