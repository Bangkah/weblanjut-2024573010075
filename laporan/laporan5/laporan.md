# Laporan Modul 5: Form Submission & Data Validation
**Mata Kuliah:** Workshop Web Lanjut <br>
**Nama:** Muhammad Dhiyaul Atha <br>
**NIM:** 2024573010075<br>
**Kelas:** TI 2B  

---

## Abstrak

Laporan praktikum ini membahas penerapan Form Submission dan Data Validation menggunakan framework Laravel. Tujuan praktikum ini adalah untuk memahami alur kerja request–response, proses validasi data pengguna, serta penerapan fitur keamanan seperti CSRF Protection. Melalui serangkaian percobaan, peserta mempelajari bagaimana Laravel menangani pengiriman form secara aman, menampilkan pesan kesalahan secara informatif, dan membangun form bertahap (multi-step form) dengan penyimpanan data sementara di session. Hasil praktikum menunjukkan bahwa mekanisme validasi Laravel mampu meningkatkan keamanan, stabilitas, dan kenyamanan pengguna dalam proses pengisian form pada aplikasi web.


---

## 1. Dasar Teori
### Form Submission dan Arsitektur Request–Response

Laravel menggunakan pola arsitektur MVC (Model–View–Controller) dalam mengelola proses pengiriman data melalui form. Setiap komponen memiliki peran tersendiri sebagai berikut:
- View: Bertanggung jawab menampilkan antarmuka pengguna berupa halaman HTML dan form yang dapat diisi.
- Controller: Menerima data yang dikirim dari form, melakukan proses validasi, dan menentukan respons yang sesuai.
- Model: Berfungsi untuk berinteraksi dengan database ketika data perlu disimpan atau diambil kembali.

Alur proses pengiriman data pada form di Laravel adalah sebagai berikut:
- Pengguna mengisi form dan menekan tombol Submit.
- Browser mengirimkan permintaan HTTP (biasanya menggunakan metode POST) ke server.
- Laravel memproses permintaan tersebut melalui serangkaian middleware, termasuk pemeriksaan CSRF token sebagai lapisan keamanan.
- Controller kemudian memeriksa dan memvalidasi data yang diterima. Jika validasi berhasil, data dapat disimpan ke database atau diproses lebih lanjut. Namun, jika validasi gagal, Laravel akan mengembalikan pesan kesalahan (error message) atau mengarahkan pengguna kembali ke halaman form dengan notifikasi yang sesuai.


### HTTP Methods

Laravel menyediakan dukungan untuk berbagai jenis metode HTTP, masing-masing memiliki fungsi spesifik:
- POST → digunakan untuk membuat atau menyimpan data baru
- PUT / PATCH → digunakan untuk memperbarui data yang sudah ada
- DELETE → digunakan untuk menghapus data
- GET → digunakan untuk mengambil atau menampilkan data
Karena form HTML secara default hanya mendukung metode GET dan POST, Laravel mengatasi keterbatasan ini dengan teknik method spoofing. Melalui directive @method('PUT'), Laravel memungkinkan simulasi metode lain seperti PUT, PATCH, atau DELETE dalam form, sehingga tetap dapat mengikuti standar RESTful meskipun dibatasi oleh HTML.


### CSRF Protection

Cross-Site Request Forgery (CSRF) adalah serangan yang memanfaatkan autentikasi pengguna aktif untuk melakukan aksi tanpa izin.
Laravel melindungi setiap form dengan CSRF token:

- Token unik dibuat untuk setiap session.

- Token disertakan dalam form menggunakan `@csrf`.

- Middleware `VerifyCsrfToken` membandingkan token dari form dan session.

Jika token tidak cocok, Laravel otomatis menolak request dengan error 419.

Contoh:

    <form method="POST" action="/submit">
        @csrf
        <input type="text" name="name">
    </form>

### Data Validation

Validasi data adalah proses memastikan input pengguna memenuhi aturan tertentu. Laravel menyediakan sistem validasi yang mudah digunakan dan fleksibel.

**Jenis Validasi**

1. **Inline Validation (Controller)**

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);


