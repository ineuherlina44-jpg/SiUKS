<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminMedicineManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_search_filter_and_view_medicine_stock(): void
    {
        $admin = $this->createAdmin();
        $safeId = $this->createMedicine('OBT-001', 'Paracetamol', 'Analgesik', 20);
        $criticalId = $this->createMedicine('OBT-002', 'Antasida', 'Pencernaan', 5);

        $this->actingAs($admin)
            ->get(route('admin.obat.index'))
            ->assertOk()
            ->assertSee('Master Obat')
            ->assertSee('Paracetamol')
            ->assertSee('Antasida')
            ->assertViewHas('totalMedicines', 2)
            ->assertViewHas('criticalMedicines', 1);

        $this->actingAs($admin)
            ->get(route('admin.obat.index', ['category' => 'Pencernaan', 'status' => 'kritis']))
            ->assertOk()
            ->assertSee('Antasida')
            ->assertDontSee('Paracetamol');

        $this->actingAs($admin)
            ->get(route('admin.obat.index', ['search' => 'OBT-001']))
            ->assertOk()
            ->assertSee('Paracetamol')
            ->assertDontSee('Antasida');

        $this->assertDatabaseHas('obat', ['id' => $safeId]);
        $this->assertDatabaseHas('obat', ['id' => $criticalId]);
    }

    public function test_admin_can_create_and_edit_medicine_in_shared_inventory(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->post(route('admin.obat.store'), [
                'kode_obat' => 'OBT-003',
                'nama_obat' => 'Oralit',
                'jenis' => 'Serbuk',
                'kategori' => 'Rehidrasi',
                'stok_tersedia' => 12,
                'satuan' => 'sachet',
            ])
            ->assertRedirect(route('admin.obat.index'))
            ->assertSessionHas('status', 'Obat berhasil ditambahkan.');

        $medicine = DB::table('obat')->where('kode_obat', 'OBT-003')->firstOrFail();
        $this->actingAs($admin)
            ->put(route('admin.obat.update', $medicine->id), [
                'kode_obat' => 'OBT-003',
                'nama_obat' => 'Oralit 200 ml',
                'jenis' => 'Serbuk',
                'kategori' => 'Rehidrasi',
                'stok_tersedia' => 4,
                'satuan' => 'sachet',
            ])
            ->assertRedirect(route('admin.obat.index'))
            ->assertSessionHas('status', 'Data obat berhasil diperbarui.');

        $this->assertDatabaseHas('obat', [
            'id' => $medicine->id,
            'nama_obat' => 'Oralit 200 ml',
            'stok_tersedia' => 4,
        ]);

        $pmr = User::create([
            'name' => 'Petugas PMR',
            'email' => 'pmr@example.test',
            'role' => 'petugas_pmr',
            'password' => 'password123',
        ]);
        DB::table('petugas_pmr')->insert([
            'id_user' => $pmr->id,
            'status_piket' => 'Aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->actingAs($pmr)
            ->get(route('pmr.obat.index', ['search' => 'Oralit']))
            ->assertOk()
            ->assertSee('Oralit 200 ml')
            ->assertSee('Kritis');
    }

    public function test_medicine_with_dispensing_history_cannot_be_deleted_but_unused_medicine_can(): void
    {
        $admin = $this->createAdmin();
        $medicineId = $this->createMedicine('OBT-004', 'Obat Riwayat', 'Umum', 10);
        $studentUser = User::create([
            'name' => 'Siswa',
            'email' => 'siswa@example.test',
            'role' => 'siswa',
            'password' => 'password123',
        ]);
        $studentId = DB::table('siswa')->insertGetId([
            'id_user' => $studentUser->id,
            'nisn' => '3000000001',
            'kelas' => 'X IPA 1',
            'jenis_kelamin' => 'Laki-laki',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $pmr = User::create([
            'name' => 'Petugas',
            'email' => 'pmr@example.test',
            'role' => 'petugas_pmr',
            'password' => 'password123',
        ]);
        $staffId = DB::table('petugas_pmr')->insertGetId([
            'id_user' => $pmr->id,
            'status_piket' => 'Aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $recordId = DB::table('rekam_medis')->insertGetId([
            'id_siswa' => $studentId,
            'id_petugas' => $staffId,
            'waktu_pemeriksaan' => now(),
            'status' => 'diizinkan_pulang',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('detail_pemberian_obat')->insert([
            'id_rekam_medis' => $recordId,
            'id_obat' => $medicineId,
            'jumlah_dosis' => 1,
            'aturan_pakai' => 'Satu kali',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.obat.destroy', $medicineId))
            ->assertRedirect(route('admin.obat.index'))
            ->assertSessionHas('error', 'Obat tidak dapat dihapus karena sudah tercatat pada riwayat pemberian.');
        $this->assertDatabaseHas('obat', ['id' => $medicineId]);
        $this->assertDatabaseHas('detail_pemberian_obat', ['id_obat' => $medicineId]);

        $unusedId = $this->createMedicine('OBT-005', 'Obat Belum Dipakai', 'Umum', 3);
        $this->actingAs($admin)
            ->delete(route('admin.obat.destroy', $unusedId))
            ->assertRedirect(route('admin.obat.index'))
            ->assertSessionHas('status', 'Obat berhasil dihapus.');
        $this->assertDatabaseMissing('obat', ['id' => $unusedId]);
    }

    public function test_non_admin_cannot_manage_master_medicine(): void
    {
        $pmr = User::create([
            'name' => 'Petugas PMR',
            'email' => 'pmr@example.test',
            'role' => 'petugas_pmr',
            'password' => 'password123',
        ]);

        $this->actingAs($pmr)
            ->get(route('admin.obat.index'))
            ->assertForbidden();
        $this->actingAs($pmr)
            ->post(route('admin.obat.store'), [])
            ->assertForbidden();
    }

    private function createAdmin(): User
    {
        return User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'role' => 'admin',
            'password' => 'password123',
        ]);
    }

    private function createMedicine(string $code, string $name, string $category, int $stock): int
    {
        return DB::table('obat')->insertGetId([
            'kode_obat' => $code,
            'nama_obat' => $name,
            'jenis' => 'Tablet',
            'kategori' => $category,
            'stok_tersedia' => $stock,
            'satuan' => 'tablet',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
