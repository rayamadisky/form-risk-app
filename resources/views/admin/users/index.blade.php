<x-app-layout>
    <x-slot name="header">
        <div class="space-y-1">
            <h2 class="font-semibold text-xl text-slate-900 leading-tight tracking-tight">
                {{ __('Manajemen Karyawan (Super Admin)') }}
            </h2>
            <p class="text-sm text-slate-500">Kelola akun, status aktif, dan mutasi user dengan tata letak yang lebih bersih dan efisien.</p>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="page-shell page-stack">

            @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                {{ session('error') }}
            </div>
            @endif

            <div class="surface-card overflow-hidden">
                <div class="p-4 sm:p-6 text-gray-900 border-b border-slate-200 flex justify-between items-center bg-slate-50">
                    <h3 class="text-lg font-bold">Daftar Karyawan BPR</h3>
                </div>

                <div class="py-6 sm:py-12">
                    <div class="page-shell">

                        <div class="flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center mb-6">
                            <h3 class="text-lg font-bold text-gray-800 uppercase tracking-wider">Manajemen Pengguna BPR</h3>
                            <button onclick="document.getElementById('modalTambah').classList.remove('hidden')" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded text-sm shadow">
                                + Tambah User Baru
                            </button>
                        </div>

                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                            <div class="overflow-x-auto -mx-4 sm:mx-0">
                            <table class="min-w-[850px] w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Nama & Username</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Cabang</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Jabatan</th>
                                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Status</th>
                                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($users as $user)
                                    <tr class="{{ !$user->is_active ? 'bg-gray-50' : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-gray-900">{{ $user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $user->username }} | {{ $user->email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 uppercase">
                                            {{ $user->branch->nama_cabang ?? 'Pusat' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 bg-indigo-100 text-indigo-800 rounded-full text-[10px] font-bold uppercase">
                                                {{ $user->roles->first()->name ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="px-2 py-1 {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} rounded text-[10px] font-bold uppercase">
                                                {{ $user->is_active ? 'Aktif' : 'Non-Aktif' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                            <button onclick="openEditModal({{ $user }})" class="inline-block text-indigo-600 hover:text-indigo-900 font-bold uppercase text-[10px]">Edit</button>

                                            <form action="{{ route('admin.users.toggle', $user->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="{{ $user->is_active ? 'text-red-600' : 'text-green-600' }} font-bold uppercase text-[10px]">
                                                    {{ $user->is_active ? 'Non-Aktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="modalTambah" class="fixed inset-0 z-50 hidden bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full">
            <div class="relative top-6 sm:top-20 mx-auto p-4 sm:p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900 uppercase">Tambah User Baru</h3>
                    <button onclick="document.getElementById('modalTambah').classList.add('hidden')" class="text-gray-400 hover:text-red-500 font-bold text-xl">&times;</button>
                </div>
                <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" name="name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Username</label>
                            <input type="text" name="username" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Password</label>
                            <input type="password" name="password" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Cabang Penempatan</label>
                            <select name="branch_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm uppercase focus:ring-indigo-500">
                                @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->nama_cabang }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jabatan (Role)</label>
                            <select name="role" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm uppercase focus:ring-indigo-500">
                                @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded text-sm w-full">Simpan Akun Baru</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="modalEdit" class="fixed inset-0 z-50 hidden bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full">
            <div class="relative top-6 sm:top-20 mx-auto p-4 sm:p-5 border w-full max-w-md shadow-lg rounded-md bg-white border-t-4 border-t-yellow-500">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900 uppercase">Edit / Mutasi User</h3>
                    <button onclick="document.getElementById('modalEdit').classList.add('hidden')" class="text-gray-400 hover:text-red-500 font-bold text-xl">&times;</button>
                </div>
                <form id="editForm" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" name="name" id="edit_name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm bg-gray-50 focus:ring-indigo-500">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Mutasi Cabang</label>
                            <select name="branch_id" id="edit_branch" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm uppercase focus:ring-indigo-500">
                                @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->nama_cabang }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Ubah Jabatan</label>
                            <select name="role" id="edit_role" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm uppercase focus:ring-indigo-500">
                                @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-4 mt-4">
                        <label class="block text-xs font-bold text-red-600 mb-1">Reset Password (Opsional)</label>
                        <input type="password" name="password" placeholder="Kosongkan jika tidak ingin diubah" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500">
                        <input type="password" name="password_confirmation" placeholder="Ketik ulang password baru" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500">
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded text-sm w-full shadow">Update Data User</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function openEditModal(user) {
                // 1. Munculin modalnya
                document.getElementById('modalEdit').classList.remove('hidden');

                // 2. Isi nilai form-nya
                document.getElementById('edit_name').value = user.name;
                document.getElementById('edit_branch').value = user.branch_id;

                // Set role (karena relasi, kita ambil dari array roles pertama)
                if (user.roles && user.roles.length > 0) {
                    document.getElementById('edit_role').value = user.roles[0].name;
                }

                // 3. Ubah tujuan action formnya dinamis ke ID user yang diklik
                document.getElementById('editForm').action = `/admin/users/${user.id}`;
            }
        </script>
    </div>
</x-app-layout>