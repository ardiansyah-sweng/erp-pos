@extends('layouts.app')

@section('title', 'Data Member')
@section('breadcrumb-prefix', 'Manajemen')
@section('breadcrumb', 'Member')

@section('content')

<style>
    .input-error {
        border-color: #f43f5e !important;
    }
    .field-error {
        margin-top: 4px;
        font-size: 12px;
        color: #fb7185;
    }
</style>
<div class="p-8">

<!-- ===================== HEADER BANNER ===================== -->

<div class="relative overflow-hidden rounded-3xl border border-cyan-500/30 bg-gradient-to-br from-[#0a1628] via-[#0c1a2e] to-[#08111f] p-8 shadow-[0_0_40px_-10px_rgba(34,211,238,.25)]">

    <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">

        <div>

            <p class="text-sm font-semibold text-cyan-400">ERP POS</p>

            <h1 class="mt-1 text-4xl font-extrabold text-white">Data Member</h1>

            <p class="mt-2 text-slate-400">Kelola seluruh member toko dengan mudah.</p>

        </div>

        <div class="flex items-center gap-6">

            <div class="text-right">

                <p id="bannerDate" class="text-sm text-slate-300"></p>

                <p id="bannerTime" class="text-3xl font-bold text-cyan-300"></p>

            </div>

            <a href="{{ url('/pos') }}" class="flex items-center gap-2 rounded-xl border border-cyan-500/40 px-5 py-3 font-semibold text-cyan-300 hover:bg-cyan-500/10 transition">
                <i data-lucide="arrow-left" class="w-[18px] h-[18px]"></i>
                Kembali ke Kasir
            </a>

        </div>

    </div>

</div>

<!-- ===================== STAT CARDS ===================== -->

<div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-5">

    <div class="rounded-2xl border border-cyan-500/30 bg-[#0a1424] p-5">

        <div class="flex items-center gap-3">
            <div class="rounded-xl bg-cyan-500/15 p-2.5">
                <i data-lucide="users" class="w-5 h-5 text-cyan-300"></i>
            </div>
            <p class="text-slate-300">Total Member</p>
        </div>

        <h2 id="totalMember" class="mt-3 text-3xl font-bold text-white">0</h2>

        <p class="mt-1 text-xs text-slate-400">Semua member terdaftar</p>

    </div>

    <div class="rounded-2xl border border-emerald-500/30 bg-[#0a1d18] p-5">

        <div class="flex items-center gap-3">
            <div class="rounded-xl bg-emerald-500/15 p-2.5">
                <i data-lucide="user" class="w-5 h-5 text-emerald-300"></i>
            </div>
            <p class="text-slate-300">Regular</p>
        </div>

        <h2 id="regularMember" class="mt-3 text-3xl font-bold text-emerald-300">0</h2>

        <p class="mt-1 text-xs text-slate-400">Member level Regular</p>

    </div>

    <div class="rounded-2xl border border-slate-400/30 bg-[#11151c] p-5">

        <div class="flex items-center gap-3">
            <div class="rounded-xl bg-slate-400/15 p-2.5">
                <i data-lucide="medal" class="w-5 h-5 text-slate-200"></i>
            </div>
            <p class="text-slate-300">Silver</p>
        </div>

        <h2 id="silverMember" class="mt-3 text-3xl font-bold text-slate-200">0</h2>

        <p class="mt-1 text-xs text-slate-400">Member level Silver</p>

    </div>

    <div class="rounded-2xl border border-amber-500/30 bg-[#1d1709] p-5">

        <div class="flex items-center gap-3">
            <div class="rounded-xl bg-amber-500/15 p-2.5">
                <i data-lucide="crown" class="w-5 h-5 text-amber-300"></i>
            </div>
            <p class="text-slate-300">Gold</p>
        </div>

        <h2 id="goldMember" class="mt-3 text-3xl font-bold text-amber-300">0</h2>

        <p class="mt-1 text-xs text-slate-400">Member level Gold</p>

    </div>

    <div class="rounded-2xl border border-cyan-500/30 bg-[#0a1424] p-5">

        <div class="flex items-center gap-3">
            <div class="rounded-xl bg-cyan-500/15 p-2.5">
                <i data-lucide="star" class="w-5 h-5 text-cyan-300"></i>
            </div>
            <p class="text-slate-300">Total Poin</p>
        </div>

        <h2 id="totalPoint" class="mt-3 text-3xl font-bold text-cyan-300">0</h2>

        <p class="mt-1 text-xs text-slate-400">Akumulasi seluruh poin</p>

    </div>

