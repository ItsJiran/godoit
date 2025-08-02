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

            <div class="form-group">
                <label class="form-label">Index</label>
                <input class="form-input" min="0" type="number" name="index" class="form-control" value="{{ old('index') }}" required>
            </div>


            @if($type == 'homepage_description')

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

            @if($type == 'homepage_testimonials')

                <div class="form-group">
                    <label class="form-label">Title</label>
                    <input class="form-input" type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                </div>

                <div id="clients-container">
                    <!-- Client forms will be added here by JavaScript -->
                </div>
    
                <div class="flex justify-between items-center mt-6">
                    <button type="button" id="add-client" style="margin-bottom:20px;" class="btn btn-primary">
                        Add Another Testimony
                    </button>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const clientsContainer = document.getElementById('clients-container');
                        const addClientBtn = document.getElementById('add-client');

                        // Fungsi untuk membuat grup input klien baru
                        function createClientGroup(index) {
                            const div = document.createElement('div');
                            div.className = 'client-group p-4 border rounded-md mb-4 bg-gray-50 relative';
                            div.innerHTML = `
                                <div style="display:flex; flex-direction:row-reverse; justify-content:space-between;">
                                    <button type="button" class="remove-client btn btn-primary" style='width:fit-content; height:fit-content;' title="Remove Client">
                                        &times; 
                                    </button>
                                    <h4 class="text-lg font-semibold mb-2">Testimony ${index + 1}</h4>
                                </div>

                                <div class="form-group">
                                    <label for="testimony_name_${index}" class="form-label">Nama : </label>
                                    <input  class="form-input" type="text" name="testimonials[${index}][name]" id="testimony_name_${index}" class="form-control">
                                </div>


                                <div class="form-group">
                                    <label for="testimony_role_${index}" class="form-label">Jabatan : </label>
                                    <input  class="form-input" type="text" name="testimonials[${index}][role]" id="testimony_role_${index}" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label for="testimony_quote_${index}" class="form-label">Quote : </label>                                  
                                    <textarea class="form-input" name="testimonials[${index}][quote]" id="testimony_quote_${index}" class="form-control" id="ckeditor" rows="10" required></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="testimony_image_${index}"  class="form-label">Gambar</label>
                                    <input class="form-input" type="file" name="testimonials[${index}][image]" id="testimony_image_${index}" class="form-control">
                                </div>
                            `;
                            return div;
                        }

                        // Fungsi untuk memperbarui indeks form setelah penghapusan
                        function updateIndices() {
                            const groups = clientsContainer.querySelectorAll('.client-group');
                            groups.forEach((group, index) => {
                                // Perbarui judul
                                group.querySelector('h4').textContent = `Client ${index + 1}`;

                                // Perbarui name dan id input
                                group.querySelectorAll('input').forEach(input => {
                                    const oldName = input.name;
                                    // Ubah index pada string name: clients[...][key]
                                    const newName = oldName.replace(/clients\[\d*\]/, `clients[${index}]`);
                                    input.name = newName;
                                    input.id = input.id.replace(/_\d+$/, '_' + index);
                                });
                            });
                        }

                        // Event listener untuk tombol "Add Another Client"
                        addClientBtn.addEventListener('click', function() {
                            const index = clientsContainer.children.length;
                            const newClientForm = createClientGroup(index);
                            clientsContainer.appendChild(newClientForm);
                        });

                        // Event listener untuk tombol "Remove Client"
                        clientsContainer.addEventListener('click', function(e) {
                            if (e.target.classList.contains('remove-client')) {
                                const clientGroup = e.target.closest('.client-group');
                                if (clientGroup && clientsContainer.children.length > 1) {
                                    clientGroup.remove();
                                    updateIndices(); // Panggil fungsi untuk memperbarui indeks setelah penghapusan
                                }
                            }
                        });

                        // Buat satu grup klien secara default saat halaman dimuat
                        if (clientsContainer.children.length === 0) {
                            clientsContainer.appendChild(createClientGroup(0));
                        }
                    });
                </script>

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

                <div class="form-group">
                    <label class="form-label">Href</label>
                    <input class="form-input" type="text" name="href" class="form-control" value="{{ old('href') }}" required>
                </div>
            @endif


            @if($type == 'homepage_clients')

            <div class="form-group">
                <label class="form-label">Title</label>
                <input class="form-input" type="text" name="title" class="form-control" value="{{ old('title') }}" required>
            </div>

            <div id="clients-container">
                <!-- Client forms will be added here by JavaScript -->
            </div>

            <div class="flex justify-between items-center mt-6">
                <button type="button" id="add-client" style="margin-bottom:20px;" class="btn btn-primary">
                    Add Another Client
                </button>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const clientsContainer = document.getElementById('clients-container');
                    const addClientBtn = document.getElementById('add-client');

                    // Fungsi untuk membuat grup input klien baru
                    function createClientGroup(index) {
                        const div = document.createElement('div');
                        div.className = 'client-group p-4 border rounded-md mb-4 bg-gray-50 relative';
                        div.innerHTML = `
                            <div style="display:flex; flex-direction:row-reverse; justify-content:space-between;">
                                <button type="button" class="remove-client btn btn-primary" style='width:fit-content; height:fit-content;' title="Remove Client">
                                    &times; 
                                </button>
                                <h4 class="text-lg font-semibold mb-2">Client ${index + 1}</h4>
                            </div>

                            <div class="form-group">
                                <label for="client_name_${index}" class="form-label">Nama : </label>
                                <input  class="form-input" type="text" name="clients[${index}][name]" id="client_name_${index}" class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="client_image_${index}"  class="form-label">Gambar</label>
                                <input class="form-input" type="file" name="clients[${index}][image]" id="client_image_${index}" class="form-control">
                            </div>
                        `;
                        return div;
                    }

                    // Fungsi untuk memperbarui indeks form setelah penghapusan
                    function updateIndices() {
                        const groups = clientsContainer.querySelectorAll('.client-group');
                        groups.forEach((group, index) => {
                            // Perbarui judul
                            group.querySelector('h4').textContent = `Client ${index + 1}`;

                            // Perbarui name dan id input
                            group.querySelectorAll('input').forEach(input => {
                                const oldName = input.name;
                                // Ubah index pada string name: clients[...][key]
                                const newName = oldName.replace(/clients\[\d*\]/, `clients[${index}]`);
                                input.name = newName;
                                input.id = input.id.replace(/_\d+$/, '_' + index);
                            });
                        });
                    }

                    // Event listener untuk tombol "Add Another Client"
                    addClientBtn.addEventListener('click', function() {
                        const index = clientsContainer.children.length;
                        const newClientForm = createClientGroup(index);
                        clientsContainer.appendChild(newClientForm);
                    });

                    // Event listener untuk tombol "Remove Client"
                    clientsContainer.addEventListener('click', function(e) {
                        if (e.target.classList.contains('remove-client')) {
                            const clientGroup = e.target.closest('.client-group');
                            if (clientGroup && clientsContainer.children.length > 1) {
                                clientGroup.remove();
                                updateIndices(); // Panggil fungsi untuk memperbarui indeks setelah penghapusan
                            }
                        }
                    });

                    // Buat satu grup klien secara default saat halaman dimuat
                    if (clientsContainer.children.length === 0) {
                        clientsContainer.appendChild(createClientGroup(0));
                    }
                });
            </script>

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