2. **Form Request Validation**

    - Membuat class validasi khusus dengan `php artisan make:request`.

    - Cocok untuk form kompleks dan reuse logic.

3. **Manual Validation**

        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);

**Contoh Rules**

- **Basic**: `required`, `nullable`, `sometimes`

- **Tipe Data**: `string`, `numeric`, `boolean`, `array`

- **Ukuran**: `min`, `max`, `between`

- **Database**: `unique`, `exists`

- **Format**: `email`, `url`, `regex`

- **Kondisional**: r`equired_if`, `required_with`, dll

**Pesan Error**

Laravel menyimpan pesan error ke dalam $errors.
Di view:

    @error('email')
        <div class="text-danger">{{ $message }}</div>
    @enderror


Untuk menampilkan semua error:

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

**Custom Validation**

Laravel memungkinkan membuat rule atau pesan error kustom:

    $request->validate([
        'email' => 'required|email'
    ], [
        'email.required' => 'Email wajib diisi!',
        'email.email' => 'Format email tidak valid!'
    ]);

### Old Input

Fitur old() mempertahankan input pengguna setelah validasi gagal, sehingga form tidak kosong saat reload:

    <input type="text" name="name" value="{{ old('name') }}">

---

## 2. Langkah-Langkah Praktikum
### 2.1 Praktikum 1 – Form Submission dan Validasi Dasar

>Langkah-langkah:

1. Buat Projek laravel pada terminal vscode

        laravel new form-app

    lalu masuk ke dalam folder projek tsb.

        cd form-app

2. Tambahkan route pada routes/web.php.

        use App\Http\Controllers\FormController;

        Route::get('/form', [FormController::class, 'showForm'])->name('form.show');
        Route::post('/form', [FormController::class, 'handleForm'])->name('form.handle');
        Route::get('/result', [FormController::class, 'showResult'])->name('form.result');


3. Buat controller FormController.

   Buat file FormController dengan perintah artisan: 

        php artisan make:controller FormController

   Masuk ke file `app/Http/Controllers/FormController` dan isikan code berikut pada class FormController:

        public function showForm()
        {
            return view('form');
        }

        public function handleForm(Request $request)
        {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email',
                'age' => 'required|integer|min:1',
                'password' => 'required|min:6',
                'gender' => 'required',
                'role' => 'required',
                'bio' => 'required',
                'confirm' => 'accepted',
            ]);

            return redirect()->route('form.result')->with('data', $validated);
        }

        public function showResult()
        {
            $data = session('data');
            return view('result', compact('data'));
        }

4. Buat view form.blade.php.

   Buat file `form.blade.php` di `resources\views` lalu masukkan kode berikut:

        <!DOCTYPE html>
        <html>
        <head>
            <title>Pengiriman Form</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body class="container mt-5">
            <h2>Form Registrasi</h2>
            <form method="POST" action="{{ route('form.handle') }}">
                @csrf
                <div class="mb-3">
                    <label>Nama</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                    @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                    @error('email') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label>Umur</label>
                    <input type="number" name="age" class="form-control" value="{{ old('age') }}">
                    @error('age') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control">
                    @error('password') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label>Jenis Kelamin</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" value="male"> Laki-laki
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" value="female"> Perempuan
                    </div>
                    @error('gender') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label>Role</label>
                    <select name="role" class="form-select">
                        <option value="">-- Pilih Role --</option>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                    @error('role') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label>Bio</label>
                    <textarea name="bio" class="form-control">{{ old('bio') }}</textarea>
                    @error('bio') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" id="confirm" name="confirm" class="form-check-input">
                    <label class="form-check-label" for="confirm">Saya konfirmasi informasi sudah benar</label>
                    @error('confirm') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
                <button type="submit" class="btn btn-primary" id="submitBtn" disabled>Submit</button>
            </form>

            <script>
                document.getElementById('confirm').addEventListener('change', function() {
                    document.getElementById('submitBtn').disabled = !this.checked;
                });
            </script>
        </body>
        </html>