</div>

<!-- ===================== TAMBAH MEMBER ===================== -->

<div class="card mt-6 p-8">

    <div class="flex items-center gap-4">

        <div class="rounded-xl bg-cyan-500/15 p-3">
            <i data-lucide="user-plus" class="w-5 h-5 text-cyan-300"></i>
        </div>

        <div>
            <h2 class="text-xl font-bold">Tambah Member Baru</h2>
            <p class="text-sm text-slate-400">Lengkapi data pelanggan untuk menjadi member.</p>
        </div>

    </div>

    <div class="grid grid-cols-1 gap-5 mt-6 md:grid-cols-2 lg:grid-cols-4">

        <div>
            <label class="mb-2 block text-sm text-slate-300">Nama Member</label>
            <div class="relative">
                <i data-lucide="user" class="input-icon w-[18px] h-[18px]"></i>
                <input id="name" class="input" placeholder="Masukkan nama member">
            </div>
        </div>

        <div>
            <label class="mb-2 block text-sm text-slate-300">Nomor HP</label>
            <div class="relative">
                <i data-lucide="phone" class="input-icon w-[18px] h-[18px]"></i>
                <input id="phone" class="input" placeholder="08xxxxxxxxxx">
            </div>
        </div>

        <div>
            <label class="mb-2 block text-sm text-slate-300">Email</label>
            <div class="relative">
                <i data-lucide="mail" class="input-icon w-[18px] h-[18px]"></i>
                <input id="email" class="input" placeholder="email@gmail.com">
            </div>
        </div>

        <div>
            <label class="mb-2 block text-sm text-slate-300">Alamat</label>
            <div class="relative">
                <i data-lucide="map-pin" class="input-icon w-[18px] h-[18px]"></i>
                <input id="address" class="input" placeholder="Alamat lengkap">
            </div>
        </div>

    </div>

    <div class="mt-6 flex justify-end">

        <button id="saveMember" class="flex items-center gap-2 rounded-xl bg-cyan-500 hover:bg-cyan-400 px-6 py-3 font-semibold text-slate-950 transition">
            <i data-lucide="save" class="w-[18px] h-[18px]"></i>
            Simpan Member
        </button>

    </div>

</div>

<!-- ===================== DAFTAR MEMBER ===================== -->

<div class="card mt-6 p-8">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h2 class="text-xl font-bold">Daftar Member</h2>
            <p class="text-sm text-slate-400">Seluruh data member yang terdaftar.</p>
        </div>

        <div class="flex items-center gap-2">

            <div class="relative sm:w-64">
                <i data-lucide="search" class="input-icon w-[18px] h-[18px]"></i>
                <input type="text" id="searchMember" placeholder="Cari member..." class="input">
            </div>

            <span id="memberCountBadge" class="flex items-center gap-2 whitespace-nowrap rounded-xl border border-cyan-500/30 bg-cyan-500/10 px-4 py-3 text-sm font-semibold text-cyan-300">
                <i data-lucide="users" class="w-4 h-4"></i>
                0 Member
            </span>

        </div>

    </div>

    <div class="mt-6 overflow-x-auto rounded-2xl border border-white/10">

        <table class="w-full min-w-[860px]">

            <thead class="bg-cyan-500/10 text-cyan-300">

                <tr>
                    <th class="p-4 text-left">Kode</th>
                    <th class="text-left">Nama</th>
                    <th class="text-left">No HP</th>
                    <th class="text-left">Email</th>
                    <th class="text-left">Level</th>
                    <th class="text-left">Poin</th>
                    <th class="text-left">Tanggal Daftar</th>
                    <th class="text-right pr-4">Aksi</th>
                </tr>

            </thead>

            <tbody id="memberTable" class="divide-y divide-white/10">

            </tbody>

        </table>

    </div>

</div>
</div>

@endsection

<!-- ===================== TOAST NOTIFICATION ===================== -->
<div id="toast"
     class="fixed top-4 right-4 z-[100] hidden rounded-2xl border px-5 py-3 text-sm font-medium shadow-2xl backdrop-blur transition-opacity duration-300">
</div>

