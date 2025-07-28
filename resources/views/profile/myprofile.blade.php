@section('title', 'My Profile')
<x-app-layout>
    <section class="hero-title">
        <h1>My Profile</h1>
    </section>
    <section class="hero">
        <div class="container min-container">
            <div class="profile-box">
                <div class="profile-info">
                    <p><strong>Nama:</strong> {{ $user->name }}</p>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                </div>

                <div class="profile-edit-link">
                    <a href="{{ route('profile.edit') }}" class="profile-edit-button">Edit Profil</a>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>