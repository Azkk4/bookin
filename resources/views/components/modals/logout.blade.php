<!-- LOGOUT MODAL -->
<div class="logout-modal-overlay" id="logoutModal">

  <div class="logout-modal">

    <h2>
      Anda yakin ingin keluar?
    </h2>

    <div class="logout-actions">

      <button
        class="btn-cancel"
        id="cancelLogout"
        type="button"
      >
        Batalkan
      </button>

      <form
        method="POST"
        action="{{ route('logout') }}"
      >
        @csrf

        <button
          type="submit"
          class="btn-confirm"
        >
          Keluar
        </button>
      </form>

    </div>

  </div>

</div>