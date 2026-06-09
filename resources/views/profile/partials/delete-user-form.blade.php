<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Setelah akun dihapus, seluruh data akun, laporan, foto laporan, dan riwayat laporan akan hilang secara permanen.
        </p>
    </header>

    <form
        id="deleteAccountForm"
        method="POST"
        action="{{ route('profile.destroy') }}"
        class="mt-6"
    >
        @csrf
        @method('DELETE')

        <button
            type="button"
            id="deleteAccountButton"
            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none transition"
        >
            Delete Account
        </button>
    </form>
</section>