<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PmrObatManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_pmr_can_manage_medicine_inventory(): void
    {
        $staffUser = $this->createPmrUser();

        $this->actingAs($staffUser)
            ->get(route('pmr.obat.index'))
            ->assertOk()
            ->assertSee('Kelola Stok Obat')
            ->assertSee('Tambah obat');

        $this->actingAs($staffUser)
            ->post(route('pmr.obat.store'), [
                'kode_obat' => 'OBT-001',
                'nama_obat' => 'Paracetamol',
                'jenis' => 'Tablet',
                'kategori' => 'Pereda nyeri',
                'stok_tersedia' => 20,
                'satuan' => 'tablet',
            ])
            ->assertRedirect(route('pmr.obat.index'));

        $medicine = DB::table('obat')->where('kode_obat', 'OBT-001')->first();
        $this->assertNotNull($medicine);

        $this->actingAs($staffUser)
            ->put(route('pmr.obat.update', $medicine->id), [
                'kode_obat' => 'OBT-001',
                'nama_obat' => 'Paracetamol 500 mg',
                'jenis' => 'Tablet',
                'kategori' => 'Pereda nyeri',
                'stok_tersedia' => 3,
                'satuan' => 'tablet',
            ])
            ->assertRedirect(route('pmr.obat.index'));

        $this->assertDatabaseHas('obat', [
            'id' => $medicine->id,
            'nama_obat' => 'Paracetamol 500 mg',
            'stok_tersedia' => 3,
        ]);

        $this->actingAs($staffUser)
            ->get(route('pmr.obat.index', ['search' => 'Paracetamol']))
            ->assertOk()
            ->assertSee('Paracetamol 500 mg')
            ->assertSee('Kritis');

        $this->actingAs($staffUser)
            ->delete(route('pmr.obat.destroy', $medicine->id))
            ->assertRedirect(route('pmr.obat.index'));

        $this->assertDatabaseMissing('obat', ['id' => $medicine->id]);
    }

    public function test_medicine_used_in_a_visit_cannot_be_deleted(): void
    {
        $staffUser = $this->createPmrUser();

        $studentUser = User::create([
            'name' => 'Siswa Contoh',
            'email' => 'student@example.test',
            'role' => 'siswa',
            'password' => 'password123',
        ]);
        $studentId = DB::table('siswa')->insertGetId([
            'id_user' => $studentUser->id,
            'nisn' => '1234567890',
            'kelas' => 'XI IPA 1',
            'jenis_kelamin' => 'Perempuan',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $staffId = DB::table('petugas_pmr')->where('id_user', $staffUser->id)->value('id');
        $recordId = DB::table('rekam_medis')->insertGetId([
            'id_siswa' => $studentId,
            'id_petugas' => $staffId,
            'waktu_pemeriksaan' => now(),
            'status' => 'diizinkan_pulang',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $medicineId = DB::table('obat')->insertGetId([
            'kode_obat' => 'OBT-002',
            'nama_obat' => 'Obat Riwayat',
            'jenis' => 'Tablet',
            'kategori' => 'Umum',
            'stok_tersedia' => 10,
            'satuan' => 'tablet',
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

        $this->actingAs($staffUser)
            ->delete(route('pmr.obat.destroy', $medicineId))
            ->assertRedirect(route('pmr.obat.index'))
            ->assertSessionHasErrors('medicine');

        $this->assertDatabaseHas('obat', ['id' => $medicineId]);
        $this->assertDatabaseHas('detail_pemberian_obat', ['id_obat' => $medicineId]);
    }

    public function test_non_pmr_user_cannot_manage_medicine_inventory(): void
    {
        $studentUser = User::create([
            'name' => 'Siswa Contoh',
            'email' => 'student@example.test',
            'role' => 'siswa',
            'password' => 'password123',
        ]);

        $this->actingAs($studentUser)
            ->get(route('pmr.obat.index'))
            ->assertForbidden();
    }

    private function createPmrUser(): User
    {
        $staffUser = User::create([
            'name' => 'Petugas PMR',
            'email' => 'pmr@example.test',
            'role' => 'petugas_pmr',
            'password' => 'password123',
        ]);

        DB::table('petugas_pmr')->insert([
            'id_user' => $staffUser->id,
            'status_piket' => 'Aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $staffUser;
    }
}