5. Buat view result.blade.php.

   Buat file `result.blade.php` di `resources\views` lalu masukkan kode berikut:

        <!DOCTYPE html>
        <html>
        <head>
            <title>Hasil Form</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body class="container mt-5">
            <h2>Data Form yang Dikirim</h2>
            @if ($data)
                <ul class="list-group">
                    @foreach ($data as $key => $value)
                        <li class="list-group-item"><strong>{{ ucfirst($key) }}:</strong> {{ $value }}</li>
                    @endforeach
                </ul>
            @else
                <p>Tidak ada data tersedia.</p>
            @endif
        </body>
        </html>

5. Jalankan aplikasi dan tunjukkan hasil di browser.

   Untuk menjalankan aplikasi kita bisa menggunakan perintah artisan berikut:

        php artisan serve

   lalu ctrl+klik `http://127.0.0.1:8000` sehingga akan diredirect ke web browser.
   
   pada URL masukkan `/form` agar masuk ke halaman web yang telah kita buat.

>Screenshot Hasil:
- Form
    ![Dok 1](gambar/F1.png)

- Hasil submit
    ![Dok 1](gambar/F2.png)

- Form Error (Kosong)
    ![Dok 1](gambar/F3.png)

### 2.2 Praktikum 2 – Custom Validation Message

>Langkah-langkah:

1. Pada projek form-app tambahkan route pada routes/web.php.

        use App\Http\Controllers\RegisterController;

        Route::get('/register', [RegisterController::class, 'showForm'])->name('register.show');
        Route::post('/register', [RegisterController::class, 'handleForm'])->name('register.handle');


2. Buat controller RegisterController.

   Buat file RegisterController dengan perintah artisan: 

        php artisan make:controller RegisterController

   Masuk ke file `app/Http/Controllers/RegisterController` dan isikan code berikut pada class RegisterController:

        public function showForm()
        {
            return view('register');
        }

        public function handleForm(Request $request)
        {
            $customMessages = [
                'name.required' => 'Kami perlu tahu nama Anda!',
                'email.required' => 'Email Anda penting bagi kami.',
                'email.email' => 'Hmm... itu tidak terlihat seperti email yang valid.',
                'password.required' => 'Jangan lupa untuk set password.',
                'password.min' => 'Password harus minimal :min karakter.',
                'username.regex' => 'Username hanya boleh berisi huruf dan angka.',
            ];

            $request->validate([
                'name' => 'required|string|max:100',
                'email' => 'required|email',
                'username' => ['required', 'regex:/^[a-zA-Z0-9]+$/'],
                'password' => 'required|min:6',
            ], $customMessages);

            return redirect()->route('register.show')->with('success', 'Registrasi berhasil!');
        }

3. Buat view register.blade.php.

   Buat file `register.blade.php` di `resources\views` lalu masukkan kode berikut:

        <!DOCTYPE html>
        <html>
        <head>
            <title>Form Register</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body class="container mt-5">
            <h2>Contoh Validasi Kustom</h2>
            
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('register.handle') }}">
                @csrf
                <div class="mb-3">
                    <label for="name">Nama Lengkap</label>
                    <input name="name" class="form-control" value="{{ old('name') }}">
                    @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label for="email">Alamat Email</label>
                    <input name="email" class="form-control" value="{{ old('email') }}">
                    @error('email') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label for="username">Username</label>
                    <input name="username" class="form-control" value="{{ old('username') }}">
                    @error('username') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label for="password">Password</label>
                    @error('password') <div class="text-danger">{{ $message }}</div> @enderror
                    <input type="password" name="password" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Register</button>
            </form>
        </body>
        </html>

4. Jalankan aplikasi dan tunjukkan hasil di browser.

   Untuk menjalankan aplikasi kita bisa menggunakan perintah artisan berikut:

        php artisan serve

   lalu ctrl+klik `http://127.0.0.1:8000` sehingga akan diredirect ke web browser.
   
   pada URL masukkan `/register` agar masuk ke halaman web yang telah kita buat.

>Screenshot Hasil:
- Form
    ![Dok 1](gambar/R1.png)

