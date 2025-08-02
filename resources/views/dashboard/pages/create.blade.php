@extends('layouts.admin')
@section('title', 'Marketing Kit')
@section('content')
<div class="min-container">
    <div class="dashboard-title">
        <h1>Create Section</h1>
    </div>

    <div class="box-formulir">

        <form action="{{ route('storeSection') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label">Landing Type</label>
                <input class="form-input" readonly type="text" name="landing_type" class="form-control" value="{{ old('landing_type') ?? $landing_type }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Type</label>
                <input class="form-input" readonly type="text" name="type" class="form-control" value="{{ old('type') ?? $type }}" required>
            </div>


            @if($type == 'homepage_description')

                <div class="form-group">
                    <label class="form-label">Index</label>
                    <input class="form-input" min="0" type="number" name="index" class="form-control" value="{{ old('index') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Title</label>
                    <input class="form-input" type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <input class="form-input" type="text" name="description" class="form-control" value="{{ old('description') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Gambar</label>
                    <input class="form-input" type="file" name="hero_image" class="form-control">
                </div>

            @endif

            @if($type == 'homepage_clients')

                <div class="form-group">
                    <label class="form-label">Title</label>
                    <input class="form-input" type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Gambar Clients 1</label>
                    <input class="form-input" type="file" name="clients_1" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">Gambar Clients 2</label>
                    <input class="form-input" type="file" name="clients_2" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">Gambar Clients 3</label>
                    <input class="form-input" type="file" name="clients_3" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">Gambar Clients 4</label>
                    <input class="form-input" type="file" name="clients_4" class="form-control">
                </div>

            @endif

            @if($type == 'homepage_product')

                <div class="form-group">
                    <label class="form-label">Title</label>
                    <input class="form-input" type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Subtitle</label>
                    <input class="form-input" type="text" name="subtitle" class="form-control" value="{{ old('subtitle') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <input class="form-input" type="text" name="description" class="form-control" value="{{ old('description') }}" required>
                </div>

            @endif


            @if($type == 'homepage_testimonials')

                <div class="form-group">
                    <label class="form-label">Title</label>
                    <input class="form-input" type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Quote 1</label>
                    <input class="form-input" type="text" name="quote-1" class="form-control" value="{{ old('quote-1') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Role 1</label>
                    <input class="form-input" type="text" name="role-1" class="form-control" value="{{ old('role-1') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Name 1</label>
                    <input class="form-input" type="text" name="name-1" class="form-control" value="{{ old('name-1') }}" required>
                </div>


                <div class="form-group">
                    <label class="form-label">Quote 2</label>
                    <input class="form-input" type="text" name="quote-2" class="form-control" value="{{ old('quote-2') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Role 2</label>
                    <input class="form-input" type="text" name="role-2" class="form-control" value="{{ old('role-2') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Name 2</label>
                    <input class="form-input" type="text" name="name-2" class="form-control" value="{{ old('name-2') }}" required>
                </div>

            @endif

            @if($type == 'homepage_faq')

                <div class="form-group">
                    <label class="form-label">Title</label>
                    <input class="form-input" type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Question 1</label>
                    <input class="form-input" type="text" name="question-1" class="form-control" value="{{ old('question-1') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Answer 1</label>
                    <input class="form-input" type="text" name="answer-1" class="form-control" value="{{ old('answer-1') }}" required>
                </div>
                
            @endif

        <button type="submit" class="btn btn-primary">Create</button>
    </form>
</div>
</div>
@endsection