<!-- ===================== MODAL EDIT MEMBER ===================== -->
<div id="editModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/70 px-4 py-6 backdrop-blur-sm">
    <div class="w-full max-w-md rounded-3xl border border-white/10 bg-slate-950 p-6 text-slate-100 shadow-2xl shadow-black/40">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-xs uppercase tracking-[0.3em] text-cyan-300/70">Edit Member</p>
                <h2 class="mt-1 text-xl font-semibold text-white">Ubah Data Member</h2>
            </div>
            <button id="closeEditModal" type="button"
                    class="rounded-full border border-white/10 bg-white/5 px-3 py-1.5 text-sm text-slate-300 transition hover:text-white">
                Tutup
            </button>
        </div>
        <div class="mt-5 space-y-4">
            <div>
                <label class="mb-2 block text-sm text-slate-300">Nama Member</label>
                <div class="relative">
                    <i data-lucide="user" class="input-icon w-[18px] h-[18px]"></i>
                    <input id="editName" class="input" placeholder="Masukkan nama member">
                </div>
            </div>
            <div>
                <label class="mb-2 block text-sm text-slate-300">Nomor HP</label>
                <div class="relative">
                    <i data-lucide="phone" class="input-icon w-[18px] h-[18px]"></i>
                    <input id="editPhone" class="input" placeholder="08xxxxxxxxxx">
                </div>
            </div>
            <div>
                <label class="mb-2 block text-sm text-slate-300">Email</label>
                <div class="relative">
                    <i data-lucide="mail" class="input-icon w-[18px] h-[18px]"></i>
                    <input id="editEmail" class="input" placeholder="email@gmail.com">
                </div>
            </div>
            <div>
                <label class="mb-2 block text-sm text-slate-300">Alamat</label>
                <div class="relative">
                    <i data-lucide="map-pin" class="input-icon w-[18px] h-[18px]"></i>
                    <textarea id="editAddress" class="input min-h-[80px] resize-none" placeholder="Alamat lengkap"></textarea>
                </div>
            </div>
        </div>
        <div class="mt-6 flex flex-col gap-2">
            <button id="saveEditBtn" type="button"
                    class="flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-emerald-400 to-cyan-400 px-4 py-3 font-semibold text-slate-950 transition hover:brightness-110">
                <i data-lucide="save" class="w-[18px] h-[18px]"></i>
                Simpan Perubahan
            </button>
            <button id="cancelEditBtn" type="button"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-sm text-slate-300 transition hover:text-white">
                Batal
            </button>
        </div>
    </div>
</div>

@push('scripts')

<script>

const API = "/customers";

const memberTable = document.getElementById("memberTable");

const totalMember = document.getElementById("totalMember");
const regularMember = document.getElementById("regularMember");
const silverMember = document.getElementById("silverMember");
const goldMember = document.getElementById("goldMember");
const totalPoint = document.getElementById("totalPoint");
const memberCountBadge = document.getElementById("memberCountBadge");

let editingId = null;

function showToast(message, type = "success") {
    const toast = document.getElementById("toast");
    toast.textContent = message;
    toast.className =
        "fixed top-4 right-4 z-[100] rounded-2xl border px-5 py-3 text-sm font-medium shadow-2xl backdrop-blur transition-opacity duration-300 " +
        (type === "success"
            ? "border-emerald-400/30 bg-emerald-400/10 text-emerald-100"
            : "border-rose-400/30 bg-rose-400/10 text-rose-100");
    toast.classList.remove("hidden");
    setTimeout(() => toast.classList.add("hidden"), 3000);
}

function openModal(id) {
    document.getElementById(id).classList.remove("hidden");
    document.getElementById(id).classList.add("flex");
    document.body.style.overflow = "hidden";
}

function showFieldError(inputId, message) {
    const input = document.getElementById(inputId);
    const existing = input.parentElement.nextElementSibling;
    if (existing && existing.classList.contains("field-error") && existing.dataset.field === inputId) {
        existing.textContent = "⚠ " + message;
        return;
    }
    input.classList.add("input-error");
    const error = document.createElement("p");
    error.className = "field-error";
    error.dataset.field = inputId;
    error.textContent = "⚠ " + message;
    input.parentElement.after(error);
}

function clearFieldErrors() {
    document.querySelectorAll(".field-error").forEach(el => el.remove());
    document.querySelectorAll(".input-error").forEach(el => el.classList.remove("input-error"));
}

