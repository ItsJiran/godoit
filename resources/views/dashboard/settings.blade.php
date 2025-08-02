@extends('layouts.admin')
@section('title', 'Settings')
@section('content')
<div class="min-container">
    <div class="dashboard-title">
        <h1>Edit Settings</h1>
    </div>

    <div class="box-formulir">
        <form action="{{ route('updateSetting') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Komisi Free ( Percent )</label>
                <input class="form-input" type="text" name="free_member_comission_percentage" class="form-control" value="{{ old('free_member_comission_percentage') ?? $free_member_comission_percentage }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Komisi Premium ( Percent )</label>
                <input class="form-input" type="text" name="premium_member_comission_percentage" class="form-control" value="{{ old('premium_member_comission_percentage') ?? $premium_member_comission_percentage }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Komisi Premium Downline 1 Level ( Percent )</label>
                <input class="form-input" type="text" name="premium_downline" class="form-control" value="{{ old('premium_downline') ?? $premium_downline }}" required>
            </div>        
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
@endsection