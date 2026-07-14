# Plan: Fix Merge Conflict di Halaman Member

## Lokasi: `resources/views/members/index.blade.php`

### Error: Merge conflict marker `<<<<<<< HEAD` di baris 763

Menyebabkan JavaScript SyntaxError → seluruh script tidak jalan → `loadCustomers()` tidak terpanggil → tabel kosong.

---

## Perbaikan

### Hapus bagian ini (baris 763–796):

**Kode LAMA (yang harus dihapus):**
```javascript
<<<<<<< HEAD                              // ← baris 763
    if(editBtn){
        const id = editBtn.dataset.id;

        const res = await fetch("/customers/" + id);
        const result = await res.json();
        if (!result.success) return;
=======                                   // ← baris 770
   if(editBtn){

        const id = editBtn.dataset.id;

        const customer = allCustomers.find(c => String(c.id) === String(id));

        if(!customer) return;

        document.getElementById("editId").value = customer.id;
        document.getElementById("editName").value = customer.name ?? "";
        document.getElementById("editPhone").value = customer.phone ?? "";
        document.getElementById("editEmail").value = customer.email ?? "";
        document.getElementById("editAddress").value = customer.address ?? "";
        
        const modal = document.getElementById("editModal");
        modal.classList.remove("hidden");
        modal.classList.add("flex");
>>>>>>> develop                           // ← baris 788

        const c = result.data;            // ← baris 790 (dead code)
        editingId = c.id;                 // ← baris 791
        document.getElementById("editName").value = c.name;
        document.getElementById("editPhone").value = c.phone;
        document.getElementById("editEmail").value = c.email ?? "";
        document.getElementById("editAddress").value = c.address ?? "";
        openModal("editModal");           // ← baris 796
    }
```

### Ganti dengan (yang benar — versi develop saja):

```javascript
    if(editBtn){

        const id = editBtn.dataset.id;

        const customer = allCustomers.find(c => String(c.id) === String(id));

        if(!customer) return;

        document.getElementById("editId").value = customer.id;
        document.getElementById("editName").value = customer.name ?? "";
        document.getElementById("editPhone").value = customer.phone ?? "";
        document.getElementById("editEmail").value = customer.email ?? "";
        document.getElementById("editAddress").value = customer.address ?? "";
        
        const modal = document.getElementById("editModal");
        modal.classList.remove("hidden");
        modal.classList.add("flex");
        openModal("editModal");
    }
```

---

## Ringkasan Perubahan

| Baris | Aksi |
|:-----:|------|
| 763 | **Hapus** `<<<<<<< HEAD` |
| 764-769 | **Hapus** blok `fetch("/customers/" + id)` versi HEAD |
| 770 | **Hapus** `=======` separator |
| 771-787 | **PERTAHANKAN** — ini versi develop yang benar (pakai `allCustomers.find`) |
| 788 | **Hapus** `>>>>>>> develop` |
| 789-796 | **Hapus** dead code (`result.data`, `editingId = c.id`, dll) — sudah tidak dipakai |
| 796 | **PERTAHANKAN** `openModal("editModal")` — pindahkan ke dalam blok `if(editBtn)` |

---

## Setelah Fix

1. Jalankan `php artisan serve` (jika belum jalan)
2. Buka `http://127.0.0.1:8000/members`
3. 15 data member akan tampil di tabel
4. Tombol Edit akan menggunakan data dari `allCustomers` (tanpa fetch ulang)
