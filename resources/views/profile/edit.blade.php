```php
@extends('layouts.app')

@section('content')

<style>

.profile-page{
    padding:40px 20px 80px;
}

.profile-container{
    max-width:1100px;
    margin:auto;
}

/* HEADER */

.profile-header{
    position:relative;
    overflow:hidden;

    background:
    linear-gradient(
        135deg,
        #1f4674,
        #226d71
    );

    border-radius:35px;

    padding:50px;

    color:white;

    display:flex;
    justify-content:space-between;
    align-items:center;

    margin-bottom:35px;
}

.profile-header::before{
    content:'';

    position:absolute;

    width:320px;
    height:320px;

    border-radius:50%;

    background:
    rgba(255,255,255,0.08);

    top:-120px;
    right:-120px;
}

.profile-user{
    position:relative;
    z-index:2;

    display:flex;
    align-items:center;
    gap:20px;
}

.profile-user img{
    width:110px;
    height:110px;

    border-radius:50%;
    object-fit:cover;

    border:4px solid rgba(255,255,255,0.2);
}

.profile-user h1{
    font-size:38px;
    margin-bottom:6px;
}

.profile-user p{
    opacity:0.9;
}

/* ACTION BUTTON */

.profile-actions{
    display:flex;
    gap:14px;
    position:relative;
    z-index:2;
}

.dashboard-btn{
    background:rgba(255,255,255,0.14);
    color:white;

    padding:14px 24px;

    border-radius:16px;

    text-decoration:none;

    font-weight:600;

    backdrop-filter:blur(10px);

    transition:0.3s;
}

.dashboard-btn:hover{
    transform:translateY(-3px);
}

.logout-btn{
    background:white;
    color:#d63031;

    padding:14px 24px;

    border:none;
    border-radius:16px;

    font-weight:600;
    cursor:pointer;

    transition:0.3s;
}

.logout-btn:hover{
    transform:translateY(-4px);
}

/* GRID */

.profile-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:30px;
}

/* CARD */

.profile-card{
    background:white;

    border-radius:30px;

    padding:35px;

    box-shadow:
    0 15px 40px rgba(0,0,0,0.06);
}

.profile-card.full{
    grid-column:1 / -1;
}

.card-title{
    margin-bottom:25px;
}

.card-title h2{
    font-size:30px;
    margin-bottom:10px;
}

.card-title p{
    color:#666;
    line-height:1.7;
}

/* FORM */

.form-group{
    margin-bottom:22px;
}

.form-group label{
    display:block;
    margin-bottom:10px;

    font-weight:600;
    color:#222;
}

.form-input{
    width:100%;

    padding:15px 18px;

    border-radius:16px;

    border:1px solid #dfe6e9;

    outline:none;

    font-size:15px;

    transition:0.3s;
}

.form-input:focus{
    border-color:#226d71;

    box-shadow:
    0 0 0 4px rgba(34,109,113,0.12);
}

/* BUTTON */

.save-btn{
    background:#226d71;
    color:white;

    border:none;

    padding:14px 24px;

    border-radius:16px;

    font-weight:600;

    cursor:pointer;

    transition:0.3s;
}

.save-btn:hover{
    transform:translateY(-3px);
}

/* FOTO */

.preview-photo{
    width:130px;
    height:130px;

    border-radius:50%;
    object-fit:cover;

    border:4px solid #eef2f7;

    margin-bottom:18px;
}

/* PASSWORD */

.password-box{
    background:
    linear-gradient(
        135deg,
        rgba(34,109,113,0.08),
        rgba(31,70,116,0.06)
    );

    padding:25px;

    border-radius:24px;
}

.password-box p{
    line-height:1.8;
    color:#444;
}

/* DELETE */

.delete-box{
    border:2px dashed rgba(214,48,49,0.2);

    border-radius:24px;

    padding:25px;
}

.delete-btn{
    margin-top:20px;

    background:#d63031;
    color:white;

    border:none;

    padding:14px 22px;

    border-radius:16px;

    cursor:pointer;

    font-weight:600;

    transition:0.3s;
}

.delete-btn:hover{
    transform:translateY(-3px);
}

/* RESPONSIVE */

@media(max-width:900px){

    .profile-grid{
        grid-template-columns:1fr;
    }

    .profile-header{
        flex-direction:column;
        gap:30px;
        text-align:center;
    }

    .profile-user{
        flex-direction:column;
    }

    .profile-user h1{
        font-size:30px;
    }

    .profile-actions{
        flex-direction:column;
        width:100%;
    }

}

</style>

<div class="profile-page">

    <div class="profile-container">

        <!-- HEADER -->
        <div class="profile-header">

            <div class="profile-user">

                <img
                    src="{{ Auth::user()->foto_profil
                        ? asset('storage/' . Auth::user()->foto_profil)
                        : asset('assets/pelapor/images/avatar-1.jpg') }}"
                    alt="profile"
                >

                <div>
                    <h1>{{ Auth::user()->name }}</h1>
                    <p>{{ Auth::user()->email }}</p>
                </div>

            </div>

            <!-- ACTION -->
            <div class="profile-actions">

                <!-- DASHBOARD -->
                <a
                    href="{{ route('dashboard') }}"
                    class="dashboard-btn"
                >
                    ← Dashboard
                </a>

                <!-- LOGOUT -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button class="logout-btn">
                        Logout
                    </button>
                </form>

            </div>

        </div>

        <!-- GRID -->
        <div class="profile-grid">

            <!-- PROFILE -->
            <div class="profile-card">

                <div class="card-title">
                    <h2>Profile Information</h2>

                    <p>
                        Update informasi akun dan foto profil kamu.
                    </p>
                </div>

                <form
                    method="POST"
                    action="{{ route('profile.update') }}"
                    enctype="multipart/form-data"
                >

                    @csrf
                    @method('PATCH')

                    <!-- FOTO -->
                    <div class="form-group">

                        <label>Foto Profil</label>

                        <img
                            class="preview-photo"
                            src="{{ Auth::user()->foto_profil
                                ? asset('storage/' . Auth::user()->foto_profil)
                                : asset('assets/pelapor/images/avatar-1.jpg') }}"
                        >

                        <input
                            type="file"
                            name="foto_profil"
                            class="form-input"
                        >

                    </div>

                    <!-- NAMA -->
                    <div class="form-group">

                        <label>Nama</label>

                        <input
                            type="text"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            class="form-input"
                            required
                        >

                    </div>

                    <!-- EMAIL -->
                    <div class="form-group">

                        <label>Email</label>

                        <input
                            type="email"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                            class="form-input"
                            required
                        >

                    </div>

                    <button class="save-btn">
                        Simpan Perubahan
                    </button>

                </form>

            </div>

            <!-- PASSWORD -->
            <div class="profile-card">

                <div class="card-title">
                    <h2>Update Password</h2>

                    <p>
                        Jaga keamanan akun dengan password yang kuat.
                    </p>
                </div>

                <div class="password-box">

                    <p>
                        Fitur update password sedang dalam pengembangan
                        dan akan segera tersedia di versi berikutnya.
                    </p>

                </div>

            </div>

            <!-- DELETE -->
            <div class="profile-card full">

                <div class="card-title">
                    <h2>Delete Account</h2>

                    <p>
                        Setelah akun dihapus, seluruh data akun akan
                        hilang secara permanen.
                    </p>
                </div>

                <div class="delete-box">

                    <p>
                        Pastikan kamu benar-benar yakin sebelum
                        menghapus akun.
                    </p>

                    <form
                        method="POST"
                        action="{{ route('profile.destroy') }}"
                    >

                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="delete-btn"
                            onclick="return confirm('Yakin ingin menghapus akun?')"
                        >
                            Hapus Akun
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection
```
