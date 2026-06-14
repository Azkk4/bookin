<div class="category-overlay" id="categoryModal">

    <div class="category-modal">

        <!-- HEADER -->
        <div class="category-top">

            <div class="category-header">
                <h3>Kelola Kategori</h3>
            </div>

            <button
                class="close-category"
                id="closeCategoryModal"
            >
                <img src="{{ asset('Flaticon/close.png') }}">
            </button>

        </div>

        <!-- CONTENT -->
        <div class="category-content">

            <!-- LEFT -->
            <div class="category-create">

                <h4>Tambah Kategori</h4>

                <input
                    type="text"
                    id="newCategoryName"
                    placeholder="Nama kategori..."
                >

                <button id="saveCategoryBtn">
                    Tambah Kategori
                </button>

                <div class="category-filter-box">

                    <label>
                        <img src="{{ asset('Flaticon/filter.png') }}" alt="">
                        Urutkan Kategori
                    </label>

                    <select id="categorySort">
                        <option value="popular">Paling Banyak Buku</option>
                        <option value="name_asc">Nama A-Z</option>
                        <option value="name_desc">Nama Z-A</option>
                        <option value="newest">Terbaru Dibuat</option>
                        <option value="oldest">Terlama Dibuat</option>
                        <option value="updated">Terakhir Diperbarui</option>
                    </select>

                </div>

            </div>

            <!-- RIGHT -->
            <div class="category-list">

                <h4>Daftar Kategori</h4>

                <div id="categoryListContainer">

                </div>

            </div>

        </div>

    </div>

</div>