- Berhasil
    ![Dok 1](gambar/R2.png)

- Form Error (Kosong)
    ![Dok 1](gambar/R3.png)

- Form Error (Tidak sesuai Aturan Validasi)
    ![Dok 1](gambar/R4.png)

### 2.3 Praktikum 3 – Multi-Step Form Submission

>Langkah-langkah:

1. Buat Projek laravel pada terminal vscode

        laravel new multistep-form-app

    lalu masuk ke dalam folder projek tsb.

        cd multistep-form-app

2. Tambahkan route pada routes/web.php.

        use App\Http\Controllers\MultiStepFormController;

        Route::get('/multistep', [MultiStepFormController::class, 'showStep1'])->name('multistep.step1');
        Route::post('/multistep/step1', [MultiStepFormController::class, 'storeStep1'])->name('multistep.storeStep1');
        Route::get('/multistep/step2', [MultiStepFormController::class, 'showStep2'])->name('multistep.step2');
        Route::post('/multistep/step2', [MultiStepFormController::class, 'storeStep2'])->name('multistep.storeStep2');
        Route::get('/multistep/step3', [MultiStepFormController::class, 'showStep3'])->name('multistep.step3');
        Route::post('/multistep/step3', [MultiStepFormController::class, 'storeStep3'])->name('multistep.storeStep3');
        Route::get('/multistep/summary', [MultiStepFormController::class, 'showSummary'])->name('multistep.summary');
        Route::post('/multistep/complete', [MultiStepFormController::class, 'complete'])->name('multistep.complete');


3. Buat controller MultiStepFormController.

   Buat file MultiStepFormController dengan perintah artisan: 

        php artisan make:controller MultiStepFormController

   Masuk ke file `app/Http/Controllers/MultiStepFormController` dan isikan code berikut pada class MultiStepFormController:

        // Step 1 - Informasi Pribadi
        public function showStep1()
        {
            return view('multistep.step1', [
                'step' => 1,
                'progress' => 0
            ]);
        }

        public function storeStep1(Request $request)
        {
            $validated = $request->validate([
                'full_name' => 'required|string|max:100',
                'email' => 'required|email',
                'phone' => 'required|string|max:15',
                'address' => 'required|string|max:200',
            ]);

            // Simpan data ke session
            session(['step1_data' => $validated]);

            return redirect()->route('multistep.step2');
        }

        // Step 2 - Informasi Pendidikan
        public function showStep2()
        {
            if (!session('step1_data')) {
                return redirect()->route('multistep.step1');
            }

            return view('multistep.step2', [
                'step' => 2,
                'progress' => 33
            ]);
        }

        public function storeStep2(Request $request)
        {
            $validated = $request->validate([
                'education' => 'required|string|max:50',
                'institution' => 'required|string|max:100',
                'graduation_year' => 'required|integer|min:1900|max:' . date('Y'),
                'major' => 'required|string|max:100',
            ]);

            session(['step2_data' => $validated]);

            return redirect()->route('multistep.step3');
        }

        // Step 3 - Pengalaman Kerja
        public function showStep3()
        {
            if (!session('step1_data') || !session('step2_data')) {
                return redirect()->route('multistep.step1');
            }

            return view('multistep.step3', [
                'step' => 3,
                'progress' => 66
            ]);
        }

        public function storeStep3(Request $request)
        {
            $validated = $request->validate([
                'current_job' => 'nullable|string|max:100',
                'company' => 'nullable|string|max:100',
                'experience_years' => 'required|integer|min:0|max:50',
                'skills' => 'required|string|max:200',
            ]);

            session(['step3_data' => $validated]);

            return redirect()->route('multistep.summary');
        }

        // Summary - Ringkasan Data
        public function showSummary()
        {
            $step1Data = session('step1_data');
            $step2Data = session('step2_data');
            $step3Data = session('step3_data');

            if (!$step1Data || !$step2Data || !$step3Data) {
                return redirect()->route('multistep.step1');
            }

            return view('multistep.summary', [
                'step' => 4,
                'progress' => 100,
                'step1Data' => $step1Data,
                'step2Data' => $step2Data,
                'step3Data' => $step3Data
            ]);
        }

        // Complete - Proses Final
        public function complete(Request $request)
        {
            // Di sini Anda bisa menyimpan data ke database
            // Untuk demo, kita hanya akan menampilkan pesan sukses

            $allData = [
                'personal' => session('step1_data'),
                'education' => session('step2_data'),
                'experience' => session('step3_data')
            ];

            // Hapus session data
            $request->session()->forget(['step1_data', 'step2_data', 'step3_data']);

            return view('multistep.complete', [
                'data' => $allData
            ]);
        }

