@section('title', 'My Profile')
<x-app-layout>
    <section class="hero">
        <div class="container min-container">
            <div class="profile-box">
                <h2 class="profile-title">Profil Saya</h2>

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