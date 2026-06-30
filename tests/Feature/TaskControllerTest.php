<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_index_displays_tasks(): void
    {
        Task::create([
            'title' => 'Cek stok display',
            'description' => 'Pastikan stok rak sesuai catatan.',
            'status' => Task::STATUS_PENDING,
        ]);

        $response = $this->get(route('tasks.index'));

        $response->assertOk();
        $response->assertViewIs('tasks.index');
        $response->assertSee('Cek stok display');
        $response->assertSee('Pending');
    }

    public function test_store_creates_task(): void
    {
        $response = $this->post(route('tasks.store'), [
            'title' => 'Rekap aktivitas kasir',
            'description' => 'Catat aktivitas shift pagi.',
            'status' => Task::STATUS_IN_PROGRESS,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Tugas berhasil ditambahkan.');

        $this->assertDatabaseHas('tasks', [
            'title' => 'Rekap aktivitas kasir',
            'description' => 'Catat aktivitas shift pagi.',
            'status' => Task::STATUS_IN_PROGRESS,
        ]);
    }

    public function test_update_changes_task_status(): void
    {
        $task = Task::create([
            'title' => 'Follow up retur',
            'description' => 'Hubungi supplier untuk retur barang.',
            'status' => Task::STATUS_PENDING,
        ]);

        $response = $this->put(route('tasks.update', $task), [
            'status' => Task::STATUS_COMPLETED,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Status tugas berhasil diperbarui.');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => Task::STATUS_COMPLETED,
        ]);
    }

    public function test_destroy_deletes_task(): void
    {
        $task = Task::create([
            'title' => 'Rapikan catatan harian',
            'status' => Task::STATUS_PENDING,
        ]);

        $response = $this->delete(route('tasks.destroy', $task));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Tugas berhasil dihapus.');

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    public function test_store_rejects_invalid_status(): void
    {
        $response = $this->post(route('tasks.store'), [
            'title' => 'Status salah',
            'status' => 'ditolak',
        ]);

        $response->assertSessionHasErrors('status');

        $this->assertDatabaseMissing('tasks', [
            'title' => 'Status salah',
        ]);
    }
}
