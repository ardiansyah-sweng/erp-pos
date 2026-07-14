@extends('layouts.app')

@section('title', 'Data Member')
@section('breadcrumb-prefix', 'Manajemen')
@section('breadcrumb', 'Member')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css">
<script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.js"></script>
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
    <!-- ===================== MODAL EDIT MEMBER ===================== -->

<div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm">

    <div class="card w-full max-w-lg p-8">

        <div class="flex items-center gap-4">
            <div class="rounded-xl bg-cyan-500/15 p-3">
                <i data-lucide="pencil" class="w-5 h-5 text-cyan-300"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold">Edit Member</h2>
                <p class="text-sm text-slate-400">Perbarui data member.</p>
            </div>
        </div>

        <input type="hidden" id="editId">

        <div class="grid grid-cols-1 gap-5 mt-6">

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
                    <input id="editAddress" class="input" placeholder="Alamat lengkap">
                </div>
            </div>

        </div>

        <div class="mt-6 flex justify-end gap-3">

            <button id="cancelEdit" class="rounded-xl border border-white/10 px-6 py-3 font-semibold text-slate-300 hover:bg-white/5 transition">
                Batal
            </button>

            <button id="saveEdit" class="flex items-center gap-2 rounded-xl bg-cyan-500 hover:bg-cyan-400 px-6 py-3 font-semibold text-slate-950 transition">
                <i data-lucide="save" class="w-[18px] h-[18px]"></i>
                Simpan Perubahan
            </button>

        </div>

    </div>

</div>
<!-- ===================== MODAL EDIT MEMBER ===================== -->

<div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm">

    <div class="card w-full max-w-lg p-8">

        <div class="flex items-center gap-4">
            <div class="rounded-xl bg-cyan-500/15 p-3">
                <i data-lucide="pencil" class="w-5 h-5 text-cyan-300"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold">Edit Member</h2>
                <p class="text-sm text-slate-400">Perbarui data member.</p>
            </div>
        </div>

        <input type="hidden" id="editId">

        <div class="grid grid-cols-1 gap-5 mt-6">

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
                    <input id="editAddress" class="input" placeholder="Alamat lengkap">
                </div>
            </div>

        </div>

        <div class="mt-6 flex justify-end gap-3">

            <button id="cancelEdit" class="rounded-xl border border-white/10 px-6 py-3 font-semibold text-slate-300 hover:bg-white/5 transition">
                Batal
            </button>

            <button id="saveEdit" class="flex items-center gap-2 rounded-xl bg-cyan-500 hover:bg-cyan-400 px-6 py-3 font-semibold text-slate-950 transition">
                <i data-lucide="save" class="w-[18px] h-[18px]"></i>
                Simpan Perubahan
            </button>

        </div>

    </div>

</div>