function removeFieldError(fieldId) {
    const input = document.getElementById(fieldId);
    const error = input.parentElement.nextElementSibling;
    if (error && error.classList.contains("field-error") && error.dataset.field === fieldId) {
        error.remove();
        input.classList.remove("input-error");
    }
}

function validateNameRealtime() {
    const input = document.getElementById("name");
    const value = input.value.trim();
    if (value === "") return;
    if (value.length < 2) {
        showFieldError("name", "Nama harus diisi minimal 2 karakter.");
    } else {
        removeFieldError("name");
    }
}

function validatePhoneRealtime() {
    const input = document.getElementById("phone");
    input.value = input.value.replace(/\D/g, "");
    const value = input.value.trim();
    if (value === "") return;
    if (!/^08\d{8,13}$/.test(value)) {
        showFieldError("phone", "Nomor HP harus diawali 08 dan minimal 10 digit.");
    } else {
        removeFieldError("phone");
    }
}

function validateEmailRealtime() {
    const input = document.getElementById("email");
    const value = input.value.trim();
    if (value === "") return;
    if (!value.includes("@")) {
        showFieldError("email", "Masukkan email yang valid (contoh: email@domain.com).");
    } else {
        removeFieldError("email");
    }
}

function closeModal(id) {
    document.getElementById(id).classList.add("hidden");
    document.getElementById(id).classList.remove("flex");
    document.body.style.overflow = "";
}

function bannerClock(){

    const now = new Date();

    document.getElementById("bannerDate").textContent =
        now.toLocaleDateString("id-ID", { weekday:"long", day:"2-digit", month:"long", year:"numeric" });

    document.getElementById("bannerTime").textContent =
        now.toLocaleTimeString("id-ID", { hour:"2-digit", minute:"2-digit", second:"2-digit" });

}

bannerClock();
setInterval(bannerClock, 1000);

function levelBadge(level){

    if(level == "Gold"){
        return '<span class="rounded-full bg-amber-500/15 text-amber-300 px-3 py-1 text-xs">Gold</span>';
    }

    if(level == "Silver"){
        return '<span class="rounded-full bg-slate-400/15 text-slate-200 px-3 py-1 text-xs">Silver</span>';
    }

    return '<span class="rounded-full bg-emerald-500/15 text-emerald-300 px-3 py-1 text-xs">Regular</span>';

}

async function loadCustomers(keyword = ""){

    const response = await fetch(

        API + (keyword ? "?search=" + encodeURIComponent(keyword) : "")

    );

    const result = await response.json();

    const customers = result.data ?? [];

    memberTable.innerHTML = "";

    totalMember.textContent = customers.length;

    regularMember.textContent =
        customers.filter(c=>c.member_level=="Regular").length;

    silverMember.textContent =
        customers.filter(c=>c.member_level=="Silver").length;

    goldMember.textContent =
        customers.filter(c=>c.member_level=="Gold").length;

    totalPoint.textContent =
        customers.reduce((a,b)=>a+Number(b.points),0);

    memberCountBadge.innerHTML =
        '<i data-lucide="users" class="w-4 h-4"></i>' + customers.length + ' Member';

    if(customers.length === 0){

        memberTable.innerHTML = `

        <tr>
            <td colspan="8" class="p-6 text-center text-slate-400">
                Belum ada member. Tambahkan member baru di atas.
            </td>
        </tr>

        `;

        lucide.createIcons();

        return;

    }

    customers.forEach(customer=>{

        const joined = customer.created_at

            ? new Date(customer.created_at).toLocaleDateString("id-ID", { day:"2-digit", month:"long", year:"numeric" })

            : "-";

        memberTable.innerHTML += `

        <tr class="hover:bg-slate-800/60 transition">

            <td class="p-4 font-semibold text-cyan-300">${customer.customer_code}</td>

            <td>${customer.name}</td>

            <td>${customer.phone}</td>

            <td>${customer.email ?? "-"}</td>

            <td>${levelBadge(customer.member_level)}</td>

            <td>⭐ ${customer.points}</td>

            <td class="text-slate-400">${joined}</td>

            <td class="text-right pr-4">

                <button class="btn-edit rounded-lg border border-cyan-500/30 p-2 text-cyan-300 hover:bg-cyan-500/10" data-id="${customer.id}">
                    <i data-lucide="pencil" class="w-4 h-4"></i>
                </button>

                <button class="btn-delete ml-2 rounded-lg border border-rose-500/30 p-2 text-rose-400 hover:bg-rose-500/10" data-id="${customer.id}">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>

            </td>

        </tr>

        `;

    });

    lucide.createIcons();

}

