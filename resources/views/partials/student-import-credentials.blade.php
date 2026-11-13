@if (session()->has('import_credentials') && count(session('import_credentials')) > 0)
    <section role="alert" aria-labelledby="student-import-credentials-title" style="margin: 16px 0; padding: 16px; border: 1px solid #e6b74c; border-radius: 12px; background: #fff8e6;">
        <h2 id="student-import-credentials-title" style="margin: 0 0 8px; font-size: 18px;">Kredensial awal siswa — tampil sekali</h2>
        <p style="margin: 0 0 12px;">Simpan atau bagikan password ini secara pribadi sekarang. Password tidak dapat dilihat lagi setelah halaman ini ditinggalkan, dan siswa wajib menggantinya saat login pertama.</p>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th scope="col" style="padding: 8px; text-align: left;">Nama</th>
                        <th scope="col" style="padding: 8px; text-align: left;">NISN (login)</th>
                        <th scope="col" style="padding: 8px; text-align: left;">Password sementara</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (session('import_credentials') as $credential)
                        <tr>
                            <td style="padding: 8px;">{{ $credential['name'] }}</td>
                            <td style="padding: 8px;"><code>{{ $credential['nisn'] }}</code></td>
                            <td style="padding: 8px;"><code>{{ $credential['password'] }}</code></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endif