</div>
</div>
<div id="editModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="rounded-2xl border border-cyan-500/30 bg-gradient-to-br from-[#0a1628] via-[#0c1a2e] to-[#08111f] p-8 shadow-2xl w-full max-w-md mx-4">
        <h2 class="text-2xl font-bold text-white mb-6">Edit Data Member</h2>
        
        <div class="space-y-4">
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
                    <input id="editAddress" class="input" placeholder="Alamat lengkap">
                </div>
            </div>
        </div>

        <div class="mt-8 flex gap-3 justify-end">
            <button id="cancelEdit" class="rounded-xl border border-slate-500/30 px-6 py-3 font-semibold text-slate-300 hover:bg-slate-500/10 transition">
                Batal
            </button>
            <button id="submitEdit" class="flex items-center gap-2 rounded-xl bg-cyan-500 hover:bg-cyan-400 px-6 py-3 font-semibold text-slate-950 transition">
                <i data-lucide="save" class="w-[18px] h-[18px]"></i>
                Simpan Perubahan
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')

<script>

const API = "/customers";

const memberTable = document.getElementById("memberTable");
let allCustomers = [];

const totalMember = document.getElementById("totalMember");
const regularMember = document.getElementById("regularMember");
const silverMember = document.getElementById("silverMember");
const goldMember = document.getElementById("goldMember");
const totalPoint = document.getElementById("totalPoint");
const memberCountBadge = document.getElementById("memberCountBadge");

const editModal = document.getElementById("editModal");
const editNameInput = document.getElementById("editName");
const editPhoneInput = document.getElementById("editPhone");
const editEmailInput = document.getElementById("editEmail");
const editAddressInput = document.getElementById("editAddress");
const submitEditBtn = document.getElementById("submitEdit");
const cancelEditBtn = document.getElementById("cancelEdit");

let currentEditId = null;

function openEditModal(id, name, phone, email, address){
    currentEditId = id;
    editNameInput.value = name || "";
    editPhoneInput.value = phone || "";
    editEmailInput.value = email || "";
    editAddressInput.value = address || "";
    editModal.classList.remove("hidden");
    editNameInput.focus();
}

function closeEditModal(){
    editModal.classList.add("hidden");
    currentEditId = null;
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

    allCustomers = customers;
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

                <button class="btn-edit rounded-lg border border-cyan-500/30 p-2 text-cyan-300 hover:bg-cyan-500/10" data-id="${customer.id}" data-name="${customer.name}" data-phone="${customer.phone}" data-email="${customer.email ?? ""}" data-address="${customer.address ?? ""}">
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

document.getElementById("saveMember")
.addEventListener("click", async function () {

    const name = document.getElementById("name").value.trim();
    const phone = document.getElementById("phone").value.trim();
    const email = document.getElementById("email").value.trim();
    const address = document.getElementById("address").value.trim();

    if(name=="" || phone==""){
        alert("Nama dan Nomor HP wajib diisi.");
        return;
    }

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

        alert("Member berhasil ditambahkan.");

        document.getElementById("name").value="";
        document.getElementById("phone").value="";
        document.getElementById("email").value="";
        document.getElementById("address").value="";

        loadCustomers();

    }else{

        alert(result.message);

    }

});

// NOTE: endpoint edit & delete di bawah ini mengikuti konvensi REST Laravel
// (PUT /customers/{id} dan DELETE /customers/{id}). Sesuaikan kalau nama
// route kamu berbeda.

memberTable.addEventListener("click", async function(e){
    const editBtn = e.target.closest(".btn-edit");
    const deleteBtn = e.target.closest(".btn-delete");
    
    if(editBtn){
        const id = editBtn.dataset.id;
        const name = editBtn.dataset.name;
        const phone = editBtn.dataset.phone;
        const email = editBtn.dataset.email;
        const address = editBtn.dataset.address;
        openEditModal(id, name, phone, email, address);

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

cancelEditBtn.addEventListener("click", closeEditModal);

submitEditBtn.addEventListener("click", async function(){
    if(!currentEditId) return;
    
    const name = editNameInput.value.trim();
    const phone = editPhoneInput.value.trim();
    const email = editEmailInput.value.trim();
    const address = editAddressInput.value.trim();
    
    if(name == "" || phone == ""){
        alert("Nama dan Nomor HP wajib diisi.");
        return;
    }
    
    const response = await fetch("/customers/" + currentEditId, {
        method:"PUT",
        headers:{
            "Content-Type":"application/json",
            "Accept":"application/json",
            "X-CSRF-TOKEN":"{{ csrf_token() }}"
        },
        body: JSON.stringify({ name, phone, email, address })
    });
    
    const result = await response.json();
    if(result.success){
        alert("Member berhasil diperbarui!");
        closeEditModal();
        loadCustomers();
    }else{
        alert(result.message ?? "Gagal mengubah member.");
    }
});

editModal.addEventListener("click", function(e){
    if(e.target === editModal){
        closeEditModal();
    }
});

document.getElementById("searchMember")
.addEventListener("keyup", function(){
    loadCustomers(this.value);
});

function closeEditModal(){

    const modal = document.getElementById("editModal");
    modal.classList.add("hidden");
    modal.classList.remove("flex");

}

document.getElementById("cancelEdit")
.addEventListener("click", closeEditModal);

document.getElementById("saveEdit")
.addEventListener("click", async function(){

    const id = document.getElementById("editId").value;
    const name = document.getElementById("editName").value.trim();
    const phone = document.getElementById("editPhone").value.trim();
    const email = document.getElementById("editEmail").value.trim();
    const address = document.getElementById("editAddress").value.trim();

    if(name=="" || phone==""){
        alert("Nama dan Nomor HP wajib diisi.");
        return;
    }

    const response = await fetch("/customers/" + id, {

        method:"PUT",

        headers:{
            "Content-Type":"application/json",
            "Accept":"application/json",
            "X-CSRF-TOKEN":"{{ csrf_token() }}"
        },

        body: JSON.stringify({ name, phone, email, address })

    });

    const result = await response.json();

    if(result.success){

        alert("Member berhasil diperbarui.");

        closeEditModal();

        loadCustomers();

    }else{

        alert(result.message ?? "Gagal mengubah member.");

    }

});

loadCustomers();

</script>

@endpush