document.getElementById("name").addEventListener("input", validateNameRealtime);
document.getElementById("phone").addEventListener("input", validatePhoneRealtime);
document.getElementById("email").addEventListener("input", validateEmailRealtime);

document.getElementById("saveMember")
.addEventListener("click", async function () {
    clearFieldErrors();

    const name    = document.getElementById("name").value.trim();
    const phone   = document.getElementById("phone").value.trim();
    const email   = document.getElementById("email").value.trim();
    const address = document.getElementById("address").value.trim();

    let valid = true;

    if (!name || name.length < 2) {
        showFieldError("name", "Nama harus diisi minimal 2 karakter.");
        valid = false;
    }

    if (!phone) {
        showFieldError("phone", "Nomor HP wajib diisi.");
        valid = false;
    } else if (!/^08\d{8,13}$/.test(phone)) {
        showFieldError("phone", "Nomor HP harus diawali 08 dan minimal 10 digit.");
        valid = false;
    }

    if (email && !email.includes("@")) {
        showFieldError("email", "Masukkan email yang valid (contoh: email@domain.com).");
        valid = false;
    }

    if (!valid) return;

    const response = await fetch("/customers",{
        method:"POST",
        headers:{
            "Content-Type":"application/json",
            "Accept":"application/json",
            "X-CSRF-TOKEN":"{{ csrf_token() }}"
        },
        body:JSON.stringify({ name, phone, email, address })
    });

    const result = await response.json();

    if(result.success){
        showToast("Member berhasil ditambahkan!", "success");
        document.getElementById("name").value="";
        document.getElementById("phone").value="";
        document.getElementById("email").value="";
        document.getElementById("address").value="";
        loadCustomers();
    }else{
        showToast(result.message ?? "Gagal menambahkan member.", "error");
    }
});

memberTable.addEventListener("click", async function(e){

    const editBtn = e.target.closest(".btn-edit");
    const deleteBtn = e.target.closest(".btn-delete");

    if(editBtn){
        const id = editBtn.dataset.id;

        const res = await fetch("/customers/" + id);
        const result = await res.json();
        if (!result.success) return;

        const c = result.data;
        editingId = c.id;
        document.getElementById("editName").value = c.name;
        document.getElementById("editPhone").value = c.phone;
        document.getElementById("editEmail").value = c.email ?? "";
        document.getElementById("editAddress").value = c.address ?? "";
        openModal("editModal");
    }

    if(deleteBtn){

        const id = deleteBtn.dataset.id;

        if(!confirm("Hapus member ini? Tindakan tidak bisa dibatalkan.")) return;

        const response = await fetch("/customers/" + id, {

            method:"DELETE",

            headers:{
                "Accept":"application/json",
                "X-CSRF-TOKEN":"{{ csrf_token() }}"
            }

        });

        const result = await response.json();

        if(result.success){
            loadCustomers();
        }else{
            alert(result.message ?? "Gagal menghapus member.");
        }

    }

});

document.getElementById("searchMember")
.addEventListener("keyup", function(){

    loadCustomers(this.value);

});

document.getElementById("saveEditBtn").addEventListener("click", async function(){
    const name    = document.getElementById("editName").value.trim();
    const phone   = document.getElementById("editPhone").value.trim();
    const email   = document.getElementById("editEmail").value.trim();
    const address = document.getElementById("editAddress").value.trim();

    if (!name || !phone) {
        showToast("Nama dan No HP wajib diisi.", "error");
        return;
    }

    const res = await fetch("/customers/" + editingId, {
        method: "PUT",
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ name, phone, email, address }),
    });

    const result = await res.json();

    if (result.success) {
        showToast("Member berhasil diperbarui!", "success");
        closeModal("editModal");
        loadCustomers();
    } else {
        showToast(result.message ?? "Gagal memperbarui member.", "error");
    }
});

function closeEditModal() {
    editingId = null;
    closeModal("editModal");
}

document.getElementById("closeEditModal").addEventListener("click", closeEditModal);
document.getElementById("cancelEditBtn").addEventListener("click", closeEditModal);
document.getElementById("editModal").addEventListener("click", function(e) {
    if (e.target === this) closeEditModal();
});

loadCustomers();

</script>

@endpush