4. Buat layouts directory di `resources\views`
        mkdir resources/views/layouts


5. Buat file `app.blade.php` di `resources\views\layouts` lalu masukkan kode berikut:

        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>@yield('title', 'Multi-Step Form')</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                .step-progress {
                    margin-bottom: 30px;
                }
                .step-item {
                    text-align: center;
                    position: relative;
                }
                .step-item .step-number {
                    width: 40px;
                    height: 40px;
                    border-radius: 50%;
                    background: #e9ecef;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin: 0 auto 10px;
                    font-weight: bold;
                }
                .step-item.active .step-number {
                    background: #007bff;
                    color: white;
                }
                .step-item.completed .step-number {
                    background: #28a745;
                    color: white;
                }
                .step-line {
                    position: absolute;
                    top: 20px;
                    left: 50%;
                    width: 100%;
                    height: 2px;
                    background: #e9ecef;
                    z-index: -1;
                }
                .form-step {
                    display: none;
                }
                .form-step.active {
                    display: block;
                }
                .card {
                    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
                }
            </style>
        </head>
        <body>
            <nav class="navbar navbar-dark bg-dark">
                <div class="container">
                    <span class="navbar-brand mb-0 h1">Multi-Step Form Demo</span>
                </div>
            </nav>

            <div class="container mt-4">
                @yield('content')
            </div>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        </body>
        </html>

4. Buat multistep directory di `resources\views`
        mkdir resources/views/multistep

5. Buat file `step1.blade.php` di `resources\views\multistep` lalu masukkan kode berikut:

        @extends('layouts.app')

        @section('title', 'Step 1 - Informasi Pribadi')

        @section('content')
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Progress Bar -->
                <div class="step-progress">
                    <div class="row">
                        <div class="col-3 step-item active">
                            <div class="step-number">1</div>
                            <div>Informasi Pribadi</div>
                        </div>
                        <div class="col-3 step-item">
                            <div class="step-number">2</div>
                            <div>Pendidikan</div>
                        </div>
                        <div class="col-3 step-item">
                            <div class="step-number">3</div>
                            <div>Pengalaman</div>
                        </div>
                        <div class="col-3 step-item">
                            <div class="step-number">4</div>
                            <div>Ringkasan</div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Step 1: Informasi Pribadi</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('multistep.storeStep1') }}">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="full_name" class="form-label">Nama Lengkap *</label>
                                    <input type="text" class="form-control @error('full_name') is-invalid @enderror" 
                                        id="full_name" name="full_name" 
                                        value="{{ old('full_name', session('step1_data.full_name') ?? '') }}" 
                                        required>
                                    @error('full_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                        id="email" name="email" 
                                        value="{{ old('email', session('step1_data.email') ?? '') }}" 
                                        required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Nomor Telepon *</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                        id="phone" name="phone" 
                                        value="{{ old('phone', session('step1_data.phone') ?? '') }}" 
                                        required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="address" class="form-label">Alamat *</label>
                                    <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                        id="address" name="address" 
                                        value="{{ old('address', session('step1_data.address') ?? '') }}" 
                                        required>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <div></div> <!-- Spacer -->
                                <button type="submit" class="btn btn-primary">
                                    Lanjut ke Step 2 <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endsection

6. Buat file `step2.blade.php` di `resources\views\multistep` lalu masukkan kode berikut:

        @extends('layouts.app')

        @section('title', 'Step 2 - Informasi Pendidikan')

        @section('content')
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Progress Bar -->
                <div class="step-progress">
                    <div class="row">
                        <div class="col-3 step-item completed">
                            <div class="step-number">✓</div>
                            <div>Informasi Pribadi</div>
                        </div>
                        <div class="col-3 step-item active">
                            <div class="step-number">2</div>
                            <div>Pendidikan</div>
                        </div>
                        <div class="col-3 step-item">
                            <div class="step-number">3</div>
                            <div>Pengalaman</div>
                        </div>
                        <div class="col-3 step-item">
                            <div class="step-number">4</div>
                            <div>Ringkasan</div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Step 2: Informasi Pendidikan</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('multistep.storeStep2') }}">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="education" class="form-label">Tingkat Pendidikan *</label>
                                <select class="form-select @error('education') is-invalid @enderror" 
                                        id="education" name="education" required>
                                    <option value="">Pilih Tingkat Pendidikan</option>
                                    <option value="SMA" {{ old('education', session('step2_data.education') ?? '') == 'SMA' ? 'selected' : '' }}>SMA</option>
                                    <option value="D3" {{ old('education', session('step2_data.education') ?? '') == 'D3' ? 'selected' : '' }}>D3</option>
                                    <option value="S1" {{ old('education', session('step2_data.education') ?? '') == 'S1' ? 'selected' : '' }}>S1</option>
                                    <option value="S2" {{ old('education', session('step2_data.education') ?? '') == 'S2' ? 'selected' : '' }}>S2</option>
                                    <option value="S3" {{ old('education', session('step2_data.education') ?? '') == 'S3' ? 'selected' : '' }}>S3</option>
                                </select>
                                @error('education')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="institution" class="form-label">Nama Institusi *</label>
                                    <input type="text" class="form-control @error('institution') is-invalid @enderror" 
                                        id="institution" name="institution" 
                                        value="{{ old('institution', session('step2_data.institution') ?? '') }}" 
                                        required>
                                    @error('institution')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="graduation_year" class="form-label">Tahun Lulus *</label>
                                    <input type="number" class="form-control @error('graduation_year') is-invalid @enderror" 
                                        id="graduation_year" name="graduation_year" 
                                        value="{{ old('graduation_year', session('step2_data.graduation_year') ?? '') }}" 
                                        min="1900" max="{{ date('Y') }}" required>
                                    @error('graduation_year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="major" class="form-label">Jurusan *</label>
                                <input type="text" class="form-control @error('major') is-invalid @enderror" 
                                    id="major" name="major" 
                                    value="{{ old('major', session('step2_data.major') ?? '') }}" 
                                    required>
                                @error('major')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('multistep.step1') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    Lanjut ke Step 3 <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endsection

7. Buat file `step3.blade.php` di `resources\views\multistep` lalu masukkan kode berikut:

        @extends('layouts.app')

        @section('title', 'Step 3 - Pengalaman Kerja')

        @section('content')
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Progress Bar -->
                <div class="step-progress">
                    <div class="row">
                        <div class="col-3 step-item completed">
                            <div class="step-number">✓</div>
                            <div>Informasi Pribadi</div>
                        </div>
                        <div class="col-3 step-item completed">
                            <div class="step-number">✓</div>
                            <div>Pendidikan</div>
                        </div>
                        <div class="col-3 step-item active">
                            <div class="step-number">3</div>
                            <div>Pengalaman</div>
                        </div>
                        <div class="col-3 step-item">
                            <div class="step-number">4</div>
                            <div>Ringkasan</div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Step 3: Pengalaman Kerja</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('multistep.storeStep3') }}">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="current_job" class="form-label">Pekerjaan Saat Ini</label>
                                    <input type="text" class="form-control @error('current_job') is-invalid @enderror" 
                                        id="current_job" name="current_job" 
                                        value="{{ old('current_job', session('step3_data.current_job') ?? '') }}">
                                    @error('current_job')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="company" class="form-label">Perusahaan</label>
                                    <input type="text" class="form-control @error('company') is-invalid @enderror" 
                                        id="company" name="company" 
                                        value="{{ old('company', session('step3_data.company') ?? '') }}">
                                    @error('company')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="experience_years" class="form-label">Pengalaman Kerja (Tahun) *</label>
                                    <input type="number" class="form-control @error('experience_years') is-invalid @enderror" 
                                        id="experience_years" name="experience_years" 
                                        value="{{ old('experience_years', session('step3_data.experience_years') ?? '') }}" 
                                        min="0" max="50" required>
                                    @error('experience_years')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="skills" class="form-label">Keahlian/Keterampilan *</label>
                                <textarea class="form-control @error('skills') is-invalid @enderror" 
                                        id="skills" name="skills" rows="3" 
                                        placeholder="Contoh: PHP, Laravel, JavaScript, MySQL..." 
                                        required>{{ old('skills', session('step3_data.skills') ?? '') }}</textarea>
                                @error('skills')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Pisahkan setiap keahlian dengan koma</div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('multistep.step2') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    Lihat Ringkasan <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endsection

8. Buat file `complete.blade.php` di `resources\views\multistep` lalu masukkan kode berikut:

        @extends('layouts.app')

        @section('title', 'Pendaftaran Selesai')

        @section('content')
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card text-center">
                    <div class="card-body py-5">
                        <div class="mb-4">
                            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        </div>
                        <h2 class="card-title text-success mb-3">Pendaftaran Berhasil!</h2>
                        <p class="card-text lead mb-4">
                            Terima kasih telah melengkapi formulir pendaftaran. Data Anda telah berhasil disimpan.
                        </p>
                        
                        <div class="alert alert-info text-start">
                            <h6><i class="fas fa-envelope me-2"></i>Informasi Penting:</h6>
                            <ul class="mb-0">
                                <li>Anda akan menerima email konfirmasi dalam 24 jam</li>
                                <li>Proses verifikasi membutuhkan waktu 2-3 hari kerja</li>
                                <li>Tim kami akan menghubungi Anda untuk langkah selanjutnya</li>
                            </ul>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('multistep.step1') }}" class="btn btn-primary me-2">
                                <i class="fas fa-plus me-2"></i>Daftar Lagi
                            </a>
                            <a href="/" class="btn btn-outline-secondary">
                                <i class="fas fa-home me-2"></i>Kembali ke Home
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Debug Data (bisa dihapus di production) -->
                @if(env('APP_DEBUG'))
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0">Data yang Disimpan (Debug)</h6>
                    </div>
                    <div class="card-body">
                        <pre>{{ json_encode($data, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endsection

9. Buat file `summary.blade.php` di `resources\views\multistep` lalu masukkan kode berikut:

        @extends('layouts.app')

        @section('title', 'Ringkasan Data')

        @section('content')
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Progress Bar -->
                <div class="step-progress">
                    <div class="row">
                        <div class="col-3 step-item completed">
                            <div class="step-number">✓</div>
                            <div>Informasi Pribadi</div>
                        </div>
                        <div class="col-3 step-item completed">
                            <div class="step-number">✓</div>
                            <div>Pendidikan</div>
                        </div>
                        <div class="col-3 step-item completed">
                            <div class="step-number">✓</div>
                            <div>Pengalaman</div>
                        </div>
                        <div class="col-3 step-item active">
                            <div class="step-number">4</div>
                            <div>Ringkasan</div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">Ringkasan Data Anda</h4>
                    </div>
                    <div class="card-body">
                        <!-- Informasi Pribadi -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2">
                                <i class="fas fa-user me-2"></i>Informasi Pribadi
                                <a href="{{ route('multistep.step1') }}" class="btn btn-sm btn-outline-primary float-end">Edit</a>
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Nama Lengkap:</strong><br>
                                    {{ $step1Data['full_name'] }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Email:</strong><br>
                                    {{ $step1Data['email'] }}
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <strong>Telepon:</strong><br>
                                    {{ $step1Data['phone'] }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Alamat:</strong><br>
                                    {{ $step1Data['address'] }}
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Pendidikan -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2">
                                <i class="fas fa-graduation-cap me-2"></i>Informasi Pendidikan
                                <a href="{{ route('multistep.step2') }}" class="btn btn-sm btn-outline-primary float-end">Edit</a>
                            </h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Tingkat Pendidikan:</strong><br>
                                    {{ $step2Data['education'] }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Institusi:</strong><br>
                                    {{ $step2Data['institution'] }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Tahun Lulus:</strong><br>
                                    {{ $step2Data['graduation_year'] }}
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-12">
                                    <strong>Jurusan:</strong><br>
                                    {{ $step2Data['major'] }}
                                </div>
                            </div>
                        </div>

                        <!-- Pengalaman Kerja -->
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2">
                                <i class="fas fa-briefcase me-2"></i>Pengalaman Kerja
                                <a href="{{ route('multistep.step3') }}" class="btn btn-sm btn-outline-primary float-end">Edit</a>
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Pekerjaan Saat Ini:</strong><br>
                                    {{ $step3Data['current_job'] ?? 'Tidak disebutkan' }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Perusahaan:</strong><br>
                                    {{ $step3Data['company'] ?? 'Tidak disebutkan' }}
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <strong>Pengalaman Kerja:</strong><br>
                                    {{ $step3Data['experience_years'] }} tahun
                                </div>
                                <div class="col-md-6">
                                    <strong>Keahlian:</strong><br>
                                    {{ $step3Data['skills'] }}
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Perhatian:</strong> Pastikan semua data sudah benar sebelum mengirim.
                        </div>

                        <form method="POST" action="{{ route('multistep.complete') }}">
                            @csrf
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('multistep.step3') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check me-2"></i>Kirim Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endsection

10. Jalankan aplikasi dan tunjukkan hasil di browser.

   Untuk menjalankan aplikasi kita bisa menggunakan perintah artisan berikut:

        php artisan serve

   lalu ctrl+klik `http://127.0.0.1:8000` sehingga akan diredirect ke web browser.
   
   pada URL masukkan `/multistep` agar masuk ke halaman web yang telah kita buat.

>Screenshot Hasil:
- Form Step 1
    ![Dok 1](gambar/M1.png)

- Form Step 2
    ![Dok 1](gambar/M2.png)

- Form Step 3
    ![Dok 1](gambar/M3.png)

- Ringkasan data Form (Sebelum submit)
    ![Dok 1](gambar/M4.png)

- Selesai submit
    ![Dok 1](gambar/M5.png)

---

## 3. Hasil dan Pembahasan

- Semua form berhasil diproses dengan validasi sesuai rule yang ditentukan.

- Kesalahan input langsung ditangani oleh sistem validasi Laravel dengan pesan error yang jelas.

- CSRF protection bekerja dengan baik, mencegah request tanpa token yang sah.

- Fitur old() memudahkan pengguna karena input sebelumnya tidak hilang saat validasi gagal.

- Pada multi-step form, session digunakan untuk menjaga data antar langkah dengan lancar.

---

## 4. Kesimpulan

1. Laravel menyediakan sistem form handling yang kuat dan aman, dilengkapi dengan fitur validasi otomatis serta perlindungan terhadap serangan CSRF (Cross-Site Request Forgery).

2. Mekanisme validasi data pada Laravel sangat fleksibel — dapat dilakukan secara langsung di dalam controller untuk kasus sederhana, atau dipisahkan ke dalam Form Request agar kode lebih terstruktur dan mudah dikelola.

3. Penerapan multi-step form dengan bantuan session membuktikan bahwa Laravel mampu menangani alur input data yang kompleks secara efisien, tanpa kehilangan data antar-tahapan.

4. Kombinasi antara validasi yang ketat, umpan balik kesalahan yang jelas (error feedback), serta antarmuka form yang ramah pengguna menjadikan Laravel sebagai salah satu framework yang unggul dalam membangun aplikasi web modern dengan pengalaman pengguna yang optimal.

---

## 5. Referensi

- Laravel Official Documentation — https://laravel.com/docs/12.x/validation

- Modul 2 - Laravel Fundamentals - https://hackmd.io/@mohdrzu/HJWzYp7Reg

---